<div class="content-header">
    <div>
        <h1 class="page-title">Control de Agotados</h1>
        <p class="page-subtitle">
            Activa o desactiva platos en tiempo real. Los cambios son instantáneos en tu carta digital.
        </p>
    </div>
    <div class="content-actions">
        <a href="<?= e(app_url('/admin/catalogo')) ?>" class="btn btn-secondary btn-sm">
            <?= icon('catalog', 14) ?>
            <span>Ir al Catálogo</span>
        </a>
    </div>
</div>

<?php if (empty($productos)): ?>
    <div class="card text-center p-6">
        <div class="icon-bubble-lg">
            <?= icon('zap', 24) ?>
        </div>
        <h3 class="text-md fw-700 text-main mb-2">Sin productos registrados</h3>
        <p class="text-muted text-base mb-5">
            Agrega platos en el Catálogo para poder controlar su disponibilidad aquí.
        </p>
        <a href="<?= e(app_url('/admin/catalogo')) ?>" class="btn btn-primary">
            <?= icon('plus', 16) ?>
            <span>Ir al Catálogo</span>
        </a>
    </div>
<?php else: ?>

<?php
$disponibles = array_filter($productos, fn($p) => !empty($p['disponible']));
$agotados    = array_filter($productos, fn($p) =>  empty($p['disponible']));
?>

<div class="kpi-grid mb-5">
    <div class="kpi-card">
        <div class="kpi-label">
            <span>Disponibles</span>
            <?= icon('check', 16) ?>
        </div>
        <div class="kpi-value text-success"><?= count($disponibles) ?></div>
        <div class="kpi-subtext">Visibles en la carta digital</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-label">
            <span>Agotados</span>
            <?= icon('x', 16) ?>
        </div>
        <div class="kpi-value text-danger"><?= count($agotados) ?></div>
        <div class="kpi-subtext">No se pueden pedir</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-label">
            <span>Total platos</span>
            <?= icon('store', 16) ?>
        </div>
        <div class="kpi-value"><?= count($productos) ?></div>
        <div class="kpi-subtext">En tu catálogo</div>
    </div>
</div>

<div class="card p-0 overflow-hidden">
    <div class="card-header-bar">
        <div>
            <span class="text-base fw-700 text-main">Todos los Platos</span>
            <span class="text-xs text-muted ml-2">Toca el toggle para cambiar el estado al instante</span>
        </div>
        <div class="flex gap-2 items-center">
            <span class="badge badge-success text-xs"><?= count($disponibles) ?> disponibles</span>
            <span class="badge badge-danger text-xs"><?= count($agotados) ?> agotados</span>
        </div>
    </div>

    <div id="lista_disponibilidad">
        <?php foreach ($productos as $prod): ?>
            <?php $isDisponible = !empty($prod['disponible']); ?>
            <div class="avail-row <?= $isDisponible ? '' : 'avail-row--off' ?>" id="row_prod_<?= $prod['id'] ?>">
                <div class="avail-thumb">
                    <?php if (!empty($prod['imagen_path'])): ?>
                        <img src="<?= e(app_url($prod['imagen_path'])) ?>" alt="" class="avail-img">
                    <?php else: ?>
                        <div class="avail-img-ph"><?= icon('image', 18) ?></div>
                    <?php endif; ?>
                </div>

                <div class="avail-info">
                    <span class="avail-name" id="txt_prod_<?= $prod['id'] ?>"><?= e($prod['nombre']) ?></span>
                    <span class="avail-meta">
                        <span class="badge badge-neutral text-xs"><?= e($prod['categoria_nombre'] ?? 'Sin sección') ?></span>
                        <span class="fw-700 text-muted tabular"><?= format_cop($prod['precio']) ?></span>
                    </span>
                </div>

                <div class="avail-pill-wrap">
                    <span class="avail-pill <?= $isDisponible ? 'avail-pill--on' : 'avail-pill--off' ?>" id="pill_prod_<?= $prod['id'] ?>">
                        <span class="avail-dot"></span>
                        <?= $isDisponible ? 'Disponible' : 'Agotado' ?>
                    </span>
                </div>

                <div class="avail-action">
                    <form method="POST" action="<?= e(app_url('/admin/disponibilidad/toggle')) ?>" class="form-toggle-disponibilidad" data-prod-id="<?= (int)$prod['id'] ?>">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id" value="<?= (int)$prod['id'] ?>">
                        <button type="submit" id="btn_prod_<?= $prod['id'] ?>"
                                class="avail-toggle <?= $isDisponible ? 'avail-toggle--on' : 'avail-toggle--off' ?>"
                                title="<?= $isDisponible ? 'Desactivar' : 'Activar' ?>">
                            <span class="avail-toggle-track">
                                <span class="avail-toggle-thumb"></span>
                            </span>
                            <span class="avail-toggle-label" id="label_prod_<?= $prod['id'] ?>">
                                <?= $isDisponible ? 'ON' : 'OFF' ?>
                            </span>
                        </button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<script nonce="<?= e($cspNonce) ?>">
async function toggleAjax(event, form, prodId) {
    if (!window.fetch) return true;
    event.preventDefault();

    const btn   = document.getElementById('btn_prod_' + prodId);
    const label = document.getElementById('label_prod_' + prodId);
    const pill  = document.getElementById('pill_prod_' + prodId);
    const row   = document.getElementById('row_prod_' + prodId);

    btn.disabled = true;

    try {
        const res  = await fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();

        if (data.success) {
            if (data.disponible == 1) {
                btn.className = 'avail-toggle avail-toggle--on';
                label.textContent = 'ON';
                row.className = 'avail-row';
                pill.className = 'avail-pill avail-pill--on';
                pill.innerHTML = '<span class="avail-dot"></span> Disponible';
            } else {
                btn.className = 'avail-toggle avail-toggle--off';
                label.textContent = 'OFF';
                row.className = 'avail-row avail-row--off';
                pill.className = 'avail-pill avail-pill--off';
                pill.innerHTML = '<span class="avail-dot"></span> Agotado';
            }
        } else {
            alert(data.message || 'Error al cambiar estado.');
        }
    } catch (e) {
        form.submit();
    } finally {
        btn.disabled = false;
    }
    return false;
}

document.querySelectorAll('.form-toggle-disponibilidad').forEach(form => {
    form.addEventListener('submit', function (e) {
        const prodId = this.getAttribute('data-prod-id');
        toggleAjax(e, this, prodId);
    });
});
</script>
