<?php
use App\Services\Factus\InvoiceDraft;
$heading = 'Configuración de facturación';
require __DIR__ . '/encabezado.php';
?>
<form id="billing-settings" method="post" action="<?= e(app_url('/admin/facturacion/configuracion')) ?>">
    <?= csrf_field() ?>
    <section class="card p-4 mb-4">
        <p class="text-muted">Restaurante <?= (int)$tenant['id'] ?> · <?= e($tenant['nombre']) ?></p>
        <div class="config-grid-2">
            <label class="form-group"><input type="checkbox" name="enabled" value="1" <?= !empty($settings['habilitada']) ? 'checked' : '' ?>> Habilitar emisión Sandbox</label>
            <label class="form-group">ID del rango <input class="form-control" name="range" inputmode="numeric" maxlength="18" value="<?= e((string)($settings['rango_id'] ?? '')) ?>"></label>
            <label class="form-group">Prefijo <input class="form-control" name="prefix" maxlength="16" value="<?= e($settings['prefijo'] ?? '') ?>"></label>
            <label class="form-group">NIT del emisor sin dígito de verificación <input class="form-control" name="nit" maxlength="20" inputmode="numeric" value="<?= e($settings['fiscal']['nit'] ?? '') ?>"></label>
            <label class="form-group">Razón social del emisor <input class="form-control" name="razon_social" maxlength="255" value="<?= e($settings['fiscal']['razon_social'] ?? '') ?>"></label>
        </div>
    </section>
    <section class="card p-4 mb-4">
        <h2>Credenciales Sandbox</h2>
        <?php if ($globalAllowed): ?>
            <label class="form-group"><input type="checkbox" name="global" value="1"> Cambiar a las credenciales globales autorizadas</label>
            <?php if (empty($settings['credenciales'])): ?><p class="text-muted">Sin credenciales propias se usará la cuenta Sandbox global autorizada para este restaurante.</p><?php endif; ?>
        <?php endif; ?>
        <?php if (!empty($settings['credenciales'])): ?><p class="text-muted">Este restaurante tiene credenciales propias guardadas.</p><?php endif; ?>
        <div class="config-grid-2">
            <?php foreach (['username' => 'Usuario', 'password' => 'Contraseña', 'client_id' => 'Client ID', 'client_secret' => 'Client Secret'] as $key => $label): ?>
                <label class="form-group"><?= e($label) ?><input class="form-control" type="password" name="<?= e($key) ?>" autocomplete="new-password" maxlength="2048"></label>
            <?php endforeach; ?>
        </div>
        <button type="button" id="billing-check-ranges" class="btn btn-secondary mt-3"
            data-url="<?= e(app_url('/admin/facturacion/configuracion/rangos')) ?>">Verificar conexión y consultar rangos</button>
        <p id="billing-range-status" class="text-muted mt-2" role="status"></p>
        <div id="billing-range-results" hidden>
            <label class="form-group">Rangos disponibles de factura de venta
                <select class="form-control" id="billing-range-select"></select>
            </label>
            <div class="config-grid-2">
                <label class="form-group">Resolución <input class="form-control" id="billing-range-resolution" readonly></label>
                <label class="form-group">Vigencia <input class="form-control" id="billing-range-validity" readonly></label>
            </div>
        </div>
    </section>
    <section class="card p-4 mb-4">
        <h2>Impuestos por producto</h2>
        <p class="text-muted">Los importes históricos incluyen el impuesto configurado. Configura también domicilio si el pedido incluye ese cobro.</p>
        <div class="table-container"><table class="table">
            <thead><tr><th>Producto o servicio</th><th>Impuesto</th><th>Tarifa (%)</th></tr></thead>
            <tbody>
            <?php foreach (array_merge($products, [['id' => 'envio', 'nombre' => 'Domicilio']]) as $product): ?>
                <?php $key = (string)$product['id']; $tax = $settings['impuestos'][$key] ?? []; ?>
                <tr>
                    <td><?= e($product['nombre']) ?><?= !empty($product['deleted_at']) ? ' (archivado)' : '' ?></td>
                    <td><select aria-label="Impuesto de <?= e($product['nombre']) ?>" class="form-control" name="taxes[<?= e($key) ?>][code]">
                        <option value="">Seleccionar</option>
                        <?php foreach (InvoiceDraft::TAXES as $code => $label): ?>
                            <option value="<?= e((string)$code) ?>" <?= ($tax['code'] ?? '') === (string)$code ? 'selected' : '' ?>><?= e($label) ?></option>
                        <?php endforeach; ?>
                    </select></td>
                    <td><input aria-label="Tarifa de <?= e($product['nombre']) ?>" class="form-control" name="taxes[<?= e($key) ?>][rate]" type="number" min="0" max="100" step="0.01" value="<?= e($tax['rate'] ?? '') ?>"></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table></div>
    </section>
    <input type="hidden" name="settings_complete" value="1">
    <button type="submit" class="btn btn-primary">Guardar configuración</button>
