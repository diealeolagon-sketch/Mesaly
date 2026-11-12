<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Controllers\Public\MenuController;
use App\Core\Model;
use App\Core\Request;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Restaurante;
use PDO;
use PHPUnit\Framework\TestCase;

class PublicOrderingAndEdgeCasesTest extends TestCase
{
    private static PDO $pdo;
    private MenuController $menuController;
    private array $restaurante;
    private int $restauranteId;
    private array $productoChurrasco;

    public static function setUpBeforeClass(): void
    {
        $dotenv = \Dotenv\Dotenv::createImmutable(dirname(__DIR__, 2));
        $dotenv->safeLoad();

        self::$pdo = Model::getPdo();
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->menuController = new MenuController();
        $this->restaurante = (new Restaurante())->findBySlug('la-parrilla-paisa');
        $this->assertNotNull($this->restaurante);
        $this->restauranteId = (int)$this->restaurante['id'];

        // Obtener Churrasco del catálogo
        $catalogo = (new Producto())->allForPublicCatalog($this->restauranteId);
        foreach ($catalogo as $p) {
            if (str_contains($p['nombre'], 'Churrasco')) {
                $this->productoChurrasco = $p;
                break;
            }
        }
        $this->assertNotEmpty($this->productoChurrasco);
    }

    private function makeOrderRequest(array $payload): Request
    {
        $uniqueIp = '10.99.' . mt_rand(1, 250) . '.' . mt_rand(1, 250);
        return new Request('POST', '/la-parrilla-paisa/pedido', $payload, [], [], [], [], $uniqueIp);
    }

