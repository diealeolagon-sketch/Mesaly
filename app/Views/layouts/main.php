<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Mesaly') ?></title>
    <link rel="icon" type="image/svg+xml" href="<?= e(app_url('/assets/img/logo-icon.svg')) ?>">
    <link rel="stylesheet" href="<?= e(app_url('/assets/css/brand.css')) ?>">
</head>
<body>
    <div style="max-width: 960px; margin: 40px auto; padding: 0 20px;">
        <?= $content ?>
    </div>
</body>
</html>