</form>
<script nonce="<?= e($cspNonce) ?>">
(() => {
    const form = document.getElementById('billing-settings');
    const button = document.getElementById('billing-check-ranges');
    const status = document.getElementById('billing-range-status');
    const results = document.getElementById('billing-range-results');
    const select = document.getElementById('billing-range-select');
    const resolution = document.getElementById('billing-range-resolution');
    const validity = document.getElementById('billing-range-validity');
    const rangeId = form.elements.namedItem('range');
    const prefix = form.elements.namedItem('prefix');
    const accountFields = ['username', 'password', 'client_id', 'client_secret', 'global'];
    let ranges = [];
    let request = null;

    select.addEventListener('change', () => {
        const range = select.value === '' ? null : ranges[Number(select.value)];
        resolution.value = range?.resolution_number ?? '';
        validity.value = range ? `${range.start_date} – ${range.end_date}` : '';
        if (range) {
            rangeId.value = range.id;
            prefix.value = range.prefix;
        }
    });

    function resetRanges() {
        ranges = [];
        results.hidden = true;
        select.replaceChildren(new Option('Seleccionar rango', ''));
        select.dispatchEvent(new Event('change'));
    }

    for (const name of accountFields) {
        form.elements.namedItem(name)?.addEventListener('input', () => {
            request?.abort();
            resetRanges();
            status.textContent = '';
        });
    }

    for (const input of [rangeId, prefix]) {
        input.addEventListener('input', () => {
            select.value = '';
            select.dispatchEvent(new Event('change'));
        });
    }

    button.addEventListener('click', async () => {
        const body = new FormData();
        for (const name of ['_csrf_token', ...accountFields]) {
            const field = form.elements.namedItem(name);
            if (field && (field.type !== 'checkbox' || field.checked)) {
                body.append(name, field.value);
            }
        }
        request = new AbortController();
        button.disabled = true;
        resetRanges();
        status.textContent = 'Consultando rangos en Factus Sandbox…';
        try {
            const response = await fetch(button.dataset.url, {
                method: 'POST', body, signal: request.signal,
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok || !Array.isArray(data.ranges)) {
                throw new Error(data.message || 'No se pudieron consultar los rangos. Verifica tu sesión e inténtalo de nuevo.');
            }
            if (request.signal.aborted) return;
            ranges = data.ranges;
            if (ranges.length === 0) {
                status.textContent = 'Conexión verificada. La cuenta no tiene rangos vigentes de factura de venta.';
                return;
            }
            ranges.forEach((range, index) => select.add(new Option(`${range.prefix} · ID ${range.id}`, String(index))));
            const current = ranges.findIndex(range => String(range.id) === rangeId.value && range.prefix === prefix.value);
            select.value = current < 0 ? '' : String(current);
            select.dispatchEvent(new Event('change'));
            results.hidden = false;
            status.textContent = 'Conexión verificada. Selecciona un rango y guarda la configuración.';
        } catch (error) {
            if (!request.signal.aborted) status.textContent = error.message;
        } finally {
            request = null;
            button.disabled = false;
        }
    });
})();
</script>
