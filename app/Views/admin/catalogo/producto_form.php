<div class="form-layout-container">
    <div class="mb-6">
        <a href="<?= e(app_url('/admin/catalogo')) ?>" class="btn-back-link">
            <?= icon('arrow-left', 16) ?>
            <span>Volver al catálogo</span>
        </a>
        <h1 class="page-title text-2xl mb-1">
            <?= !empty($producto) ? 'Editar Plato o Bebida' : 'Nuevo Plato o Bebida' ?>
        </h1>
        <p class="page-subtitle text-base text-muted m-0">
            Completa la información del plato, sube una fotografía atractiva y asigna modificadores opcionales u obligatorios.
        </p>
    </div>

    <form method="POST" action="<?= e(app_url('/admin/catalogo/producto/guardar')) ?>" enctype="multipart/form-data" id="producto_form">
        <?= csrf_field() ?>
        <input type="hidden" name="id" value="<?= (int)($producto['id'] ?? 0) ?>">

        <div class="product-form-grid align-start">
            
            <!-- ═══ COLUMNA IZQUIERDA: FOTO Y ESTADO ═══ -->
            <div class="stack gap-5">
                <div class="card card-p-6">
                    <div class="mb-4">
                        <h2 class="text-sm fw-700 text-main m-0 mb-1">
                            Fotografía del Plato
                        </h2>
                        <p class="text-xs text-muted m-0">
                            Formatos JPG, PNG o WebP. Tamaño sugerido cuadrado o 4:3 (máx. 8 MB).
                        </p>
                    </div>

                    <!-- Input de archivo (oculto y desacoplado para evitar conflictos de eventos) -->
                    <input type="file" id="imagen_input" name="imagen" accept="image/jpeg,image/png,image/webp">

                    <!-- Dropzone clickeable, arrastrable y accesible -->
                    <?php $hasImage = !empty($producto['imagen_path']); ?>
                    <div class="dropzone <?= $hasImage ? 'has-preview' : '' ?>" id="dropzone_container" data-input="imagen_input" tabindex="0" role="button" aria-label="Seleccionar fotografía del plato">
                        <div id="preview_wrapper" class="w-full">
                            <img src="<?= $hasImage ? e(app_url($producto['imagen_path'])) : '' ?>" 
                                 id="preview_img" 
                                 class="dropzone-preview" 
                                 alt="Foto del plato">

                            <div class="dropzone-placeholder" id="dropzone_placeholder">
                                <div class="dz-icon">
                                    <?= icon('upload', 22) ?>
                                </div>
                                <strong class="dz-label">
                                    Selecciona o arrastra una foto aquí
                                </strong>
                                <span class="dz-hint mb-3">
                                    Haz clic en cualquier parte de este recuadro
                                </span>
                                <span class="btn btn-secondary btn-sm no-events">
                                    <?= icon('image', 14) ?>
                                    <span>Buscar en tu equipo</span>
                                </span>
                            </div>

                            <div class="dropzone-change-btn" id="dropzone_change_btn">
                                <span class="btn btn-secondary btn-sm no-events">
                                    <?= icon('upload', 14) ?>
                                    <span>Cambiar fotografía</span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Switches de estado y visibilidad -->
                    <div class="toggle-group-border">
                        <label class="toggle-option-card">
                            <input type="checkbox" name="disponible" value="1" <?= (!isset($producto['disponible']) || !empty($producto['disponible'])) ? 'checked' : '' ?> class="form-checkbox">
                            <div>
                                <span class="toggle-option-title">Disponible para pedidos</span>
                                <span class="toggle-option-desc">Si lo desmarcas, se mostrará con aviso de "Agotado" en la carta.</span>
                            </div>
                        </label>

                        <label class="toggle-option-card">
                            <input type="checkbox" name="destacado" value="1" <?= !empty($producto['destacado']) ? 'checked' : '' ?> class="form-checkbox">
                            <div>
                                <span class="toggle-option-title">Plato destacado ⭐</span>
                                <span class="toggle-option-desc">Aparecerá en la sección superior de recomendados de la carta.</span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- ═══ COLUMNA DERECHA: INFORMACIÓN DETALLADA Y MODIFICADORES ═══ -->
            <div class="stack gap-5">
                <div class="card card-p-7">
                    <div class="card-header-bordered">
                        <h2 class="text-md fw-700 text-main m-0 mb-1">
                            Información Principal
                        </h2>
                        <p class="text-xs text-muted m-0">
                            Define el nombre, categoría, precio y descripción que verá el cliente.
                        </p>
                    </div>

                    <div class="config-grid-2 mb-1">
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
                            <div class="price-input-container">
                                <span class="price-currency-symbol">$</span>
                                <input type="number" id="precio" name="precio" class="form-control input-price-field" value="<?= (int)($producto['precio'] ?? 0) ?>" min="0" step="100" placeholder="Ej: 38000" required>
                            </div>
                            <span class="form-hint">Sin puntos ni decimales (ej: 38000).</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="nombre" class="form-label">Nombre del Plato o Bebida *</label>
                        <input type="text" id="nombre" name="nombre" class="form-control fw-600" value="<?= e($producto['nombre'] ?? '') ?>" placeholder="Ej: Hamburguesa Artesanal Doble Carne 300g" required>
                        <span class="form-hint">Sé claro y apetecible. Este es el título principal de la tarjeta.</span>
                    </div>

                    <div class="form-group">
                        <label for="descripcion" class="form-label">Descripción o Ingredientes</label>
                        <textarea id="descripcion" name="descripcion" class="form-control" rows="3" placeholder="Describe los ingredientes, guarniciones y preparación de forma apetitosa para el comensal."><?= e($producto['descripcion'] ?? '') ?></textarea>
                        <span class="form-hint">Ej: Doble carne 100% res madurada, queso cheddar fundido, tocineta ahumada y papas rústicas.</span>
                    </div>

                    <div class="form-group input-w-240">
                        <label for="orden" class="form-label">Posición en la sección</label>
                        <input type="number" id="orden" name="orden" class="form-control" value="<?= (int)($producto['orden'] ?? 0) ?>" min="0" placeholder="0 = primero">
                        <span class="form-hint">Orden de aparición (0 aparece primero).</span>
                    </div>

                    <!-- ═══ ASIGNACIÓN DE MODIFICADORES ═══ -->
                    <div class="modifiers-section-box">
                        <div class="flex items-center justify-between flex-wrap gap-2 mb-4">
                            <div>
                                <h3 class="text-sm fw-700 text-main m-0 mb-1">
                                    Modificadores y Opciones Extra
                                </h3>
                                <p class="text-xs text-muted m-0">
                                    Elige los grupos de personalización que el cliente verá al pedir este plato.
                                </p>
                            </div>
                            <a href="<?= e(app_url('/admin/modificadores')) ?>" target="_blank" class="btn btn-secondary btn-sm">
                                <span>Administrar grupos</span>
                                <?= icon('external-link', 12) ?>
                            </a>
                        </div>

                        <?php if (empty($grupos)): ?>
                            <div class="modifier-empty-options">
                                Aún no has configurado grupos de modificadores. Puedes guardar el plato ahora y asociarlos después desde <a href="<?= e(app_url('/admin/modificadores')) ?>" class="text-brand fw-600">Modificadores</a>.
                            </div>
                        <?php else: ?>
                            <div class="stack gap-2 mt-3">
                                <?php foreach ($grupos as $grupo): ?>
                                    <label class="modifier-check-card">
                                        <input type="checkbox" name="grupos[]" value="<?= e($grupo['id']) ?>" <?= in_array($grupo['id'], $gruposSeleccionados) ? 'checked' : '' ?> class="form-checkbox mt-1">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="text-sm fw-700 text-main"><?= e($grupo['nombre']) ?></span>
                                                <span class="badge badge-neutral text-xs">
                                                    <?= $grupo['tipo'] === 'unica' ? 'Selección única' : 'Selección múltiple' ?>
                                                    <?= !empty($grupo['obligatorio']) ? '· Obligatorio' : '· Opcional' ?>
                                                </span>
                                            </div>
                                            <?php if (!empty($grupo['opciones'])): ?>
                                                <p class="text-xs text-muted m-0 mt-1">
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
                    <div class="form-actions-bar">
                        <a href="<?= e(app_url('/admin/catalogo')) ?>" class="btn btn-secondary btn-cancel-action">
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary btn-submit-action">
                            <?= icon('check', 16) ?>
                            <span><?= !empty($producto) ? 'Guardar Cambios' : 'Guardar y Publicar Plato' ?></span>
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>