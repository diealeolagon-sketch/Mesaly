<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
    <div>
        <h2>Catálogo y Menú Digital</h2>
        <p style="color: var(--brand-gray-muted); font-size: 14px;">
            Organiza tus categorías y administra los productos que verán tus clientes.
        </p>
    </div>
    <div style="display: flex; gap: 10px;">
        <button type="button" class="btn btn-secondary" onclick="document.getElementById('modal_categoria').style.display='flex'">
            + Nueva Categoría (<?= $totalCategorias ?>/<?= $maxCategorias ?>)
        </button>
        <a href="<?= e(app_url('/admin/catalogo/producto/nuevo')) ?>" class="btn btn-primary">
            + Nuevo Producto (<?= $totalProductos ?>/<?= $maxProductos ?>)
        </a>
    </div>
</div>

<!-- 1. CATEGORÍAS -->
<div class="card" style="margin-bottom: 24px;">
    <h3 style="font-size: 16px; margin-bottom: 12px; display: flex; justify-content: space-between; align-items: center;">
        <span>Categorías de la Carta</span>
        <small style="color: var(--brand-gray-muted); font-size: 12px;">Arrastra o define el número de orden</small>
    </h3>

    <?php if (empty($categorias)): ?>
        <p style="color: var(--brand-gray-muted); padding: 12px 0;">
            No tienes categorías creadas. Agrega al menos una (ej: Entradas, Platos Fuertes, Bebidas) para organizar tus productos.
        </p>
    <?php else: ?>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <?php foreach ($categorias as $cat): ?>
                <div style="background: <?= !empty($cat['activa']) ? 'var(--brand-cream)' : '#F3F4F6' ?>; border: 1px solid var(--brand-gray-border); padding: 8px 14px; border-radius: var(--radius-md); display: flex; align-items: center; gap: 10px;">
                    <span style="font-weight: 600; font-size: 14px; <?= empty($cat['activa']) ? 'text-decoration: line-through; color: var(--brand-gray-muted);' : '' ?>">
                        <?= e($cat['nombre']) ?>
                    </span>
                    <span style="font-size: 11px; color: var(--brand-gray-muted);">(Orden: <?= (int)$cat['orden'] ?>)</span>
                    <button type="button" onclick="editarCat(<?= e(json_encode($cat)) ?>)" style="background:none; border:none; cursor:pointer; color: var(--brand-primary); font-size: 13px;" title="Editar">
                        ✏️
                    </button>
                    <form method="POST" action="<?= e(app_url('/admin/catalogo/categoria/' . $cat['id'] . '/eliminar')) ?>" style="display: inline;" onsubmit="return confirm('¿Seguro que deseas eliminar esta categoría? Sus productos no se borrarán pero quedarán sin categoría visible.');">
                        <?= csrf_field() ?>
                        <button type="submit" style="background:none; border:none; cursor:pointer; color: #DC2626; font-size: 13px;" title="Eliminar">
                            🗑️
                        </button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- 2. PRODUCTOS -->
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 10px;">
        <h3>Platos y Productos</h3>
        <a href="<?= e(app_url('/admin/disponibilidad')) ?>" class="btn btn-secondary btn-sm" style="color: #DC2626; background: #FEF2F2; border-color: #FECACA;">
            ⚡ Edición Rápida de Agotados (Móvil)
        </a>
    </div>

    <?php if (empty($productos)): ?>
        <p style="text-align: center; color: var(--brand-gray-muted); padding: 24px 0;">
            Aún no has agregado platos a tu menú. ¡Haz clic en "+ Nuevo Producto" para comenzar!
        </p>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 60px;">Foto</th>
                        <th>Nombre y Descripción</th>
                        <th>Categoría</th>
                        <th>Precio (COP)</th>
                        <th>Estado</th>
                        <th style="text-align: right;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $prod): ?>
                        <tr>
                            <td>
                                <?php if (!empty($prod['imagen_path'])): ?>
                                    <img src="<?= e(app_url($prod['imagen_path'])) ?>" alt="" style="width: 48px; height: 48px; border-radius: var(--radius-sm); object-fit: cover;">
                                <?php else: ?>
                                    <div style="width: 48px; height: 48px; border-radius: var(--radius-sm); background: var(--brand-gray-light); display: flex; align-items: center; justify-content: center; font-size: 20px;">
                                        🍽️
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?= e($prod['nombre']) ?></strong>
                                <?php if (!empty($prod['destacado'])): ?>
                                    <span class="badge" style="background: #FEF3C7; color: #92400E; margin-left: 6px; font-size: 11px;">⭐ Destacado</span>
                                <?php endif; ?>
                                <?php if (!empty($prod['descripcion'])): ?>
                                    <p style="font-size: 12px; color: var(--brand-gray-muted); margin-top: 4px; max-width: 380px;">
                                        <?= e($prod['descripcion']) ?>
                                    </p>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge" style="background: var(--brand-gray-light); color: var(--brand-ink);">
                                    <?= e($prod['categoria_nombre'] ?? 'Sin categoría') ?>
                                </span>
                            </td>
                            <td>
                                <strong style="color: var(--brand-ink);"><?= format_cop($prod['precio']) ?></strong>
                            </td>
                            <td>
                                <form method="POST" action="<?= e(app_url('/admin/catalogo/producto/' . $prod['id'] . '/toggle-agotado')) ?>">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-sm" style="background: <?= !empty($prod['disponible']) ? '#ECFDF5' : '#FEF2F2' ?>; color: <?= !empty($prod['disponible']) ? '#065F46' : '#991B1B' ?>; border: 1px solid <?= !empty($prod['disponible']) ? '#A7F3D0' : '#FECACA' ?>; cursor: pointer;">
                                        <?= !empty($prod['disponible']) ? '✓ Disponible' : '✕ Agotado' ?>
                                    </button>
                                </form>
                            </td>
                            <td style="text-align: right;">
                                <a href="<?= e(app_url('/admin/catalogo/producto/' . $prod['id'] . '/editar')) ?>" class="btn btn-secondary btn-sm">
                                    Editar
                                </a>
                                <form method="POST" action="<?= e(app_url('/admin/catalogo/producto/' . $prod['id'] . '/eliminar')) ?>" style="display: inline;" onsubmit="return confirm('¿Seguro que deseas eliminar este producto? Se ocultará del menú pero su historial de ventas se preservará.');">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-secondary btn-sm" style="color: #DC2626;">
                                        Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- MODAL CREAR / EDITAR CATEGORÍA -->
