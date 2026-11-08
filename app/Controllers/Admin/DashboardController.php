<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\TenantContext;
use App\Models\Categoria;
use App\Models\GrupoModificador;
use App\Models\Producto;
use App\Services\SuscripcionService;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $tenant = TenantContext::getTenant();

        $categoriaModel = new Categoria();
        $productoModel = new Producto();
        $modificadorModel = new GrupoModificador();

        $totalCategorias = $categoriaModel->count();
        $totalProductos = $productoModel->count();
        $totalGrupos = $modificadorModel->count();
        $estadoSuscripcion = SuscripcionService::estadoDe($tenant);

        return $this->render('admin/dashboard/index', [
            'title' => 'Panel de Control — ' . ($tenant['nombre'] ?? 'Mesaly'),
            'tenant' => $tenant,
            'estadoSuscripcion' => $estadoSuscripcion,
            'stats' => [
                'categorias' => $totalCategorias,
                'productos' => $totalProductos,
                'grupos' => $totalGrupos,
            ],
        ], 'admin');
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
