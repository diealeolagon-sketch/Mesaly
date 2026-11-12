<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Controllers\Super\NegocioController;
use App\Core\Auth;
use App\Core\Model;
use App\Core\Request;
use App\Core\Session;
use App\Models\Restaurante;
use App\Models\Usuario;
use App\Services\SuscripcionService;
use PDO;
use PHPUnit\Framework\TestCase;

class SuperAdminFunctionalTest extends TestCase
{
    private static PDO $pdo;
    private Restaurante $restauranteModel;
    private Usuario $usuarioModel;
    private NegocioController $superController;

    public static function setUpBeforeClass(): void
    {
        $dotenv = \Dotenv\Dotenv::createImmutable(dirname(__DIR__, 2));
        $dotenv->safeLoad();

        self::$pdo = Model::getPdo();
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->restauranteModel = new Restaurante();
        $this->usuarioModel = new Usuario();
        $this->superController = new NegocioController();

        // Autenticar como Super Admin para pruebas
        $superUser = $this->usuarioModel->findByEmail('superadmin@mesaly.co');
        if ($superUser) {
            Auth::login($superUser);
        }
    }

    protected function tearDown(): void
    {
        Auth::logout();
        parent::tearDown();
    }

    public function testListadoNegociosYConteoEstados(): void
    {
        $request = new Request('GET', '/superadmin', [], ['filtro' => 'todos']);
        $response = $this->superController->index($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('Vencimientos y Negocios', $response->getContent());
        $this->assertStringContainsString('La Parrilla Paisa', $response->getContent());
    }

    public function testFiltroPorBusqueda(): void
    {
        $request = new Request('GET', '/superadmin', [], ['filtro' => 'todos', 'q' => 'parrilla']);
        $response = $this->superController->index($request);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString('La Parrilla Paisa', $response->getContent());

        // Búsqueda inexistente: no debe aparecer en la tabla de resultados
        $requestInexistente = new Request('GET', '/superadmin', [], ['filtro' => 'todos', 'q' => 'restaurante_fantasma_xyz']);
        $responseInexistente = $this->superController->index($requestInexistente);
        $this->assertSame(200, $responseInexistente->getStatusCode());
        $this->assertStringNotContainsString('data-nombre="La Parrilla Paisa"', $responseInexistente->getContent());
    }

    public function testCrearNegocioValidacionYPersistencia(): void
    {
        $testSlug = 'test-bistro-' . time();
        $testEmail = 'admin.' . time() . '@testbistro.co';

        $postData = [
            'nombre' => 'Test Bistro Gourmet',
            'slug' => $testSlug,
            'whatsapp' => '573109876543',
            'precio' => '59000',
            'ciclo' => 'mensual',
            'dias_gracia' => '5',
            'notas_internas' => 'Cliente de prueba automatizada',
            'admin_nombre' => 'Chef Pierre',
            'admin_email' => $testEmail,
            'en_prueba' => '1',
        ];

        $request = new Request('POST', '/superadmin/negocios/crear', $postData);
        $response = $this->superController->crear($request);

        // Debe renderizar la pantalla de credenciales iniciales
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringContainsString($testEmail, $response->getContent());

        // Verificar que el restaurante se creó en la BD
        $creado = $this->restauranteModel->findBySlug($testSlug);
        $this->assertNotNull($creado);
        $this->assertSame('Test Bistro Gourmet', $creado['nombre']);
        $this->assertSame(59000, (int)$creado['precio']);
        $this->assertSame(5, (int)$creado['dias_gracia']);
        $this->assertSame(1, (int)$creado['en_prueba']);

        // Verificar que el usuario administrador fue creado
        $usuarioCreado = $this->usuarioModel->findByEmail($testEmail);
        $this->assertNotNull($usuarioCreado);
        $this->assertSame((int)$creado['id'], (int)$usuarioCreado['restaurante_id']);
        $this->assertSame(Auth::ROLE_ADMIN_NEGOCIO, $usuarioCreado['rol']);

        // Cleanup
        self::$pdo->prepare("DELETE FROM `usuarios` WHERE `id` = :id")->execute(['id' => $usuarioCreado['id']]);
        self::$pdo->prepare("DELETE FROM `restaurantes` WHERE `id` = :id")->execute(['id' => $creado['id']]);
    }

    public function testRegistrarPagoManualExtiendeFecha(): void
    {
        $demo = $this->restauranteModel->findBySlug('la-parrilla-paisa');
        $this->assertNotNull($demo);
        $rid = (int)$demo['id'];
        $fechaVencOriginal = $demo['fecha_vencimiento'];

        $postPago = [
            'monto' => '49000',
            'metodo' => 'bancolombia',
            'referencia' => 'TRANS-TEST-999',
            'meses' => '1',
            'nota' => 'Pago de prueba unitaria',
        ];

        $request = new Request('POST', "/superadmin/negocios/{$rid}/pago", $postPago);
        $response = $this->superController->registrarPago($request, ['id' => (string)$rid]);

        $this->assertTrue($response->isRedirection());

        // Verificar que se insertó el pago
        $stmtPago = self::$pdo->prepare("SELECT * FROM `pagos_suscripcion` WHERE `referencia` = 'TRANS-TEST-999'");
        $stmtPago->execute();
        $pagoRow = $stmtPago->fetch(PDO::FETCH_ASSOC);
        $this->assertNotNull($pagoRow);
        $this->assertSame(49000, (int)$pagoRow['monto']);
        $this->assertSame('bancolombia', $pagoRow['metodo']);

        // Verificar que el vencimiento cambió y es posterior
        $demoActualizado = $this->restauranteModel->find($rid);
        $this->assertGreaterThanOrEqual($fechaVencOriginal, $demoActualizado['fecha_vencimiento']);

        // Cleanup del pago de prueba y restaurar fecha original
        self::$pdo->prepare("DELETE FROM `pagos_suscripcion` WHERE `id` = :id")->execute(['id' => $pagoRow['id']]);
        self::$pdo->prepare("UPDATE `restaurantes` SET `fecha_vencimiento` = :fv WHERE `id` = :id")->execute([
            'fv' => $fechaVencOriginal,
            'id' => $rid,
        ]);
    }

    public function testExtenderDiasManualmente(): void
    {
        $demo = $this->restauranteModel->findBySlug('la-parrilla-paisa');
        $this->assertNotNull($demo);
        $rid = (int)$demo['id'];
        $fechaVencOriginal = $demo['fecha_vencimiento'];

        $request = new Request('POST', "/superadmin/negocios/{$rid}/extender", [
            'dias' => '7',
            'motivo' => 'Cortesía de prueba',
        ]);
        $response = $this->superController->extenderDias($request, ['id' => (string)$rid]);

        $this->assertTrue($response->isRedirection());

        $demoPost = $this->restauranteModel->find($rid);
        $fechaCalculadaEsperada = date('Y-m-d', strtotime($fechaVencOriginal . ' +7 days'));
        $this->assertSame($fechaCalculadaEsperada, $demoPost['fecha_vencimiento']);

        // Restaurar fecha
        self::$pdo->prepare("UPDATE `restaurantes` SET `fecha_vencimiento` = :fv WHERE `id` = :id")->execute([
            'fv' => $fechaVencOriginal,
            'id' => $rid,
        ]);
    }

    public function testImpersonationVerComoYDejarVerComo(): void
    {
        $demo = $this->restauranteModel->findBySlug('la-parrilla-paisa');
        $this->assertNotNull($demo);
        $rid = (int)$demo['id'];

        $request = new Request('POST', "/superadmin/negocios/{$rid}/ver-como");
        $response = $this->superController->verComo($request, ['id' => (string)$rid]);

        $this->assertTrue($response->isRedirection());
        $this->assertSame($rid, Session::get('impersonating_tenant_id'));

        // Salir del modo ver como
        $requestSalir = new Request('POST', '/superadmin/dejar-ver-como');
        $responseSalir = $this->superController->dejarVerComo($requestSalir);

        $this->assertTrue($responseSalir->isRedirection());
        $this->assertNull(Session::get('impersonating_tenant_id'));
    }

    public function testMetricasGlobalesYHistorialPagosJson(): void
    {
        $requestMetricas = new Request('GET', '/superadmin/metricas');
        $responseMetricas = $this->superController->metricas($requestMetricas);

        $this->assertSame(200, $responseMetricas->getStatusCode());
        $this->assertStringContainsString('Métricas Globales de la Red', $responseMetricas->getContent());
        $this->assertStringContainsString('Recaudación de Suscripciones', $responseMetricas->getContent());

        // Historial pagos JSON
        $demo = $this->restauranteModel->findBySlug('la-parrilla-paisa');
        $rid = (int)$demo['id'];
        $requestJson = new Request('GET', "/superadmin/negocios/{$rid}/pagos-json");
        $responseJson = $this->superController->historialPagosJson($requestJson, ['id' => (string)$rid]);

        $this->assertSame(200, $responseJson->getStatusCode());
        $data = json_decode($responseJson->getContent(), true);
        $this->assertTrue($data['success']);
        $this->assertIsArray($data['pagos']);
    }
}
