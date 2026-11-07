<div style="max-width: 760px; margin: 0 auto;">
    <div style="margin-bottom: 20px;">
        <a href="<?= e(app_url('/admin/catalogo')) ?>" style="font-size: 13px;">← Volver al catálogo</a>
        <h2 style="margin-top: 8px;"><?= !empty($producto) ? 'Editar Plato o Bebida' : 'Agregar Nuevo Plato al Menú' ?></h2>
    </div>

    <div class="card">
        <form method="POST" action="<?= e(app_url('/admin/catalogo/producto/guardar')) ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= (int)($producto['id'] ?? 0) ?>">

            <div class="form-group">
                <label for="categoria_id" class="form-label">Categoría *</label>
                <select id="categoria_id" name="categoria_id" class="form-control" required autofocus>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?= e($cat['id']) ?>" <?= (!empty($producto['categoria_id']) && $producto['categoria_id'] == $cat['id']) ? 'selected' : '' ?>>
                            <?= e($cat['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="nombre" class="form-label">Nombre del Producto *</label>
                <input type="text" id="nombre" name="nombre" class="form-control" value="<?= e($producto['nombre'] ?? '') ?>" placeholder="Ej: Hamburguesa Especial" required>
            </div>

            <div class="form-group">
                <label for="descripcion" class="form-label">Descripción o Ingredientes</label>
                <textarea id="descripcion" name="descripcion" class="form-control" rows="3" placeholder="Ej: 180g de carne angus, queso cheddar, tocineta ahumada, cebolla caramelizada y salsa tártara de la casa."><?= e($producto['descripcion'] ?? '') ?></textarea>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <div class="form-group">
                    <label for="precio" class="form-label">Precio en Pesos (COP, sin decimales) *</label>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="font-weight: 700; color: var(--brand-ink);">$</span>
                        <input type="number" id="precio" name="precio" class="form-control" value="<?= (int)($producto['precio'] ?? 0) ?>" min="0" step="100" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="orden" class="form-label">Orden en la Carta</label>
                    <input type="number" id="orden" name="orden" class="form-control" value="<?= (int)($producto['orden'] ?? 0) ?>" min="0">
                </div>
            </div>

            <div class="form-group" style="margin-top: 10px;">
                <label for="imagen" class="form-label">Foto del Plato (JPG, PNG o WebP, máx 3 MB)</label>
                <?php if (!empty($producto['imagen_path'])): ?>
                    <div style="margin-bottom: 8px; display: flex; align-items: center; gap: 12px;">
                        <img src="<?= e(app_url($producto['imagen_path'])) ?>" alt="" style="width: 80px; height: 80px; border-radius: var(--radius-sm); object-fit: cover;">
                        <span style="font-size: 12px; color: var(--brand-gray-muted);">Imagen actual. Sube una nueva para reemplazarla.</span>
                    </div>
                <?php endif; ?>
                <input type="file" id="imagen" name="imagen" class="form-control" accept="image/jpeg,image/png,image/webp">
            </div>

            <div style="display: flex; gap: 24px; margin: 20px 0; padding: 14px; background: var(--brand-cream); border-radius: var(--radius-md); flex-wrap: wrap;">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-weight: 600;">
                    <input type="checkbox" name="disponible" value="1" <?= (!isset($producto['disponible']) || !empty($producto['disponible'])) ? 'checked' : '' ?> style="width: 18px; height: 18px;">
                    <span>Producto Disponible (si lo apagas figurará como "Agotado")</span>
                </label>

                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-weight: 600;">
                    <input type="checkbox" name="destacado" value="1" <?= !empty($producto['destacado']) ? 'checked' : '' ?> style="width: 18px; height: 18px;">
                    <span>Destacar en lo alto de la categoría (⭐)</span>
                </label>
            </div>

            <!-- GRUPOS DE MODIFICADORES -->
            <div style="margin-top: 24px; border-top: 1px solid var(--brand-gray-border); padding-top: 16px;">
                <h4 style="margin-bottom: 8px;">Grupos de Modificadores y Opciones Extra</h4>
                <p style="font-size: 13px; color: var(--brand-gray-muted); margin-bottom: 14px;">
                    Selecciona qué grupos aplican a este producto (ej. Términos de carne, salsas, o adiciones con costo):
                </p>

                <?php if (empty($grupos)): ?>
                    <p style="font-size: 13px; color: var(--brand-gray-muted);">
                        No tienes grupos de modificadores creados aún. Puedes crearlos en la sección 
                        <a href="<?= e(app_url('/admin/modificadores')) ?>">Modificadores</a>.
                    </p>
                <?php else: ?>
                    <div style="display: grid; gap: 10px;">
                        <?php foreach ($grupos as $grupo): ?>
                            <label style="display: flex; align-items: flex-start; gap: 10px; padding: 10px 14px; border: 1px solid var(--brand-gray-border); border-radius: var(--radius-sm); cursor: pointer; background: #fff;">
                                <input type="checkbox" name="grupos[]" value="<?= e($grupo['id']) ?>" <?= in_array($grupo['id'], $gruposSeleccionados) ? 'checked' : '' ?> style="margin-top: 3px; width: 18px; height: 18px;">
                                <div>
                                    <strong style="font-size: 14px;"><?= e($grupo['nombre']) ?></strong>
                                    <span class="badge" style="background: var(--brand-gray-light); font-size: 11px; margin-left: 6px;">
                                        <?= $grupo['tipo'] === 'unica' ? 'Selección única' : 'Selección múltiple' ?>
                                        <?= !empty($grupo['obligatorio']) ? '· Obligatorio' : '· Opcional' ?>
                                    </span>
                                    <?php if (!empty($grupo['opciones'])): ?>
                                        <div style="font-size: 12px; color: var(--brand-gray-muted); margin-top: 4px;">
                                            Opciones: <?= e(implode(', ', array_column($grupo['opciones'], 'nombre'))) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 12px; margin-top: 28px;">
                <a href="<?= e(app_url('/admin/catalogo')) ?>" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">
                    <?= !empty($producto) ? 'Guardar Cambios' : 'Crear Producto' ?>
                </button>
            </div>
        </form>
    </div>
</div>
