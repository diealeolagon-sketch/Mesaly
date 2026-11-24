<div class="auth-clean-header text-center mb-5">
    <div class="inline-flex items-center gap-2 px-3 py-1 bg-brand-light rounded-pill mb-3">
        <?= icon('lock', 14) ?>
        <span class="text-xs fw-700 text-brand text-uppercase tracking-wider">Seguridad de Acceso</span>
    </div>

    <div class="flex justify-center mb-3">
        <div class="auth-mascot-avatar-wrap" style="width: 100px; height: 100px; border-radius: 50%; background: linear-gradient(135deg, #FFF7ED, #FFEDD5); border: 3px solid #EA580C; box-shadow: 0 8px 24px rgba(234, 88, 12, 0.15); display: flex; align-items: center; justify-content: center; overflow: hidden;">
            <img src="<?= e(asset_url('/assets/img/landing/waitress-qr-transparent.png')) ?>" alt="Seguridad Mesaly" style="width: 85px; height: auto; object-fit: contain; transform: translateY(4px);">
        </div>
    </div>

    <h2 class="auth-clean-title text-2xl mb-1">Actualizar Contraseña</h2>
    <p class="auth-clean-subtitle text-sm text-muted m-0">
        Define una clave segura para proteger la información y comandas de tu restaurante.
    </p>
</div>

<form method="POST" action="<?= e(app_url('/cambiar-password')) ?>" class="auth-form auth-clean-form">
    <?= csrf_field() ?>

    <div class="auth-clean-group mb-4">
        <label for="password" class="auth-clean-label">Nueva Contraseña</label>
        <div class="auth-clean-input-box">
            <span class="auth-clean-input-icon">
                <?= icon('lock', 18) ?>
            </span>
            <input type="password" id="password" name="password" class="auth-clean-input" placeholder="Mínimo 10 caracteres" autocomplete="new-password" required autofocus minlength="10">
            <button type="button" class="auth-clean-pwd-toggle" data-action="toggle-password" data-target="password" aria-label="Mostrar u ocultar contraseña" title="Mostrar contraseña">
                <?= icon('eye', 16) ?>
            </button>
        </div>
        <span class="text-xs text-muted mt-1 block">Debe contener al menos 10 caracteres.</span>
    </div>

    <div class="auth-clean-group mb-5">
        <label for="password_confirmation" class="auth-clean-label">Confirmar Nueva Contraseña</label>
        <div class="auth-clean-input-box">
            <span class="auth-clean-input-icon">
                <?= icon('lock', 18) ?>
            </span>
            <input type="password" id="password_confirmation" name="password_confirmation" class="auth-clean-input" placeholder="Repite tu nueva contraseña" autocomplete="new-password" required minlength="10">
            <button type="button" class="auth-clean-pwd-toggle" data-action="toggle-password" data-target="password_confirmation" aria-label="Mostrar u ocultar contraseña" title="Mostrar contraseña">
                <?= icon('eye', 16) ?>
            </button>
        </div>
    </div>

    <div class="auth-clean-btn-stack">
        <button type="submit" class="auth-btn-primary auth-btn-dark-pill w-full">
            <span>Guardar Contraseña y Continuar</span>
            <?= icon('arrow-right', 16) ?>
        </button>

        <a href="<?= e(app_url('/admin')) ?>" class="btn btn-secondary btn-sm text-center w-full">
            <span>Volver al Panel</span>
        </a>
    </div>
</form>

<div class="auth-clean-forgot-row mt-4 text-center">
    <span>¿Necesitas ayuda con tu cuenta?</span>
    <a href="<?= e(support_whatsapp_url('Hola equipo de soporte Mesaly, necesito asistencia con el cambio de contraseña de mi restaurante.')) ?>" target="_blank" rel="noopener">
        Contactar Soporte por WhatsApp
    </a>
</div>
