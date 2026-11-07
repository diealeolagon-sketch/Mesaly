<?php

declare(strict_types=1);

/**
 * @var App\Core\Router $router
 */

use App\Controllers\AuthController;
use App\Controllers\Super\DashboardController as SuperDashboardController;
use App\Core\Auth;
use App\Middleware\AuthMiddleware;
use App\Middleware\CsrfMiddleware;
use App\Middleware\RolMiddleware;

// -----------------------------------------------------------------------------
// Rutas de Autenticación
// -----------------------------------------------------------------------------
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login'], [new CsrfMiddleware()]);
$router->post('/logout', [AuthController::class, 'logout'], [new CsrfMiddleware()]);

$router->get('/cambiar-password', [AuthController::class, 'showChangePassword'], [new AuthMiddleware()]);
$router->post('/cambiar-password', [AuthController::class, 'changePassword'], [new AuthMiddleware(), new CsrfMiddleware()]);

// -----------------------------------------------------------------------------
// Ruta principal / Landing (Fase 0: Redirige a /login)
// -----------------------------------------------------------------------------
$router->get('/', function ($request) {
    if (Auth::check()) {
        $url = Auth::isSuperAdmin() ? app_url('/superadmin') : app_url('/admin');
        header("Location: {$url}");
        exit;
    }
    header("Location: " . app_url('/login'));
    exit;
});

// -----------------------------------------------------------------------------
// Rutas Super Admin (/superadmin)
// -----------------------------------------------------------------------------
$router->get('/superadmin', [SuperDashboardController::class, 'index'], [
    new AuthMiddleware(),
    new RolMiddleware(Auth::ROLE_SUPER_ADMIN),
]);
