<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\TenantContext;
use App\Models\Producto;

class DisponibilidadController extends Controller
{
    private Producto $productoModel;

    public function __construct()
    {
        $this->productoModel = new Producto();
    }

    public function index(Request $request): Response
    {
        $tenant = TenantContext::getTenant();
        $productos = $this->productoModel->allWithCategory();

        return $this->render('admin/disponibilidad/index', [
            'title' => 'Disponibilidad Rápida (Agotados) — ' . ($tenant['nombre'] ?? 'Mesaly'),
            'productos' => $productos,
        ], 'admin');
    }

    public function toggle(Request $request): Response
    {
        $id = (int)$request->input('id');
        $producto = $this->productoModel->find($id);

        if (!$producto) {
            if ($request->isAjax()) {
                return $this->json(['success' => false, 'message' => 'Producto no encontrado.'], 404);
            }
            Session::flash('error', 'Producto no encontrado.');
            return $this->back();
        }

        $nuevoEstado = !empty($producto['disponible']) ? 0 : 1;
        $this->productoModel->update($id, ['disponible' => $nuevoEstado]);

        if ($request->isAjax()) {
            return $this->json([
                'success' => true,
                'disponible' => $nuevoEstado,
                'producto_id' => $id,
                'message' => $nuevoEstado ? 'Producto disponible' : 'Producto agotado',
            ]);
        }

        Session::flash('success', $nuevoEstado ? "Disponible: {$producto['nombre']}" : "Agotado: {$producto['nombre']}");
        return $this->back();
    }
}
