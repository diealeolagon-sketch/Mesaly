<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Pedido;
use PHPUnit\Framework\TestCase;

class PedidoAdminAndDashboardTest extends TestCase
{
    private Pedido $pedidoModel;

    protected function setUp(): void
    {
        parent::setUp();
        $dotenv = \Dotenv\Dotenv::createImmutable(dirname(__DIR__, 2));
        $dotenv->safeLoad();

        $this->pedidoModel = new Pedido();
    }

    public function testPedidoEstadosConfiguracion(): void
    {
        $estados = Pedido::ESTADOS;
        $this->assertArrayHasKey('recibido', $estados);
        $this->assertArrayHasKey('listo', $estados);
        $this->assertArrayHasKey('en_camino', $estados);
        $this->assertArrayHasKey('entregado', $estados);
        $this->assertArrayHasKey('rechazado', $estados);

        $this->assertFalse($estados['recibido']['final']);
        $this->assertFalse($estados['listo']['final']);
        $this->assertFalse($estados['en_camino']['final']);
        $this->assertTrue($estados['entregado']['final']);
        $this->assertTrue($estados['rechazado']['final']);
    }

    public function testMetricasMesEstructura(): void
    {
        $tenantId = 1;
        $metricas = $this->pedidoModel->metricasMes($tenantId);

        $this->assertIsArray($metricas);
        $this->assertArrayHasKey('ventas_mes', $metricas);
        $this->assertArrayHasKey('pedidos_mes', $metricas);
        $this->assertArrayHasKey('ticket_promedio', $metricas);
        $this->assertArrayHasKey('pendientes', $metricas);

        $this->assertGreaterThanOrEqual(0, $metricas['ventas_mes']);
        $this->assertGreaterThanOrEqual(0, $metricas['pedidos_mes']);
        $this->assertGreaterThanOrEqual(0, $metricas['ticket_promedio']);
        $this->assertGreaterThanOrEqual(0, $metricas['pendientes']);
    }

    public function testVentasPorDiaSerie30Dias(): void
    {
        $tenantId = 1;
        $serie = $this->pedidoModel->ventasPorDia($tenantId, 30);

        $this->assertCount(30, $serie);
        foreach ($serie as $dia) {
            $this->assertArrayHasKey('fecha', $dia);
            $this->assertArrayHasKey('label', $dia);
            $this->assertArrayHasKey('total', $dia);
            $this->assertArrayHasKey('pedidos', $dia);
            $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', $dia['fecha']);
        }
    }

    public function testPedidosPorDiaSemanaCompleto(): void
    {
        $tenantId = 1;
        $dias = $this->pedidoModel->pedidosPorDiaSemana($tenantId);

        $this->assertIsArray($dias);
        $this->assertCount(7, $dias);
        $diasEsperados = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
        foreach ($diasEsperados as $nomDia) {
            $this->assertArrayHasKey($nomDia, $dias);
            $this->assertGreaterThanOrEqual(0, $dias[$nomDia]);
        }
    }

    public function testEstadoInvalidoLanzaExcepcion(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->pedidoModel->cambiarEstado(1, 1, 'estado_inexistente');
    }

    public function testPedidoInexistenteNoSeReportaComoActualizado(): void
    {
        $pedidoIdInexistente = (int)\App\Core\Model::getPdo()
            ->query('SELECT COALESCE(MAX(id), 0) + 1000 FROM `pedidos`')
            ->fetchColumn();

        $this->assertFalse(
            $this->pedidoModel->cambiarEstado($pedidoIdInexistente, 1, 'listo')
        );
    }

