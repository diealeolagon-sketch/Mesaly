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

        $request = new Request('POST', '/la-parrilla-paisa/pedido', $payload);
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

        $request = new Request('POST', '/la-parrilla-paisa/pedido', $payloadSpam);
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

        $request = new Request('POST', '/la-parrilla-paisa/pedido', $payload);
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

        $request = new Request('POST', '/la-parrilla-paisa/pedido', $payload);
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

        $request = new Request('POST', '/la-parrilla-paisa/pedido', $payloadHack);
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

        $request = new Request('POST', '/la-parrilla-paisa/pedido', $payloadSinDir);
        $resSinDir = $this->menuController->crearPedido($request, ['slug' => 'la-parrilla-paisa']);
        $this->assertSame(400, $resSinDir->getStatusCode());
        $data = json_decode($resSinDir->getContent(), true);
        $this->assertStringContainsString('dirección de entrega', $data['error']);
    }
}
