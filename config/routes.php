<?php

declare(strict_types=1);

/**
 * @var App\Core\Router $router
 */

use App\Controllers\Admin\CatalogoController;
use App\Controllers\Admin\ConfigController;
use App\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Controllers\Admin\DisponibilidadController;
use App\Controllers\Admin\MesaController;
use App\Controllers\Admin\ModificadorController;
use App\Controllers\Admin\PedidoAdminController;
use App\Controllers\Admin\ReporteController;
use App\Controllers\AuthController;
use App\Controllers\Public\LandingController;
use App\Controllers\Public\MenuController;
use App\Controllers\Super\NegocioController;
use App\Core\Auth;
use App\Middleware\AuthMiddleware;
use App\Middleware\CsrfMiddleware;
use App\Middleware\RolMiddleware;
use App\Middleware\SuscripcionMiddleware;
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
// Raíz — Landing Page Oficial SaaS
// -----------------------------------------------------------------------------
$router->get('/', [LandingController::class, 'index']);

// -----------------------------------------------------------------------------
// Super Admin (/superadmin)
// -----------------------------------------------------------------------------
$superMiddlewares = [new AuthMiddleware(), new RolMiddleware(Auth::ROLE_SUPER_ADMIN)];
$superPostMiddlewares = [new AuthMiddleware(), new RolMiddleware(Auth::ROLE_SUPER_ADMIN), new CsrfMiddleware()];

$router->get('/superadmin', [NegocioController::class, 'index'], $superMiddlewares);

// Negocios y Vencimientos
$router->get('/superadmin/negocios', [NegocioController::class, 'index'], $superMiddlewares);
$router->get('/superadmin/negocios/crear', [NegocioController::class, 'showCrear'], $superMiddlewares);
$router->post('/superadmin/negocios/crear', [NegocioController::class, 'crear'], $superPostMiddlewares);
$router->get('/superadmin/negocios/{id}/editar', [NegocioController::class, 'showEditar'], $superMiddlewares);
$router->post('/superadmin/negocios/{id}/editar', [NegocioController::class, 'editar'], $superPostMiddlewares);
$router->post('/superadmin/negocios/{id}/reset-password', [NegocioController::class, 'resetPassword'], $superPostMiddlewares);
$router->post('/superadmin/negocios/{id}/pago', [NegocioController::class, 'registrarPago'], $superPostMiddlewares);
$router->post('/superadmin/negocios/{id}/extender', [NegocioController::class, 'extenderDias'], $superPostMiddlewares);
$router->post('/superadmin/negocios/{id}/estado', [NegocioController::class, 'cambiarEstado'], $superPostMiddlewares);
$router->post('/superadmin/negocios/{id}/ver-como', [NegocioController::class, 'verComo'], $superPostMiddlewares);
$router->post('/superadmin/dejar-ver-como', [NegocioController::class, 'dejarVerComo'], $superPostMiddlewares);
$router->get('/superadmin/pagos', [NegocioController::class, 'pagos'], $superMiddlewares);
$router->get('/superadmin/negocios/{id}/pagos-json', [NegocioController::class, 'historialPagosJson'], $superMiddlewares);
$router->get('/superadmin/metricas', [NegocioController::class, 'metricas'], $superMiddlewares);
$router->get('/superadmin/logs', [NegocioController::class, 'logs'], $superMiddlewares);

// -----------------------------------------------------------------------------
// Admin de Negocio (/admin)
// -----------------------------------------------------------------------------
$adminBaseMiddlewares = [new AuthMiddleware(), new TenantMiddleware()];
$adminMiddlewares = [new AuthMiddleware(), new TenantMiddleware(), new SuscripcionMiddleware()];
$adminPostMiddlewares = [new AuthMiddleware(), new TenantMiddleware(), new SuscripcionMiddleware(), new CsrfMiddleware()];

$router->get('/admin/suscripcion-vencida', [AdminDashboardController::class, 'suscripcionVencida'], $adminBaseMiddlewares);
$router->get('/admin', [AdminDashboardController::class, 'index'], $adminMiddlewares);
$router->post('/admin/toggle-recibiendo-pedidos', [AdminDashboardController::class, 'toggleRecibiendoPedidos'], $adminPostMiddlewares);
$router->post('/admin/pagar-suscripcion', [AdminDashboardController::class, 'pagarSuscripcion'], $adminPostMiddlewares);

// Pedidos en Vivo y Operación (Fase 3)
$router->get('/admin/pedidos/poll-nuevos', [PedidoAdminController::class, 'pollNuevos'], $adminMiddlewares);
$router->get('/admin/pedidos', [PedidoAdminController::class, 'index'], $adminMiddlewares);
$router->get('/admin/pedidos/{id}', [PedidoAdminController::class, 'verComanda'], $adminMiddlewares);
$router->post('/admin/pedidos/{id}/estado', [PedidoAdminController::class, 'cambiarEstado'], $adminPostMiddlewares);
$router->post('/admin/pedidos/{id}/liberar-mesa', [PedidoAdminController::class, 'liberarMesa'], $adminPostMiddlewares);
$router->post('/admin/mesas/{id}/liberar', [PedidoAdminController::class, 'liberarMesaPorId'], $adminPostMiddlewares);
$router->get('/admin/pedidos/{id}/detalle-json', [PedidoAdminController::class, 'detalleJson'], $adminMiddlewares);

// Reportes Financieros y Exportación a Excel
$router->get('/admin/reportes', [ReporteController::class, 'index'], $adminMiddlewares);
$router->get('/admin/reportes/exportar', [ReporteController::class, 'exportar'], $adminMiddlewares);

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

// Mesas y Códigos QR (Fase 2)
$router->get('/admin/mesas', [MesaController::class, 'index'], $adminMiddlewares);
$router->post('/admin/mesas/crear', [MesaController::class, 'guardar'], $adminPostMiddlewares);
$router->post('/admin/mesas/rapida', [MesaController::class, 'generarRapida'], $adminPostMiddlewares);
$router->post('/admin/mesas/completar-tope', [MesaController::class, 'completarTope'], $adminPostMiddlewares);
$router->post('/admin/mesas/lote', [MesaController::class, 'generarLote'], $adminPostMiddlewares);
$router->post('/admin/mesas/{id}/editar', [MesaController::class, 'actualizar'], $adminPostMiddlewares);
$router->post('/admin/mesas/{id}/toggle', [MesaController::class, 'toggle'], $adminPostMiddlewares);
$router->post('/admin/mesas/{id}/eliminar', [MesaController::class, 'eliminar'], $adminPostMiddlewares);
$router->get('/admin/mesas/{id}/qr', [MesaController::class, 'descargarQr'], $adminMiddlewares);
$router->get('/admin/mesas/qr-general', [MesaController::class, 'descargarQrGeneral'], $adminMiddlewares);
$router->get('/admin/mesas/pdf', [MesaController::class, 'descargarPdf'], $adminMiddlewares);

// -----------------------------------------------------------------------------
// Menú Digital Público (Comensales)
// -----------------------------------------------------------------------------
$router->get('/{slug}/mesa/{numero}', [MenuController::class, 'mesa']);
$router->post('/{slug}/mesa/limpiar', [MenuController::class, 'limpiarMesa']);
$router->post('/{slug}/pedido', [MenuController::class, 'crearPedido']);
$router->get('/{slug}', [MenuController::class, 'show']);

