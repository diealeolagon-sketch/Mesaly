<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Panel de Control — Mesaly') ?></title>
    <link rel="icon" type="image/svg+xml" href="<?= e(app_url('/assets/img/logo-icon.svg')) ?>">
    <link rel="stylesheet" href="<?= e(asset_url('/assets/css/fonts.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset_url('/assets/css/mesaly.css')) ?>">
</head>
<body class="admin-body">
    <?php
    $currentUri = $_SERVER['REQUEST_URI'] ?? '';
    ?>
    <header class="top-nav">
        <div class="top-nav-inner">
            <div class="nav-brand-group">
                <a href="<?= e(app_url('/admin')) ?>" class="nav-brand-link">
                    <img src="<?= e(app_url('/assets/img/logo-horizontal.svg')) ?>" alt="Mesaly" class="nav-brand-logo">
                </a>

                <?php if (!empty($tenant['nombre'])): ?>
                    <span class="nav-tenant-badge">
                        <?= icon('store', 15) ?>
                        <span><?= e($tenant['nombre']) ?></span>
                    </span>
                <?php endif; ?>
            </div>

            <nav class="nav-links-wrap">
                <a href="<?= e(app_url('/admin')) ?>" class="nav-link-item <?= $currentUri === '/admin' || str_ends_with($currentUri, '/admin') ? 'active' : '' ?>">
                    <?= icon('dashboard', 16) ?>
                    <span>Resumen</span>
                </a>
                <a href="<?= e(app_url('/admin/catalogo')) ?>" class="nav-link-item <?= str_contains($currentUri, '/catalogo') ? 'active' : '' ?>">
                    <?= icon('catalog', 16) ?>
                    <span>Catálogo</span>
                </a>
                <a href="<?= e(app_url('/admin/modificadores')) ?>" class="nav-link-item <?= str_contains($currentUri, '/modificadores') ? 'active' : '' ?>">
                    <?= icon('modifiers', 16) ?>
                    <span>Modificadores</span>
                </a>
                <a href="<?= e(app_url('/admin/disponibilidad')) ?>" class="nav-link-item nav-link-agotados <?= str_contains($currentUri, '/disponibilidad') ? 'active' : '' ?>">
                    <?= icon('zap', 16) ?>
                    <span>Agotados</span>
                </a>
                <a href="<?= e(app_url('/admin/configuracion')) ?>" class="nav-link-item <?= str_contains($currentUri, '/configuracion') ? 'active' : '' ?>">
                    <?= icon('settings', 16) ?>
                    <span>Configuración</span>
                </a>
            </nav>

            <div class="nav-user-actions">
                <?php if (!empty($tenant['slug'])): ?>
                    <a href="<?= e(app_url('/' . $tenant['slug'])) ?>" target="_blank" class="btn-action btn-secondary btn-sm nav-btn-preview">
                        <?= icon('eye', 14) ?>
                        <span>Ver Menú</span>
                    </a>
                <?php endif; ?>

                <form method="POST" action="<?= e(app_url('/logout')) ?>" class="nav-logout-form">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn-action btn-secondary btn-sm" title="Cerrar sesión">
                        <?= icon('logout', 14) ?>
                        <span>Salir</span>
                    </button>
                </form>
            </div>
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

    <script nonce="<?= e($cspNonce) ?>" src="<?= e(asset_url('/assets/js/admin.js')) ?>"></script>
</body>
</html>
