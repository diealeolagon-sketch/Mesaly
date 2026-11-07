<div class="page-header">
    <div>
        <h1 class="page-title">Panel de Super Admin</h1>
        <p class="page-subtitle">Consola central de Mesaly — control de negocios y vencimientos.</p>
    </div>
</div>

<div class="card">
    <div class="ui-panel-header">
        <h2 class="ui-panel-title">Estado del Sistema</h2>
        <span class="badge-pill success">
            <span style="width: 7px; height: 7px; background: #059669; border-radius: 50%; display: inline-block;"></span>
            Operativo
        </span>
    </div>
    <p style="font-size: 13.5px; color: var(--color-text-muted); margin-bottom: 16px;">
        Sesión activa para <strong style="color: var(--color-text-main);"><?= e($user['email'] ?? '') ?></strong> con rol <strong style="color: var(--color-brand);"><?= e($user['rol'] ?? '') ?></strong>.
    </p>
    <a href="<?= e(app_url('/superadmin/negocios')) ?>" class="btn btn-primary">
        <?= icon('store', 16) ?>
        <span>Ver Negocios Registrados</span>
    </a>
</div>