    public function testCreacionDetalleTransicionEstadoYAislamientoTenant(): void
    {
        $tenantId = 1;

        // 1. Registrar pedido completo con ítems y opciones de modificador
        $datosPedido = [
            'modalidad' => 'mesa',
            'mesa_numero' => 99,
            'cliente_nombre' => 'Cliente Test Unit',
            'cliente_telefono' => '3001234567',
            'metodo_pago' => 'efectivo',
            'subtotal' => 45000,
            'costo_envio' => 0,
            'total' => 45000,
            'notas_entrega' => 'Mesa junto al ventanal',
            'ip' => '127.0.0.1',
        ];

        $items = [
            [
                'producto_id' => 1,
                'nombre_producto' => 'Churrasco 400g Test',
                'precio_unitario' => 45000,
                'cantidad' => 1,
                'total_linea' => 45000,
                'nota' => 'Término 3/4',
                'opciones' => [
                    [
                        'nombre_grupo' => 'Término de la carne',
                        'nombre_opcion' => 'Tres cuartos',
                        'precio_extra' => 0,
                    ],
                ],
            ],
        ];

        $pedidoRes = $this->pedidoModel->crear($tenantId, $datosPedido, $items);
        $this->assertNotEmpty($pedidoRes['codigo']);
        $pedidoId = (int)$pedidoRes['id'];
        $this->assertGreaterThan(0, $pedidoId);

        // 2. Obtener detalle completo y verificar asociaciones
        $detalle = $this->pedidoModel->obtenerDetalleCompleto($pedidoId, $tenantId);
        $this->assertNotNull($detalle);
        $this->assertSame('Cliente Test Unit', $detalle['cliente_nombre']);
        $this->assertSame('recibido', $detalle['estado']);
        $this->assertSame(45000, (int)$detalle['total']);
        $this->assertCount(1, $detalle['items']);
        $this->assertSame('Churrasco 400g Test', $detalle['items'][0]['nombre_producto']);
        $this->assertCount(1, $detalle['items'][0]['opciones']);
        $this->assertSame('Tres cuartos', $detalle['items'][0]['opciones'][0]['nombre_opcion']);

        // 3. Aislamiento multitenant: otro restaurante (999) no puede ver el pedido
        $detalleAjeno = $this->pedidoModel->obtenerDetalleCompleto($pedidoId, 999);
        $this->assertNull($detalleAjeno);

        // 4. Cambiar estado a 'listo'
        $okListo = $this->pedidoModel->cambiarEstado($pedidoId, $tenantId, 'listo');
        $this->assertTrue($okListo);
        $detalleListo = $this->pedidoModel->obtenerDetalleCompleto($pedidoId, $tenantId);
        $this->assertSame('listo', $detalleListo['estado']);

        // 5. Cambiar estado a 'en_camino'
        $okCamino = $this->pedidoModel->cambiarEstado($pedidoId, $tenantId, 'en_camino');
        $this->assertTrue($okCamino);
        $detalleCamino = $this->pedidoModel->obtenerDetalleCompleto($pedidoId, $tenantId);
        $this->assertSame('en_camino', $detalleCamino['estado']);

        // 6. Cambiar estado a 'entregado'
        $okEntregado = $this->pedidoModel->cambiarEstado($pedidoId, $tenantId, 'entregado');
        $this->assertTrue($okEntregado);
        $detalleEntregado = $this->pedidoModel->obtenerDetalleCompleto($pedidoId, $tenantId);
        $this->assertSame('entregado', $detalleEntregado['estado']);

        $okMismoEstado = $this->pedidoModel->cambiarEstado($pedidoId, $tenantId, 'entregado');
        $this->assertTrue($okMismoEstado);

        // 7. Aislamiento en cambio de estado: restaurante ajeno no puede modificarlo
        $okAjeno = $this->pedidoModel->cambiarEstado($pedidoId, 999, 'rechazado');
        $this->assertFalse($okAjeno);
        // El UPDATE afectará 0 filas porque WHERE restaurante_id = 999 no coincide
        $detallePostAjeno = $this->pedidoModel->obtenerDetalleCompleto($pedidoId, $tenantId);
        $this->assertSame('entregado', $detallePostAjeno['estado']);

        // 8. Conteos por estado reflejan el pedido entregado
        $conteos = $this->pedidoModel->conteosPorEstado($tenantId);
        $this->assertGreaterThan(0, $conteos['entregado']);
        $this->assertGreaterThan(0, $conteos['todos']);

        // 9. Top productos incluye el plato vendido
        $top = $this->pedidoModel->topProductos($tenantId, 5);
        $this->assertIsArray($top);
        $nombresTop = array_column($top, 'nombre');
        $this->assertContains('Churrasco 400g Test', $nombresTop);
    }
}
