<div style="max-width: 1040px; margin: 0 auto;">
    <div style="margin-bottom: 24px;">
        <a href="<?= e(app_url('/admin/catalogo')) ?>" style="font-size: 13.5px; font-weight: 600; color: var(--brand-primary); text-decoration: none;">
            ← Volver al catálogo de platos
        </a>
        <h1 style="font-size: 26px; margin-top: 10px; color: var(--admin-text-main);">
            <?= !empty($producto) ? 'Editar Plato o Bebida' : 'Agregar Nuevo Plato al Menú' ?>
        </h1>
        <p style="color: var(--admin-text-muted); font-size: 14px;">
            Completa la información, sube una foto atractiva y asigna los modificadores para que tus clientes armen su pedido.
        </p>
    </div>

    <form method="POST" action="<?= e(app_url('/admin/catalogo/producto/guardar')) ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <input type="hidden" name="id" value="<?= (int)($producto['id'] ?? 0) ?>">

        <div style="display: grid; grid-template-columns: 340px 1fr; gap: 28px; align-items: start;" class="form-grid-pc">
            
            <!-- COLUMNA IZQUIERDA: FOTO DEL PRODUCTO Y DISPONIBILIDAD -->
            <div>
                <div class="modern-card">
                    <h3 style="font-size: 16px; margin-bottom: 8px;">📸 Foto del Plato</h3>
                    <p style="font-size: 13px; color: var(--admin-text-muted); margin-bottom: 16px;">
                        Los platos con foto reciben hasta un 70% más pedidos en los menús digitales.
                    </p>

                    <!-- Zona interactiva de subida con preview en vivo -->
                    <div class="image-dropzone" id="dropzone_container" onclick="document.getElementById('imagen_input').click()">
                        <input type="file" id="imagen_input" name="imagen" accept="image/jpeg,image/png,image/webp" onchange="previewImage(this)">

                        <div id="preview_wrapper">
                            <?php if (!empty($producto['imagen_path'])): ?>
                                <img src="<?= e(app_url($producto['imagen_path'])) ?>" id="preview_img" class="dropzone-preview" alt="Foto actual">
                                <span style="display: block; font-size: 12px; font-weight: 600; color: var(--brand-primary);">
                                    🔄 Haz clic para cambiar la foto
                                </span>
                            <?php else: ?>
                                <img src="" id="preview_img" class="dropzone-preview" alt="Vista previa" style="display: none;">
                                <div id="dropzone_placeholder">
                                    <span style="font-size: 38px; display: block; margin-bottom: 8px;">📷</span>
                                    <strong style="font-size: 14px; color: var(--admin-text-main); display: block;">
                                        Arrastra una foto o haz clic aquí
                                    </strong>
                                    <span style="font-size: 12px; color: var(--admin-text-subtle); display: block; margin-top: 4px;">
                                        JPG, PNG o WebP (máx. 3 MB)
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Switches de estado y visibilidad -->
                    <div style="margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--admin-border); display: grid; gap: 14px;">
                        <label style="display: flex; align-items: flex-start; gap: 10px; cursor: pointer;">
                            <input type="checkbox" name="disponible" value="1" <?= (!isset($producto['disponible']) || !empty($producto['disponible'])) ? 'checked' : '' ?> style="width: 20px; height: 20px; margin-top: 2px;">
                            <div>
                                <strong style="font-size: 14px; color: var(--admin-text-main); display: block;">Disponible para pedidos</strong>
                                <span style="font-size: 12px; color: var(--admin-text-muted);">Si lo desmarcas, el cliente verá la etiqueta "Agotado".</span>
                            </div>
                        </label>

                        <label style="display: flex; align-items: flex-start; gap: 10px; cursor: pointer;">
                            <input type="checkbox" name="destacado" value="1" <?= !empty($producto['destacado']) ? 'checked' : '' ?> style="width: 20px; height: 20px; margin-top: 2px;">
                            <div>
                                <strong style="font-size: 14px; color: var(--admin-text-main); display: block;">Plato Destacado (⭐)</strong>
                                <span style="font-size: 12px; color: var(--admin-text-muted);">Aparecerá en los primeros lugares y con insignia de recomendado.</span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- COLUMNA DERECHA: INFORMACIÓN DETALLADA Y MODIFICADORES -->
            <div>
                <div class="modern-card">
                    <h3 style="font-size: 17px; margin-bottom: 18px; border-bottom: 1px solid var(--admin-border); padding-bottom: 12px;">
                        Información Principal
                    </h3>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label for="categoria_id" class="form-label">Categoría a la que pertenece *</label>
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
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span style="font-size: 16px; font-weight: 800; color: var(--admin-text-main);">$</span>
                                <input type="number" id="precio" name="precio" class="form-control" value="<?= (int)($producto['precio'] ?? 0) ?>" min="0" step="100" placeholder="Ej: 38000" required style="font-size: 16px; font-weight: 700;">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="nombre" class="form-label">Nombre del Plato o Bebida *</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" value="<?= e($producto['nombre'] ?? '') ?>" placeholder="Ej: Churrasco al Carbón 350g" required style="font-size: 15px;">
                    </div>

                    <div class="form-group">
                        <label for="descripcion" class="form-label">Descripción, Acompañamientos o Ingredientes</label>
                        <textarea id="descripcion" name="descripcion" class="form-control" rows="3" placeholder="Describe los ingredientes, guarniciones y preparación de forma apetitosa para el cliente."><?= e($producto['descripcion'] ?? '') ?></textarea>
                    </div>

                    <div class="form-group" style="max-width: 220px;">
                        <label for="orden" class="form-label">Orden numérico en la categoría</label>
                        <input type="number" id="orden" name="orden" class="form-control" value="<?= (int)($producto['orden'] ?? 0) ?>" min="0" placeholder="0 = primero">
                    </div>

                    <!-- ASIGNACIÓN DE GRUPOS DE MODIFICADORES -->
                    <div style="margin-top: 32px; padding-top: 24px; border-top: 1px solid var(--admin-border);">
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                            <div>
                                <h4 style="font-size: 16px; color: var(--admin-text-main); margin-bottom: 4px;">
                                    Modificadores y Opciones Extra para este Plato
                                </h4>
                                <p style="font-size: 13px; color: var(--admin-text-muted); margin: 0;">
                                    Marca las opciones que el cliente podrá elegir (ej: Término de la carne, salsas, tocineta extra).
                                </p>
                            </div>
                            <a href="<?= e(app_url('/admin/modificadores')) ?>" target="_blank" style="font-size: 12.5px; font-weight: 600; color: var(--brand-primary); text-decoration: none;">
                                + Administrar Grupos ↗
                            </a>
                        </div>

                        <?php if (empty($grupos)): ?>
                            <div style="background: #FFFBF5; border: 1px solid #FED7AA; border-radius: var(--radius-md); padding: 14px; font-size: 13px; color: #9A3412;">
                                💡 Aún no has creado grupos de modificadores. Puedes crear platos ahora y luego asignarles modificadores desde la sección <a href="<?= e(app_url('/admin/modificadores')) ?>">Modificadores</a>.
                            </div>
                        <?php else: ?>
                            <div style="display: grid; gap: 10px; margin-top: 14px;">
                                <?php foreach ($grupos as $grupo): ?>
                                    <label style="display: flex; align-items: flex-start; gap: 12px; padding: 12px 16px; border: 1px solid var(--admin-border); border-radius: var(--radius-md); background: #FAFBFD; cursor: pointer; transition: border-color 0.15s ease;">
                                        <input type="checkbox" name="grupos[]" value="<?= e($grupo['id']) ?>" <?= in_array($grupo['id'], $gruposSeleccionados) ? 'checked' : '' ?> style="margin-top: 3px; width: 18px; height: 18px;">
                                        <div>
                                            <div style="display: flex; align-items: center; gap: 8px;">
                                                <strong style="font-size: 14.5px; color: var(--admin-text-main);"><?= e($grupo['nombre']) ?></strong>
                                                <span class="badge" style="background: var(--admin-border); font-size: 11px;">
                                                    <?= $grupo['tipo'] === 'unica' ? 'Selección única' : 'Selección múltiple' ?>
                                                    <?= !empty($grupo['obligatorio']) ? '· Obligatorio' : '· Opcional' ?>
                                                </span>
                                            </div>
                                            <?php if (!empty($grupo['opciones'])): ?>
                                                <p style="font-size: 12.5px; color: var(--admin-text-muted); margin: 4px 0 0 0;">
                                                    Opciones: <?= e(implode(', ', array_column($grupo['opciones'], 'nombre'))) ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 14px; margin-top: 32px;">
                        <a href="<?= e(app_url('/admin/catalogo')) ?>" class="btn-modern btn-modern-secondary">
                            Cancelar
                        </a>
                        <button type="submit" class="btn-modern btn-modern-primary" style="padding: 12px 28px;">
                            <?= !empty($producto) ? 'Guardar Cambios' : 'Guardar y Publicar Plato' ?>
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>

<style nonce="<?= e($cspNonce) ?>">
@media (max-width: 820px) {
    .form-grid-pc {
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
