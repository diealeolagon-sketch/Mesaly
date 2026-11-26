<?php
use App\Services\Factus\InvoiceDraft;
$heading = 'Configuración de facturación';
require __DIR__ . '/encabezado.php';
?>
<form method="post" action="<?= e(app_url('/admin/facturacion/configuracion')) ?>">
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
