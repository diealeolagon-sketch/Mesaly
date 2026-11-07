<h1 class="auth-title">Bienvenido</h1>
<p class="auth-subtitle">Ingresa tus credenciales para administrar tu restaurante</p>

<form method="POST" action="<?= e(app_url('/login')) ?>">
    <?= csrf_field() ?>

    <div class="form-group">
        <label for="email" class="form-label">Correo Electrónico</label>
        <input type="email" id="email" name="email" class="form-control" placeholder="correo@tudominio.com" required autofocus style="padding: 11px 14px; font-size: 14px;">
    </div>

    <div class="form-group">
        <label for="password" class="form-label">Contraseña</label>
        <input type="password" id="password" name="password" class="form-control" placeholder="••••••••••••" required style="padding: 11px 14px; font-size: 14px;">
    </div>

    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 12px 18px; font-size: 14px; font-weight: 700; margin-top: 8px;">
        Acceder al Panel
    </button>
</form>
