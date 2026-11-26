<?php
use App\Services\Factus\InvoiceDraft;
$heading = 'Revisar factura del pedido ' . $order['codigo'];
$base = '/admin/facturacion/pedidos/' . (int)$order['id'];
require __DIR__ . '/encabezado.php';
?>
<?php require __DIR__ . '/importes.php'; ?>
<section class="card p-4">
    <h2>Comprador y pago</h2>
    <p><?= e($customer['company'] ?? $customer['names']) ?> · <?= e($customer['identification']) ?></p>
    <p><?= $payment['payment_form'] === '1' ? 'Contado' : 'Crédito' ?> · <?= e(InvoiceDraft::METHODS[$payment['payment_method_code']]) ?></p>
    <?php if (isset($payment['due_date'])): ?><p>Vencimiento <?= e($payment['due_date']) ?></p><?php endif; ?>
    <form method="post" action="<?= e(app_url($base . '/emitir')) ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="ticket" value="<?= e($ticket) ?>">
        <label class="form-group"><input type="checkbox" name="confirm" value="1" required> Confirmo la emisión de este documento de prueba en Factus Sandbox.</label>
        <div class="flex gap-2 flex-wrap">
            <a class="btn btn-secondary" href="<?= e(app_url($base)) ?>">Volver a editar</a>
            <button class="btn btn-primary" type="submit">Confirmar emisión</button>
        </div>
    </form>
</section>
