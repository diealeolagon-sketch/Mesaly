<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Error — Mesaly') ?></title>
    <link rel="icon" type="image/svg+xml" href="<?= e(app_url('/assets/img/logo-icon.svg')) ?>">
    <link rel="stylesheet" href="<?= e(asset_url('/assets/css/fonts.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset_url('/assets/css/mesaly.css')) ?>">
</head>
<body class="admin-body error-page">
    <div class="error-wrap">
        <div class="error-logo">
            <img src="<?= e(app_url('/assets/img/logo-horizontal.svg')) ?>" alt="Mesaly">
        </div>
        <?= $content ?>
    </div>
</body>
</html>
