<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

    <!-- Fuentes Autohospedadas Locales (Sin CDN externo, compatible con CSP) -->
    <link rel="preload" href="<?= e(app_url('/assets/fonts/inter_400_normal.woff2')) ?>" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="<?= e(app_url('/assets/fonts/outfit_600_normal.woff2')) ?>" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="<?= e(asset_url('/assets/css/fonts.css')) ?>">

    <!-- CSS de Mesaly con versionado automático -->
    <link rel="stylesheet" href="<?= e(asset_url('/assets/css/mesaly.css')) ?>">

    <?php
    $fuente = $restaurante['fuente'] ?? 'Moderna';
    if (in_array($fuente, ['Inter', 'Poppins', 'Outfit', 'Roboto'], true)) {
        $fuente = 'Moderna';
    } elseif (in_array($fuente, ['Playfair', 'Playfair Display'], true)) {
        $fuente = 'Elegante';
    } elseif ($fuente === 'System') {
        $fuente = 'Sistema';
    }

    $pares = [
        'Moderna' => [
            'heading' => "'Outfit', sans-serif",
            'body' => "'Inter', sans-serif",
        ],
        'Calida' => [
            'heading' => "'Fraunces', serif",
            'body' => "'Inter', sans-serif",
        ],
        'Elegante' => [
            'heading' => "'Playfair Display', serif",
            'body' => "'Inter', sans-serif",
        ],
        'Urbana' => [
            'heading' => "'Oswald', sans-serif",
            'body' => "'Inter', sans-serif",
        ],
        'Sistema' => [
            'heading' => "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif",
            'body' => "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif",
        ],
    ];
    $parSeleccionado = $pares[$fuente] ?? $pares['Moderna'];
    ?>

    <!-- Variables Dinámicas de Marca del Restaurante con Nonce CSP -->
    <style nonce="<?= e($cspNonce) ?>">
        :root {
            --menu-primary: <?= !empty($restaurante['color_primario']) ? e($restaurante['color_primario']) : '#E85D04' ?>;
            --menu-secondary: <?= !empty($restaurante['color_secundario']) ? e($restaurante['color_secundario']) : '#FFB703' ?>;
            --menu-bg: <?= !empty($restaurante['color_fondo']) ? e($restaurante['color_fondo']) : '#FFF7EA' ?>;
            --menu-text: <?= !empty($restaurante['color_texto']) ? e($restaurante['color_texto']) : '#1E1B18' ?>;
            --menu-font-heading: <?= $parSeleccionado['heading'] ?>;
            --menu-font-body: <?= $parSeleccionado['body'] ?>;
            --menu-font: var(--menu-font-body);
        }
        body.public-menu-body {
            font-family: var(--menu-font-body);
            background-color: var(--menu-bg);
            color: var(--menu-text);
            margin: 0;
            padding: 0;
            -webkit-tap-highlight-color: transparent;
        }
        .menu-brand-title,
        .menu-section-title,
        .product-modal-title,
        .group-title,
        .checkout-section-title {
            font-family: var(--menu-font-heading);
        }
    </style>
</head>
<body class="public-menu-body">

    <?= $content ?>

</body>
</html>
