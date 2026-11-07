<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Panel de Administración — Mesaly') ?></title>
    <link rel="icon" type="image/svg+xml" href="<?= e(app_url('/assets/img/logo-icon.svg')) ?>">
    <link rel="stylesheet" href="<?= e(app_url('/assets/css/brand.css')) ?>">
    <style nonce="<?= e($cspNonce) ?>">
        .admin-nav {
            background-color: var(--brand-white);
            border-bottom: 1px solid var(--brand-gray-border);
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }
        .admin-nav-left {
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }
        .admin-nav-brand {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .admin-nav-brand img {
            height: 32px;
        }
        .admin-nav-title {
            font-weight: 700;
            font-size: 16px;
            color: var(--brand-ink);
        }
        .admin-nav-links {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        .admin-nav-links a {
            font-weight: 600;
            font-size: 14px;
            color: var(--brand-ink);
            padding: 8px 12px;
            border-radius: var(--radius-sm);
        }
        .admin-nav-links a:hover {
            background: var(--brand-gray-light);
            color: var(--brand-primary);
        }
        .admin-nav-user {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 13px;
        }
        .admin-container {
            max-width: 1200px;
            margin: 28px auto;
            padding: 0 20px;
        }
        .card {
            background-color: var(--brand-white);
            border-radius: var(--radius-md);
            border: 1px solid var(--brand-gray-border);
            padding: 24px;
            box-shadow: var(--shadow-sm);
            margin-bottom: 24px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
        }
        .table th, .table td {
            padding: 12px 14px;
            text-align: left;
            border-bottom: 1px solid var(--brand-gray-border);
            font-size: 14px;
        }
        .table th {
            background-color: var(--brand-cream);
            font-weight: 600;
        }
        .btn-sm {
            min-height: 32px;
            padding: 4px 10px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <header class="admin-nav">
        <div class="admin-nav-left">
            <div class="admin-nav-brand">
                <a href="<?= e(app_url('/admin')) ?>">
                    <img src="<?= e(app_url('/assets/img/logo-horizontal.svg')) ?>" alt="Mesaly">
                </a>
            </div>
            <nav class="admin-nav-links">
                <a href="<?= e(app_url('/admin')) ?>">Resumen</a>
                <a href="<?= e(app_url('/admin/catalogo')) ?>">Catálogo</a>
                <a href="<?= e(app_url('/admin/modificadores')) ?>">Modificadores</a>
                <a href="<?= e(app_url('/admin/disponibilidad')) ?>" style="color: #DC2626;">⚡ Agotados</a>
                <a href="<?= e(app_url('/admin/configuracion')) ?>">Configuración</a>
            </nav>
        </div>
        <div class="admin-nav-user">
            <?php if (!empty($tenant['slug'])): ?>
                <a href="<?= e(app_url('/' . $tenant['slug'])) ?>" target="_blank" class="btn btn-secondary btn-sm" style="background: #FFF7EA; border-color: var(--brand-primary); color: var(--brand-primary);">
                    👁 Ver Menú
                </a>
            <?php endif; ?>
            <span><strong><?= e($user['nombre'] ?? 'Admin') ?></strong></span>
            <form method="POST" action="<?= e(app_url('/logout')) ?>" style="display: inline;">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-secondary btn-sm">
                    Salir
                </button>
            </form>
        </div>
    </header>

    <main class="admin-container">
        <?php if (!empty($flash_success)): ?>
            <div class="alert alert-success"><?= e($flash_success) ?></div>
        <?php endif; ?>

        <?php if (!empty($flash_error)): ?>
            <div class="alert alert-error"><?= e($flash_error) ?></div>
        <?php endif; ?>

        <?= $content ?>
    </main>
</body>
</html>
