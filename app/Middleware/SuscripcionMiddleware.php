<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\TenantContext;
use App\Services\SuscripcionService;

class SuscripcionMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, array $params = []): ?Response
    {
        $tenant = TenantContext::getTenant();
        if (!$tenant) {
            return null;
        }

        // Si es Super Admin en modo soporte, permitir acceso
        $user = Auth::user();
        if ($user && $user['rol'] === Auth::ROLE_SUPER_ADMIN) {
            return null;
        }

        $estado = SuscripcionService::estadoDe($tenant);

        // Si está bloqueado y no está en la página de cuenta suspendida
        $path = $request->path();
        if ($estado['bloqueado'] && $path !== '/admin/suscripcion-vencida') {
            $res = new Response();
            return $res->redirect(app_url('/admin/suscripcion-vencida'));
        }

        return null;
    }
}
