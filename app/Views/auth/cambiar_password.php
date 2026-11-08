<div class="auth-form-header">
    <h1 class="auth-form-title">Actualizar Contraseña</h1>
    <p class="auth-form-subtitle">Por seguridad, debes definir una contraseña nueva de al menos 10 caracteres.</p>
</div>

<form method="POST" action="<?= e(app_url('/cambiar-password')) ?>">
    <?= csrf_field() ?>

    <div class="auth-input-group">
        <label for="password">Nueva Contraseña (mínimo 10 caracteres)</label>
        <div class="auth-input-inner">
            <span class="auth-input-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
            </span>
            <input type="password" id="password" name="password" class="auth-text-input" minlength="10" placeholder="••••••••••••" required autofocus>
        </div>
    </div>

    <div class="auth-input-group">
        <label for="password_confirmation">Confirmar Nueva Contraseña</label>
        <div class="auth-input-inner">
            <span class="auth-input-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
            </span>
            <input type="password" id="password_confirmation" name="password_confirmation" class="auth-text-input" minlength="10" placeholder="••••••••••••" required>
        </div>
    </div>

    <button type="submit" class="auth-btn-primary">
        <span>Guardar y Continuar</span>
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M5 12h14M13 6l6 6-6 6"/>
        </svg>
    </button>
</form>
