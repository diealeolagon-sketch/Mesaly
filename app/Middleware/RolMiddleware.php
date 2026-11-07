<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;

class RolMiddleware implements MiddlewareInterface
{
    private string $rolEsperado;

    public function __construct(string $rolEsperado = Auth::ROLE_SUPER_ADMIN)
    {
        $this->rolEsperado = $rolEsperado;
    }

    public function handle(Request $request, array $params = []): ?Response
    {
        $rolActual = Auth::role();

        if ($rolActual !== $this->rolEsperado) {
            $response = new Response();
            $response->setStatusCode(403);

            if ($request->isAjax()) {
                return $response->json([
                    'success' => false,
                    'message' => 'No tiene permisos para realizar esta acción.',
                ], 403);
            }

            try {
                $html = View::render('errors/403', [
                    'title' => 'Acceso denegado',
                    'message' => 'No tiene permisos suficientes para acceder a este panel.',
                ], 'error');
                return $response->setContent($html);
            } catch (\Throwable) {
                return $response->setContent('<h1>403 — Acceso denegado</h1>');
            }
        }

        return null;
    }
}
