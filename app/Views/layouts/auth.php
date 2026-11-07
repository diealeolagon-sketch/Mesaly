<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Mesaly') ?></title>
    <link rel="icon" type="image/svg+xml" href="<?= e(app_url('/assets/img/logo-icon.svg')) ?>">
    <link rel="stylesheet" href="<?= e(app_url('/assets/css/brand.css')) ?>">
    <style nonce="<?= e($cspNonce) ?>">
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .auth-card {
            width: 100%;
            max-width: 420px;
            background-color: var(--brand-white);
            border-radius: var(--radius-lg);
            padding: 36px 32px;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--brand-gray-border);
        }
        .auth-logo {
            text-align: center;
            margin-bottom: 28px;
        }
        .auth-logo img {
            max-height: 48px;
            width: auto;
        }
        .auth-title {
            font-size: 20px;
            text-align: center;
            margin-bottom: 8px;
        }
        .auth-subtitle {
            font-size: 13px;
            color: var(--brand-gray-muted);
            text-align: center;
            margin-bottom: 24px;
        }
        .auth-footer {
            margin-top: 24px;
            text-align: center;
            font-size: 12px;
            color: var(--brand-gray-muted);
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-logo">
                <img src="<?= e(app_url('/assets/img/logo-horizontal.svg')) ?>" alt="Mesaly">
            </div>

            <?php if (!empty($flash_success)): ?>
                <div class="alert alert-success"><?= e($flash_success) ?></div>
            <?php endif; ?>

            <?php if (!empty($flash_error)): ?>
                <div class="alert alert-error"><?= e($flash_error) ?></div>
            <?php endif; ?>

            <?= $content ?>

            <div class="auth-footer">
                &copy; <?= date('Y') ?> Mesaly. Todos los derechos reservados.
            </div>
        </div>
    </div>
</body>
</html>
