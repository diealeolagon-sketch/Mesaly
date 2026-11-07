<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;

class AuthMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, array $params = []): ?Response
    {
        if (!Auth::check()) {
            Session::flash('error', 'Debe iniciar sesión para acceder a este recurso.');
            $response = new Response();
            return $response->redirect(app_url('/login'));
        }

        $user = Auth::user();
        $uri = $request->getUri();

        // Si debe cambiar contraseña por primer ingreso, forzarlo excepto en rutas de cambio o logout
        if (!empty($user['debe_cambiar_password']) && $uri !== '/cambiar-password' && $uri !== '/logout') {
            $response = new Response();
            return $response->redirect(app_url('/cambiar-password'));
        }

        return null;
    }
}
