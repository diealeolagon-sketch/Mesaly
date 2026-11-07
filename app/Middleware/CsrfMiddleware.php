<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Csrf;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;

class CsrfMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, array $params = []): ?Response
    {
        $method = $request->getMethod();
        if (in_array($method, ['GET', 'HEAD', 'OPTIONS'], true)) {
            return null;
        }

        $token = $request->post('_csrf_token')
            ?? $request->json('_csrf_token')
            ?? $request->header('x-csrf-token');

        if (!Csrf::validate($token)) {
            $response = new Response();
            $response->setStatusCode(403);

            if ($request->isAjax()) {
                return $response->json([
                    'success' => false,
                    'message' => 'Token CSRF inválido o expirado. Por favor recargue la página.',
                ], 403);
            }

            try {
                $html = View::render('errors/403', [
                    'title' => 'Acceso denegado (CSRF)',
                    'message' => 'La sesión del formulario ha expirado o el token CSRF es inválido. Por favor regrese y recargue.',
                ], 'error');
                return $response->setContent($html);
            } catch (\Throwable) {
                return $response->setContent('<h1>403 — Token de seguridad inválido</h1>');
            }
        }

        return null;
    }
}
