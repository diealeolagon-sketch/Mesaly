<div class="auth-form-header">
    <h1 class="auth-form-title">Actualizar Contraseña</h1>
    <p class="auth-form-subtitle">Por seguridad, debes definir una contraseña nueva de al menos 10 caracteres para tu cuenta.</p>
</div>

<form method="POST" action="<?= e(app_url('/cambiar-password')) ?>" class="auth-form">
    <?= csrf_field() ?>

    <div class="auth-input-group">
        <label for="password">Nueva Contraseña (mínimo 10 caracteres)</label>
        <div class="auth-input-inner">
            <span class="auth-input-icon">
                <?= icon('lock', 18) ?>
            </span>
            <input type="password" id="password" name="password" class="auth-text-input" minlength="10" placeholder="••••••••••••" autocomplete="new-password" required autofocus>
            <button type="button" class="auth-toggle-pwd" data-action="toggle-password" data-target="password" aria-label="Mostrar u ocultar contraseña" title="Mostrar contraseña">
                <?= icon('eye', 16) ?>
            </button>
        </div>
    </div>

    <div class="auth-input-group">
        <label for="password_confirmation">Confirmar Nueva Contraseña</label>
        <div class="auth-input-inner">
            <span class="auth-input-icon">
                <?= icon('lock', 18) ?>
            </span>
            <input type="password" id="password_confirmation" name="password_confirmation" class="auth-text-input" minlength="10" placeholder="••••••••••••" autocomplete="new-password" required>
            <button type="button" class="auth-toggle-pwd" data-action="toggle-password" data-target="password_confirmation" aria-label="Mostrar u ocultar contraseña" title="Mostrar contraseña">
                <?= icon('eye', 16) ?>
            </button>
        </div>
    </div>

    <button type="submit" class="auth-btn-primary">
        <span>Guardar y Continuar</span>
        <?= icon('arrow-right', 18) ?>
    </button>
</form>
