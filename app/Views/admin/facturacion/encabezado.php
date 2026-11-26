<div class="content-header mb-4">
    <div>
        <h1 class="page-title text-2xl mb-1"><?= e($heading) ?></h1>
        <p class="text-muted">Factus Sandbox · Documentos de prueba</p>
    </div>
    <div class="flex items-center gap-2 flex-wrap">
        <a class="btn btn-secondary btn-sm" href="<?= e(app_url('/admin/facturacion')) ?>"><?= icon('receipt', 16) ?> Historial</a>
        <a class="btn btn-secondary btn-sm" href="<?= e(app_url('/admin/facturacion/configuracion')) ?>"><?= icon('settings', 16) ?> Configuración</a>
    </div>
</div>
