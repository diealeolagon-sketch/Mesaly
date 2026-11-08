<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Controllers\Public\MenuController;
use App\Core\Request;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Restaurante;
use PHPUnit\Framework\TestCase;

class PublicMenuAndPedidoTest extends TestCase
{
    private ?array $restauranteDemo = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->restauranteDemo = (new Restaurante())->findBySlug('la-parrilla-paisa');
    }

    public function testRestauranteDemoExistsWithCatalogAndModifiers(): void
    {
        $this->assertNotNull($this->restauranteDemo, 'El restaurante demo La Parrilla Paisa debe existir en los seeds.');
        $restauranteId = (int)$this->restauranteDemo['id'];

        $productoModel = new Producto();
        $catalogo = $productoModel->allForPublicCatalog($restauranteId);

        $this->assertNotEmpty($catalogo, 'El catálogo público debe devolver productos para el restaurante.');

        // Verificar que los platos con modificadores los tienen cargados
        $churrasco = null;
        foreach ($catalogo as $p) {
            if (str_contains($p['nombre'], 'Churrasco')) {
                $churrasco = $p;
                break;
            }
        }

        $this->assertNotNull($churrasco, 'Debe existir el plato Churrasco.');
        $this->assertNotEmpty($churrasco['grupos'], 'Churrasco debe tener grupos de modificadores asignados.');
        
        $nombresGrupos = array_column($churrasco['grupos'], 'nombre');
        $this->assertContains('Término de la Carne', $nombresGrupos);
    }

    public function testCrearPedidoCompletoInDatabase(): void
    {
        $restauranteId = (int)$this->restauranteDemo['id'];
        $pedidoModel = new Pedido();

        $datosPedido = [
            'modalidad' => 'mesa',
            'mesa_id' => null,
            'mesa_numero' => 7,
            'cliente_nombre' => 'Test Mesa 7',
            'cliente_telefono' => '3001234567',
            'direccion' => null,
            'barrio' => null,
            'notas_entrega' => null,
            'hora_recogida' => null,
            'metodo_pago' => 'efectivo',
            'subtotal' => 46000,
            'costo_envio' => 0,
            'total' => 46000,
            'ip' => '127.0.0.1',
        ];

        $items = [
            [
                'producto_id' => 4,
                'nombre_producto' => 'Churrasco al Carbón 350g',
                'precio_unitario' => 42000,
                'cantidad' => 1,
                'nota' => 'Término medio por favor',
                'total_linea' => 46000,
                'opciones' => [
                    ['nombre_grupo' => 'Término de la Carne', 'nombre_opcion' => 'Término Medio (Jugoso)', 'precio_extra' => 0],
                    ['nombre_grupo' => 'Adicionales Especiales', 'nombre_opcion' => 'Porción de Tocineta Crocante', 'precio_extra' => 4000],
                ],
            ],
        ];

        $pedido = $pedidoModel->crearPedidoCompleto($this->restauranteDemo, $datosPedido, $items);

        $this->assertNotEmpty($pedido['codigo']);
        $this->assertStringStartsWith('LAP-', $pedido['codigo']);
        $this->assertSame(46000, $pedido['total']);
        $this->assertSame('mesa', $pedido['modalidad']);
    }

    public function testHoneypotRejectsBots(): void
    {
        $controller = new MenuController();

        $request = new Request(
            'POST',
            '/la-parrilla-paisa/pedido',
            ['empresa' => 'Bot Spam Co', 'website' => 'http://spam.com'],
            [],
            [],
            [],
            [],
            '127.0.0.77'
        );

        $response = $controller->crearPedido($request, ['slug' => 'la-parrilla-paisa']);
        $this->assertSame(400, $response->getStatusCode());

        $body = json_decode($response->getContent(), true);
        $this->assertFalse($body['ok']);
        $this->assertSame('Solicitud rechazada.', $body['error']);
    }

    public function testCrearPedidoFailsWithEmptyCart(): void
    {
        $controller = new MenuController();

        $request = new Request(
            'POST',
            '/la-parrilla-paisa/pedido',
            [
                'empresa' => '',
                'website' => '',
                'modalidad' => 'mesa',
                'mesa_numero' => 3,
                'cliente_nombre' => 'Test',
                'items' => [],
            ],
            [],
            [],
            [],
            [],
            '127.0.0.88'
        );

        $response = $controller->crearPedido($request, ['slug' => 'la-parrilla-paisa']);
        $this->assertSame(400, $response->getStatusCode());

        $body = json_decode($response->getContent(), true);
        $this->assertFalse($body['ok']);
        $this->assertStringContainsString('vacío', $body['error']);
    }

    public function testRateLimitTriggersAtFifthOrder(): void
    {
        $pedidoModel = new Pedido();
        $testIp = '192.168.1.99';

        // Al inicio debe estar libre
        $this->assertFalse($pedidoModel->haExcedidoRateLimit($testIp, 5, 10));
    }
}
