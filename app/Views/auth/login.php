<div class="auth-form-header">
    <h1 class="auth-form-title">Bienvenido de nuevo</h1>
    <p class="auth-form-subtitle">Ingresa tus credenciales para administrar tu restaurante</p>
</div>

<form method="POST" action="<?= e(app_url('/login')) ?>">
    <?= csrf_field() ?>

    <div class="auth-input-group">
        <label for="email">Correo Electrónico</label>
        <div class="auth-input-inner">
            <span class="auth-input-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect width="20" height="16" x="2" y="4" rx="2"/>
                    <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>
                </svg>
            </span>
            <input type="email" id="email" name="email" class="auth-text-input" placeholder="correo@tudominio.com" required autofocus>
        </div>
    </div>

    <div class="auth-input-group">
        <label for="password">Contraseña</label>
        <div class="auth-input-inner">
            <span class="auth-input-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
            </span>
            <input type="password" id="password" name="password" class="auth-text-input" placeholder="••••••••••••" required>
        </div>
    </div>

    <button type="submit" class="auth-btn-primary">
        <span>Acceder al Panel</span>
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M5 12h14M13 6l6 6-6 6"/>
        </svg>
    </button>
</form>

<div class="auth-form-footer">
    <p style="margin: 0;">¿Olvidaste tu contraseña o tienes problemas para ingresar?<br>
    <a href="https://wa.me/573001234567?text=Hola,%20necesito%20ayuda%20para%20ingresar%20a%20mi%20cuenta%20de%20Mesaly" target="_blank" rel="noopener">Solicitar soporte técnico por WhatsApp →</a></p>
</div>
