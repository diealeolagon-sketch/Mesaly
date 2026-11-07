<div class="error-code" style="color: var(--color-danger);">403</div>
<h1>Acceso Restringido</h1>
<p><?= e($message ?? 'No tienes permisos suficientes para acceder a este recurso. Contacta al administrador si crees que esto es un error.') ?></p>
<a href="<?= e(app_url('/login')) ?>" class="btn btn-secondary" style="margin: 0 auto;">
    <?= icon('arrow-left', 16) ?>
    <span>Volver al Inicio</span>
</a>
