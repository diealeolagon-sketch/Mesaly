<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Mesaly — Software Gastronómico, Menú Digital y Pedidos en Mesa con QR') ?></title>
    <meta name="description" content="Mesaly es el software gastronómico todo en uno para restaurantes: Menú digital interactivo, pedidos en mesa con código QR, despacho a WhatsApp sin comisiones y control de comandas en vivo.">
    <meta name="keywords" content="menu digital restaurante, pedidos whatsapp restaurante, qr en mesas restaurante, software gastronomico colombia, pos restaurante, comandas cocina">
    
    <!-- OpenGraph / Social Sharing -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="Mesaly — Software Gastronómico, Menú Digital y Pedidos con QR">
    <meta property="og:description" content="Digitaliza tu restaurante sin pagar comisiones por venta. Menú digital, pedidos a WhatsApp y atención en mesas con QR.">
    <meta property="og:image" content="<?= e(asset_url('/assets/img/auth-bg.jpg')) ?>">
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="<?= e(asset_url('/assets/img/logo-icon.svg')) ?>">

    <!-- Tipografía Oficial Plus Jakarta Sans y Tokens -->
    <link rel="stylesheet" href="<?= e(asset_url('/assets/css/tokens.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset_url('/assets/css/fonts.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset_url('/assets/css/landing.css')) ?>?v=1">
</head>
<body class="landing-body">
    <?= $content ?>
</body>
</html>
