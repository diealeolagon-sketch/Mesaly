<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Model;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\TenantContext;
use App\Core\Validator;
use App\Models\Auditoria;
use App\Models\Categoria;
use App\Models\GrupoModificador;
use App\Models\Producto;
use App\Services\ImagenService;

class CatalogoController extends Controller
{
    private Categoria $categoriaModel;
    private Producto $productoModel;
    private GrupoModificador $grupoModel;
    private ImagenService $imagenService;

    public function __construct()
    {
        $this->categoriaModel = new Categoria();
        $this->productoModel = new Producto();
        $this->grupoModel = new GrupoModificador();
        $this->imagenService = new ImagenService();
    }

    public function index(Request $request): Response
    {
        $tenant = TenantContext::getTenant();
        $categorias = $this->categoriaModel->allOrdered();
        $productos = $this->productoModel->allWithCategory();
        $grupos = $this->grupoModel->allWithOptions();

        return $this->render('admin/catalogo/index', [
            'title' => 'Catálogo y Menú — ' . ($tenant['nombre'] ?? 'Mesaly'),
            'categorias' => $categorias,
            'productos' => $productos,
            'grupos' => $grupos,
            'totalCategorias' => count($categorias),
            'totalProductos' => count($productos),
        ], 'admin');
    }

    // -------------------------------------------------------------------------
    // CATEGORÍAS
    // -------------------------------------------------------------------------

    public function guardarCategoria(Request $request): Response
    {
        $id = (int)$request->post('id', 0);
        $nombre = trim((string)$request->post('nombre'));
        $orden = (int)$request->post('orden', 0);
        $activa = (bool)$request->post('activa', true);

        $validator = Validator::make([
            'nombre' => $nombre,
        ], [
            'nombre' => 'required|min:2|max:100',
        ]);

        if ($validator->fails()) {
            Session::flash('error', $validator->firstError());
            return $this->back();
        }

        $tenant = TenantContext::getTenant();
        if ($id > 0) {
            // Edición
            $cat = $this->categoriaModel->find($id);
            if (!$cat) {
                Session::flash('error', 'Categoría no encontrada.');
                return $this->back();
            }

            $this->categoriaModel->update($id, [
                'nombre' => $nombre,
                'orden' => $orden,
                'activa' => $activa ? 1 : 0,
            ]);

            Auditoria::registrar('editar_categoria', 'categorias', $id, $cat, [
                'nombre' => $nombre,
                'orden' => $orden,
            ], (int)$tenant['id']);

            Session::flash('success', 'Categoría actualizada exitosamente.');
        } else {
            // Creación sin límites de plan
            $catId = $this->categoriaModel->create([
                'nombre' => $nombre,
                'orden' => $orden,
                'activa' => $activa ? 1 : 0,
            ]);

            Auditoria::registrar('crear_categoria', 'categorias', $catId, null, [
                'nombre' => $nombre,
            ], (int)$tenant['id']);

            Session::flash('success', 'Categoría creada exitosamente.');
        }

        return $this->redirect(app_url('/admin/catalogo'));
    }

    public function eliminarCategoria(Request $request, array $params): Response
    {
        $id = (int)($params['id'] ?? 0);
        $cat = $this->categoriaModel->find($id);

        if (!$cat) {
            Session::flash('error', 'Categoría no encontrada.');
            return $this->back();
        }

        // Borrado lógico
        $this->categoriaModel->delete($id);

        Auditoria::registrar('eliminar_categoria', 'categorias', $id, $cat, null, (int)TenantContext::getTenantId());

        Session::flash('success', 'Categoría eliminada del menú.');
        return $this->redirect(app_url('/admin/catalogo'));
    }

    // -------------------------------------------------------------------------
    // PRODUCTOS
    // -------------------------------------------------------------------------

    public function showProductoForm(Request $request, array $params): Response
    {
        $id = (int)($params['id'] ?? 0);
        $producto = $id > 0 ? $this->productoModel->findWithDetails($id) : null;

        if ($id > 0 && !$producto) {
            Session::flash('error', 'Producto no encontrado.');
            return $this->redirect(app_url('/admin/catalogo'));
        }

        $categorias = $this->categoriaModel->allOrdered();
        if (empty($categorias)) {
            Session::flash('error', 'Primero debes crear al menos una categoría antes de añadir productos.');
            return $this->redirect(app_url('/admin/catalogo'));
        }

        $grupos = $this->grupoModel->allWithOptions();
        $gruposSeleccionados = $id > 0 ? $this->productoModel->getGroupIds($id) : [];

        return $this->render('admin/catalogo/producto_form', [
            'title' => ($producto ? 'Editar Producto' : 'Nuevo Producto') . ' — Mesaly',
            'producto' => $producto,
            'categorias' => $categorias,
            'grupos' => $grupos,
            'gruposSeleccionados' => $gruposSeleccionados,
        ], 'admin');
    }

