<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Iniciar Sesión — Mesaly') ?></title>
    <link rel="icon" type="image/svg+xml" href="<?= e(app_url('/assets/img/logo-icon.svg')) ?>">
    <link rel="stylesheet" href="<?= e(app_url('/assets/css/mesaly.css')) ?>?v=5">
</head>
<body class="auth-body">
    <div class="auth-split-layout">
        <!-- Panel Izquierdo: Showcase de Marca y Propuesta de Valor -->
        <aside class="auth-hero-panel">
            <div class="auth-hero-top">
                <a href="<?= e(app_url('/')) ?>" style="display: inline-flex; align-items: center; text-decoration: none;">
                    <img src="<?= e(app_url('/assets/img/logo-horizontal.svg')) ?>" alt="Mesaly" style="height: 38px; width: auto; filter: brightness(0) invert(1);">
                </a>
                <span class="auth-hero-badge">
                    <span>⚡ SaaS Gastronómico</span>
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
                            <span style="font-size: 18px;">🔥</span>
                            <div>
                                <span style="display: block; line-height: 1.2;">Fuego & Brasa Grill</span>
                                <span style="font-size: 11px; color: #10B981; font-weight: 500;">● Abierto · Recibiendo pedidos</span>
                            </div>
                        </div>
                        <span class="auth-mockup-badge">Mesa 04</span>
                    </div>

                    <div class="auth-mockup-item">
                        <div class="auth-mockup-thumb">🥩</div>
                        <div class="auth-mockup-details">
                            <h4 class="auth-mockup-title">Churrasco al Carbón 350g</h4>
                            <p class="auth-mockup-desc">Corte Angus madurado + Papas criollas + Chimichurri</p>
                        </div>
                        <div style="text-align: right; flex-shrink: 0;">
                            <span class="auth-mockup-price">$ 42.000</span>
                            <span style="display: block; font-size: 10px; color: #94A3B8;">COP</span>
                        </div>
                    </div>
                </div>

                <!-- Píldoras de Beneficios -->
                <div class="auth-hero-features">
                    <span class="auth-feature-chip">
                        <span>🏷️</span> 0% Comisiones por venta
                    </span>
                    <span class="auth-feature-chip">
                        <span>📲</span> QR para mesa y domicilio
                    </span>
                    <span class="auth-feature-chip">
                        <span>⚡</span> Agotados en 1 segundo
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
                <span style="font-size: 13px; color: #64748B;">
                    ¿Nuevo negocio? <a href="https://wa.me/573001234567" target="_blank" rel="noopener" style="color: var(--color-brand); font-weight: 600; text-decoration: none;">Contactar Soporte</a>
                </span>
            </div>

            <div class="auth-form-container">
                <!-- Logo en móvil -->
                <div style="margin-bottom: 24px; display: none;" class="auth-mobile-logo">
                    <img src="<?= e(app_url('/assets/img/logo-horizontal.svg')) ?>" alt="Mesaly" style="height: 34px;">
                </div>

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

            <div class="auth-panel-bottom">
                <span>🔒 Acceso seguro con cifrado SSL</span>
                <span>Colombia · COP</span>
            </div>
        </main>
    </div>

    <style nonce="<?= e($cspNonce) ?>">
        @media (max-width: 960px) {
            .auth-mobile-logo {
                display: block !important;
                text-align: center;
            }
        }
    </style>
</body>
</html>
