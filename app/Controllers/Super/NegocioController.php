<?php

declare(strict_types=1);

namespace App\Controllers\Super;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Model;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Validator;
use App\Models\Auditoria;
use App\Models\Plan;
use App\Models\Restaurante;
use App\Models\Usuario;

class NegocioController extends Controller
{
    private Restaurante $restauranteModel;
    private Plan $planModel;
    private Usuario $usuarioModel;

    public function __construct()
    {
        $this->restauranteModel = new Restaurante();
        $this->planModel = new Plan();
        $this->usuarioModel = new Usuario();
    }

    public function index(Request $request): Response
    {
        $negocios = $this->restauranteModel->allWithPlan();

        return $this->render('super/negocios/index', [
            'title' => 'Gestión de Negocios — Super Admin',
            'negocios' => $negocios,
        ], 'super');
    }

    public function showCrear(Request $request): Response
    {
        $planes = $this->planModel->allActive();

        return $this->render('super/negocios/crear', [
            'title' => 'Crear Nuevo Negocio — Super Admin',
            'planes' => $planes,
        ], 'super');
    }

    public function crear(Request $request): Response
    {
        $nombre = trim((string)$request->post('nombre'));
        $slug = strtolower(trim((string)$request->post('slug')));
        $whatsapp = preg_replace('/\D/', '', (string)$request->post('whatsapp'));
        $planId = (int)$request->post('plan_id');
        $ciclo = (string)$request->post('ciclo');
        $enPrueba = (bool)$request->post('en_prueba');
        $adminNombre = trim((string)$request->post('admin_nombre'));
        $adminEmail = strtolower(trim((string)$request->post('admin_email')));

        $validator = Validator::make([
            'nombre' => $nombre,
            'slug' => $slug,
            'whatsapp' => $whatsapp,
            'plan_id' => $planId,
            'ciclo' => $ciclo,
            'admin_nombre' => $adminNombre,
            'admin_email' => $adminEmail,
        ], [
            'nombre' => 'required|min:3|max:150',
            'slug' => 'required|slug|min:3|max:40',
            'whatsapp' => 'required|min:10|max:15',
            'plan_id' => 'required|numeric',
            'ciclo' => 'required|in:mensual,anual',
            'admin_nombre' => 'required|min:3|max:150',
            'admin_email' => 'required|email',
        ]);

        if ($validator->fails()) {
            Session::flash('error', $validator->firstError());
            return $this->back();
        }

        // Verificar unicidad de slug
        if (!$this->restauranteModel->isSlugAvailable($slug)) {
            Session::flash('error', "El slug '{$slug}' ya está en uso por otro negocio o reservado.");
            return $this->back();
        }

        // Verificar unicidad de correo
        if ($this->usuarioModel->findByEmail($adminEmail)) {
            Session::flash('error', "El correo electrónico '{$adminEmail}' ya está registrado en la plataforma.");
            return $this->back();
        }

        // Generar contraseña temporal segura de 12 caracteres (ej: Paisa98a!7c)
        $tempPassword = 'Mesaly' . rand(100, 999) . '!' . bin2hex(random_bytes(2));
        $passwordHash = Auth::hashPassword($tempPassword);

        // Fechas de suscripción
        $now = date('Y-m-d H:i:s');
        $today = date('Y-m-d');
        $diaCorte = (int)date('d');

        if ($enPrueba) {
            $fechaFinPrueba = date('Y-m-d', strtotime('+15 days'));
            $fechaVencimiento = $fechaFinPrueba;
        } else {
            $fechaFinPrueba = null;
            $intervalo = $ciclo === 'anual' ? '+1 year' : '+1 month';
            $fechaVencimiento = date('Y-m-d', strtotime($intervalo));
        }

        Model::beginTransaction();
        try {
            $restauranteId = $this->restauranteModel->create([
                'nombre' => $nombre,
                'slug' => $slug,
                'whatsapp' => $whatsapp,
                'plan_id' => $planId,
                'ciclo' => $ciclo,
                'en_prueba' => $enPrueba ? 1 : 0,
                'fecha_fin_prueba' => $fechaFinPrueba,
                'fecha_vencimiento' => $fechaVencimiento,
                'dia_corte' => $diaCorte,
                'moneda' => 'COP',
                'color_primario' => '#E85D04',
                'color_secundario' => '#FFB703',
                'color_fondo' => '#FFF7EA',
                'color_texto' => '#1E1B18',
                'recibiendo_pedidos' => 1,
                'acepta_mesa' => 1,
                'acepta_domicilio' => 1,
                'acepta_recoger' => 1,
                'siguiente_consecutivo' => 1,
            ]);

            // Crear usuario administrador del negocio
            $usuarioId = $this->usuarioModel->create([
                'restaurante_id' => $restauranteId,
                'nombre' => $adminNombre,
                'email' => $adminEmail,
                'password_hash' => $passwordHash,
                'rol' => Auth::ROLE_ADMIN_NEGOCIO,
                'debe_cambiar_password' => 1,
                'activo' => 1,
            ]);

            Auditoria::registrar('crear_negocio', 'restaurantes', $restauranteId, null, [
                'nombre' => $nombre,
                'slug' => $slug,
                'admin_email' => $adminEmail,
            ], $restauranteId);

            Model::commit();

            return $this->render('super/negocios/credenciales', [
                'title' => 'Negocio Creado Exitosamente — Mesaly',
                'restaurante' => [
                    'id' => $restauranteId,
                    'nombre' => $nombre,
                    'slug' => $slug,
                ],
                'admin' => [
                    'nombre' => $adminNombre,
                    'email' => $adminEmail,
                    'tempPassword' => $tempPassword,
                ],
            ], 'super');

        } catch (\Throwable $e) {
            Model::rollBack();
            Session::flash('error', "Error al crear negocio: " . $e->getMessage());
            return $this->back();
        }
    }

