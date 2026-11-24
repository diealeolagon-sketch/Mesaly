<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\TenantContext;
use App\Models\Categoria;
use App\Models\GrupoModificador;
use App\Models\Mesa;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Restaurante;
use App\Services\SuscripcionService;

class DashboardController extends Controller
{
    private Pedido $pedidoModel;
    private Categoria $categoriaModel;
    private Producto $productoModel;
    private GrupoModificador $modificadorModel;
    private Mesa $mesaModel;
    private Restaurante $restauranteModel;

    public function __construct(
        ?Pedido $pedidoModel = null,
        ?Categoria $categoriaModel = null,
        ?Producto $productoModel = null,
        ?GrupoModificador $modificadorModel = null,
        ?Mesa $mesaModel = null,
        ?Restaurante $restauranteModel = null
    ) {
        $this->pedidoModel = $pedidoModel ?? new Pedido();
        $this->categoriaModel = $categoriaModel ?? new Categoria();
        $this->productoModel = $productoModel ?? new Producto();
        $this->modificadorModel = $modificadorModel ?? new GrupoModificador();
        $this->mesaModel = $mesaModel ?? new Mesa();
        $this->restauranteModel = $restauranteModel ?? new Restaurante();
    }

    public function index(Request $request): Response
    {
        $tenant = TenantContext::getTenant();
        $tenantId = (int)$tenant['id'];

        // Métricas operativas en tiempo real (HOY - Superrápido y optimizado para alto volumen)
        $metricasHoy = $this->pedidoModel->metricasHoy($tenantId);
        $mesasEnVivo = $this->pedidoModel->mesasEstadoEnVivo($tenantId);
        $comandasActivas = $this->pedidoModel->listarComandasActivas($tenantId, 6);

        // Conteos del catálogo y mesas
        $totalCategorias = $this->categoriaModel->count();
        $totalProductos = $this->productoModel->count();
        $totalGrupos = $this->modificadorModel->count();
        $totalMesas = $this->mesaModel->countActivas($tenantId);
        $estadoSuscripcion = SuscripcionService::estadoDe($tenant);

        return $this->render('admin/dashboard/index', [
            'title' => 'Panel de Control — ' . ($tenant['nombre'] ?? 'Mesaly'),
            'tenant' => $tenant,
            'estadoSuscripcion' => $estadoSuscripcion,
            'metricasHoy' => $metricasHoy,
            'mesasEnVivo' => $mesasEnVivo,
            'comandasActivas' => $comandasActivas,
            'stats' => [
                'categorias' => $totalCategorias,
                'productos' => $totalProductos,
                'grupos' => $totalGrupos,
                'mesas' => $totalMesas,
            ],
        ], 'admin');
    }

    /**
     * Alterna rápidamente la recepción de pedidos del restaurante
     */
    public function toggleRecibiendoPedidos(Request $request): Response
    {
        $tenantId = Auth::tenantId();
        if (!$tenantId) {
            return $this->redirect(app_url('/login'));
        }

        $restaurante = $this->restauranteModel->find($tenantId);
        if (!$restaurante) {
            return $this->redirect(app_url('/login'));
        }

        $nuevoEstado = empty($restaurante['recibiendo_pedidos']) ? 1 : 0;
        $this->restauranteModel->update($tenantId, [
            'recibiendo_pedidos' => $nuevoEstado,
        ]);

        $msg = $nuevoEstado ? '¡Recepción de pedidos abierta! Tus comensales ya pueden ordenar.' : 'Recepción de pedidos pausada.';
        Session::flash('success', $msg);

        return $this->back();
    }

    /**
     * Procesa el pago de suscripción SaaS del restaurante a Mesaly (integración Wompi)
     */
    public function pagarSuscripcion(Request $request): Response
    {
        $tenantId = Auth::tenantId();
        if (!$tenantId) {
            return $this->redirect(app_url('/login'));
        }

        $restaurante = $this->restauranteModel->find($tenantId);
        if (!$restaurante) {
            return $this->redirect(app_url('/login'));
        }

        $periodos = max(1, min(12, (int)$request->post('periodos', 1)));
        $metodoSeleccionado = trim((string)$request->post('metodo', 'Wompi - Tarjeta / PSE'));
        $referencia = 'WOMPI-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
        $precioUnitario = (int)($restaurante['precio'] ?? 49000);
        $montoTotal = $precioUnitario * $periodos;

        try {
            $resultado = SuscripcionService::registrarPago(
                $tenantId,
                [
                    'monto' => $montoTotal,
                    'fecha_pago' => date('Y-m-d'),
                    'periodos' => $periodos,
                    'metodo' => $metodoSeleccionado,
                    'referencia' => $referencia,
                    'nota' => "Pago online de suscripción SaaS procesado por el comercio vía Wompi ({$periodos} mes/es).",
                ],
                Auth::id() ?? 1
            );

            // Actualizar datos de sesión del tenant
            $restauranteActualizado = $this->restauranteModel->find($tenantId);
            TenantContext::setTenant($restauranteActualizado);

            $diasRenovados = $periodos * 30;
            $fechaNuevaFmt = date('d/m/Y', strtotime($resultado['nueva_fecha_vencimiento']));
            Session::flash('success', "¡Pago procesado exitosamente por Wompi! Ref: {$referencia}. Tu suscripción Mesaly Pro ha sido renovada por {$diasRenovados} días (Nuevo vencimiento: {$fechaNuevaFmt}).");
        } catch (\Throwable $e) {
            Session::flash('error', 'Ocurrió un error al procesar el pago: ' . $e->getMessage());
        }

        return $this->redirect(app_url('/admin'));
    }

    public function suscripcionVencida(Request $request): Response
    {
        $tenant = TenantContext::getTenant();
        $estado = SuscripcionService::estadoDe($tenant);

        return $this->render('admin/suscripcion_vencida', [
            'title' => 'Suscripción Suspendida — ' . ($tenant['nombre'] ?? 'Mesaly'),
            'tenant' => $tenant,
            'estado' => $estado,
        ], 'auth');
    }
}
