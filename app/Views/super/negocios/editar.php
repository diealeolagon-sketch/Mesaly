<div style="max-width: 760px; margin: 0 auto;">
    <div style="margin-bottom: 32px;">
        <a href="<?= e(app_url('/superadmin/negocios')) ?>" style="display: inline-flex; align-items: center; gap: 8px; font-size: 13.5px; font-weight: 600; color: var(--color-text-muted); text-decoration: none; margin-bottom: 14px; transition: color 150ms var(--ease-fluid);">
            <?= icon('arrow-left', 16) ?>
            <span>Volver a la lista de negocios</span>
        </a>
        <h1 class="page-title" style="font-size: 24px; margin-bottom: 6px;">
            Editar Negocio: <?= e($negocio['nombre']) ?>
        </h1>
        <p class="page-subtitle" style="font-size: 14px; color: var(--color-text-muted); margin: 0;">
            Modifica la configuración de suscripción, canales de contacto y datos principales del restaurante.
        </p>
    </div>

    <div class="card" style="padding: 32px;">
        <form method="POST" action="<?= e(app_url('/superadmin/negocios/' . $negocio['id'] . '/editar')) ?>">
            <?= csrf_field() ?>

            <div class="form-group">
                <label for="nombre" class="form-label">Nombre Comercial del Negocio *</label>
                <input type="text" id="nombre" name="nombre" class="form-control" value="<?= e($negocio['nombre']) ?>" required autofocus>
                <span class="form-hint">Nombre que se muestra en la cabecera del menú digital.</span>
            </div>

            <div class="form-group">
                <label for="slug" class="form-label">
                    Slug de la URL *
                    <span style="font-weight: 700; color: var(--color-danger); font-size: 12px; margin-left: 6px; display: inline-flex; align-items: center; gap: 4px;"><?= icon('alert', 13) ?> ¡Cuidado! Cambiarlo alterará los códigos QR ya impresos</span>
                </label>
                <div class="input-prefix-wrap">
                    <span class="input-prefix">mesaly.com/</span>
                    <input type="text" id="slug" name="slug" class="form-control" value="<?= e($negocio['slug']) ?>" required pattern="^[a-z0-9]+(-[a-z0-9]+)*$" style="border-radius: 0 var(--radius-md) var(--radius-md) 0; font-weight: 600;">
                </div>
                <span class="form-hint">Dirección web pública del menú digital.</span>
            </div>

            <div class="form-group">
                <label for="whatsapp" class="form-label">WhatsApp Oficial Receptor de Pedidos *</label>
                <div class="input-prefix-wrap">
                    <span class="input-prefix">+</span>
                    <input type="tel" id="whatsapp" name="whatsapp" class="form-control" value="<?= e($negocio['whatsapp']) ?>" required style="border-radius: 0 var(--radius-md) var(--radius-md) 0; font-weight: 600; font-variant-numeric: tabular-nums;">
                </div>
                <span class="form-hint">Con código de país sin símbolos (ej: 573001234567).</span>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;" class="config-grid-2">
                <div class="form-group">
                    <label for="plan_id" class="form-label">Plan Asignado *</label>
                    <select id="plan_id" name="plan_id" class="form-control" required>
                        <?php foreach ($planes as $plan): ?>
                            <option value="<?= e($plan['id']) ?>" <?= $plan['id'] == $negocio['plan_id'] ? 'selected' : '' ?>>
                                <?= e($plan['nombre']) ?> (<?= format_cop($plan['precio_mensual']) ?>/mes)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <span class="form-hint">Límites y funcionalidades del restaurante.</span>
                </div>

                <div class="form-group">
                    <label for="ciclo" class="form-label">Ciclo de Facturación *</label>
                    <select id="ciclo" name="ciclo" class="form-control" required>
                        <option value="mensual" <?= $negocio['ciclo'] === 'mensual' ? 'selected' : '' ?>>Mensual</option>
                        <option value="anual" <?= $negocio['ciclo'] === 'anual' ? 'selected' : '' ?>>Anual</option>
                    </select>
                    <span class="form-hint">Frecuencia de corte y renovación.</span>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 14px; margin-top: 36px; padding-top: 24px; border-top: 1px solid var(--color-border-subtle);">
                <a href="<?= e(app_url('/superadmin/negocios')) ?>" class="btn btn-secondary" style="padding: 11px 24px;">
                    Cancelar
                </a>
                <button type="submit" class="btn btn-primary" style="padding: 11px 32px; font-size: 14px; font-weight: 700;">
                    <?= icon('check', 16) ?>
                    <span>Guardar Cambios</span>
                </button>
            </div>
        </form>
    </div>
</div>
