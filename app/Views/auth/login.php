<h1 class="auth-title">Bienvenido</h1>
<p class="auth-subtitle">Ingresa tus credenciales para acceder al panel</p>

<form method="POST" action="<?= e(app_url('/login')) ?>">
    <?= csrf_field() ?>

    <div class="form-group">
        <label for="email" class="form-label">Correo electrónico</label>
        <input type="email" id="email" name="email" class="form-control" placeholder="correo@ejemplo.com" required autofocus>
    </div>

    <div class="form-group">
        <label for="password" class="form-label">Contraseña</label>
        <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
    </div>

    <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 8px;">
        Iniciar Sesión
    </button>
</form>
