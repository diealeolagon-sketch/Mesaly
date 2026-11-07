<div style="max-width: 720px; margin: 0 auto;">
    <div style="margin-bottom: 24px;">
        <a href="<?= e(app_url('/superadmin/negocios')) ?>" class="btn btn-secondary btn-sm" style="margin-bottom: 16px;">
            <?= icon('arrow-left', 14) ?>
            <span>Volver al listado</span>
        </a>
        <h1 class="page-title">Editar Negocio: <?= e($negocio['nombre']) ?></h1>
        <p class="page-subtitle">Modifica los datos del restaurante. El slug afecta el QR si lo cambias.</p>
    </div>

    <div class="card">
        <form method="POST" action="<?= e(app_url('/superadmin/negocios/' . $negocio['id'] . '/editar')) ?>">
            <?= csrf_field() ?>

            <div class="form-group">
                <label for="nombre" class="form-label">Nombre del Negocio *</label>
                <input type="text" id="nombre" name="nombre" class="form-control" value="<?= e($negocio['nombre']) ?>" required>
            </div>

            <div class="form-group">
                <label for="slug" class="form-label">
                    Slug de la URL *
                    <span style="font-weight: 400; color: var(--color-danger); font-size: 12px;">¡Cuidado! Cambiarlo altera los códigos QR</span>
                </label>
                <div style="display: flex; align-items: center; gap: 0;">
                    <span style="padding: 9px 12px; background: var(--color-bg-subtle); border: 1px solid var(--color-border); border-right: none; border-radius: var(--radius-sm) 0 0 var(--radius-sm); font-size: 13px; color: var(--color-text-muted); white-space: nowrap;">mesaly.com/</span>
                    <input type="text" id="slug" name="slug" class="form-control" value="<?= e($negocio['slug']) ?>" required pattern="^[a-z0-9]+(-[a-z0-9]+)*$" style="border-radius: 0 var(--radius-sm) var(--radius-sm) 0;">
                </div>
            </div>

            <div class="form-group">
                <label for="whatsapp" class="form-label">Número de WhatsApp del Negocio *</label>
                <input type="tel" id="whatsapp" name="whatsapp" class="form-control" value="<?= e($negocio['whatsapp']) ?>" required>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label for="plan_id" class="form-label">Plan Asignado *</label>
                    <select id="plan_id" name="plan_id" class="form-control" required>
                        <?php foreach ($planes as $plan): ?>
                            <option value="<?= e($plan['id']) ?>" <?= $plan['id'] == $negocio['plan_id'] ? 'selected' : '' ?>>
                                <?= e($plan['nombre']) ?> (<?= format_cop($plan['precio_mensual']) ?>/mes)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="ciclo" class="form-label">Ciclo de Facturación *</label>
                    <select id="ciclo" name="ciclo" class="form-control" required>
                        <option value="mensual" <?= $negocio['ciclo'] === 'mensual' ? 'selected' : '' ?>>Mensual</option>
                        <option value="anual" <?= $negocio['ciclo'] === 'anual' ? 'selected' : '' ?>>Anual</option>
                    </select>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--color-border-subtle);">
                <a href="<?= e(app_url('/superadmin/negocios')) ?>" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <?= icon('check', 16) ?>
                    <span>Guardar Cambios</span>
                </button>
            </div>
        </form>
    </div>
</div>
