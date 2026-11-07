<div class="content-header">
    <div>
        <h1 class="page-title">Catálogo y Carta Digital</h1>
        <p class="page-subtitle">
            Organiza tus platos por secciones, define precios en pesos colombianos y gestiona su disponibilidad.
        </p>
    </div>

    <div class="content-actions">
        <button type="button" class="btn btn-secondary" onclick="abrirCatModal()">
            <?= icon('folder', 16) ?>
            <span>Nueva Sección (<?= $totalCategorias ?>/<?= $maxCategorias ?>)</span>
        </button>
        <a href="<?= e(app_url('/admin/catalogo/producto/nuevo')) ?>" class="btn btn-primary">
            <?= icon('plus', 16) ?>
            <span>Nuevo Plato (<?= $totalProductos ?>/<?= $maxProductos ?>)</span>
        </a>
    </div>
</div>

<!-- RESUMEN DE SECCIONES -->
<div class="card" style="margin-bottom: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 8px;">
        <div>
            <h2 style="font-size: 15px; font-weight: 700; color: var(--color-text-main); margin: 0 0 2px 0;">
                Secciones del Menú
            </h2>
            <p style="font-size: 13px; color: var(--color-text-muted); margin: 0;">
                Pestañas de navegación visibles para tus clientes en la carta digital.
            </p>
        </div>
        <span class="badge badge-neutral">
            <?= $totalCategorias ?> de <?= $maxCategorias ?> secciones permitidas
        </span>
    </div>

    <?php if (empty($categorias)): ?>
        <p style="color: var(--color-text-muted); font-size: 13.5px; margin: 12px 0 0 0;">
            Aún no tienes secciones creadas. Haz clic en <strong>Nueva Sección</strong> (ej: <em>Platos Fuertes</em> o <em>Bebidas</em>) para clasificar tus productos.
        </p>
    <?php else: ?>
        <div class="cat-chips-wrap">
            <?php foreach ($categorias as $cat): ?>
                <?php $isActiva = !empty($cat['activa']); ?>
                <div class="cat-chip <?= !$isActiva ? 'inactive' : '' ?>">
                    <span class="cat-chip-name">
                        <?= e($cat['nombre']) ?>
                    </span>
                    <span class="cat-chip-order">
                        #<?= (int)$cat['orden'] ?>
                    </span>
                    <button type="button" onclick="editarCat(<?= e(json_encode($cat)) ?>)" class="cat-chip-btn" title="Editar sección">
                        <?= icon('edit', 14) ?>
                    </button>
                    <form method="POST" action="<?= e(app_url('/admin/catalogo/categoria/' . $cat['id'] . '/eliminar')) ?>" style="display: inline;" onsubmit="return confirm('¿Seguro que deseas eliminar esta sección?');">
                        <?= csrf_field() ?>
                        <button type="submit" class="cat-chip-btn danger" title="Eliminar sección">
                            <?= icon('x', 14) ?>
                        </button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- LISTADO DE PLATOS Y BEBIDAS -->