    public function guardarProducto(Request $request): Response
    {
        $id = (int)$request->post('id', 0);
        $nombre = trim((string)$request->post('nombre'));
        $descripcion = trim((string)$request->post('descripcion'));
        $precio = max(0, (int)$request->post('precio', 0));
        $categoriaId = (int)$request->post('categoria_id');
        $disponible = (bool)$request->post('disponible', true);
        $destacado = (bool)$request->post('destacado', false);
        $orden = (int)$request->post('orden', 0);
        $gruposSeleccionados = (array)$request->post('grupos', []);

        $validator = Validator::make([
            'nombre' => $nombre,
            'precio' => $precio,
            'categoria_id' => $categoriaId,
        ], [
            'nombre' => 'required|min:2|max:150',
            'precio' => 'required|numeric|min:0',
            'categoria_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            Session::flash('error', $validator->firstError());
            return $this->back();
        }

        // Verificar que la categoría pertenezca a este restaurante (Anti-IDOR)
        $cat = $this->categoriaModel->find($categoriaId);
        if (!$cat) {
            Session::flash('error', 'La categoría seleccionada no es válida.');
            return $this->back();
        }

        $tenant = TenantContext::getTenant();
        $tenantId = (int)$tenant['id'];

        // Subida de imagen si viene un archivo
        $imagenPath = null;
        if ($id > 0) {
            $existente = $this->productoModel->find($id);
            if (!$existente) {
                Session::flash('error', 'Producto no encontrado.');
                return $this->redirect(app_url('/admin/catalogo'));
            }
            $imagenPath = $existente['imagen_path'];
        }

        $imgFile = $request->file('imagen');
        if ($imgFile && !empty($imgFile['tmp_name'])) {
            try {
                $nuevaImg = $this->imagenService->procesarYGuardar($imgFile, $tenantId, 800);
                if ($imagenPath) {
                    $this->imagenService->eliminar($imagenPath);
                }
                $imagenPath = $nuevaImg;
            } catch (\Throwable $e) {
                Session::flash('error', "Error al procesar la imagen: " . $e->getMessage());
                return $this->back();
            }
        }

        Model::beginTransaction();
        try {
            $data = [
                'categoria_id' => $categoriaId,
                'nombre' => $nombre,
                'descripcion' => $descripcion,
                'precio' => $precio,
                'imagen_path' => $imagenPath,
                'disponible' => $disponible ? 1 : 0,
                'destacado' => $destacado ? 1 : 0,
                'orden' => $orden,
            ];

            if ($id > 0) {
                $this->productoModel->update($id, $data);
                $productoId = $id;
                Auditoria::registrar('editar_producto', 'productos', $id, $existente, $data, $tenantId);
                Session::flash('success', 'Producto actualizado exitosamente.');
            } else {
                // Creación de producto sin límites
                $productoId = $this->productoModel->create($data);
                Auditoria::registrar('crear_producto', 'productos', $productoId, null, $data, $tenantId);
                Session::flash('success', 'Producto agregado al catálogo.');
            }

            // Sincronizar grupos de modificadores asignados
            $this->productoModel->syncGroups($productoId, $gruposSeleccionados);

            Model::commit();
            return $this->redirect(app_url('/admin/catalogo'));

        } catch (\Throwable $e) {
            Model::rollBack();
            Session::flash('error', 'Error al guardar producto: ' . $e->getMessage());
            return $this->back();
        }
    }

    public function toggleAgotado(Request $request, array $params): Response
    {
        $id = (int)($params['id'] ?? 0);
        $prod = $this->productoModel->find($id);

        if (!$prod) {
            Session::flash('error', 'Producto no encontrado.');
            return $this->back();
        }

        $nuevoEstado = !empty($prod['disponible']) ? 0 : 1;
        $this->productoModel->update($id, ['disponible' => $nuevoEstado]);

        $msg = $nuevoEstado ? "Producto '{$prod['nombre']}' marcado como Disponible." : "Producto '{$prod['nombre']}' marcado como Agotado.";
        Session::flash('success', $msg);
        return $this->back();
    }

    public function eliminarProducto(Request $request, array $params): Response
    {
        $id = (int)($params['id'] ?? 0);
        $prod = $this->productoModel->find($id);

        if (!$prod) {
            Session::flash('error', 'Producto no encontrado.');
            return $this->back();
        }

        // Borrado lógico para preservar integridad de pedidos históricos
        $this->productoModel->delete($id);

        Auditoria::registrar('eliminar_producto', 'productos', $id, $prod, null, (int)TenantContext::getTenantId());

        Session::flash('success', 'Producto eliminado del catálogo.');
        return $this->redirect(app_url('/admin/catalogo'));
    }
}
