<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
    <div>
        <span style="font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; color: var(--brand-primary);">
            Gestión de Menú
        </span>
        <h1 style="font-size: 26px; margin: 4px 0 6px 0; color: var(--admin-text-main);">
            Catálogo y Carta Digital
        </h1>
        <p style="color: var(--admin-text-muted); font-size: 14px; margin: 0;">
            Organiza tus platos por categorías, define precios en pesos colombianos y mantén tu carta actualizada.
        </p>
    </div>

    <div style="display: flex; gap: 12px; flex-wrap: wrap;">
        <button type="button" class="btn-modern btn-modern-secondary" onclick="abrirCatModal()">
            📂 + Nueva Categoría (<?= $totalCategorias ?>/<?= $maxCategorias ?>)
        </button>
        <a href="<?= e(app_url('/admin/catalogo/producto/nuevo')) ?>" class="btn-modern btn-modern-primary">
            🍽️ + Nuevo Plato o Bebida (<?= $totalProductos ?>/<?= $maxProductos ?>)
        </a>
    </div>
</div>

<!-- EXPLAINER: CATEGORÍAS VS PRODUCTOS -->
<div class="guide-banner">
    <div class="guide-icon">📂</div>
    <div>
        <h3 class="guide-title">¿Cómo está estructurado tu catálogo?</h3>
        <p class="guide-desc">
            <strong>Las Categorías</strong> dividen tu carta en pestañas horizontales (ej. <em>Entradas</em>, <em>Platos Fuertes</em>, <em>Bebidas</em>).<br>
            <strong>Los Platos y Productos</strong> van dentro de cada categoría con su foto, descripción apetitosa y precio.
        </p>
    </div>
</div>

<!-- 1. CATEGORÍAS EN TABS / CHIPS -->
<div class="modern-card" style="padding: 22px 24px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px;">
        <h3 style="font-size: 16px; color: var(--admin-text-main);">
            Secciones / Categorías del Menú
        </h3>
        <span style="font-size: 12.5px; color: var(--admin-text-muted);">
            Límite de tu plan: <?= $totalCategorias ?> de <?= $maxCategorias ?> usadas
        </span>
    </div>

    <?php if (empty($categorias)): ?>
        <p style="color: var(--admin-text-muted); font-size: 13.5px; padding: 12px 0;">
            Aún no tienes categorías creadas. Crea al menos una (ej: <em>Platos Fuertes</em>) para poder agregar tus platos.
        </p>
    <?php else: ?>
        <div style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
            <?php foreach ($categorias as $cat): ?>
                <div style="background: <?= !empty($cat['activa']) ? '#FFF9F2' : '#F1F5F9' ?>; border: 1px solid <?= !empty($cat['activa']) ? 'rgba(232, 93, 4, 0.25)' : 'var(--admin-border)' ?>; padding: 8px 14px; border-radius: var(--radius-pill); display: inline-flex; align-items: center; gap: 10px; transition: all 0.15s ease;">
                    <span style="font-weight: 700; font-size: 13.5px; color: <?= !empty($cat['activa']) ? 'var(--admin-text-main)' : 'var(--admin-text-subtle)' ?>; <?= empty($cat['activa']) ? 'text-decoration: line-through;' : '' ?>">
                        <?= e($cat['nombre']) ?>
                    </span>
                    <span style="font-size: 11px; color: var(--admin-text-subtle); background: rgba(0,0,0,0.05); padding: 2px 6px; border-radius: 4px;">
                        #<?= (int)$cat['orden'] ?>
                    </span>
                    <button type="button" onclick="editarCat(<?= e(json_encode($cat)) ?>)" style="background:none; border:none; cursor:pointer; font-size: 13px; color: var(--brand-primary); padding: 2px;" title="Editar">
                        ✏️
                    </button>
                    <form method="POST" action="<?= e(app_url('/admin/catalogo/categoria/' . $cat['id'] . '/eliminar')) ?>" style="display: inline;" onsubmit="return confirm('¿Seguro que deseas eliminar esta categoría?');">
                        <?= csrf_field() ?>
                        <button type="submit" style="background:none; border:none; cursor:pointer; font-size: 13px; color: var(--brand-danger); padding: 2px;" title="Eliminar">
                            ✕
                        </button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- 2. TABLA DE PRODUCTOS -->