<div class="card" style="padding: 0; overflow: hidden;">
    <div style="padding: 20px 24px; border-bottom: 1px solid var(--color-border-subtle); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <div>
            <h2 style="font-size: 16px; font-weight: 700; color: var(--color-text-main); margin: 0 0 2px 0;">
                Platos Registrados (<?= count($productos) ?>)
            </h2>
            <p style="font-size: 13px; color: var(--color-text-muted); margin: 0;">
                Los cambios se reflejan inmediatamente en tu menú digital.
            </p>
        </div>
        <a href="<?= e(app_url('/admin/disponibilidad')) ?>" class="btn btn-secondary btn-sm">
            <?= icon('zap', 14) ?>
            <span>Control Rápido de Agotados</span>
        </a>
    </div>

    <?php if (empty($productos)): ?>
        <div style="text-align: center; padding: 56px 24px;">
            <div style="width: 48px; height: 48px; border-radius: 50%; background: var(--color-bg-subtle); display: inline-flex; align-items: center; justify-content: center; color: var(--color-text-subtle); margin-bottom: 16px;">
                <?= icon('menu', 22) ?>
            </div>
            <h3 style="font-size: 16px; font-weight: 700; color: var(--color-text-main); margin: 0 0 6px 0;">Aún no tienes platos en tu carta</h3>
            <p style="color: var(--color-text-muted); font-size: 14px; max-width: 420px; margin: 0 auto 20px auto;">
                Sube tus platos con foto y precio en pesos colombianos para comenzar a recibir pedidos.
            </p>
            <a href="<?= e(app_url('/admin/catalogo/producto/nuevo')) ?>" class="btn btn-primary">
                <?= icon('plus', 16) ?>
                <span>Agregar Plato</span>
            </a>
        </div>
    <?php else: ?>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 64px;">Foto</th>
                        <th>Plato / Bebida</th>
                        <th>Sección</th>
                        <th style="text-align: right;">Precio (COP)</th>
                        <th style="text-align: center;">Disponibilidad</th>
                        <th style="text-align: right;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $prod): ?>
                        <tr>
                            <td>
                                <?php if (!empty($prod['imagen_path'])): ?>
                                    <img src="<?= e(app_url($prod['imagen_path'])) ?>" alt="" class="table-thumb">
                                <?php else: ?>
                                    <div class="table-thumb-placeholder">
                                        <?= icon('image', 18) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="prod-title-wrap">
                                    <span class="prod-title">
                                        <?= e($prod['nombre']) ?>
                                    </span>
                                    <?php if (!empty($prod['destacado'])): ?>
                                        <span class="badge badge-warning">
                                            <?= icon('star', 11) ?>
                                            <span>Destacado</span>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <?php if (!empty($prod['descripcion'])): ?>
                                    <p class="prod-desc">
                                        <?= e($prod['descripcion']) ?>
                                    </p>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge badge-neutral">
                                    <?= e($prod['categoria_nombre'] ?? 'Sin sección') ?>
                                </span>
                            </td>
                            <td class="prod-price">
                                <?= format_cop($prod['precio']) ?>
                            </td>
                            <td style="text-align: center;">
                                <form method="POST" action="<?= e(app_url('/admin/catalogo/producto/' . $prod['id'] . '/toggle-agotado')) ?>" style="display: inline;">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="badge <?= !empty($prod['disponible']) ? 'badge-success' : 'badge-danger' ?>" style="cursor: pointer; border: none; font-size: 12px; font-weight: 600; padding: 4px 10px;">
                                        <?= !empty($prod['disponible']) ? icon('check', 12) . ' Disponible' : icon('x', 12) . ' Agotado' ?>
                                    </button>
                                </form>
                            </td>
                            <td style="text-align: right;">
                                <div class="action-btns">
                                    <a href="<?= e(app_url('/admin/catalogo/producto/' . $prod['id'] . '/editar')) ?>" class="btn btn-secondary btn-sm" title="Editar plato">
                                        <?= icon('edit', 14) ?>
                                        <span>Editar</span>
                                    </a>
                                    <form method="POST" action="<?= e(app_url('/admin/catalogo/producto/' . $prod['id'] . '/eliminar')) ?>" onsubmit="return confirm('¿Seguro que deseas eliminar este producto? Se ocultará del menú pero su historial se mantendrá intacto.');" style="display: inline;">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-secondary btn-sm" style="color: var(--color-danger);" title="Eliminar plato">
                                            <?= icon('trash', 14) ?>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- MODAL DE SECCIÓN -->
<div id="modal_categoria" class="modal-backdrop" style="display: none;">
    <div class="modal-dialog">
        <h3 id="cat_modal_title" class="modal-title">
            Nueva Sección del Menú
        </h3>
        <form method="POST" action="<?= e(app_url('/admin/catalogo/categoria/guardar')) ?>">
            <?= csrf_field() ?>
            <input type="hidden" id="cat_id" name="id" value="0">

            <div class="form-group">
                <label for="cat_nombre" class="form-label">Nombre de la Sección *</label>
                <input type="text" id="cat_nombre" name="nombre" class="form-control" placeholder="Ej: Platos Fuertes, Bebidas, Postres" required autofocus>
            </div>

            <div class="form-group">
                <label for="cat_orden" class="form-label">Posición en el menú (0 = primera pestaña)</label>
                <input type="number" id="cat_orden" name="orden" class="form-control" value="0" min="0">
            </div>

            <div class="form-group" style="margin-top: 14px;">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 13.5px; color: var(--color-text-main);">
                    <input type="checkbox" id="cat_activa" name="activa" value="1" checked style="width: 16px; height: 16px; accent-color: var(--color-brand);">
                    <span>Sección activa y visible en el menú público</span>
                </label>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="cerrarCatModal()">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar Sección</button>
            </div>
        </form>
    </div>
</div>

<script nonce="<?= e($cspNonce) ?>">
function abrirCatModal() {
    document.getElementById('cat_modal_title').innerText = 'Nueva Sección del Menú';
    document.getElementById('cat_id').value = '0';
    document.getElementById('cat_nombre').value = '';
    document.getElementById('cat_orden').value = '0';
    document.getElementById('cat_activa').checked = true;
    document.getElementById('modal_categoria').style.display = 'flex';
}

function editarCat(cat) {
    document.getElementById('cat_modal_title').innerText = 'Editar Sección';
    document.getElementById('cat_id').value = cat.id;
    document.getElementById('cat_nombre').value = cat.nombre;
    document.getElementById('cat_orden').value = cat.orden;
    document.getElementById('cat_activa').checked = cat.activa == 1;
    document.getElementById('modal_categoria').style.display = 'flex';
}

function cerrarCatModal() {
    document.getElementById('modal_categoria').style.display = 'none';
}
</script>
