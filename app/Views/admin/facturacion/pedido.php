<?php
use App\Models\FacturaElectronica;
use App\Services\Factus\InvoiceDraft;
$heading = 'Facturación del pedido ' . $order['codigo'];
$base = '/admin/facturacion/pedidos/' . (int)$order['id'];
require __DIR__ . '/encabezado.php';
?>
<?php if ($invoice !== null): ?>
    <?php $preview = $invoice['snapshot']['preview']; ?>
    <section class="card p-4 mb-4">
        <h2><?= e(FacturaElectronica::STATES[$invoice['estado']]) ?></h2>
        <dl>
            <dt>Referencia</dt><dd><?= e($invoice['referencia']) ?></dd>
            <dt>Comprador</dt><dd><?= e($invoice['comprador']) ?></dd>
            <?php if ($invoice['numero']): ?><dt>Factura</dt><dd><?= e($invoice['numero']) ?></dd><?php endif; ?>
            <?php if ($invoice['emitida_at']): ?><dt>Emisión registrada</dt><dd><?= e($invoice['emitida_at']) ?></dd><?php endif; ?>
            <?php if ($invoice['validada_at']): ?><dt>Validación registrada</dt><dd><?= e($invoice['validada_at']) ?></dd><?php endif; ?>
            <?php if ($invoice['cufe']): ?><dt>CUFE</dt><dd><wbr><?= implode('<wbr>', array_map('e', str_split($invoice['cufe'], 24))) ?></dd><?php endif; ?>
        </dl>
        <?php if ($invoice['error']): ?><p role="alert"><?= e($invoice['error']) ?></p><?php endif; ?>
        <div class="flex gap-2 flex-wrap">
            <?php if ($invoice['estado'] === 'validada'): ?>
                <a class="btn btn-secondary" href="<?= e(app_url($base . '/documentos/pdf')) ?>">Descargar PDF</a>
                <a class="btn btn-secondary" href="<?= e(app_url($base . '/documentos/xml')) ?>">Descargar XML</a>
                <a class="btn btn-secondary" target="_blank" rel="noopener" href="<?= e(app_url($base . '/documentos/pdf?imprimir=1')) ?>"><?= icon('printer', 16) ?> Imprimir factura</a>
            <?php endif; ?>
            <form method="post" action="<?= e(app_url($base . '/estado')) ?>">
                <?= csrf_field() ?><button class="btn btn-secondary" type="submit">Consultar estado en Factus</button>
            </form>
        </div>
        <?php if ($invoice['estado'] === 'incierta' && $invoice['numero'] === null && $invoice['puede_revisar']): ?>
            <form method="post" class="mt-3" action="<?= e(app_url($base . '/reintentar')) ?>">
                <?= csrf_field() ?>
                <label class="form-group"><input type="checkbox" name="confirm" value="1" required> Confirmo consultar en Factus y reintentar con la misma referencia si sigue sin aparecer.</label>
                <button class="btn btn-secondary" type="submit">Consultar y reintentar</button>
            </form>
        <?php endif; ?>
    </section>
    <?php require __DIR__ . '/importes.php'; ?>
    <section class="card p-4">
        <h2>Historial de operaciones</h2>
        <div class="table-container"><table class="table">
            <thead><tr><th>Fecha</th><th>Operación</th><th>Usuario</th><th>Detalle</th></tr></thead>
            <tbody><?php foreach ($events as $event): ?>
                <tr><td><?= e($event['created_at']) ?></td><td><?= e(FacturaElectronica::STATES[$event['accion']] ?? str_replace('_', ' ', $event['accion'])) ?></td>
                    <td><?= (int)$event['usuario_id'] ?></td><td><?= e($event['detalle'] ?? '—') ?></td></tr>
            <?php endforeach; ?></tbody>
        </table></div>
    </section>
<?php elseif ($preview === null): ?>
    <section class="card p-4" role="alert"><?= e($blocked) ?></section>
<?php else: ?>
    <?php require __DIR__ . '/importes.php'; ?>
    <form method="post" action="<?= e(app_url($base . '/revisar')) ?>" class="card p-4" id="invoice-buyer-form">
        <?= csrf_field() ?>
        <div class="config-grid-2">
            <label class="form-group">Comprador <select class="form-control" name="buyer_type">
                <option value="final">Consumidor final</option><option value="identified">Cliente identificado</option>
            </select></label>
            <label class="form-group">Forma de pago <select class="form-control" name="payment_form">
                <option value="1">Contado</option><option value="2">Crédito</option>
            </select></label>
            <?php $suggested = match ($order['metodo_pago'] ?? '') {
                'efectivo' => '10', 'transferencia', 'nequi', 'daviplata' => '47', default => '',
            }; ?>
            <label class="form-group">Medio de pago <select class="form-control" name="payment_method" required>
                <option value="">Seleccionar</option>
                <?php foreach (InvoiceDraft::METHODS as $code => $label): ?>
                    <option value="<?= e((string)$code) ?>" <?= $suggested === (string)$code ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select></label>
            <label class="form-group is-hidden" id="invoice-credit-date">Vencimiento del pago <input class="form-control" type="date" name="due_date" min="<?= e(date('Y-m-d')) ?>"></label>
        </div>
        <fieldset id="identified-invoice-buyer" hidden>
            <legend>Datos del cliente identificado</legend>
            <div class="config-grid-2">
                <label class="form-group">Tipo de identificación <select class="form-control" name="document" data-buyer-required>
                    <option value="">Seleccionar</option>
                    <?php foreach (InvoiceDraft::DOCUMENTS as $code => $label): ?><option value="<?= e((string)$code) ?>"><?= e($label) ?></option><?php endforeach; ?>
                </select></label>
                <label class="form-group">Identificación sin dígito de verificación si es NIT <input class="form-control" name="identification" maxlength="30" data-buyer-required></label>
                <label class="form-group">Tipo de organización <select class="form-control" name="organization" data-buyer-required>
                    <option value="">Seleccionar</option>
                    <?php foreach (InvoiceDraft::ORGANIZATIONS as $code => $label): ?><option value="<?= e((string)$code) ?>"><?= e($label) ?></option><?php endforeach; ?>
                </select></label>
                <label class="form-group">Nombre o razón social <input class="form-control" name="name" maxlength="255" data-buyer-required></label>
            </div>
            <p>Responsabilidades fiscales, si corresponde</p>
            <div class="flex gap-3 flex-wrap">
                <?php foreach (InvoiceDraft::RESPONSIBILITIES as $code => $label): ?>
                    <label><input type="checkbox" name="responsibilities[]" value="<?= e($code) ?>"> <?= e($label) ?></label>
                <?php endforeach; ?>
            </div>
        </fieldset>
        <div class="mt-3"><button class="btn btn-primary" type="submit">Revisar factura</button></div>
    </form>
    <script nonce="<?= e($cspNonce) ?>">
        const billingForm = document.getElementById('invoice-buyer-form');
        const buyerFields = document.getElementById('identified-invoice-buyer');
        const creditDate = document.getElementById('invoice-credit-date');
        function updateInvoiceFields() {
            const identified = billingForm.elements.buyer_type.value === 'identified';
            buyerFields.hidden = !identified;
            buyerFields.disabled = !identified;
            buyerFields.querySelectorAll('[data-buyer-required]').forEach(field => field.required = identified);
            const credit = billingForm.elements.payment_form.value === '2';
            creditDate.classList.toggle('is-hidden', !credit);
            billingForm.elements.due_date.required = credit;
        }
        billingForm.addEventListener('change', updateInvoiceFields);
        updateInvoiceFields();
    </script>
<?php endif; ?>
