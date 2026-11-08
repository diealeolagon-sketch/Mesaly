<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Super Admin — Mesaly') ?></title>
    <link rel="icon" type="image/svg+xml" href="<?= e(app_url('/assets/img/logo-icon.svg')) ?>">
    <link rel="stylesheet" href="<?= e(asset_url('/assets/css/fonts.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset_url('/assets/css/mesaly.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset_url('/assets/css/admin.css')) ?>">
</head>
<body class="admin-body">
    <?php $currentUri = $_SERVER['REQUEST_URI'] ?? ''; ?>
    <header class="top-nav">
        <div class="top-nav-inner">
            <div class="nav-brand-group">
                <a href="<?= e(app_url('/superadmin')) ?>" class="nav-brand-link">
                    <img src="<?= e(app_url('/assets/img/logo-horizontal.svg')) ?>" alt="Mesaly" class="nav-brand-logo">
                </a>
                <span class="nav-tenant-badge nav-tenant-badge-super">
                    <?= icon('settings', 14) ?>
                    <span>Super Admin</span>
                </span>
            </div>

            <nav class="nav-links-wrap">
                <a href="<?= e(app_url('/superadmin')) ?>" class="nav-link-item <?= (str_ends_with(rtrim($currentUri, '/'), '/superadmin') || str_contains($currentUri, '/superadmin?')) ? 'active' : '' ?>">
                    <?= icon('dashboard', 16) ?>
                    <span>Vencimientos</span>
                </a>
                <a href="<?= e(app_url('/superadmin/pagos')) ?>" class="nav-link-item <?= str_contains($currentUri, '/superadmin/pagos') ? 'active' : '' ?>">
                    <?= icon('cash', 16) ?>
                    <span>Historial de Pagos</span>
                </a>
                <a href="<?= e(app_url('/superadmin/metricas')) ?>" class="nav-link-item <?= str_contains($currentUri, '/superadmin/metricas') ? 'active' : '' ?>">
                    <?= icon('chart', 16) ?>
                    <span>Métricas Globales</span>
                </a>
            </nav>

            <div class="nav-user-actions" style="display: flex; align-items: center; gap: 12px;">
                <span class="nav-user-name" style="font-size: 13px; font-weight: 600; color: var(--color-text-main);">
                    <?= e($user['nombre'] ?? 'Super Admin') ?>
                </span>
                <form method="POST" action="<?= e(app_url('/logout')) ?>" class="nav-logout-form">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-secondary btn-sm" title="Cerrar sesión">
                        <?= icon('logout', 14) ?>
                        <span>Salir</span>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <main class="admin-shell">
        <?php if (!empty($flash_success)): ?>
            <div class="alert alert-success auth-alert-gap">
                <?= icon('check', 16) ?>
                <span><?= e($flash_success) ?></span>
            </div>
        <?php endif; ?>

        <?php if (!empty($flash_error)): ?>
            <div class="alert alert-danger auth-alert-gap">
                <?= icon('alert', 16) ?>
                <span><?= e($flash_error) ?></span>
            </div>
        <?php endif; ?>

        <?= $content ?>
    </main>

    <script nonce="<?= e($cspNonce) ?>" src="<?= e(asset_url('/assets/js/admin.js')) ?>"></script>
</body>
</html>