<div id="modal_categoria" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 1000; padding: 20px;">
    <div class="card" style="max-width: 440px; width: 100%; margin: 0;">
        <h3 id="cat_modal_title" style="margin-bottom: 16px;">Nueva Categoría</h3>
        <form method="POST" action="<?= e(app_url('/admin/catalogo/categoria/guardar')) ?>">
            <?= csrf_field() ?>
            <input type="hidden" id="cat_id" name="id" value="0">

            <div class="form-group">
                <label for="cat_nombre" class="form-label">Nombre de la Categoría *</label>
                <input type="text" id="cat_nombre" name="nombre" class="form-control" placeholder="Ej: Platos Fuertes" required>
            </div>

            <div class="form-group">
                <label for="cat_orden" class="form-label">Orden numérico</label>
                <input type="number" id="cat_orden" name="orden" class="form-control" value="0" min="0">
            </div>

            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                    <input type="checkbox" id="cat_activa" name="activa" value="1" checked style="width: 18px; height: 18px;">
                    <span>Categoría activa (visible en el menú público)</span>
                </label>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 20px;">
                <button type="button" class="btn btn-secondary" onclick="cerrarCatModal()">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar Categoría</button>
            </div>
        </form>
    </div>
</div>

<script nonce="<?= e($cspNonce) ?>">
function editarCat(cat) {
    document.getElementById('cat_modal_title').innerText = 'Editar Categoría';
    document.getElementById('cat_id').value = cat.id;
    document.getElementById('cat_nombre').value = cat.nombre;
    document.getElementById('cat_orden').value = cat.orden;
    document.getElementById('cat_activa').checked = cat.activa == 1;
    document.getElementById('modal_categoria').style.display = 'flex';
}

function cerrarCatModal() {
    document.getElementById('modal_categoria').style.display = 'none';
    document.getElementById('cat_id').value = '0';
    document.getElementById('cat_nombre').value = '';
    document.getElementById('cat_orden').value = '0';
    document.getElementById('cat_activa').checked = true;
}
</script>
