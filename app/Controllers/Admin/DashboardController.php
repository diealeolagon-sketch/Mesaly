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

        // Métricas comerciales del restaurante
        $metricasMes = $this->pedidoModel->metricasMes($tenantId);
        $ventasPorDia = $this->pedidoModel->ventasPorDia($tenantId, 30);
        $pedidosPorDiaSemana = $this->pedidoModel->pedidosPorDiaSemana($tenantId);
        $topProductos = $this->pedidoModel->topProductos($tenantId, 5);

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
            'metricasMes' => $metricasMes,
            'ventasPorDia' => $ventasPorDia,
            'pedidosPorDiaSemana' => $pedidosPorDiaSemana,
            'topProductos' => $topProductos,
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
