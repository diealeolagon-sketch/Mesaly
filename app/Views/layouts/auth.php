<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Iniciar Sesión — Mesaly') ?></title>
    <link rel="icon" type="image/svg+xml" href="<?= e(app_url('/assets/img/logo-icon.svg')) ?>">
    <link rel="stylesheet" href="<?= e(asset_url('/assets/css/fonts.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset_url('/assets/css/mesaly.css')) ?>">
</head>
<body class="auth-body">
    <div class="auth-split-layout">
        <!-- Panel Izquierdo: Showcase de Marca y Propuesta de Valor -->
        <aside class="auth-hero-panel">
            <div class="auth-hero-top">
                <a href="<?= e(app_url('/')) ?>" class="auth-brand-logo-link">
                    <img src="<?= e(app_url('/assets/img/logo-horizontal.svg')) ?>" alt="Mesaly" class="auth-logo-img">
                </a>
                <span class="auth-hero-badge">
                    <?= icon('zap', 14) ?>
                    <span>SaaS Gastronómico</span>
                </span>
            </div>

            <div class="auth-hero-content">
                <h1 class="auth-hero-headline">
                    Tu restaurante en el <span class="highlight">bolsillo</span> de tus clientes.
                </h1>
                <p class="auth-hero-subtext">
                    La plataforma operativa diseñada para restaurantes modernos: cartas digitales interactivas, pedidos directos a tu WhatsApp y control de platos en tiempo real.
                </p>

                <!-- Mockup de Menú Digital en Vivo -->
                <div class="auth-mockup-card">
                    <div class="auth-mockup-header">
                        <div class="auth-mockup-store">
                            <span class="auth-mockup-store-icon"><?= icon('store', 16) ?></span>
                            <div>
                                <span class="auth-mockup-store-name">Fuego &amp; Brasa Grill</span>
                                <span class="auth-mockup-store-status">● Abierto · Recibiendo pedidos</span>
                            </div>
                        </div>
                        <span class="auth-mockup-badge">Mesa 04</span>
                    </div>

                    <div class="auth-mockup-item">
                        <div class="auth-mockup-thumb"><?= icon('cutlery', 18) ?></div>
                        <div class="auth-mockup-details">
                            <h4 class="auth-mockup-title">Churrasco al Carbón 350g</h4>
                            <p class="auth-mockup-desc">Corte Angus madurado + Papas criollas + Chimichurri</p>
                        </div>
                        <div class="auth-mockup-price-wrap">
                            <span class="auth-mockup-price">$ 42.000</span>
                            <span class="auth-mockup-curr">COP</span>
                        </div>
                    </div>
                </div>

                <!-- Píldoras de Beneficios -->
                <div class="auth-hero-features">
                    <span class="auth-feature-chip">
                        <?= icon('tag', 14) ?> <span>0% Comisiones por venta</span>
                    </span>
                    <span class="auth-feature-chip">
                        <?= icon('smartphone', 14) ?> <span>QR para mesa y domicilio</span>
                    </span>
                    <span class="auth-feature-chip">
                        <?= icon('zap', 14) ?> <span>Agotados en tiempo real</span>
                    </span>
                </div>
            </div>

            <div class="auth-hero-footer">
                <span>© <?= date('Y') ?> Mesaly</span>
                <span>·</span>
                <span>Plataforma de Menús Digitales Interactivos</span>
            </div>
        </aside>

        <!-- Panel Derecho: Formulario de Autenticación -->
        <main class="auth-form-panel">
            <div class="auth-form-top">
                <span class="auth-support-text">
                    ¿Nuevo negocio? <a href="https://wa.me/573001234567" target="_blank" rel="noopener" class="auth-support-link">Contactar Soporte</a>
                </span>
            </div>

            <div class="auth-form-container">
                <!-- Logo en móvil -->
                <div class="auth-mobile-logo">
                    <img src="<?= e(app_url('/assets/img/logo-horizontal.svg')) ?>" alt="Mesaly" class="auth-mobile-logo-img">
                </div>

                <?php if (!empty($flash_success)): ?>
                    <div class="alert alert-success auth-alert-gap">
                        <?= icon('check', 16) ?>
                        <span><?= e($flash_success) ?></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($flash_error)): ?>
                    <div class="alert alert-danger auth-alert-gap">
                        <?= icon('alert', 16) ?>
                        <span><?= e($flash_error) ?></span>
                    </div>
                <?php endif; ?>

                <?= $content ?>
            </div>

            <div class="auth-panel-bottom">
                <span><?= icon('lock', 13) ?> Acceso seguro con cifrado SSL</span>
                <span>Colombia · COP</span>
            </div>
        </main>
    </div>
</body>
</html>
