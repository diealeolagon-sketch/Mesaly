<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\TenantContext;
use App\Models\Categoria;
use App\Models\GrupoModificador;
use App\Models\Plan;
use App\Models\Producto;

class DashboardController extends Controller
{
    public function index(Request $request): Response
    {
        $tenant = TenantContext::getTenant();

        $categoriaModel = new Categoria();
        $productoModel = new Producto();
        $modificadorModel = new GrupoModificador();
        $planModel = new Plan();

        $totalCategorias = $categoriaModel->count();
        $totalProductos = $productoModel->count();
        $totalGrupos = $modificadorModel->count();
        $plan = $tenant['plan_id'] ? $planModel->find((int)$tenant['plan_id']) : null;

        return $this->render('admin/dashboard/index', [
            'title' => 'Panel de Control — ' . ($tenant['nombre'] ?? 'Mesaly'),
            'tenant' => $tenant,
            'plan' => $plan,
            'stats' => [
                'categorias' => $totalCategorias,
                'productos' => $totalProductos,
                'grupos' => $totalGrupos,
            ],
        ], 'admin');
    }
}
