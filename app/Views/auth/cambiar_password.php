<div class="auth-clean-header">
    <h2 class="auth-clean-title">Actualizar Contraseña</h2>
    <p class="auth-clean-subtitle">
        Ingresa una nueva clave de acceso segura para proteger tu cuenta y la gestión de tus restaurantes.
    </p>
</div>

<form method="POST" action="<?= e(app_url('/cambiar-password')) ?>" class="auth-form auth-clean-form">
    <?= csrf_field() ?>

    <div class="auth-clean-group">
        <label for="password" class="auth-clean-label">Nueva Contraseña</label>
        <div class="auth-clean-input-box">
            <span class="auth-clean-input-icon">
                <?= icon('lock', 18) ?>
            </span>
            <input type="password" id="password" name="password" class="auth-clean-input" placeholder="Mínimo 10 caracteres" autocomplete="new-password" required autofocus>
            <button type="button" class="auth-clean-pwd-toggle" data-action="toggle-password" data-target="password" aria-label="Mostrar u ocultar contraseña" title="Mostrar contraseña">
                <?= icon('eye', 16) ?>
            </button>
        </div>
        <small style="display:block; margin-top:6px; font-size:12px; color:var(--color-text-muted, #64748B);">
            La contraseña debe tener al menos 10 caracteres.
        </small>
    </div>

    <div class="auth-clean-group">
        <label for="password_confirmation" class="auth-clean-label">Confirmar Nueva Contraseña</label>
        <div class="auth-clean-input-box">
            <span class="auth-clean-input-icon">
                <?= icon('lock', 18) ?>
            </span>
            <input type="password" id="password_confirmation" name="password_confirmation" class="auth-clean-input" placeholder="Repite la nueva contraseña" autocomplete="new-password" required>
            <button type="button" class="auth-clean-pwd-toggle" data-action="toggle-password" data-target="password_confirmation" aria-label="Mostrar u ocultar contraseña" title="Mostrar contraseña">
                <?= icon('eye', 16) ?>
            </button>
        </div>
    </div>

    <div class="auth-clean-btn-stack">
        <button type="submit" class="auth-btn-primary auth-btn-dark-pill">
            <span>Guardar y Actualizar Clave</span>
        </button>

        <?php 
            $backUrl = app_url('/');
            if (\App\Core\Auth::isSuperAdmin()) {
                $backUrl = app_url('/superadmin');
            } elseif (\App\Core\Auth::isAdmin()) {
                $backUrl = app_url('/admin');
            }
        ?>
        <a href="<?= e($backUrl) ?>" class="auth-btn-whatsapp-pill" style="border: 1px solid #E2E8F0; background: #FFFFFF; color: #475569;">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            <span>Volver al Panel</span>
        </a>
    </div>
</form>

<div class="auth-clean-forgot-row">
    <span>¿Necesitas asistencia con tu acceso?</span>
    <a href="<?= e(support_whatsapp_url('Hola equipo de soporte Mesaly, necesito asistencia para actualizar mi clave.')) ?>" target="_blank" rel="noopener">
        Soporte por WhatsApp
    </a>
</div>
