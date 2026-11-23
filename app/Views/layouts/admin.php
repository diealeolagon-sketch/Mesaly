<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <title><?= e($title ?? 'Panel de Control — Mesaly') ?></title>
    <link rel="icon" type="image/svg+xml" href="<?= e(asset_url('/assets/img/logo-icon.svg')) ?>">
    <link rel="stylesheet" href="<?= e(asset_url('/assets/css/tokens.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset_url('/assets/css/fonts.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset_url('/assets/css/mesaly.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset_url('/assets/css/admin.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset_url('/assets/vendor/flatpickr/flatpickr.min.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset_url('/assets/css/flatpickr-mesaly.css')) ?>">
</head>
<body class="admin-body admin-has-sidebar">
    <?php if (!empty(\App\Core\Session::get('impersonating_tenant_id'))): ?>
        <div class="impersonation-bar">
            <div class="flex items-center gap-2">
                <?= icon('sparkles', 15) ?>
                <span>Modo Soporte Super Admin: Estás administrando <strong><?= e($tenant['nombre'] ?? '') ?></strong>. Acciones registradas en auditoría.</span>
            </div>
            <form method="POST" action="<?= e(app_url('/superadmin/dejar-ver-como')) ?>" class="m-0">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-sm btn-impersonation-return">
                    Volver a Super Admin &rarr;
                </button>
            </form>
        </div>
    <?php endif; ?>

    <?php
    $currentUri = $_SERVER['REQUEST_URI'] ?? '';
    ?>

    <div class="admin-app-layout">
        <!-- Backdrop para móvil -->
        <div class="admin-sidebar-backdrop" id="adminSidebarBackdrop" aria-hidden="true"></div>

        <!-- SIDEBAR DE NAVEGACIÓN -->
        <aside class="admin-sidebar" id="adminSidebar">
            <div class="admin-sidebar-header">
                <a href="<?= e(app_url('/admin')) ?>" class="admin-sidebar-brand" title="Mesaly Admin">
                    <img src="<?= e(asset_url('/assets/img/logo-horizontal.svg')) ?>" alt="Mesaly" class="admin-sidebar-logo">
                </a>
                <button type="button" class="admin-sidebar-close" id="adminSidebarClose" aria-label="Cerrar navegación">
                    <?= icon('x', 20) ?>
                </button>
            </div>

            <?php if (!empty($tenant['nombre'])): ?>
                <div class="sidebar-tenant-badge">
                    <span class="sidebar-tenant-icon"><?= icon('store', 16) ?></span>
                    <div class="sidebar-tenant-details">
                        <span class="sidebar-tenant-title" title="<?= e($tenant['nombre']) ?>"><?= e($tenant['nombre']) ?></span>
                        <span class="sidebar-tenant-sub">Restaurante Activo</span>
                    </div>
                </div>
            <?php endif; ?>

            <div class="admin-sidebar-nav-container">
                <div class="sidebar-nav-group">
                    <span class="sidebar-group-label">Operación</span>
                    <nav class="sidebar-nav-list">
                        <a href="<?= e(app_url('/admin')) ?>" class="sidebar-nav-link <?= ($currentUri === '/admin' || str_ends_with($currentUri, '/admin') || str_ends_with($currentUri, '/admin/')) ? 'active' : '' ?>">
                            <?= icon('dashboard', 18) ?>
                            <span>Resumen</span>
                        </a>
                        <a href="<?= e(app_url('/admin/pedidos')) ?>" class="sidebar-nav-link <?= str_contains($currentUri, '/pedidos') ? 'active' : '' ?>">
                            <?= icon('cart', 18) ?>
                            <span>Pedidos en Vivo</span>
                        </a>
                        <a href="<?= e(app_url('/admin/mesas')) ?>" class="sidebar-nav-link <?= str_contains($currentUri, '/mesas') ? 'active' : '' ?>">
                            <?= icon('cutlery', 18) ?>
                            <span>Mesas &amp; QR</span>
                        </a>
                        <a href="<?= e(app_url('/admin/disponibilidad')) ?>" class="sidebar-nav-link <?= str_contains($currentUri, '/disponibilidad') ? 'active' : '' ?>">
                            <?= icon('zap', 18) ?>
                            <span>Agotados</span>
                        </a>
                    </nav>
                </div>

                <div class="sidebar-nav-group">
                    <span class="sidebar-group-label">Gestión &amp; Finanzas</span>
                    <nav class="sidebar-nav-list">
                        <a href="<?= e(app_url('/admin/reportes')) ?>" class="sidebar-nav-link <?= str_contains($currentUri, '/reportes') ? 'active' : '' ?>">
                            <?= icon('chart', 18) ?>
                            <span>Reportes &amp; Cierre</span>
                        </a>
                        <a href="<?= e(app_url('/admin/catalogo')) ?>" class="sidebar-nav-link <?= str_contains($currentUri, '/catalogo') ? 'active' : '' ?>">
                            <?= icon('catalog', 18) ?>
                            <span>Catálogo de Menú</span>
                        </a>
                        <a href="<?= e(app_url('/admin/modificadores')) ?>" class="sidebar-nav-link <?= str_contains($currentUri, '/modificadores') ? 'active' : '' ?>">
                            <?= icon('modifiers', 18) ?>
                            <span>Modificadores</span>
                        </a>
                        <a href="<?= e(app_url('/admin/configuracion')) ?>" class="sidebar-nav-link <?= str_contains($currentUri, '/configuracion') ? 'active' : '' ?>">
                            <?= icon('settings', 18) ?>
                            <span>Configuración</span>
                        </a>
                    </nav>
                </div>
            </div>

            <div class="admin-sidebar-footer">
                <?php if (!empty($tenant['slug'])): ?>
                    <a href="<?= e(app_url('/' . $tenant['slug'])) ?>" target="_blank" class="sidebar-footer-link" title="Abrir Menú Digital de Clientes">
                        <?= icon('eye', 16) ?>
                        <span>Ver Menú Digital</span>
                        <?= icon('external-link', 14, 'sidebar-footer-ext') ?>
                    </a>
                <?php endif; ?>

                <a href="<?= e(support_whatsapp_url('Hola soporte Mesaly, necesito asistencia con mi restaurante: ' . ($tenant['nombre'] ?? ''))) ?>" target="_blank" rel="noopener" class="sidebar-footer-link" title="Contactar a Soporte Mesaly por WhatsApp (+57 322 899 1339)">
                    <?= icon('whatsapp', 16) ?>
                    <span>Soporte Mesaly</span>
                    <?= icon('external-link', 14, 'sidebar-footer-ext') ?>
                </a>

                <form method="POST" action="<?= e(app_url('/logout')) ?>" class="m-0 w-full">
                    <?= csrf_field() ?>
                    <button type="submit" class="sidebar-footer-link sidebar-logout-btn" title="Cerrar sesión">
                        <?= icon('logout', 16) ?>
                        <span>Cerrar Sesión</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- ÁREA PRINCIPAL CON HEADER SUPERIOR -->
        <div class="admin-main-area">
            <header class="admin-header-bar">
                <div class="admin-header-left">
                    <button type="button" class="admin-header-toggle" id="adminSidebarToggle" aria-label="Abrir navegación">
                        <?= icon('menu', 20) ?>
                    </button>
                    <div class="admin-header-breadcrumb">
                        <span class="admin-breadcrumb-tenant"><?= e($tenant['nombre'] ?? 'Mesaly') ?></span>
                        <span class="admin-breadcrumb-sep">/</span>
                        <span class="admin-breadcrumb-title"><?= e($title ?? 'Panel') ?></span>
                    </div>
                </div>

                <div class="admin-header-right">
                    <button type="button" class="btn-action btn-secondary btn-sm nav-bell-btn" id="btnAdminBell" data-poll-url="<?= e(app_url('/admin/pedidos/poll-nuevos')) ?>" title="Notificaciones de nuevos pedidos (Clic para probar o silenciar sonido)" aria-label="Notificaciones de pedidos">
                        <?= icon('bell', 16) ?>
                        <span class="nav-bell-dot is-hidden" id="adminBellDot"></span>
                    </button>

                    <?php if (!empty($tenant['slug'])): ?>
                        <a href="<?= e(app_url('/' . $tenant['slug'])) ?>" target="_blank" class="btn btn-secondary btn-sm admin-preview-btn">
                            <?= icon('eye', 14) ?>
                            <span>Ver Menú</span>
                        </a>
                    <?php endif; ?>
                </div>
            </header>

            <main class="admin-shell">
                <?php if (!empty($flash_success)): ?>
                    <div class="alert alert-success">
                        <?= icon('check', 16) ?>
                        <span><?= e($flash_success) ?></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($flash_error)): ?>
                    <div class="alert alert-danger">
                        <?= icon('alert', 16) ?>
                        <span><?= e($flash_error) ?></span>
                    </div>
                <?php endif; ?>

                <?= $content ?>
            </main>
        </div>
    </div>

    <div id="adminToastContainer" class="admin-toast-container" aria-live="polite"></div>

    <script nonce="<?= e($cspNonce) ?>" src="<?= e(asset_url('/assets/vendor/chartjs/chart.umd.js')) ?>"></script>
    <script nonce="<?= e($cspNonce) ?>" src="<?= e(asset_url('/assets/vendor/flatpickr/flatpickr.min.js')) ?>"></script>
    <script nonce="<?= e($cspNonce) ?>" src="<?= e(asset_url('/assets/vendor/flatpickr/es.js')) ?>"></script>
    <script nonce="<?= e($cspNonce) ?>" src="<?= e(asset_url('/assets/js/admin.js')) ?>"></script>
</body>
</html>
