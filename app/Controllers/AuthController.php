<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Model;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Validator;

class AuthController extends Controller
{
    public function showLogin(Request $request): Response
    {
        if (Auth::check()) {
            return $this->redirect(Auth::isSuperAdmin() ? app_url('/superadmin') : app_url('/admin'));
        }

        return $this->render('auth/login', [
            'title' => 'Iniciar Sesión — Mesaly',
        ], 'auth');
    }

    public function login(Request $request): Response
    {
        $email = trim((string)$request->post('email'));
        $password = (string)$request->post('password');

        $validator = Validator::make([
            'email' => $email,
            'password' => $password,
        ], [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            Session::flash('error', $validator->firstError());
            return $this->redirect(app_url('/login'));
        }

        $result = Auth::attempt($email, $password, $request->ip());

        if (!$result['success']) {
            Session::flash('error', $result['message']);
            return $this->redirect(app_url('/login'));
        }

        $user = $result['user'];
        if (!empty($user['debe_cambiar_password'])) {
            return $this->redirect(app_url('/cambiar-password'));
        }

        if ($user['rol'] === Auth::ROLE_SUPER_ADMIN) {
            return $this->redirect(app_url('/superadmin'));
        }

        return $this->redirect(app_url('/admin'));
    }

    public function logout(Request $request): Response
    {
        Auth::logout();
        Session::flash('success', 'Sesión cerrada exitosamente.');
        return $this->redirect(app_url('/login'));
    }

    public function showChangePassword(Request $request): Response
    {
        if (!Auth::check()) {
            return $this->redirect(app_url('/login'));
        }

        return $this->render('auth/cambiar_password', [
            'title' => 'Actualizar Contraseña — Mesaly',
        ], 'auth');
    }

    public function changePassword(Request $request): Response
    {
        if (!Auth::check()) {
            return $this->redirect(app_url('/login'));
        }

        $newPassword = (string)$request->post('password');
        $confirmPassword = (string)$request->post('password_confirmation');

        $validator = Validator::make([
            'password' => $newPassword,
        ], [
            'password' => 'required|min:10',
        ]);

        if ($validator->fails()) {
            Session::flash('error', $validator->firstError());
            return $this->redirect(app_url('/cambiar-password'));
        }

        if ($newPassword !== $confirmPassword) {
            Session::flash('error', 'Las contraseñas no coinciden.');
            return $this->redirect(app_url('/cambiar-password'));
        }

        $userId = Auth::id();
        $hash = Auth::hashPassword($newPassword);

        $pdo = Model::getPdo();
        $stmt = $pdo->prepare("UPDATE `usuarios` SET `password_hash` = :hash, `debe_cambiar_password` = 0, `updated_at` = :now WHERE `id` = :id");
        $stmt->execute([
            'hash' => $hash,
            'now' => date('Y-m-d H:i:s'),
            'id' => $userId,
        ]);

        // Actualizar en sesión
        $user = Auth::user();
        $user['debe_cambiar_password'] = 0;
        Session::set('user', $user);

        Session::flash('success', 'Contraseña actualizada correctamente.');

        if (Auth::isSuperAdmin()) {
            return $this->redirect(app_url('/superadmin'));
        }

        return $this->redirect(app_url('/admin'));
    }
}
