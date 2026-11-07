<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Model;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\TenantContext;

class TenantMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, array $params = []): ?Response
    {
        $user = Auth::user();
        if (!$user) {
            $res = new Response();
            return $res->redirect(app_url('/login'));
        }

        $tenantId = null;

        // Caso 1: Super Admin en modo "Ver como"
        if ($user['rol'] === Auth::ROLE_SUPER_ADMIN) {
            $impersonatedId = Session::get('impersonating_tenant_id');
            if ($impersonatedId) {
                $tenantId = (int)$impersonatedId;
            }
        } else {
            // Caso 2: Admin de negocio regular
            $tenantId = isset($user['restaurante_id']) ? (int)$user['restaurante_id'] : null;
        }

        if (!$tenantId) {
            $res = new Response();
            $res->setStatusCode(403);
            return $res->setContent("<h1>Acceso no autorizado: no hay un restaurante vinculado a esta cuenta.</h1>");
        }

        // Cargar restaurante desde la base de datos
        $pdo = Model::getPdo();
        $stmt = $pdo->prepare("SELECT * FROM `restaurantes` WHERE `id` = :id AND `deleted_at` IS NULL LIMIT 1");
        $stmt->execute(['id' => $tenantId]);
        $restaurante = $stmt->fetch();

        if (!$restaurante) {
            $res = new Response();
            $res->setStatusCode(404);
            return $res->setContent("<h1>El restaurante asociado ya no existe o fue dado de baja.</h1>");
        }

        // Fijar en el contexto centralizado
        TenantContext::setTenant($restaurante);

        return null;
    }
}
