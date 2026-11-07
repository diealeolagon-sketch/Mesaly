<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Panel de Administración — Mesaly') ?></title>
    <link rel="icon" type="image/svg+xml" href="<?= e(app_url('/assets/img/logo-icon.svg')) ?>">
    <link rel="stylesheet" href="<?= e(app_url('/assets/css/brand.css')) ?>">
    <link rel="stylesheet" href="<?= e(app_url('/assets/css/admin.css')) ?>">
</head>
<body class="admin-body">
    <?php
    $currentUri = $_SERVER['REQUEST_URI'] ?? '';
    ?>
    <header class="admin-navbar">
        <div class="admin-nav-left">
            <a href="<?= e(app_url('/admin')) ?>" class="admin-brand-link">
                <img src="<?= e(app_url('/assets/img/logo-horizontal.svg')) ?>" alt="Mesaly" class="admin-brand-logo">
            </a>

            <?php if (!empty($tenant['nombre'])): ?>
                <span class="admin-restaurant-badge" title="Restaurante activo">
                    🍽️ <?= e($tenant['nombre']) ?>
                </span>
            <?php endif; ?>

            <nav class="admin-nav-menu">
                <a href="<?= e(app_url('/admin')) ?>" class="admin-nav-item <?= $currentUri === '/admin' || str_ends_with($currentUri, '/admin') ? 'active' : '' ?>">
                    <span>📊</span> Resumen
                </a>
                <a href="<?= e(app_url('/admin/catalogo')) ?>" class="admin-nav-item <?= str_contains($currentUri, '/catalogo') ? 'active' : '' ?>">
                    <span>📋</span> Catálogo
                </a>
                <a href="<?= e(app_url('/admin/modificadores')) ?>" class="admin-nav-item <?= str_contains($currentUri, '/modificadores') ? 'active' : '' ?>">
                    <span>⚙️</span> Modificadores
                </a>
                <a href="<?= e(app_url('/admin/disponibilidad')) ?>" class="admin-nav-item <?= str_contains($currentUri, '/disponibilidad') ? 'active' : '' ?>" style="color: #DC2626;">
                    <span>⚡</span> Agotados
                </a>
                <a href="<?= e(app_url('/admin/configuracion')) ?>" class="admin-nav-item <?= str_contains($currentUri, '/configuracion') ? 'active' : '' ?>">
                    <span>🎨</span> Configuración
                </a>
            </nav>
        </div>

        <div class="admin-nav-right">
            <?php if (!empty($tenant['slug'])): ?>
                <a href="<?= e(app_url('/' . $tenant['slug'])) ?>" target="_blank" class="btn-modern btn-modern-secondary btn-modern-sm" style="color: var(--brand-primary); font-weight: 700;">
                    👁️ Ver Menú Público
                </a>
            <?php endif; ?>

            <div style="font-size: 13.5px; color: var(--admin-text-muted); display: flex; align-items: center; gap: 8px;">
                <span>Hola, <strong><?= e($user['nombre'] ?? 'Admin') ?></strong></span>
            </div>

            <form method="POST" action="<?= e(app_url('/logout')) ?>" style="display: inline;">
                <?= csrf_field() ?>
                <button type="submit" class="btn-modern btn-modern-secondary btn-modern-sm" title="Cerrar sesión">
                    Cerrar Sesión
                </button>
            </form>
        </div>
    </header>

    <main style="max-width: 1240px; margin: 32px auto; padding: 0 24px;">
        <?php if (!empty($flash_success)): ?>
            <div class="alert alert-success" style="border-radius: var(--radius-md); box-shadow: var(--shadow-subtle);">
                <strong>✓ Éxito:</strong> <?= e($flash_success) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($flash_error)): ?>
            <div class="alert alert-error" style="border-radius: var(--radius-md); box-shadow: var(--shadow-subtle);">
                <strong>✕ Error:</strong> <?= e($flash_error) ?>
            </div>
        <?php endif; ?>

        <?= $content ?>
    </main>
</body>
</html>
