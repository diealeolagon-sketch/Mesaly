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

<!-- RESUMEN DE ESTRUCTURA Y SECCIONES -->
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
        <span class="badge badge-neutral" style="font-size: 12px;">
            <?= $totalCategorias ?> de <?= $maxCategorias ?> secciones permitidas
        </span>
    </div>

    <?php if (empty($categorias)): ?>
        <p style="color: var(--color-text-muted); font-size: 13.5px; margin: 12px 0 0 0;">
            Aún no tienes secciones creadas. Haz clic en <strong>Nueva Sección</strong> (ej: <em>Platos Fuertes</em> o <em>Bebidas</em>) para clasificar tus productos.
        </p>
    <?php else: ?>
        <div style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
            <?php foreach ($categorias as $cat): ?>
                <?php $isActiva = !empty($cat['activa']); ?>
                <div style="display: inline-flex; align-items: center; gap: 8px; padding: 6px 12px; border-radius: var(--radius-md); border: 1px solid <?= $isActiva ? 'var(--color-border)' : 'var(--color-border-subtle)' ?>; background: <?= $isActiva ? 'var(--color-surface)' : 'var(--color-bg-subtle)' ?>; font-size: 13px;">
                    <span style="font-weight: 600; color: <?= $isActiva ? 'var(--color-text-main)' : 'var(--color-text-subtle)' ?>; <?= !$isActiva ? 'text-decoration: line-through;' : '' ?>">
                        <?= e($cat['nombre']) ?>
                    </span>
                    <span style="font-size: 11px; color: var(--color-text-subtle); background: var(--color-bg-subtle); padding: 1px 6px; border-radius: 4px; font-variant-numeric: tabular-nums;">
                        #<?= (int)$cat['orden'] ?>
                    </span>
                    <button type="button" onclick="editarCat(<?= e(json_encode($cat)) ?>)" style="background: none; border: none; cursor: pointer; color: var(--color-text-muted); padding: 2px; display: inline-flex; align-items: center;" title="Editar sección">
                        <?= icon('edit', 14) ?>
                    </button>
                    <form method="POST" action="<?= e(app_url('/admin/catalogo/categoria/' . $cat['id'] . '/eliminar')) ?>" style="display: inline;" onsubmit="return confirm('¿Seguro que deseas eliminar esta sección?');">
                        <?= csrf_field() ?>
                        <button type="submit" style="background: none; border: none; cursor: pointer; color: var(--color-text-subtle); padding: 2px; display: inline-flex; align-items: center;" title="Eliminar sección">
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
                        <th style="width: 72px;">Foto</th>
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
                                    <img src="<?= e(app_url($prod['imagen_path'])) ?>" alt="" style="width: 52px; height: 52px; border-radius: var(--radius-sm); object-fit: cover; border: 1px solid var(--color-border); display: block;">
                                <?php else: ?>
                                    <div style="width: 52px; height: 52px; border-radius: var(--radius-sm); background: var(--color-bg-subtle); border: 1px dashed var(--color-border); display: flex; align-items: center; justify-content: center; color: var(--color-text-subtle);">
                                        <?= icon('image', 20) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <span style="font-weight: 600; font-size: 14px; color: var(--color-text-main);">
                                        <?= e($prod['nombre']) ?>
                                    </span>
                                    <?php if (!empty($prod['destacado'])): ?>
                                        <span class="badge badge-warning" style="display: inline-flex; align-items: center; gap: 4px; padding: 2px 6px;">
                                            <?= icon('star', 11) ?>
                                            <span>Destacado</span>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <?php if (!empty($prod['descripcion'])): ?>
                                    <p style="font-size: 12.5px; color: var(--color-text-muted); margin: 3px 0 0 0; max-width: 440px; line-height: 1.4;">
                                        <?= e($prod['descripcion']) ?>
                                    </p>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge badge-neutral" style="font-weight: 500;">
                                    <?= e($prod['categoria_nombre'] ?? 'Sin sección') ?>
                                </span>
                            </td>
                            <td style="text-align: right; font-weight: 700; color: var(--color-text-main); font-variant-numeric: tabular-nums; font-size: 14px;">
                                <?= format_cop($prod['precio']) ?>
                            </td>
                            <td style="text-align: center;">
                                <form method="POST" action="<?= e(app_url('/admin/catalogo/producto/' . $prod['id'] . '/toggle-agotado')) ?>" style="display: inline;">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="badge <?= !empty($prod['disponible']) ? 'badge-success' : 'badge-danger' ?>" style="cursor: pointer; border: none; font-size: 12px; font-weight: 600; padding: 4px 10px; display: inline-flex; align-items: center; gap: 5px;">
                                        <?= !empty($prod['disponible']) ? icon('check', 12) . ' Disponible' : icon('x', 12) . ' Agotado' ?>
                                    </button>
                                </form>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: inline-flex; gap: 6px; align-items: center;">
                                    <a href="<?= e(app_url('/admin/catalogo/producto/' . $prod['id'] . '/editar')) ?>" class="btn btn-secondary btn-sm" title="Editar plato" style="padding: 6px 10px;">
                                        <?= icon('edit', 14) ?>
                                        <span>Editar</span>
                                    </a>
                                    <form method="POST" action="<?= e(app_url('/admin/catalogo/producto/' . $prod['id'] . '/eliminar')) ?>" onsubmit="return confirm('¿Seguro que deseas eliminar este producto? Se ocultará del menú pero su historial se mantendrá intacto.');" style="display: inline;">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-secondary btn-sm" style="color: var(--color-danger); border-color: var(--color-border); padding: 6px 10px;" title="Eliminar plato">
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

<!-- MODAL DE SECCIÓN / CATEGORÍA -->
<div id="modal_categoria" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.45); backdrop-filter: blur(4px); align-items: center; justify-content: center; z-index: 1000; padding: 20px;">
    <div class="card" style="max-width: 440px; width: 100%; margin: 0; box-shadow: var(--shadow-modal);">
        <h3 id="cat_modal_title" style="font-size: 17px; font-weight: 700; margin: 0 0 16px 0; color: var(--color-text-main);">
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

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px; padding-top: 16px; border-top: 1px solid var(--color-border-subtle);">
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
