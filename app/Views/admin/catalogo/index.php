<div class="content-header">
    <div>
        <h1 class="page-title">Catálogo y Carta Digital</h1>
        <p class="page-subtitle">
            Organiza tus platos por secciones, define precios en pesos colombianos y gestiona su disponibilidad.
        </p>
    </div>

    <div class="content-actions">
        <button type="button" class="btn btn-secondary" id="btnNuevaSeccion">
            <?= icon('folder', 16) ?>
            <span>Nueva Sección</span>
        </button>
        <a href="<?= e(app_url('/admin/catalogo/producto/nuevo')) ?>" class="btn btn-primary">
            <?= icon('plus', 16) ?>
            <span>Nuevo Plato</span>
        </a>
    </div>
</div>

<!-- RESUMEN DE SECCIONES -->
<div class="card mb-5">
    <div class="flex items-center justify-between flex-wrap gap-2 mb-4">
        <div>
            <h2 class="text-sm fw-700 text-main m-0 mb-1">
                Secciones del Menú
            </h2>
            <p class="text-xs text-muted m-0">
                Pestañas de navegación visibles para tus clientes en la carta digital.
            </p>
        </div>
        <span class="badge badge-neutral">
            <?= $totalCategorias ?> <?= $totalCategorias === 1 ? 'sección' : 'secciones' ?>
        </span>
    </div>

    <?php if (empty($categorias)): ?>
        <p class="text-muted text-sm mt-3 mb-0">
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
                    <button type="button" class="cat-chip-btn btn-editar-cat" data-cat="<?= e(json_encode($cat)) ?>" title="Editar sección">
                        <?= icon('edit', 14) ?>
                    </button>
                    <form method="POST" action="<?= e(app_url('/admin/catalogo/categoria/' . $cat['id'] . '/eliminar')) ?>" class="inline m-0" data-confirm="¿Seguro que deseas eliminar esta sección?">
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
<div class="card p-0 overflow-hidden">
    <div class="card-header-bar">
        <div>
            <h2 class="text-md fw-700 text-main m-0 mb-1">
                Platos Registrados (<?= count($productos) ?>)
            </h2>
            <p class="text-xs text-muted m-0">
                Los cambios se reflejan inmediatamente en tu menú digital.
            </p>
        </div>
        <a href="<?= e(app_url('/admin/disponibilidad')) ?>" class="btn btn-secondary btn-sm">
            <?= icon('zap', 14) ?>
            <span>Control Rápido de Agotados</span>
        </a>
    </div>

    <?php if (empty($productos)): ?>
        <div class="empty-state-box p-6">
            <div class="icon-bubble-lg mx-auto mb-4">
                <?= icon('menu', 22) ?>
            </div>
            <h3 class="text-md fw-700 text-main m-0 mb-2">Aún no tienes platos en tu carta</h3>
            <p class="text-muted text-sm max-w-md mx-auto mb-5">
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
                        <th class="col-foto">Foto</th>
                        <th>Plato / Bebida</th>
                        <th>Sección</th>
                        <th class="text-right">Precio (COP)</th>
                        <th class="text-center">Disponibilidad</th>
                        <th class="text-right">Acciones</th>
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
                            <td class="text-center">
                                <form method="POST" action="<?= e(app_url('/admin/catalogo/producto/' . $prod['id'] . '/toggle-agotado')) ?>" class="inline m-0">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="badge btn-badge-toggle <?= !empty($prod['disponible']) ? 'badge-success' : 'badge-danger' ?>">
                                        <?= !empty($prod['disponible']) ? icon('check', 12) . ' Disponible' : icon('x', 12) . ' Agotado' ?>
                                    </button>
                                </form>
                            </td>
                            <td class="text-right">
                                <div class="action-btns">
                                    <a href="<?= e(app_url('/admin/catalogo/producto/' . $prod['id'] . '/editar')) ?>" class="btn btn-secondary btn-sm" title="Editar plato">
                                        <?= icon('edit', 14) ?>
                                        <span>Editar</span>
                                    </a>
                                    <form method="POST" action="<?= e(app_url('/admin/catalogo/producto/' . $prod['id'] . '/eliminar')) ?>" data-confirm="¿Seguro que deseas eliminar este producto? Se ocultará del menú pero su historial se mantendrá intacto." class="inline m-0">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-secondary btn-sm text-danger" title="Eliminar plato">
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
<div id="modal_categoria" class="modal-backdrop modal-backdrop-modern">
    <div class="modal-dialog modal-dialog-modern modal-dialog-md">
        <div class="modal-header-modern">
            <div class="modal-brand-header">
                <div class="modal-brand-header-left">
                    <img src="<?= e(asset_url('/assets/img/logo-horizontal.svg')) ?>" alt="Mesaly" class="modal-brand-mesaly-logo">
                    <span class="modal-brand-divider"></span>
                    <div class="modal-brand-titles">
                        <h3 id="cat_modal_title" class="modal-brand-title">Nueva Sección del Menú</h3>
                        <span class="modal-brand-subtitle">Organización de Platos y Categorías</span>
                    </div>
                </div>
                <button type="button" class="modal-btn-close-circle" id="btnCerrarCatModalTop" onclick="cerrarCatModal()" aria-label="Cerrar modal" title="Cerrar">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
        </div>

        <form method="POST" action="<?= e(app_url('/admin/catalogo/categoria/guardar')) ?>">
            <?= csrf_field() ?>
            <input type="hidden" id="cat_id" name="id" value="0">

            <div class="modal-body-modern">
                <div class="form-group">
                    <label for="cat_nombre" class="form-label">Nombre de la Sección *</label>
                    <input type="text" id="cat_nombre" name="nombre" class="form-control" placeholder="Ej: Platos Fuertes, Bebidas, Postres" required autofocus>
                    <span class="form-hint">Aparecerá en la barra de navegación del menú.</span>
                </div>

                <div class="form-group">
                    <label for="cat_orden" class="form-label">Posición en el menú</label>
                    <input type="number" id="cat_orden" name="orden" class="form-control input-w-sm" value="0" min="0">
                    <span class="form-hint">0 = primera pestaña visible para los comensales.</span>
                </div>

                <div class="form-group mb-0">
                    <label class="toggle-option-card m-0">
                        <input type="checkbox" id="cat_activa" name="activa" value="1" checked class="form-checkbox">
                        <div>
                            <span class="toggle-option-title">Sección visible y activa</span>
                            <span class="toggle-option-desc">Si se desactiva, se ocultará temporalmente en la carta digital.</span>
                        </div>
                    </label>
                </div>
            </div>

            <div class="modal-footer-modern">
                <button type="button" class="btn btn-secondary" id="btnCancelarCatModal">Cancelar</button>
                <button type="submit" class="btn btn-primary btn-save-action">Guardar Sección</button>
            </div>
        </form>
    </div>
