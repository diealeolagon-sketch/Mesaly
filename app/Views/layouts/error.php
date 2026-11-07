<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Error — Mesaly') ?></title>
    <link rel="icon" type="image/svg+xml" href="<?= e(app_url('/assets/img/logo-icon.svg')) ?>">
    <link rel="stylesheet" href="<?= e(app_url('/assets/css/admin.css')) ?>?v=2">
    <style nonce="<?= e($cspNonce) ?>">
        body {
            background: var(--color-bg);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            text-align: center;
        }
        .error-wrap {
            max-width: 480px;
            width: 100%;
        }
        .error-code {
            font-size: 72px;
            font-weight: 800;
            color: var(--color-brand);
            line-height: 1;
            margin-bottom: 8px;
            letter-spacing: -0.04em;
        }
        .error-wrap h1 {
            font-size: 22px;
            font-weight: 700;
            color: var(--color-text-main);
            margin: 0 0 10px 0;
        }
        .error-wrap p {
            color: var(--color-text-muted);
            margin-bottom: 28px;
            font-size: 14px;
            line-height: 1.6;
        }
        .error-logo {
            margin-bottom: 32px;
        }
        .error-logo img {
            height: 32px;
            width: auto;
            opacity: 0.7;
        }
    </style>
</head>
<body class="admin-body">
    <div class="error-wrap">
        <div class="error-logo">
            <img src="<?= e(app_url('/assets/img/logo-horizontal.svg')) ?>" alt="Mesaly">
        </div>
        <?= $content ?>
    </div>
</body>
</html>
