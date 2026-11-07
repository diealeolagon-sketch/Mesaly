<div style="max-width: 720px; margin: 0 auto;">
    <div style="margin-bottom: 20px;">
        <a href="<?= e(app_url('/superadmin/negocios')) ?>" style="font-size: 13px;">← Volver al listado</a>
        <h2 style="margin-top: 8px;">Editar Negocio: <?= e($negocio['nombre']) ?></h2>
    </div>

    <div class="card">
        <form method="POST" action="<?= e(app_url('/superadmin/negocios/' . $negocio['id'] . '/editar')) ?>">
            <?= csrf_field() ?>

            <div class="form-group">
                <label for="nombre" class="form-label">Nombre del Negocio *</label>
                <input type="text" id="nombre" name="nombre" class="form-control" value="<?= e($negocio['nombre']) ?>" required>
            </div>

            <div class="form-group">
                <label for="slug" class="form-label">Slug de la URL * (¡Cuidado! Cambiarlo altera los códigos QR)</label>
                <div style="display: flex; align-items: center; gap: 6px;">
                    <span style="color: var(--brand-gray-muted); font-size: 13px;">mesaly.com/</span>
                    <input type="text" id="slug" name="slug" class="form-control" value="<?= e($negocio['slug']) ?>" required pattern="^[a-z0-9]+(-[a-z0-9]+)*$">
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

            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px;">
                <a href="<?= e(app_url('/superadmin/negocios')) ?>" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>
