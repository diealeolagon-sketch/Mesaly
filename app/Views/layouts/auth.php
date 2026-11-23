<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Iniciar Sesión — Mesaly') ?></title>
    <link rel="icon" type="image/svg+xml" href="<?= e(asset_url('/assets/img/logo-icon.svg')) ?>">
    <link rel="stylesheet" href="<?= e(asset_url('/assets/css/tokens.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset_url('/assets/css/fonts.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset_url('/assets/css/mesaly.css')) ?>">
</head>
<body class="auth-body-split">
    <div class="auth-container-split">
        <!-- Panel Izquierdo: Hero de Marca con Curvas y Mascota 3D -->
        <aside class="auth-hero-side">
            <div class="auth-hero-side-bg" aria-hidden="true"></div>

            <div class="auth-hero-side-top">
                <div class="auth-hero-sparkle" aria-hidden="true">
                    <svg width="34" height="34" viewBox="0 0 24 24" fill="#FFFFFF">
                        <path d="M12 0L14.3 9.7L24 12L14.3 14.3L12 24L9.7 14.3L0 12L9.7 9.7L12 0Z"/>
                    </svg>
                </div>
            </div>

            <div class="auth-hero-side-content">
                <h1 class="auth-hero-side-title">
                    ¡Hola<br>
                    <span>Mesaly!</span>
                </h1>

                <p class="auth-hero-side-desc">
                    Automatiza la toma de pedidos, administra tus mesas con QR inteligente y recibe comandas directas a WhatsApp sin pagar comisiones.
                </p>

                <div class="auth-hero-mascot-container">
                    <img src="<?= e(asset_url('/assets/img/landing/mascot-manager-waving.png')) ?>" alt="Gerente Mesaly" class="auth-hero-mascot-img">
                </div>
            </div>

            <div class="auth-hero-side-footer">
                <span>© <?= date('Y') ?> Mesaly. Todos los derechos reservados.</span>
            </div>
        </aside>

        <!-- Panel Derecho: Formulario Blanco Limpio y Profesional -->
        <main class="auth-form-side">
            <div class="auth-form-side-inner">
                <div class="auth-form-brand-row">
                    <a href="<?= e(app_url('/')) ?>" title="Mesaly">
                        <img src="<?= e(asset_url('/assets/img/logo-horizontal.svg')) ?>" alt="Mesaly" class="auth-form-logo-img">
                    </a>
                </div>

                <?php if (!empty($flash_success)): ?>
                    <div class="alert alert-success auth-alert-gap mb-4">
                        <?= icon('check', 16) ?>
                        <span><?= e($flash_success) ?></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($flash_error)): ?>
                    <div class="alert alert-danger auth-alert-gap mb-4">
                        <?= icon('alert', 16) ?>
                        <span><?= e($flash_error) ?></span>
                    </div>
                <?php endif; ?>

                <?= $content ?>
            </div>
        </main>
    </div>

    <script nonce="<?= e($cspNonce) ?>" src="<?= e(asset_url('/assets/js/auth.js')) ?>"></script>
</body>
</html>
