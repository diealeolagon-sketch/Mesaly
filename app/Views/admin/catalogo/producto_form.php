<div style="max-width: 1040px; margin: 0 auto;">
    <div style="margin-bottom: 32px;">
        <a href="<?= e(app_url('/admin/catalogo')) ?>" style="display: inline-flex; align-items: center; gap: 8px; font-size: 13.5px; font-weight: 600; color: var(--color-text-muted); text-decoration: none; margin-bottom: 14px; transition: color 150ms var(--ease-fluid);">
            <?= icon('arrow-left', 16) ?>
            <span>Volver al catálogo</span>
        </a>
        <h1 class="page-title" style="font-size: 24px; margin-bottom: 6px;">
            <?= !empty($producto) ? 'Editar Plato o Bebida' : 'Nuevo Plato o Bebida' ?>
        </h1>
        <p class="page-subtitle" style="font-size: 14px; color: var(--color-text-muted); margin: 0;">
            Completa la información del plato, sube una fotografía atractiva y asigna modificadores opcionales u obligatorios.
        </p>
    </div>

    <form method="POST" action="<?= e(app_url('/admin/catalogo/producto/guardar')) ?>" enctype="multipart/form-data" id="producto_form">
        <?= csrf_field() ?>
        <input type="hidden" name="id" value="<?= (int)($producto['id'] ?? 0) ?>">

        <div style="display: grid; grid-template-columns: 360px 1fr; gap: 32px; align-items: start;" class="product-form-grid">
            
            <!-- ═══ COLUMNA IZQUIERDA: FOTO Y ESTADO ═══ -->
            <div style="display: flex; flex-direction: column; gap: 24px;">
                <div class="card" style="padding: 24px;">
                    <div style="margin-bottom: 16px;">
                        <h2 style="font-size: 15px; font-weight: 700; color: var(--color-text-main); margin: 0 0 4px 0;">
                            Fotografía del Plato
                        </h2>
                        <p style="font-size: 12.5px; color: var(--color-text-muted); margin: 0;">
                            Formatos JPG, PNG o WebP. Tamaño sugerido cuadrado o 4:3 (máx. 8 MB).
                        </p>
                    </div>

                    <!-- Input de archivo (oculto y desacoplado para evitar conflictos de eventos) -->
                    <input type="file" id="imagen_input" name="imagen" accept="image/jpeg,image/png,image/webp" style="display: none;">

                    <!-- Dropzone clickeable, arrastrable y accesible -->
                    <div class="dropzone" id="dropzone_container" data-input="imagen_input" tabindex="0" role="button" aria-label="Seleccionar fotografía del plato">
                        <div id="preview_wrapper" style="width: 100%;">
                            <?php $hasImage = !empty($producto['imagen_path']); ?>
                            <img src="<?= $hasImage ? e(app_url($producto['imagen_path'])) : '' ?>" 
                                 id="preview_img" 
                                 class="dropzone-preview" 
                                 alt="Foto del plato" 
                                 style="<?= $hasImage ? 'display: block;' : 'display: none;' ?>">

                            <div class="dropzone-placeholder" id="dropzone_placeholder" style="<?= $hasImage ? 'display: none;' : '' ?>">
                                <div class="dz-icon">
                                    <?= icon('upload', 22) ?>
                                </div>
                                <strong class="dz-label">
                                    Selecciona o arrastra una foto aquí
                                </strong>
                                <span class="dz-hint" style="margin-bottom: 12px;">
                                    Haz clic en cualquier parte de este recuadro
                                </span>
                                <span class="btn btn-secondary btn-sm" style="pointer-events: none;">
                                    <?= icon('image', 14) ?>
                                    <span>Buscar en tu equipo</span>
                                </span>
                            </div>

                            <div class="dropzone-change-btn" id="dropzone_change_btn" style="margin-top: 14px; <?= $hasImage ? '' : 'display: none;' ?>">
                                <span class="btn btn-secondary btn-sm" style="pointer-events: none;">
                                    <?= icon('upload', 14) ?>
                                    <span>Cambiar fotografía</span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Switches de estado y visibilidad -->
                    <div style="margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--color-border-subtle); display: flex; flex-direction: column; gap: 14px;">
                        <label class="toggle-option-card">
                            <input type="checkbox" name="disponible" value="1" <?= (!isset($producto['disponible']) || !empty($producto['disponible'])) ? 'checked' : '' ?> style="width: 18px; height: 18px; margin-top: 2px; accent-color: var(--color-brand); flex-shrink: 0;">
                            <div>
                                <span class="toggle-option-title">Disponible para pedidos</span>
                                <span class="toggle-option-desc">Si lo desmarcas, se mostrará con aviso de "Agotado" en la carta.</span>
                            </div>
                        </label>

                        <label class="toggle-option-card">
                            <input type="checkbox" name="destacado" value="1" <?= !empty($producto['destacado']) ? 'checked' : '' ?> style="width: 18px; height: 18px; margin-top: 2px; accent-color: var(--color-brand); flex-shrink: 0;">
                            <div>
                                <span class="toggle-option-title">Plato destacado ⭐</span>
                                <span class="toggle-option-desc">Aparecerá en la sección superior de recomendados de la carta.</span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- ═══ COLUMNA DERECHA: INFORMACIÓN DETALLADA Y MODIFICADORES ═══ -->
            <div style="display: flex; flex-direction: column; gap: 24px;">
                <div class="card" style="padding: 28px;">
                    <div style="margin-bottom: 24px; padding-bottom: 14px; border-bottom: 1px solid var(--color-border-subtle);">
                        <h2 style="font-size: 16px; font-weight: 700; color: var(--color-text-main); margin: 0 0 4px 0;">
                            Información Principal
                        </h2>
                        <p style="font-size: 13px; color: var(--color-text-muted); margin: 0;">
                            Define el nombre, categoría, precio y descripción que verá el cliente.
                        </p>
                    </div>

                    <div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 20px; margin-bottom: 4px;" class="config-grid-2">
                        <div class="form-group">
                            <label for="categoria_id" class="form-label">Sección o Categoría del Menú *</label>
                            <select id="categoria_id" name="categoria_id" class="form-control" required>
                                <?php foreach ($categorias as $cat): ?>
                                    <option value="<?= e($cat['id']) ?>" <?= (!empty($producto['categoria_id']) && $producto['categoria_id'] == $cat['id']) ? 'selected' : '' ?>>
                                        <?= e($cat['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <span class="form-hint">Pestaña del menú digital donde aparecerá el plato.</span>
                        </div>

                        <div class="form-group">
                            <label for="precio" class="form-label">Precio en Pesos Colombianos (COP) *</label>
                            <div style="position: relative;">
                                <span style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); font-weight: 700; color: var(--color-text-muted); font-size: 14px;">$</span>
                                <input type="number" id="precio" name="precio" class="form-control" value="<?= (int)($producto['precio'] ?? 0) ?>" min="0" step="100" placeholder="Ej: 38000" required style="padding-left: 32px; font-weight: 700; font-variant-numeric: tabular-nums; font-size: 15px;">
                            </div>
                            <span class="form-hint">Sin puntos ni decimales (ej: 38000).</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="nombre" class="form-label">Nombre del Plato o Bebida *</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" value="<?= e($producto['nombre'] ?? '') ?>" placeholder="Ej: Hamburguesa Artesanal Doble Carne 300g" required style="font-weight: 600;">
                        <span class="form-hint">Sé claro y apetecible. Este es el título principal de la tarjeta.</span>
                    </div>

                    <div class="form-group">
                        <label for="descripcion" class="form-label">Descripción o Ingredientes</label>
                        <textarea id="descripcion" name="descripcion" class="form-control" rows="3" placeholder="Describe los ingredientes, guarniciones y preparación de forma apetitosa para el comensal."><?= e($producto['descripcion'] ?? '') ?></textarea>
                        <span class="form-hint">Ej: Doble carne 100% res madurada, queso cheddar fundido, tocineta ahumada y papas rústicas.</span>
                    </div>

                    <div class="form-group" style="max-width: 240px;">
                        <label for="orden" class="form-label">Posición en la sección</label>
                        <input type="number" id="orden" name="orden" class="form-control" value="<?= (int)($producto['orden'] ?? 0) ?>" min="0" placeholder="0 = primero">
                        <span class="form-hint">Orden de aparición (0 aparece primero).</span>
                    </div>

                    <!-- ═══ ASIGNACIÓN DE MODIFICADORES ═══ -->
                    <div style="margin-top: 36px; padding-top: 24px; border-top: 1px solid var(--color-border-subtle);">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 8px;">
                            <div>
                                <h3 style="font-size: 15px; font-weight: 700; color: var(--color-text-main); margin: 0 0 3px 0;">
                                    Modificadores y Opciones Extra
                                </h3>
                                <p style="font-size: 13px; color: var(--color-text-muted); margin: 0;">
                                    Elige los grupos de personalización que el cliente verá al pedir este plato.
                                </p>
                            </div>
                            <a href="<?= e(app_url('/admin/modificadores')) ?>" target="_blank" class="btn btn-secondary btn-sm">
                                <span>Administrar grupos</span>
                                <?= icon('external-link', 12) ?>
                            </a>
                        </div>

                        <?php if (empty($grupos)): ?>
                            <div style="padding: 18px 20px; background: var(--color-bg-subtle); border: 1.5px dashed var(--color-border); border-radius: var(--radius-md); font-size: 13.5px; color: var(--color-text-muted); text-align: center;">
                                Aún no has configurado grupos de modificadores. Puedes guardar el plato ahora y asociarlos después desde <a href="<?= e(app_url('/admin/modificadores')) ?>" style="color: var(--color-brand); font-weight: 600;">Modificadores</a>.
                            </div>
                        <?php else: ?>
                            <div style="display: flex; flex-direction: column; gap: 10px; margin-top: 12px;">
                                <?php foreach ($grupos as $grupo): ?>
                                    <label style="display: flex; align-items: flex-start; gap: 14px; padding: 14px 18px; border: 1.5px solid var(--color-border-subtle); border-radius: var(--radius-md); background: var(--color-surface); cursor: pointer; transition: all 140ms ease;">
                                        <input type="checkbox" name="grupos[]" value="<?= e($grupo['id']) ?>" <?= in_array($grupo['id'], $gruposSeleccionados) ? 'checked' : '' ?> style="margin-top: 3px; width: 18px; height: 18px; accent-color: var(--color-brand); flex-shrink: 0;">
                                        <div style="flex: 1;">
                                            <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                                                <span style="font-size: 14px; font-weight: 700; color: var(--color-text-main);"><?= e($grupo['nombre']) ?></span>
                                                <span class="badge badge-neutral" style="font-size: 11px;">
                                                    <?= $grupo['tipo'] === 'unica' ? 'Selección única' : 'Selección múltiple' ?>
                                                    <?= !empty($grupo['obligatorio']) ? '· Obligatorio' : '· Opcional' ?>
                                                </span>
                                            </div>
                                            <?php if (!empty($grupo['opciones'])): ?>
                                                <p style="font-size: 12.5px; color: var(--color-text-muted); margin: 4px 0 0 0; line-height: 1.4;">
                                                    Opciones: <?= e(implode(', ', array_column($grupo['opciones'], 'nombre'))) ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Botones de guardado -->
                    <div style="display: flex; justify-content: flex-end; gap: 14px; margin-top: 36px; padding-top: 24px; border-top: 1px solid var(--color-border-subtle);">
                        <a href="<?= e(app_url('/admin/catalogo')) ?>" class="btn btn-secondary" style="padding: 11px 24px;">
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary" style="padding: 11px 32px; font-size: 14px; font-weight: 700;">
                            <?= icon('check', 16) ?>
                            <span><?= !empty($producto) ? 'Guardar Cambios' : 'Guardar y Publicar Plato' ?></span>
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>
