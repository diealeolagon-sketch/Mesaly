<?php

declare(strict_types=1);

/**
 * @var App\Core\Router $router
 */

use App\Controllers\Admin\CatalogoController;
use App\Controllers\Admin\ConfigController;
use App\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Controllers\Admin\DisponibilidadController;
use App\Controllers\Admin\ModificadorController;
use App\Controllers\AuthController;
use App\Controllers\Public\MenuController;
use App\Controllers\Super\DashboardController as SuperDashboardController;
use App\Controllers\Super\NegocioController;
use App\Core\Auth;
use App\Middleware\AuthMiddleware;
use App\Middleware\CsrfMiddleware;
use App\Middleware\RolMiddleware;
use App\Middleware\TenantMiddleware;

// -----------------------------------------------------------------------------
// Autenticación
// -----------------------------------------------------------------------------
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login'], [new CsrfMiddleware()]);
$router->post('/logout', [AuthController::class, 'logout'], [new CsrfMiddleware()]);

$router->get('/cambiar-password', [AuthController::class, 'showChangePassword'], [new AuthMiddleware()]);
$router->post('/cambiar-password', [AuthController::class, 'changePassword'], [new AuthMiddleware(), new CsrfMiddleware()]);

// -----------------------------------------------------------------------------
// Raíz
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
// Super Admin (/superadmin)
// -----------------------------------------------------------------------------
$superMiddlewares = [new AuthMiddleware(), new RolMiddleware(Auth::ROLE_SUPER_ADMIN)];
$superPostMiddlewares = [new AuthMiddleware(), new RolMiddleware(Auth::ROLE_SUPER_ADMIN), new CsrfMiddleware()];

$router->get('/superadmin', [SuperDashboardController::class, 'index'], $superMiddlewares);

// Negocios
$router->get('/superadmin/negocios', [NegocioController::class, 'index'], $superMiddlewares);
$router->get('/superadmin/negocios/crear', [NegocioController::class, 'showCrear'], $superMiddlewares);
$router->post('/superadmin/negocios/crear', [NegocioController::class, 'crear'], $superPostMiddlewares);
$router->get('/superadmin/negocios/{id}/editar', [NegocioController::class, 'showEditar'], $superMiddlewares);
$router->post('/superadmin/negocios/{id}/editar', [NegocioController::class, 'editar'], $superPostMiddlewares);
$router->post('/superadmin/negocios/{id}/reset-password', [NegocioController::class, 'resetPassword'], $superPostMiddlewares);

// -----------------------------------------------------------------------------
// Admin de Negocio (/admin)
// -----------------------------------------------------------------------------
$adminMiddlewares = [new AuthMiddleware(), new TenantMiddleware()];
$adminPostMiddlewares = [new AuthMiddleware(), new TenantMiddleware(), new CsrfMiddleware()];

$router->get('/admin', [AdminDashboardController::class, 'index'], $adminMiddlewares);

// Configuración
$router->get('/admin/configuracion', [ConfigController::class, 'show'], $adminMiddlewares);
$router->post('/admin/configuracion', [ConfigController::class, 'guardar'], $adminPostMiddlewares);

// Catálogo (Categorías y Productos)
$router->get('/admin/catalogo', [CatalogoController::class, 'index'], $adminMiddlewares);
$router->post('/admin/catalogo/categoria/guardar', [CatalogoController::class, 'guardarCategoria'], $adminPostMiddlewares);
$router->post('/admin/catalogo/categoria/{id}/eliminar', [CatalogoController::class, 'eliminarCategoria'], $adminPostMiddlewares);

$router->get('/admin/catalogo/producto/nuevo', [CatalogoController::class, 'showProductoForm'], $adminMiddlewares);
$router->get('/admin/catalogo/producto/{id}/editar', [CatalogoController::class, 'showProductoForm'], $adminMiddlewares);
$router->post('/admin/catalogo/producto/guardar', [CatalogoController::class, 'guardarProducto'], $adminPostMiddlewares);
$router->post('/admin/catalogo/producto/{id}/toggle-agotado', [CatalogoController::class, 'toggleAgotado'], $adminPostMiddlewares);
$router->post('/admin/catalogo/producto/{id}/eliminar', [CatalogoController::class, 'eliminarProducto'], $adminPostMiddlewares);

// Modificadores
$router->get('/admin/modificadores', [ModificadorController::class, 'index'], $adminMiddlewares);
$router->post('/admin/modificadores/grupo/guardar', [ModificadorController::class, 'guardarGrupo'], $adminPostMiddlewares);
$router->post('/admin/modificadores/grupo/{id}/eliminar', [ModificadorController::class, 'eliminarGrupo'], $adminPostMiddlewares);
$router->post('/admin/modificadores/opcion/guardar', [ModificadorController::class, 'guardarOpcion'], $adminPostMiddlewares);
$router->post('/admin/modificadores/opcion/{id}/eliminar', [ModificadorController::class, 'eliminarOpcion'], $adminPostMiddlewares);

// Disponibilidad rápida
$router->get('/admin/disponibilidad', [DisponibilidadController::class, 'index'], $adminMiddlewares);
$router->post('/admin/disponibilidad/toggle', [DisponibilidadController::class, 'toggle'], $adminPostMiddlewares);

// -----------------------------------------------------------------------------
// Menú Digital Público (Comensales)
// -----------------------------------------------------------------------------
$router->get('/{slug}/mesa/{numero}', [MenuController::class, 'mesa']);
$router->post('/{slug}/mesa/limpiar', [MenuController::class, 'limpiarMesa']);
$router->post('/{slug}/pedido', [MenuController::class, 'crearPedido']);
$router->get('/{slug}', [MenuController::class, 'show']);