    public function testBloqueoPedidoCuandoRestauranteEstaPausado(): void
    {
        // 1. Pausar recepción de pedidos
        self::$pdo->prepare("UPDATE `restaurantes` SET `recibiendo_pedidos` = 0 WHERE `id` = :id")
            ->execute(['id' => $this->restauranteId]);

        $payload = [
            'modalidad' => 'mesa',
            'mesa_numero' => 5,
            'items' => [
                [
                    'producto_id' => $this->productoChurrasco['id'],
                    'cantidad' => 1,
                    'opciones_ids' => [1],
                ],
            ],
        ];

        $request = $this->makeOrderRequest($payload);
        $response = $this->menuController->crearPedido($request, ['slug' => 'la-parrilla-paisa']);

        $this->assertSame(400, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertFalse($data['ok']);
        $this->assertStringContainsString('no está recibiendo pedidos', $data['error']);

        // Restaurar estado activo
        self::$pdo->prepare("UPDATE `restaurantes` SET `recibiendo_pedidos` = 1 WHERE `id` = :id")
            ->execute(['id' => $this->restauranteId]);
    }

    public function testHoneypotAntiBot(): void
    {
        $payloadSpam = [
            'empresa' => 'Bot Spammer S.A.S',
            'modalidad' => 'mesa',
            'mesa_numero' => 3,
            'items' => [['producto_id' => $this->productoChurrasco['id'], 'cantidad' => 1]],
        ];

        $request = $this->makeOrderRequest($payloadSpam);
        $response = $this->menuController->crearPedido($request, ['slug' => 'la-parrilla-paisa']);

        $this->assertSame(400, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertFalse($data['ok']);
        $this->assertSame('Solicitud rechazada.', $data['error']);
    }

    public function testCarritoVacioRechazado(): void
    {
        $payload = [
            'modalidad' => 'mesa',
            'mesa_numero' => 3,
            'items' => [],
        ];

        $request = $this->makeOrderRequest($payload);
        $response = $this->menuController->crearPedido($request, ['slug' => 'la-parrilla-paisa']);

        $this->assertSame(400, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertStringContainsString('carrito de compras está vacío', $data['error']);
    }

    public function testValidacionModificadorObligatorio(): void
    {
        // Enviar Churrasco sin seleccionar el término de la carne (que es obligatorio)
        $payload = [
            'modalidad' => 'mesa',
            'mesa_numero' => 3,
            'items' => [
                [
                    'producto_id' => $this->productoChurrasco['id'],
                    'cantidad' => 1,
                    'opciones_ids' => [], // Omitido deliberadamente
                ],
            ],
        ];

        $request = $this->makeOrderRequest($payload);
        $response = $this->menuController->crearPedido($request, ['slug' => 'la-parrilla-paisa']);

        $this->assertSame(400, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertFalse($data['ok']);
        $this->assertStringContainsString('opciones requeridas', $data['error']);
    }

    public function testRecalculoServidorInmuneAAlteracionDePrecios(): void
    {
        // Buscar las opciones del grupo del churrasco
        $opcTermino = $this->productoChurrasco['grupos'][0]['opciones'][0]; // Extra $0
        $opcAdicional = null;
        foreach ($this->productoChurrasco['grupos'] as $g) {
            foreach ($g['opciones'] as $o) {
                if ($o['precio_extra'] > 0) {
                    $opcAdicional = $o;
                    break 2;
                }
            }
        }

        $opcionesIds = [(int)$opcTermino['id']];
        $extraEsperado = 0;
        if ($opcAdicional) {
            $opcionesIds[] = (int)$opcAdicional['id'];
            $extraEsperado += (int)$opcAdicional['precio_extra'];
        }

        $precioRealEsperado = (int)$this->productoChurrasco['precio'] + $extraEsperado;

        // Intentar hackear enviando precio manipulado en el payload (ej. $100 pesos)
        $payloadHack = [
            'modalidad' => 'mesa',
            'mesa_numero' => 4,
            'items' => [
                [
                    'producto_id' => $this->productoChurrasco['id'],
                    'precio_unitario' => 100, // Intento de alteración fraudulenta
                    'cantidad' => 1,
                    'opciones_ids' => $opcionesIds,
                ],
            ],
        ];

        $request = $this->makeOrderRequest($payloadHack);
        $response = $this->menuController->crearPedido($request, ['slug' => 'la-parrilla-paisa']);

        $this->assertSame(200, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertTrue($data['ok']);

        // El servidor debió calcular el total auténtico ignorando los $100
        $this->assertSame($precioRealEsperado, $data['total']);
        $this->assertNotEmpty($data['whatsapp_url']);
        $this->assertStringContainsString('https://wa.me/', $data['whatsapp_url']);

        // Cleanup del pedido creado en la BD
        self::$pdo->prepare("DELETE FROM `pedido_items` WHERE `pedido_id` IN (SELECT id FROM `pedidos` WHERE `codigo` = :cod)")
            ->execute(['cod' => $data['codigo']]);
        self::$pdo->prepare("DELETE FROM `pedidos` WHERE `codigo` = :cod")
            ->execute(['cod' => $data['codigo']]);
    }

    public function testDomicilioValidaCamposRequeridosYPedidoMinimo(): void
    {
        // 1. Falta teléfono o dirección
        $payloadSinDir = [
            'modalidad' => 'domicilio',
            'cliente_nombre' => 'Carlos Cliente',
            'cliente_telefono' => '3110001122',
            'direccion' => '', // Vacío
            'items' => [
                [
                    'producto_id' => $this->productoChurrasco['id'],
                    'cantidad' => 1,
                    'opciones_ids' => [(int)$this->productoChurrasco['grupos'][0]['opciones'][0]['id']],
                ],
            ],
        ];

        $request = $this->makeOrderRequest($payloadSinDir);
        $resSinDir = $this->menuController->crearPedido($request, ['slug' => 'la-parrilla-paisa']);
        $this->assertSame(400, $resSinDir->getStatusCode());
        $data = json_decode($resSinDir->getContent(), true);
        $this->assertStringContainsString('dirección de entrega', $data['error']);
    }

    public function testPedidoEnMesaNoRequiereDireccionNiTelefonoYAutogeneraNombre(): void
    {
        $payloadMesa = [
            'modalidad' => 'mesa',
            'mesa_numero' => 8,
            'cliente_nombre' => '', // Vacío deliberadamente
            'cliente_telefono' => '', // Vacío deliberadamente
            'direccion' => 'Calle Falsa 123 (debe ignorarse)',
            'barrio' => 'Barrio Fantasma (debe ignorarse)',
            'notas_entrega' => 'Bebidas sin hielo por favor',
            'items' => [
                [
                    'producto_id' => $this->productoChurrasco['id'],
                    'cantidad' => 1,
                    'opciones_ids' => [(int)$this->productoChurrasco['grupos'][0]['opciones'][0]['id']],
                ],
            ],
        ];

        $request = $this->makeOrderRequest($payloadMesa);
        $response = $this->menuController->crearPedido($request, ['slug' => 'la-parrilla-paisa']);

        $this->assertSame(200, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertTrue($data['ok']);

        // Consultar registro guardado en la BD
        $stmt = self::$pdo->prepare("SELECT p.*, m.numero AS mesa_numero_rel FROM `pedidos` p LEFT JOIN `mesas` m ON p.mesa_id = m.id WHERE p.codigo = :cod");
        $stmt->execute(['cod' => $data['codigo']]);
        $pedidoDb = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertNotNull($pedidoDb);
        $this->assertSame('mesa', $pedidoDb['modalidad']);
        $this->assertGreaterThan(0, (int)$pedidoDb['mesa_id']);
        $this->assertSame(8, (int)$pedidoDb['mesa_numero_rel']);
        // Nombre generado automáticamente
        $this->assertSame('Mesa 8', $pedidoDb['cliente_nombre']);
        // Teléfono, dirección y barrio deben ser NULL en mesa
        $this->assertNull($pedidoDb['cliente_telefono']);
        $this->assertNull($pedidoDb['direccion']);
        $this->assertNull($pedidoDb['barrio']);
        // Notas para la cocina se conservan
        $this->assertSame('Bebidas sin hielo por favor', $pedidoDb['notas_entrega']);

        // WhatsApp URL no debe contener 'Dirección' ni 'Barrio'
        $this->assertStringNotContainsString('Direcci', urldecode($data['whatsapp_url']));
        $this->assertStringContainsString('Mesa #8', urldecode($data['whatsapp_url']));

        // Cleanup
        self::$pdo->prepare("DELETE FROM `pedido_items` WHERE `pedido_id` = :id")->execute(['id' => $pedidoDb['id']]);
        self::$pdo->prepare("DELETE FROM `pedidos` WHERE `id` = :id")->execute(['id' => $pedidoDb['id']]);
    }

    public function testEnlaceNormalSinMesaPermiteDomicilioYSalirMesa(): void
    {
        // 1. Simular que el usuario tenía una mesa previa en sesión
        $_SESSION['mesaly_mesa_' . $this->restauranteId] = [
            'numero' => 4,
            'expires_at' => time() + 3600,
        ];

        // 2. Al entrar al enlace normal SIN parámetro ?mesa, la sesión de mesa debe limpiarse
        $reqNormal = new Request('GET', '/la-parrilla-paisa');
        $respNormal = $this->menuController->show($reqNormal, ['slug' => 'la-parrilla-paisa']);
        $this->assertSame(200, $respNormal->getStatusCode());
        $this->assertArrayNotHasKey('mesaly_mesa_' . $this->restauranteId, $_SESSION);

        // 3. Al entrar explícitamente con ?mesa=2 se activa la mesa
        $reqMesa = new Request('GET', '/la-parrilla-paisa', [], ['mesa' => 2]);
        $respMesa = $this->menuController->show($reqMesa, ['slug' => 'la-parrilla-paisa']);
        $this->assertSame(200, $respMesa->getStatusCode());
        $this->assertArrayHasKey('mesaly_mesa_' . $this->restauranteId, $_SESSION);
        $this->assertSame(2, $_SESSION['mesaly_mesa_' . $this->restauranteId]['numero']);

        // 4. Al solicitar salir de mesa (?salir_mesa=1) se limpia
        $reqSalir = new Request('GET', '/la-parrilla-paisa', [], ['salir_mesa' => '1']);
        $respSalir = $this->menuController->show($reqSalir, ['slug' => 'la-parrilla-paisa']);
        $this->assertSame(200, $respSalir->getStatusCode());
        $this->assertArrayNotHasKey('mesaly_mesa_' . $this->restauranteId, $_SESSION);
    }
}
