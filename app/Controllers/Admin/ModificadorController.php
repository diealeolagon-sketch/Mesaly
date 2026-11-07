<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\TenantContext;
use App\Core\Validator;
use App\Models\Auditoria;
use App\Models\GrupoModificador;
use App\Models\OpcionModificador;

class ModificadorController extends Controller
{
    private GrupoModificador $grupoModel;
    private OpcionModificador $opcionModel;

    public function __construct()
    {
        $this->grupoModel = new GrupoModificador();
        $this->opcionModel = new OpcionModificador();
    }

    public function index(Request $request): Response
    {
        $tenant = TenantContext::getTenant();
        $grupos = $this->grupoModel->allWithOptions();

        return $this->render('admin/modificadores/index', [
            'title' => 'Modificadores y Opciones — ' . ($tenant['nombre'] ?? 'Mesaly'),
            'grupos' => $grupos,
        ], 'admin');
    }

    public function guardarGrupo(Request $request): Response
    {
        $id = (int)$request->post('id', 0);
        $nombre = trim((string)$request->post('nombre'));
        $tipo = (string)$request->post('tipo', 'unica');
        $obligatorio = (bool)$request->post('obligatorio', false);
        $min = max(0, (int)$request->post('min_selecciones', 0));
        $max = max(1, (int)$request->post('max_selecciones', 1));

        if ($tipo === 'unica') {
            $min = $obligatorio ? 1 : 0;
            $max = 1;
        }

        $validator = Validator::make([
            'nombre' => $nombre,
            'tipo' => $tipo,
        ], [
            'nombre' => 'required|min:2|max:100',
            'tipo' => 'required|in:unica,multiple',
        ]);

        if ($validator->fails()) {
            Session::flash('error', $validator->firstError());
            return $this->back();
        }

        $tenantId = (int)TenantContext::getTenantId();
        $data = [
            'nombre' => $nombre,
            'tipo' => $tipo,
            'obligatorio' => $obligatorio ? 1 : 0,
            'min_selecciones' => $min,
            'max_selecciones' => $max,
        ];

        if ($id > 0) {
            $existente = $this->grupoModel->find($id);
            if (!$existente) {
                Session::flash('error', 'Grupo no encontrado.');
                return $this->back();
            }
            $this->grupoModel->update($id, $data);
            Auditoria::registrar('editar_grupo_modificador', 'grupos_modificadores', $id, $existente, $data, $tenantId);
            Session::flash('success', 'Grupo de modificadores actualizado.');
        } else {
            $grupoId = $this->grupoModel->create($data);
            Auditoria::registrar('crear_grupo_modificador', 'grupos_modificadores', $grupoId, null, $data, $tenantId);
            Session::flash('success', 'Grupo de modificadores creado.');
        }

        return $this->redirect(app_url('/admin/modificadores'));
    }

    public function eliminarGrupo(Request $request, array $params): Response
    {
        $id = (int)($params['id'] ?? 0);
        $grupo = $this->grupoModel->find($id);

        if (!$grupo) {
            Session::flash('error', 'Grupo no encontrado.');
            return $this->back();
        }

        $this->grupoModel->delete($id);
        Auditoria::registrar('eliminar_grupo_modificador', 'grupos_modificadores', $id, $grupo, null, (int)TenantContext::getTenantId());

        Session::flash('success', 'Grupo de modificadores eliminado.');
        return $this->redirect(app_url('/admin/modificadores'));
    }

    public function guardarOpcion(Request $request): Response
    {
        $grupoId = (int)$request->post('grupo_id');
        $id = (int)$request->post('id', 0);
        $nombre = trim((string)$request->post('nombre'));
        $precioExtra = max(0, (int)$request->post('precio_extra', 0));
        $orden = (int)$request->post('orden', 0);

        // Verificar pertenencia del grupo al tenant
        $grupo = $this->grupoModel->find($grupoId);
        if (!$grupo) {
            Session::flash('error', 'Grupo no válido.');
            return $this->back();
        }

        $validator = Validator::make([
            'nombre' => $nombre,
            'precio_extra' => $precioExtra,
        ], [
            'nombre' => 'required|min:1|max:100',
            'precio_extra' => 'numeric|min:0',
        ]);

        if ($validator->fails()) {
            Session::flash('error', $validator->firstError());
            return $this->back();
        }

        $tenantId = (int)TenantContext::getTenantId();

        if ($id > 0) {
            $opcion = $this->opcionModel->findScopedByTenant($id, $tenantId);
            if (!$opcion) {
                Session::flash('error', 'Opción no encontrada.');
                return $this->back();
            }

            $this->opcionModel->update($id, [
                'nombre' => $nombre,
                'precio_extra' => $precioExtra,
                'orden' => $orden,
            ]);

            Session::flash('success', 'Opción actualizada.');
        } else {
            $this->opcionModel->create([
                'grupo_id' => $grupoId,
                'nombre' => $nombre,
                'precio_extra' => $precioExtra,
                'orden' => $orden,
                'activa' => 1,
            ]);

            Session::flash('success', 'Opción añadida al grupo.');
        }

        return $this->redirect(app_url('/admin/modificadores'));
    }

    public function eliminarOpcion(Request $request, array $params): Response
    {
        $id = (int)($params['id'] ?? 0);
        $tenantId = (int)TenantContext::getTenantId();

        $eliminada = $this->opcionModel->deleteScoped($id, $tenantId);
        if (!$eliminada) {
            Session::flash('error', 'No se pudo eliminar la opción.');
        } else {
            Session::flash('success', 'Opción eliminada del grupo.');
        }

        return $this->redirect(app_url('/admin/modificadores'));
    }
}
