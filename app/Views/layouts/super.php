<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Super Admin — Mesaly') ?></title>
    <link rel="icon" type="image/svg+xml" href="<?= e(app_url('/assets/img/logo-icon.svg')) ?>">
    <link rel="stylesheet" href="<?= e(app_url('/assets/css/admin.css')) ?>?v=2">
</head>
<body class="admin-body">
    <?php $currentUri = $_SERVER['REQUEST_URI'] ?? ''; ?>
    <header class="top-nav">
        <div class="top-nav-inner">
            <div class="nav-brand-group">
                <a href="<?= e(app_url('/superadmin')) ?>" style="display: flex; align-items: center;">
                    <img src="<?= e(app_url('/assets/img/logo-horizontal.svg')) ?>" alt="Mesaly" class="nav-brand-logo">
                </a>
                <span class="nav-tenant-badge" style="background: #FEF2F2; color: #DC2626; border-color: #FECACA;">
                    <?= icon('settings', 14) ?>
                    <span>Super Admin</span>
                </span>
            </div>

            <nav class="nav-links-wrap">
                <a href="<?= e(app_url('/superadmin')) ?>" class="nav-link-item <?= str_ends_with(rtrim($currentUri, '/'), '/superadmin') ? 'active' : '' ?>">
                    <?= icon('dashboard', 16) ?>
                    <span>Vencimientos</span>
                </a>
                <a href="<?= e(app_url('/superadmin/negocios')) ?>" class="nav-link-item <?= str_contains($currentUri, '/negocios') ? 'active' : '' ?>">
                    <?= icon('store', 16) ?>
                    <span>Negocios</span>
                </a>
            </nav>

            <div class="nav-user-actions">
                <span style="font-size: 13px; color: var(--color-text-muted);">
                    <?= e($user['nombre'] ?? 'Super Admin') ?>
                </span>
                <form method="POST" action="<?= e(app_url('/logout')) ?>" style="display: inline;">
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
            <div class="alert alert-success" style="margin-bottom: 20px;">
                <?= icon('check', 16) ?>
                <span><?= e($flash_success) ?></span>
            </div>
        <?php endif; ?>

        <?php if (!empty($flash_error)): ?>
            <div class="alert alert-error" style="margin-bottom: 20px;">
                <?= icon('x', 16) ?>
                <span><?= e($flash_error) ?></span>
            </div>
        <?php endif; ?>

        <?= $content ?>
    </main>
</body>
</html>
