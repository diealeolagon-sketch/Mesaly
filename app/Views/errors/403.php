<div class="error-code">403</div>
<h1>Acceso Restringido</h1>
<p><?= e($message ?? 'No tienes permisos suficientes para acceder a este recurso.') ?></p>
<a href="<?= e(app_url('/login')) ?>" class="btn btn-primary">Volver al Inicio</a>
