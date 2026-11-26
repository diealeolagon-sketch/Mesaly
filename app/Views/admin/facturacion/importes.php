<section class="card p-4 mb-4">
    <h2>Productos e importes (COP)</h2>
    <div class="table-container"><table class="table">
        <thead><tr><th>Código</th><th>Producto</th><th>Cantidad</th><th>Unitario</th><th>Descuento</th><th>Base</th><th>Impuesto</th><th>Subtotal</th></tr></thead>
        <tbody>
        <?php foreach ($preview['lines'] as $line): ?>
            <tr>
                <td><?= e($line['code']) ?></td><td><?= e($line['name']) ?></td><td><?= (int)$line['quantity'] ?></td>
                <td><?= e($line['unit']) ?></td><td><?= e($line['discount']) ?></td><td><?= e($line['base']) ?></td>
                <td><?= e($line['tax_label'] . ' · ' . $line['tax']) ?></td><td><?= e($line['total']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table></div>
    <div class="flex gap-3 flex-wrap">
        <span>Subtotal antes de impuestos <?= e($preview['net']) ?></span><span>Impuestos <?= e($preview['tax']) ?></span>
        <strong>Total <?= e($preview['total']) ?> COP</strong>
    </div>
</section>
