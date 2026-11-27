<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Controllers\Admin\FacturacionController;
use App\Core\Model;
use App\Core\Request;
use App\Core\Session;
use App\Core\TenantContext;
use App\Middleware\CsrfMiddleware;
use App\Models\FacturaElectronica;
use App\Services\Factus\AuthenticatedFactusClient;
use App\Services\Factus\ClockInterface;
use App\Services\Factus\FactusAuthService;
use App\Services\Factus\FactusClientInterface;
use App\Services\Factus\FactusConfig;
use App\Services\Factus\FactusToken;
use App\Services\Factus\TokenStoreInterface;
use Dotenv\Dotenv;
use PDO;
use PHPUnit\Framework\TestCase;

final class FacturacionPersistenceTest extends TestCase
{
    public function testPersistsOnceAndScopesInvoiceHistoryAndAdminViews(): void
    {
        if (getenv('MESALY_TEST_FACTURACION_MYSQL') !== '1') {
            self::markTestSkipped('Activa MESALY_TEST_FACTURACION_MYSQL=1 para verificar el esquema de facturación en MySQL local.');
        }
        Dotenv::createImmutable(dirname(__DIR__, 2))->load();
        $config = require dirname(__DIR__, 2) . '/config/database.php';
        self::assertContains($config['host'], ['127.0.0.1', 'localhost']);
        $pdo = new PDO("mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset=utf8mb4",
            $config['username'], $config['password'], $config['options']);
        $schema = file_get_contents(dirname(__DIR__, 2) . '/database/migrations/002_facturacion_electronica.sql');
        foreach (explode(';', $schema) as $statement) {
            if (trim($statement) === '') {
                continue;
            }
            $temporary = preg_replace('/^\s*CREATE TABLE IF NOT EXISTS/', 'CREATE TEMPORARY TABLE', $statement);
            $temporary = preg_replace('/,\s*FOREIGN KEY[^\r\n]+?(?=,?\r?\n)/', '', $temporary);
            $pdo->exec($temporary);
        }
        $pdo->exec("CREATE TEMPORARY TABLE pedidos (
            id BIGINT PRIMARY KEY, restaurante_id BIGINT, codigo VARCHAR(30), consecutivo INT,
            cliente_nombre VARCHAR(255), estado VARCHAR(20), total INT, created_at DATETIME,
            mesa_id BIGINT NULL, subtotal INT, costo_envio INT, metodo_pago VARCHAR(20)
        ) ENGINE=InnoDB");
        $pdo->exec("INSERT INTO pedidos VALUES
            (10, 1, 'TEST-10', 10, 'Persona de prueba', 'entregado', 1000, NOW(), 1, 1000, 0, 'efectivo'),
            (11, 2, 'TEST-11', 11, 'Persona de prueba', 'entregado', 1000, NOW(), NULL, 1000, 0, 'efectivo'),
            (12, 1, 'TEST-12', 12, 'Persona de prueba', 'entregado', 1000, NOW(), NULL, 1000, 0, 'efectivo')");
        $pdo->exec('CREATE TEMPORARY TABLE mesas (id BIGINT PRIMARY KEY, numero INT, nombre VARCHAR(100))');
        $pdo->exec('CREATE TEMPORARY TABLE pedido_items (id BIGINT PRIMARY KEY, pedido_id BIGINT, producto_id BIGINT,
            cantidad INT, total_linea INT, precio_unitario INT, nombre_producto VARCHAR(100))');
        $pdo->exec("INSERT INTO pedido_items VALUES (20, 12, 22, 1, 1000, 1000, 'Producto de prueba')");
        $pdo->exec('CREATE TEMPORARY TABLE pedido_item_opciones (id BIGINT PRIMARY KEY, pedido_item_id BIGINT)');
        self::assertInstanceOf(PDO::class, $pdo);
        Model::setPdo($pdo);
        try {
            $model = new FacturaElectronica();
            $snapshot = ['payload' => ['numbering_range_id' => 389, 'customer' => ['names' => 'Consumidor Final']],
                'preview' => ['total' => '1000.00']];
            $model->saveSettings(1, 1, [
                'habilitada' => 1, 'rango_id' => 389, 'prefijo' => 'SETP',
                'fiscal' => ['nit' => '123', 'razon_social' => 'Pruebas'],
                'impuestos' => [22 => ['code' => 'excluded', 'rate' => '0']], 'credenciales' => null,
            ], false);
            self::assertSame('SETP', $model->settings(1)['prefijo']);
            $pending = ['q' => '', 'estado' => 'sin_factura', 'desde' => '', 'hasta' => ''];
            self::assertSame([12, 10], array_column($model->listing(1, $pending, 1)['rows'], 'id'));
            self::assertSame([11], array_column($model->listing(2, $pending, 1)['rows'], 'id'));
            $id = $model->reserve(1, 10, 1, str_repeat('a', 64), 'MESALY-SB-test', $snapshot);
            self::assertIsInt($id);
            self::assertNull($model->reserve(1, 10, 1, str_repeat('a', 64), 'MESALY-SB-another', $snapshot));
            self::assertNull($model->invoice(2, 10));
            $model->record(1, $id, 1, 0, ['estado' => 'incierta', 'error' => 'Sin confirmación']);
            $model->record(1, $id, 1, 0, ['estado' => 'rechazada']);
            self::assertSame('incierta', $model->invoice(1, 10)['estado']);
            self::assertFalse($model->claimRetry(1, $id, 1, 1));
            $pdo->exec('UPDATE facturas_electronicas SET updated_at = DATE_SUB(NOW(), INTERVAL 6 MINUTE)');
            self::assertTrue($model->claimRetry(1, $id, 1, 1));
            self::assertFalse($model->claimRetry(1, $id, 1, 1));
            $model->record(1, $id, 1, 2, ['estado' => 'validada', 'numero' => 'SETP1']);
            $model->record(1, $id, 1, 3, ['estado' => 'rechazada']);
            self::assertSame('validada', $model->invoice(1, 10)['estado']);
            self::assertCount(4, $model->events(1, $id));
            $list = $model->listing(1, ['q' => 'SETP1', 'estado' => 'validada', 'desde' => '', 'hasta' => ''], 1);
            self::assertSame([10], array_column($list['rows'], 'id'));
            self::assertSame([12], array_column($model->listing(1, $pending, 1)['rows'], 'id'));

            Session::set('user', ['id' => 1, 'rol' => 'admin_negocio', 'restaurante_id' => 1, 'nombre' => 'Pruebas']);
            TenantContext::setTenant(['id' => 1, 'slug' => 'pruebas', 'nombre' => 'Pruebas']);
            $response = (new FacturacionController($model))->index(new Request('GET', '/admin/facturacion'));
            self::assertSame(200, $response->getStatusCode());
            self::assertStringContainsString('Factus Sandbox', $response->getContent());
            self::assertStringContainsString('SETP1', $response->getContent());
            self::assertStringContainsString('Generar factura electrónica', $response->getContent());
            self::assertStringNotContainsString('TEST-11', $response->getContent());
            $controller = new FacturacionController($model, fn (): array => $this->simulatedConnection());
            $settings = $controller->settings(new Request('GET', '/admin/facturacion/configuracion'));
            self::assertStringContainsString('Verificar conexión y consultar rangos', $settings->getContent());
            $rangeRequest = new Request('POST', '/admin/facturacion/configuracion/rangos');
            self::assertSame(403, (new CsrfMiddleware())->handle($rangeRequest)?->getStatusCode());
            $ranges = $controller->ranges($rangeRequest);
            self::assertSame(200, $ranges->getStatusCode());
            self::assertSame('no-store', $ranges->getHeader('Cache-Control'));
            self::assertSame(['ranges' => [[
                'id' => 389, 'prefix' => 'SETP', 'resolution_number' => '18760000001',
                'start_date' => '2000-01-01', 'end_date' => '2099-01-01',
            ]]], json_decode($ranges->getContent(), true, 16, JSON_THROW_ON_ERROR));
            self::assertSame(422, $controller->ranges(new Request(
                'POST', '/admin/facturacion/configuracion/rangos', ['username' => 'parcial'],
            ))->getStatusCode());
            self::assertNull($model->settings(1)['credenciales']);
            $form = $controller->show(new Request('GET', '/admin/facturacion/pedidos/12'), ['id' => 12]);
            self::assertSame(200, $form->getStatusCode());
            self::assertStringContainsString('Producto de prueba', $form->getContent());
            $review = $controller->review(new Request('POST', '/admin/facturacion/pedidos/12/revisar',
                ['buyer_type' => 'final', 'payment_form' => '1', 'payment_method' => '10']), ['id' => 12]);
            self::assertSame(200, $review->getStatusCode());
            self::assertStringContainsString('Confirmar emisión', $review->getContent());
            $wrongTicket = new Request('POST', '/admin/facturacion/pedidos/12/emitir', ['confirm' => '1', 'ticket' => 'incorrecto']);
            self::assertTrue($controller->issue($wrongTicket, ['id' => 12])->isRedirection());
            self::assertNull($model->invoice(1, 12));
            $confirmed = new Request('POST', '/admin/facturacion/pedidos/12/emitir', [
                'confirm' => '1', 'ticket' => Session::get('facturacion_confirmacion')['ticket'],
            ]);
            self::assertTrue($controller->issue($confirmed, ['id' => 12])->isRedirection());
            self::assertSame('validada', $model->invoice(1, 12)['estado']);
            self::assertSame('SETP2', $model->invoice(1, 12)['numero']);
            $pdf = $controller->document(new Request('GET', '/admin/facturacion/pedidos/12/documentos/pdf'),
                ['id' => 12, 'formato' => 'pdf']);
            self::assertSame('%PDF-1.7 Factus de prueba', $pdf->getContent());
            $issue = new Request('POST', '/admin/facturacion/pedidos/10/emitir', ['confirm' => '1']);
            self::assertSame(403, (new CsrfMiddleware())->handle($issue)?->getStatusCode());
            self::assertTrue((new FacturacionController($model))->issue($issue, ['id' => 10])->isRedirection());
            self::assertSame('MESALY-SB-test', $model->invoice(1, 10)['referencia']);
            self::assertSame(404, (new FacturacionController($model))
                ->document(new Request('GET', '/admin/facturacion/pedidos/11/documentos/pdf'), ['id' => 11, 'formato' => 'pdf'])
                ->getStatusCode());

            Session::set('user', ['id' => 1, 'rol' => 'super_admin', 'restaurante_id' => 1]);
            self::assertSame(403, (new FacturacionController($model))->index(new Request('GET', '/admin/facturacion'))->getStatusCode());
        } finally {
            Session::remove('user');
            TenantContext::clear();
            Model::setPdo(null);
        }
    }

    private function simulatedConnection(): array
    {
        $client = $this->createStub(FactusClientInterface::class);
        $client->method('requestJson')->willReturnCallback(
            static function (string $method, string $path, array $body): array {
                if ($path === '/v2/numbering-ranges?filter%5Bdocument%5D=21&page=1') {
                    return ['status' => 'OK', 'data' => [
                        'data' => [['id' => 389, 'document' => 'Factura de Venta', 'prefix' => 'SETP', 'is_active' => true,
                            'resolution_number' => '18760000001', 'is_expired' => false,
                            'start_date' => '2000-01-01', 'end_date' => '2099-01-01']],
                        'pagination' => ['current_page' => 1, 'last_page' => 1],
                    ]];
                }
                if ($path === '/v2/bills/SETP2/download-pdf') {
                    return ['data' => ['pdf_base_64_encoded' => base64_encode('%PDF-1.7 Factus de prueba')]];
                }
                self::assertSame('POST', $method);
                self::assertSame('/v2/bills/validate', $path);
                self::assertSame(389, $body['numbering_range_id']);
                self::assertSame('1000.00', $body['payment_details'][0]['amount']);
                self::assertSame('P-22', $body['items'][0]['code_reference']);
                return ['data' => ['reference_code' => $body['reference_code'], 'number' => 'SETP2',
                    'cufe' => str_repeat('b', 96), 'is_validated' => true]];
            }
        );
        $clock = $this->createStub(ClockInterface::class);
        $clock->method('now')->willReturn(1000);
        $store = $this->createStub(TokenStoreInterface::class);
        $store->method('load')->willReturn(new FactusToken('test-access', '', 'Bearer', 5000));
        $config = new FactusConfig('sandbox', 'https://api-sandbox.factus.com.co', '', '', '', '', '');
        $auth = new FactusAuthService($config, $client, $store, $clock, '');
        return [str_repeat('a', 64), new AuthenticatedFactusClient($client, $auth)];
    }
}
