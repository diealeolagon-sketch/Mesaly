<div style="max-width: 980px; margin: 0 auto;">
    <div style="margin-bottom: 24px;">
        <a href="<?= e(app_url('/admin/catalogo')) ?>" style="display: inline-flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 500; color: var(--color-text-muted); text-decoration: none; margin-bottom: 12px; transition: color 150ms var(--ease-fluid);">
            <?= icon('arrow-left', 14) ?>
            <span>Volver al catálogo</span>
        </a>
        <h1 class="page-title">
            <?= !empty($producto) ? 'Editar Plato o Bebida' : 'Nuevo Plato o Bebida' ?>
        </h1>
        <p class="page-subtitle">
            Completa la información del plato, sube una foto atractiva y asigna los modificadores que el cliente podrá elegir.
        </p>
    </div>

    <form method="POST" action="<?= e(app_url('/admin/catalogo/producto/guardar')) ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <input type="hidden" name="id" value="<?= (int)($producto['id'] ?? 0) ?>">

        <div style="display: grid; grid-template-columns: 320px 1fr; gap: 24px; align-items: start;" class="product-form-grid">
            
            <!-- COLUMNA IZQUIERDA: FOTO Y DISPONIBILIDAD -->
            <div style="display: flex; flex-direction: column; gap: 20px;">
                <div class="card">
                    <h2 style="font-size: 14px; font-weight: 700; color: var(--color-text-main); margin: 0 0 6px 0;">
                        Foto del Plato
                    </h2>
                    <p style="font-size: 12.5px; color: var(--color-text-muted); margin: 0 0 16px 0;">
                        Formato JPG, PNG o WebP. Máximo 3 MB.
                    </p>

                    <!-- Zona interactiva de subida con preview -->
                    <div class="dropzone" id="dropzone_container" onclick="document.getElementById('imagen_input').click()">
                        <input type="file" id="imagen_input" name="imagen" accept="image/jpeg,image/png,image/webp" onchange="previewImage(this)">

                        <div id="preview_wrapper" style="width: 100%;">
                            <?php if (!empty($producto['imagen_path'])): ?>
                                <img src="<?= e(app_url($producto['imagen_path'])) ?>" id="preview_img" class="dropzone-preview" alt="Foto actual">
                                <span style="display: inline-flex; align-items: center; justify-content: center; gap: 6px; font-size: 12px; font-weight: 600; color: var(--color-brand); margin-top: 10px; width: 100%;">
                                    <?= icon('upload', 14) ?> Cambiar foto
                                </span>
                            <?php else: ?>
                                <img src="" id="preview_img" class="dropzone-preview" alt="Vista previa" style="display: none;">
                                <div id="dropzone_placeholder">
                                    <div style="width: 44px; height: 44px; border-radius: 50%; background: var(--color-bg-subtle); display: inline-flex; align-items: center; justify-content: center; color: var(--color-text-subtle); margin-bottom: 10px;">
                                        <?= icon('upload', 20) ?>
                                    </div>
                                    <strong style="font-size: 13.5px; color: var(--color-text-main); display: block;">
                                        Haz clic para subir foto
                                    </strong>
                                    <span style="font-size: 12px; color: var(--color-text-subtle); display: block; margin-top: 2px;">
                                        o arrastra y suelta aquí
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Switches de estado -->
                    <div style="margin-top: 20px; padding-top: 16px; border-top: 1px solid var(--color-border-subtle); display: flex; flex-direction: column; gap: 14px;">
                        <label style="display: flex; align-items: flex-start; gap: 10px; cursor: pointer;">
                            <input type="checkbox" name="disponible" value="1" <?= (!isset($producto['disponible']) || !empty($producto['disponible'])) ? 'checked' : '' ?> style="width: 18px; height: 18px; margin-top: 2px; accent-color: var(--color-brand);">
                            <div>
                                <span style="font-size: 13.5px; font-weight: 600; color: var(--color-text-main); display: block;">Disponible para pedidos</span>
                                <span style="font-size: 12px; color: var(--color-text-muted);">Si lo desmarcas, se mostrará como "Agotado".</span>
                            </div>
                        </label>

                        <label style="display: flex; align-items: flex-start; gap: 10px; cursor: pointer;">
                            <input type="checkbox" name="destacado" value="1" <?= !empty($producto['destacado']) ? 'checked' : '' ?> style="width: 18px; height: 18px; margin-top: 2px; accent-color: var(--color-brand);">
                            <div>
                                <span style="font-size: 13.5px; font-weight: 600; color: var(--color-text-main); display: block;">Plato destacado</span>
                                <span style="font-size: 12px; color: var(--color-text-muted);">Tendrá distintivo visual y mayor prioridad.</span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- COLUMNA DERECHA: INFORMACIÓN DETALLADA Y MODIFICADORES -->
            <div style="display: flex; flex-direction: column; gap: 20px;">
                <div class="card">
                    <h2 style="font-size: 15px; font-weight: 700; color: var(--color-text-main); margin: 0 0 18px 0; border-bottom: 1px solid var(--color-border-subtle); padding-bottom: 12px;">
                        Información Principal
                    </h2>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label for="categoria_id" class="form-label">Sección a la que pertenece *</label>
                            <select id="categoria_id" name="categoria_id" class="form-control" required>
                                <?php foreach ($categorias as $cat): ?>
                                    <option value="<?= e($cat['id']) ?>" <?= (!empty($producto['categoria_id']) && $producto['categoria_id'] == $cat['id']) ? 'selected' : '' ?>>
                                        <?= e($cat['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="precio" class="form-label">Precio en Pesos (COP, sin decimales) *</label>
                            <div style="position: relative;">
                                <span style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); font-weight: 600; color: var(--color-text-subtle);">$</span>
                                <input type="number" id="precio" name="precio" class="form-control" value="<?= (int)($producto['precio'] ?? 0) ?>" min="0" step="100" placeholder="Ej: 38000" required style="padding-left: 28px; font-weight: 600; font-variant-numeric: tabular-nums;">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="nombre" class="form-label">Nombre del Plato o Bebida *</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" value="<?= e($producto['nombre'] ?? '') ?>" placeholder="Ej: Churrasco al Carbón 350g" required>
                    </div>

                    <div class="form-group">
                        <label for="descripcion" class="form-label">Descripción o Ingredientes</label>
                        <textarea id="descripcion" name="descripcion" class="form-control" rows="3" placeholder="Describe los ingredientes, guarniciones y preparación de forma apetitosa para el comensal."><?= e($producto['descripcion'] ?? '') ?></textarea>
                    </div>

                    <div class="form-group" style="max-width: 200px;">
                        <label for="orden" class="form-label">Orden numérico en la sección</label>
                        <input type="number" id="orden" name="orden" class="form-control" value="<?= (int)($producto['orden'] ?? 0) ?>" min="0" placeholder="0 = primero">
                    </div>

                    <!-- ASIGNACIÓN DE MODIFICADORES -->
                    <div style="margin-top: 28px; padding-top: 20px; border-top: 1px solid var(--color-border-subtle);">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; flex-wrap: wrap; gap: 8px;">
                            <div>
                                <h3 style="font-size: 14px; font-weight: 700; color: var(--color-text-main); margin: 0 0 2px 0;">
                                    Modificadores y Opciones Extra
                                </h3>
                                <p style="font-size: 12.5px; color: var(--color-text-muted); margin: 0;">
                                    Selecciona los grupos aplicables a este plato (ej: Término, salsas, adicionales).
                                </p>
                            </div>
                            <a href="<?= e(app_url('/admin/modificadores')) ?>" target="_blank" style="font-size: 12px; font-weight: 600; color: var(--color-brand); text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                                <span>Administrar grupos</span>
                                <?= icon('external-link', 12) ?>
                            </a>
                        </div>

                        <?php if (empty($grupos)): ?>
                            <div style="padding: 14px; background: var(--color-bg-subtle); border: 1px dashed var(--color-border); border-radius: var(--radius-md); font-size: 13px; color: var(--color-text-muted);">
                                Aún no has configurado grupos de modificadores. Puedes guardar el plato ahora y asociarlos después desde la sección <a href="<?= e(app_url('/admin/modificadores')) ?>" style="color: var(--color-brand); font-weight: 600;">Modificadores</a>.
                            </div>
                        <?php else: ?>
                            <div style="display: flex; flex-direction: column; gap: 8px; margin-top: 12px;">
                                <?php foreach ($grupos as $grupo): ?>
                                    <label style="display: flex; align-items: flex-start; gap: 12px; padding: 10px 14px; border: 1px solid var(--color-border-subtle); border-radius: var(--radius-md); background: var(--color-surface); cursor: pointer; transition: border-color 150ms var(--ease-fluid);">
                                        <input type="checkbox" name="grupos[]" value="<?= e($grupo['id']) ?>" <?= in_array($grupo['id'], $gruposSeleccionados) ? 'checked' : '' ?> style="margin-top: 3px; width: 16px; height: 16px; accent-color: var(--color-brand);">
                                        <div style="flex: 1;">
                                            <div style="display: flex; align-items: center; gap: 8px;">
                                                <span style="font-size: 13.5px; font-weight: 600; color: var(--color-text-main);"><?= e($grupo['nombre']) ?></span>
                                                <span class="badge badge-neutral" style="font-size: 11px;">
                                                    <?= $grupo['tipo'] === 'unica' ? 'Selección única' : 'Selección múltiple' ?>
                                                    <?= !empty($grupo['obligatorio']) ? '· Obligatorio' : '· Opcional' ?>
                                                </span>
                                            </div>
                                            <?php if (!empty($grupo['opciones'])): ?>
                                                <p style="font-size: 12px; color: var(--color-text-muted); margin: 3px 0 0 0;">
                                                    Opciones: <?= e(implode(', ', array_column($grupo['opciones'], 'nombre'))) ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 28px; padding-top: 16px; border-top: 1px solid var(--color-border-subtle);">
                        <a href="<?= e(app_url('/admin/catalogo')) ?>" class="btn btn-secondary">
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary" style="padding: 10px 24px;">
                            <?= !empty($producto) ? 'Guardar Cambios' : 'Guardar Plato' ?>
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>

<style nonce="<?= e($cspNonce) ?>">
@media (max-width: 820px) {
    .product-form-grid {
        grid-template-columns: 1fr !important;
    }
}
</style>

<script nonce="<?= e($cspNonce) ?>">
function previewImage(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        if (file.size > 3145728) {
            alert('La imagen seleccionada supera el límite máximo de 3 MB. Por favor elige una imagen más liviana.');
            input.value = '';
            return;
        }

        const previewImg = document.getElementById('preview_img');
        const placeholder = document.getElementById('dropzone_placeholder');

        previewImg.src = URL.createObjectURL(file);
        previewImg.style.display = 'block';
        if (placeholder) {
            placeholder.style.display = 'none';
        }
    }
}
</script>
