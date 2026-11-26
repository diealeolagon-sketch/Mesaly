<?php
use App\Models\FacturaElectronica;
$heading = 'Facturación electrónica';
require __DIR__ . '/encabezado.php';
?>
<div class="kpi-grid gap-3 mb-4">
    <?php foreach (FacturaElectronica::STATES as $key => $label): ?>
        <a href="<?= e(app_url('/admin/facturacion?' . http_build_query(array_replace($filters, ['estado' => $key])))) ?>" class="kpi-card-modern">
            <span class="text-muted"><?= e($label) ?></span>
            <strong class="text-xl"><?= (int)($metrics[$key]['cantidad'] ?? 0) ?></strong>
            <?php if ($key === 'validada'): ?>
                <span><?= e('COP ' . ($metrics[$key]['total'] ?? '0.00')) ?></span>
            <?php endif; ?>
        </a>
    <?php endforeach; ?>
</div>
<section class="card p-4 mb-4">
    <form method="get" action="<?= e(app_url('/admin/facturacion')) ?>" class="flex gap-2 items-end flex-wrap">
        <label>Pedido, factura o cliente <input class="form-control" name="q" value="<?= e($filters['q']) ?>" maxlength="100"></label>
        <label>Estado <select class="form-control" name="estado">
            <option value="">Todos</option>
            <?php foreach (FacturaElectronica::STATES as $key => $label): ?>
                <option value="<?= e($key) ?>" <?= $filters['estado'] === $key ? 'selected' : '' ?>><?= e($label) ?></option>
            <?php endforeach; ?>
        </select></label>
        <label>Desde <input class="form-control" type="date" name="desde" value="<?= e($filters['desde']) ?>"></label>
        <label>Hasta <input class="form-control" type="date" name="hasta" value="<?= e($filters['hasta']) ?>"></label>
        <button type="submit" class="btn btn-primary">Filtrar</button>
    </form>
</section>
<div class="card table-container">
    <table class="table">
        <thead><tr><th>Pedido</th><th>Factura</th><th>Cliente</th><th>Fecha</th><th>Estado</th><th>Total COP</th><th>Acción</th></tr></thead>
        <tbody>
        <?php foreach ($rows as $row): ?>
            <tr>
                <td><?= e($row['codigo']) ?></td><td><?= e($row['numero'] ?? '—') ?></td>
                <td><?= e($row['comprador'] ?? '—') ?></td><td><?= e($row['fecha']) ?></td>
                <td><?= e(FacturaElectronica::STATES[$row['estado']]) ?></td><td><?= e((string)$row['total']) ?></td>
                <td><a class="btn btn-secondary btn-sm" href="<?= e(app_url('/admin/facturacion/pedidos/' . $row['id'])) ?>">
                    <?= $row['estado'] === 'sin_factura' ? 'Generar factura electrónica' : 'Ver detalle' ?>
                </a></td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$rows): ?><tr><td colspan="7">No hay resultados para estos filtros.</td></tr><?php endif; ?>
        </tbody>
    </table>
</div>
<nav class="flex gap-2 items-center mt-3" aria-label="Páginas">
    <?php if ($page > 1): ?><a class="btn btn-secondary btn-sm" href="<?= e(app_url('/admin/facturacion?' . http_build_query($filters + ['page' => $page - 1]))) ?>">Anterior</a><?php endif; ?>
    <span>Página <?= (int)$page ?> de <?= (int)$pages ?></span>
    <?php if ($page < $pages): ?><a class="btn btn-secondary btn-sm" href="<?= e(app_url('/admin/facturacion?' . http_build_query($filters + ['page' => $page + 1]))) ?>">Siguiente</a><?php endif; ?>
</nav>