    public function showEditar(Request $request, array $params): Response
    {
        $id = (int)($params['id'] ?? 0);
        $negocio = $this->restauranteModel->find($id);

        if (!$negocio) {
            Session::flash('error', 'Negocio no encontrado.');
            return $this->redirect(app_url('/superadmin/negocios'));
        }

        $planes = $this->planModel->allActive();
        $admin = $this->usuarioModel->findAdminByRestaurante($id);

        return $this->render('super/negocios/editar', [
            'title' => 'Editar Negocio — Super Admin',
            'negocio' => $negocio,
            'planes' => $planes,
            'admin' => $admin,
        ], 'super');
    }

    public function editar(Request $request, array $params): Response
    {
        $id = (int)($params['id'] ?? 0);
        $negocio = $this->restauranteModel->find($id);

        if (!$negocio) {
            Session::flash('error', 'Negocio no encontrado.');
            return $this->redirect(app_url('/superadmin/negocios'));
        }

        $nombre = trim((string)$request->post('nombre'));
        $slug = strtolower(trim((string)$request->post('slug')));
        $whatsapp = preg_replace('/\D/', '', (string)$request->post('whatsapp'));
        $planId = (int)$request->post('plan_id');
        $ciclo = (string)$request->post('ciclo');

        $validator = Validator::make([
            'nombre' => $nombre,
            'slug' => $slug,
            'whatsapp' => $whatsapp,
            'plan_id' => $planId,
            'ciclo' => $ciclo,
        ], [
            'nombre' => 'required|min:3|max:150',
            'slug' => 'required|slug|min:3|max:40',
            'whatsapp' => 'required|min:10|max:15',
            'plan_id' => 'required|numeric',
            'ciclo' => 'required|in:mensual,anual',
        ]);

        if ($validator->fails()) {
            Session::flash('error', $validator->firstError());
            return $this->back();
        }

        if (!$this->restauranteModel->isSlugAvailable($slug, $id)) {
            Session::flash('error', "El slug '{$slug}' ya está en uso o reservado.");
            return $this->back();
        }

        $this->restauranteModel->update($id, [
            'nombre' => $nombre,
            'slug' => $slug,
            'whatsapp' => $whatsapp,
            'plan_id' => $planId,
            'ciclo' => $ciclo,
        ]);

        Auditoria::registrar('editar_negocio', 'restaurantes', $id, $negocio, [
            'nombre' => $nombre,
            'slug' => $slug,
            'whatsapp' => $whatsapp,
            'plan_id' => $planId,
            'ciclo' => $ciclo,
        ], $id);

        Session::flash('success', 'Negocio actualizado exitosamente.');
        return $this->redirect(app_url('/superadmin/negocios'));
    }

    public function resetPassword(Request $request, array $params): Response
    {
        $id = (int)($params['id'] ?? 0);
        $negocio = $this->restauranteModel->find($id);
        $admin = $this->usuarioModel->findAdminByRestaurante($id);

        if (!$negocio || !$admin) {
            Session::flash('error', 'No se encontró el administrador del negocio.');
            return $this->redirect(app_url('/superadmin/negocios'));
        }

        $tempPassword = 'Reset' . rand(100, 999) . '!' . bin2hex(random_bytes(2));
        $hash = Auth::hashPassword($tempPassword);

        $this->usuarioModel->update((int)$admin['id'], [
            'password_hash' => $hash,
            'debe_cambiar_password' => 1,
        ]);

        Auditoria::registrar('reset_password_admin', 'usuarios', (int)$admin['id'], null, [
            'email' => $admin['email'],
        ], $id);

        return $this->render('super/negocios/credenciales', [
            'title' => 'Contraseña Restablecida — Super Admin',
            'restaurante' => $negocio,
            'admin' => [
                'nombre' => $admin['nombre'],
                'email' => $admin['email'],
                'tempPassword' => $tempPassword,
            ],
            'esReset' => true,
        ], 'super');
    }
}