<div class="modern-card" style="padding: 0; overflow: hidden;">
    <div style="padding: 22px 26px; border-bottom: 1px solid var(--admin-border); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
        <div>
            <h3 style="font-size: 17px; margin: 0 0 4px 0;">Platos y Bebidas Registrados</h3>
            <span style="font-size: 13px; color: var(--admin-text-muted);">
                Total: <?= count($productos) ?> platos · Los cambios se reflejan inmediatamente en tu menú digital
            </span>
        </div>
        <a href="<?= e(app_url('/admin/disponibilidad')) ?>" class="btn-modern btn-modern-danger btn-modern-sm">
            ⚡ Modo Rápido de Agotados
        </a>
    </div>

    <?php if (empty($productos)): ?>
        <div style="text-align: center; padding: 48px 24px;">
            <span style="font-size: 42px; display: block; margin-bottom: 12px;">🍽️</span>
            <h4 style="font-size: 17px; margin-bottom: 6px;">Aún no tienes platos en tu carta</h4>
            <p style="color: var(--admin-text-muted); font-size: 14px; max-width: 440px; margin: 0 auto 20px auto;">
                Sube tus productos con fotos apetitosas y precios en pesos colombianos para empezar a recibir pedidos.
            </p>
            <a href="<?= e(app_url('/admin/catalogo/producto/nuevo')) ?>" class="btn-modern btn-modern-primary">
                + Agregar Mi Primer Plato
            </a>
        </div>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th style="width: 80px;">Foto</th>
                        <th>Plato / Bebida</th>
                        <th>Categoría</th>
                        <th>Precio (COP)</th>
                        <th>Disponibilidad</th>
                        <th style="text-align: right;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $prod): ?>
                        <tr>
                            <td>
                                <?php if (!empty($prod['imagen_path'])): ?>
                                    <img src="<?= e(app_url($prod['imagen_path'])) ?>" alt="" style="width: 56px; height: 56px; border-radius: var(--radius-sm); object-fit: cover; border: 1px solid var(--admin-border);">
                                <?php else: ?>
                                    <div style="width: 56px; height: 56px; border-radius: var(--radius-sm); background: #F1F5F9; display: flex; align-items: center; justify-content: center; font-size: 22px; color: var(--admin-text-subtle);">
                                        🍽️
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <strong style="font-size: 15px; color: var(--admin-text-main);"><?= e($prod['nombre']) ?></strong>
                                    <?php if (!empty($prod['destacado'])): ?>
                                        <span style="background: #FEF3C7; color: #92400E; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: var(--radius-pill); border: 1px solid #FDE68A;">
                                            ⭐ Destacado
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <?php if (!empty($prod['descripcion'])): ?>
                                    <p style="font-size: 13px; color: var(--admin-text-muted); margin: 4px 0 0 0; max-width: 420px; line-height: 1.35;">
                                        <?= e($prod['descripcion']) ?>
                                    </p>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span style="font-size: 13px; font-weight: 600; color: var(--admin-text-muted); background: #F8FAFC; padding: 4px 10px; border-radius: var(--radius-sm); border: 1px solid var(--admin-border);">
                                    <?= e($prod['categoria_nombre'] ?? 'Sin categoría') ?>
                                </span>
                            </td>
                            <td>
                                <strong style="font-size: 15px; color: var(--admin-text-main); font-variant-numeric: tabular-nums;">
                                    <?= format_cop($prod['precio']) ?>
                                </strong>
                            </td>
                            <td>
                                <form method="POST" action="<?= e(app_url('/admin/catalogo/producto/' . $prod['id'] . '/toggle-agotado')) ?>">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn-modern btn-modern-sm" style="font-weight: 700; background: <?= !empty($prod['disponible']) ? 'var(--brand-success-light)' : 'var(--brand-danger-light)' ?>; color: <?= !empty($prod['disponible']) ? '#047857' : '#B91C1C' ?>; border-color: <?= !empty($prod['disponible']) ? 'rgba(16, 185, 129, 0.25)' : 'rgba(239, 68, 68, 0.25)' ?>;">
                                        <?= !empty($prod['disponible']) ? '✓ Disponible' : '✕ Agotado' ?>
                                    </button>
                                </form>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: inline-flex; gap: 8px;">
                                    <a href="<?= e(app_url('/admin/catalogo/producto/' . $prod['id'] . '/editar')) ?>" class="btn-modern btn-modern-secondary btn-modern-sm">
                                        Editar
                                    </a>
                                    <form method="POST" action="<?= e(app_url('/admin/catalogo/producto/' . $prod['id'] . '/eliminar')) ?>" onsubmit="return confirm('¿Seguro que deseas eliminar este producto? Se ocultará del menú pero su historial se mantendrá intacto.');" style="display: inline;">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn-modern btn-modern-danger btn-modern-sm">
                                            Eliminar
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

<!-- MODAL DE CATEGORÍA ELEGANTE -->
<div id="modal_categoria" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.55); backdrop-filter: blur(4px); align-items: center; justify-content: center; z-index: 1000; padding: 20px;">
    <div class="modern-card" style="max-width: 460px; width: 100%; margin: 0; box-shadow: var(--shadow-elevated);">
        <h3 id="cat_modal_title" style="font-size: 18px; margin-bottom: 16px;">Nueva Sección / Categoría</h3>
        <form method="POST" action="<?= e(app_url('/admin/catalogo/categoria/guardar')) ?>">
            <?= csrf_field() ?>
            <input type="hidden" id="cat_id" name="id" value="0">

            <div class="form-group">
                <label for="cat_nombre" class="form-label">Nombre de la Categoría *</label>
                <input type="text" id="cat_nombre" name="nombre" class="form-control" placeholder="Ej: Platos Fuertes, Bebidas, Postres" required autofocus>
            </div>

            <div class="form-group">
                <label for="cat_orden" class="form-label">Orden en el menú (0 = primero, 1 = segundo...)</label>
                <input type="number" id="cat_orden" name="orden" class="form-control" value="0" min="0">
            </div>

            <div class="form-group" style="margin-top: 16px;">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 13.5px;">
                    <input type="checkbox" id="cat_activa" name="activa" value="1" checked style="width: 18px; height: 18px;">
                    <span>Categoría activa y visible en el menú público</span>
                </label>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px;">
                <button type="button" class="btn-modern btn-modern-secondary" onclick="cerrarCatModal()">Cancelar</button>
                <button type="submit" class="btn-modern btn-modern-primary">Guardar Categoría</button>
            </div>
        </form>
    </div>
</div>

<script nonce="<?= e($cspNonce) ?>">
function abrirCatModal() {
    document.getElementById('cat_modal_title').innerText = 'Nueva Sección / Categoría';
    document.getElementById('cat_id').value = '0';
    document.getElementById('cat_nombre').value = '';
    document.getElementById('cat_orden').value = '0';
    document.getElementById('cat_activa').checked = true;
    document.getElementById('modal_categoria').style.display = 'flex';
}

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
}
</script>
