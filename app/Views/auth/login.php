<div class="auth-clean-header">
    <h2 class="auth-clean-title">¡Bienvenido de nuevo!</h2>
    <p class="auth-clean-subtitle">
        ¿No tienes una cuenta aún?
        <a href="<?= e(support_whatsapp_url('Hola, deseo adquirir Mesaly para mi restaurante.')) ?>" target="_blank" rel="noopener" class="auth-clean-link">
            Solicita acceso ahora
        </a>. Toma menos de un minuto.
    </p>
</div>

<form method="POST" action="<?= e(app_url('/login')) ?>" class="auth-form auth-clean-form">
    <?= csrf_field() ?>

    <div class="auth-clean-group">
        <label for="email" class="auth-clean-label">Correo Electrónico</label>
        <div class="auth-clean-input-box">
            <span class="auth-clean-input-icon">
                <?= icon('mail', 18) ?>
            </span>
            <input type="email" id="email" name="email" class="auth-clean-input" placeholder="correo@tudominio.com" autocomplete="email" required autofocus>
        </div>
    </div>

    <div class="auth-clean-group">
        <label for="password" class="auth-clean-label">Contraseña</label>
        <div class="auth-clean-input-box">
            <span class="auth-clean-input-icon">
                <?= icon('lock', 18) ?>
            </span>
            <input type="password" id="password" name="password" class="auth-clean-input" placeholder="••••••••••••" autocomplete="current-password" required>
            <button type="button" class="auth-clean-pwd-toggle" data-action="toggle-password" data-target="password" aria-label="Mostrar u ocultar contraseña" title="Mostrar contraseña">
                <?= icon('eye', 16) ?>
            </button>
        </div>
    </div>

    <div class="auth-clean-btn-stack">
        <button type="submit" class="auth-btn-primary auth-btn-dark-pill">
            <span>Iniciar Sesión Ahora</span>
        </button>

        <a href="<?= e(support_whatsapp_url('Hola equipo de soporte Mesaly, necesito asistencia para ingresar a mi cuenta.')) ?>" target="_blank" rel="noopener" class="auth-btn-whatsapp-pill">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
            </svg>
            <span>Soporte Directo por WhatsApp</span>
        </a>
    </div>
</form>

<div class="auth-clean-forgot-row">
    <span>¿Olvidaste tu contraseña?</span>
    <a href="<?= e(support_whatsapp_url('Hola, olvidé mi contraseña de acceso a Mesaly.')) ?>" target="_blank" rel="noopener">
        Haz clic aquí
    </a>
</div>
