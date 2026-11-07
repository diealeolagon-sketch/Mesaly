<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Error — Mesaly') ?></title>
    <link rel="icon" type="image/svg+xml" href="<?= e(app_url('/assets/img/logo-icon.svg')) ?>">
    <link rel="stylesheet" href="<?= e(app_url('/assets/css/brand.css')) ?>">
    <style nonce="<?= e($cspNonce) ?>">
        .error-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            text-align: center;
        }
        .error-card {
            max-width: 500px;
            background: var(--brand-white);
            border-radius: var(--radius-lg);
            padding: 40px 32px;
            border: 1px solid var(--brand-gray-border);
            box-shadow: var(--shadow-md);
        }
        .error-code {
            font-size: 64px;
            font-weight: 800;
            color: var(--brand-primary);
            line-height: 1;
            margin-bottom: 12px;
        }
        .error-card h1 {
            font-size: 20px;
            margin-bottom: 12px;
        }
        .error-card p {
            color: var(--brand-gray-muted);
            margin-bottom: 24px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-card">
            <?= $content ?>
        </div>
    </div>
</body>
</html>
