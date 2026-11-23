<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Mesaly') ?></title>
    <link rel="icon" type="image/svg+xml" href="<?= e(asset_url('/assets/img/logo-icon.svg')) ?>">
    <link rel="stylesheet" href="<?= e(asset_url('/assets/css/tokens.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset_url('/assets/css/fonts.css')) ?>">
    <link rel="stylesheet" href="<?= e(app_url('/assets/css/mesaly.css')) ?>?v=5">
</head>
<body>
    <div class="container-main-shell">
        <?= $content ?>
    </div>
</body>
</html>
