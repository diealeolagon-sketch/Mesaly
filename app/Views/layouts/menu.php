<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= e($title ?? ($restaurante['nombre'] . ' — Menú Digital')) ?></title>
    
    <!-- Meta tags para compartir en WhatsApp y Redes Sociales -->
    <meta name="description" content="<?= e($restaurante['descripcion'] ?? 'Consulta nuestra carta interactiva y realiza tu pedido en línea.') ?>">
    <meta property="og:title" content="<?= e($restaurante['nombre']) ?> — Menú Digital">
    <meta property="og:description" content="<?= e($restaurante['descripcion'] ?? 'Consulta nuestra carta interactiva y realiza tu pedido en línea.') ?>">
    <meta property="og:type" content="website">
    <?php if (!empty($restaurante['logo_path'])): ?>
        <meta property="og:image" content="<?= e(app_url($restaurante['logo_path'])) ?>">
    <?php endif; ?>

    <!-- Favicon -->
    <?php if (!empty($restaurante['logo_path'])): ?>
        <link rel="icon" type="image/webp" href="<?= e(app_url($restaurante['logo_path'])) ?>">
    <?php else: ?>
        <link rel="icon" type="image/svg+xml" href="<?= e(app_url('/assets/img/logo-icon.svg')) ?>">
    <?php endif; ?>

    <!-- Fuentes de Google -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- CSS Unificado de Mesaly -->
    <link rel="stylesheet" href="<?= e(app_url('/assets/css/mesaly.css')) ?>?v=6">

    <!-- Variables Dinámicas de Marca del Restaurante -->
    <style>
        :root {
            --menu-primary: <?= !empty($restaurante['color_primario']) ? e($restaurante['color_primario']) : '#E85D04' ?>;
            --menu-secondary: <?= !empty($restaurante['color_secundario']) ? e($restaurante['color_secundario']) : '#FFB703' ?>;
            --menu-bg: <?= !empty($restaurante['color_fondo']) ? e($restaurante['color_fondo']) : '#FFF7EA' ?>;
            --menu-text: <?= !empty($restaurante['color_texto']) ? e($restaurante['color_texto']) : '#1E1B18' ?>;
            --menu-font: '<?= !empty($restaurante['fuente']) ? e($restaurante['fuente']) : 'Inter' ?>', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        body.public-menu-body {
            font-family: var(--menu-font);
            background-color: var(--menu-bg);
            color: var(--menu-text);
            margin: 0;
            padding: 0;
            -webkit-tap-highlight-color: transparent;
        }
    </style>
</head>
<body class="public-menu-body">

    <?= $content ?>

</body>
</html>
