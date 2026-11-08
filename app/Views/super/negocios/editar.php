<div style="max-width: 760px; margin: 0 auto;">
    <div style="margin-bottom: 32px;">
        <a href="<?= e(app_url('/superadmin')) ?>" style="display: inline-flex; align-items: center; gap: 8px; font-size: 13.5px; font-weight: 600; color: var(--color-text-muted); text-decoration: none; margin-bottom: 14px; transition: color 150ms var(--ease-fluid);">
            <?= icon('arrow-left', 16) ?>
            <span>Volver a Vencimientos</span>
        </a>
        <h1 class="page-title" style="font-size: 24px; margin-bottom: 6px;">
            Editar Negocio: <?= e($negocio['nombre']) ?>
        </h1>
        <p class="page-subtitle" style="font-size: 14px; color: var(--color-text-muted); margin: 0;">
            Modifica los datos comerciales, precio pactado y parámetros de vencimiento.
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

            <!-- PRECIO ACORDADO Y CICLO -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;" class="config-grid-2">
                <div class="form-group">
                    <label for="precio" class="form-label">Precio Acordado (COP) *</label>
                    <input type="number" id="precio" name="precio" class="form-control" value="<?= (int)($negocio['precio'] ?? 49000) ?>" min="10000" step="1000" required style="font-size: 15px; font-weight: 700;">
                    <span class="form-hint">Tarifa que paga el negocio por período.</span>
                </div>

                <div class="form-group">
                    <label for="ciclo" class="form-label">Ciclo de Facturación *</label>
                    <select id="ciclo" name="ciclo" class="form-control" required>
                        <option value="mensual" <?= ($negocio['ciclo'] ?? 'mensual') === 'mensual' ? 'selected' : '' ?>>Mensual</option>
                        <option value="anual" <?= ($negocio['ciclo'] ?? '') === 'anual' ? 'selected' : '' ?>>Anual</option>
                    </select>
                    <span class="form-hint">Frecuencia de renovación pactada.</span>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;" class="config-grid-2">
                <div class="form-group">
                    <label for="fecha_vencimiento" class="form-label">Fecha de Vencimiento Actual *</label>
                    <input type="date" id="fecha_vencimiento" name="fecha_vencimiento" class="form-control" value="<?= e($negocio['fecha_vencimiento'] ?? date('Y-m-d')) ?>" required>
                    <span class="form-hint">Fecha en la que expira la suscripción actual.</span>
                </div>

                <div class="form-group">
                    <label for="dias_gracia" class="form-label">Días de Gracia</label>
                    <input type="number" id="dias_gracia" name="dias_gracia" class="form-control" value="<?= (int)($negocio['dias_gracia'] ?? 3) ?>" min="1" max="30" required>
                    <span class="form-hint">Margen permitido tras el vencimiento antes del bloqueo.</span>
                </div>
            </div>

            <div class="form-group">
                <label for="notas_internas" class="form-label">Notas Internas (Solo Super Admin)</label>
                <textarea id="notas_internas" name="notas_internas" class="form-control" rows="2" placeholder="Notas sobre acuerdos comerciales, contacto o cobranzas..."><?= e($negocio['notas_internas'] ?? '') ?></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 14px; margin-top: 36px; padding-top: 24px; border-top: 1px solid var(--color-border-subtle);">
                <a href="<?= e(app_url('/superadmin')) ?>" class="btn btn-secondary" style="padding: 11px 24px;">
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
