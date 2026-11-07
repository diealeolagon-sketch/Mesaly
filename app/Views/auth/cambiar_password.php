<h1 class="auth-title">Actualizar Contraseña</h1>
<p class="auth-subtitle">Por seguridad, debes definir una contraseña nueva de al menos 10 caracteres.</p>

<form method="POST" action="<?= e(app_url('/cambiar-password')) ?>">
    <?= csrf_field() ?>

    <div class="form-group">
        <label for="password" class="form-label">Nueva Contraseña (mínimo 10 caracteres)</label>
        <input type="password" id="password" name="password" class="form-control" minlength="10" placeholder="••••••••••••" required autofocus>
    </div>

    <div class="form-group">
        <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" minlength="10" placeholder="••••••••••••" required>
    </div>

    <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 8px;">
        Guardar y Continuar
    </button>
</form>
