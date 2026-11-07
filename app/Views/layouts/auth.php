<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Iniciar Sesión — Mesaly') ?></title>
    <link rel="icon" type="image/svg+xml" href="<?= e(app_url('/assets/img/logo-icon.svg')) ?>">
    <link rel="stylesheet" href="<?= e(app_url('/assets/css/brand.css')) ?>" nonce="<?= e($cspNonce) ?>">
    <link rel="stylesheet" href="<?= e(app_url('/assets/css/admin.css')) ?>" nonce="<?= e($cspNonce) ?>">
    <style nonce="<?= e($cspNonce) ?>">
        body.auth-body {
            background-color: #F8FAFC;
            background-image: radial-gradient(at 0% 0%, rgba(232, 93, 4, 0.04) 0px, transparent 50%),
                              radial-gradient(at 100% 100%, rgba(255, 183, 3, 0.05) 0px, transparent 50%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }

        .auth-wrapper {
            width: 100%;
            max-width: 420px;
        }

        .auth-brand {
            text-align: center;
            margin-bottom: 28px;
        }

        .auth-brand img {
            height: 38px;
            width: auto;
        }

        .auth-card-main {
            background: #FFFFFF;
            border: 1px solid rgba(15, 23, 42, 0.08);
            border-radius: 16px;
            padding: 36px 32px;
            box-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.04), 0 12px 24px -4px rgba(15, 23, 42, 0.06);
        }

        .auth-title {
            font-size: 20px;
            font-weight: 700;
            color: #0F172A;
            margin: 0 0 6px 0;
            letter-spacing: -0.015em;
        }

        .auth-subtitle {
            font-size: 13.5px;
            color: #64748B;
            margin: 0 0 24px 0;
            line-height: 1.45;
        }

        .auth-footer-text {
            text-align: center;
            font-size: 12.5px;
            color: #94A3B8;
            margin-top: 24px;
        }
    </style>
</head>
<body class="auth-body">
    <div class="auth-wrapper">
        <div class="auth-brand">
            <a href="<?= e(app_url('/')) ?>" style="display: inline-block;">
                <img src="<?= e(app_url('/assets/img/logo-horizontal.svg')) ?>" alt="Mesaly">
            </a>
        </div>

        <div class="auth-card-main">
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
        </div>

        <div class="auth-footer-text">
            &copy; <?= date('Y') ?> Mesaly &middot; Menús Digitales Interactivos
        </div>
    </div>
</body>
</html>