</div>

<script nonce="<?= e($cspNonce) ?>">
function abrirCatModal() {
    const modal = document.getElementById('modal_categoria');
    document.getElementById('cat_modal_title').innerText = 'Nueva Sección del Menú';
    document.getElementById('cat_id').value = '0';
    document.getElementById('cat_nombre').value = '';
    document.getElementById('cat_orden').value = '0';
    document.getElementById('cat_activa').checked = true;
    modal.classList.add('is-open', 'is-active');
    setTimeout(() => {
        const inp = document.getElementById('cat_nombre');
        if (inp) inp.focus();
    }, 60);
}

function editarCat(cat) {
    const modal = document.getElementById('modal_categoria');
    document.getElementById('cat_modal_title').innerText = 'Editar Sección';
    document.getElementById('cat_id').value = cat.id;
    document.getElementById('cat_nombre').value = cat.nombre;
    document.getElementById('cat_orden').value = cat.orden;
    document.getElementById('cat_activa').checked = cat.activa == 1;
    modal.classList.add('is-open', 'is-active');
    setTimeout(() => {
        const inp = document.getElementById('cat_nombre');
        if (inp) inp.focus();
    }, 60);
}

function cerrarCatModal() {
    const modal = document.getElementById('modal_categoria');
    modal.classList.remove('is-open', 'is-active');
}

document.getElementById('modal_categoria')?.addEventListener('click', function(e) {
    if (e.target === this) {
        cerrarCatModal();
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        cerrarCatModal();
    }
});

// Enlace directo de eventos (compatible con CSP)
document.getElementById('btnNuevaSeccion')?.addEventListener('click', abrirCatModal);
document.getElementById('btnCancelarCatModal')?.addEventListener('click', cerrarCatModal);
document.querySelectorAll('.btn-editar-cat').forEach(btn => {
    btn.addEventListener('click', function() {
        try {
            const raw = this.getAttribute('data-cat');
            if (raw) editarCat(JSON.parse(raw));
        } catch(e) {
            console.error('Error parseando datos de categoría:', e);
        }
    });
});
</script>