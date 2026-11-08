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
    <div class="card" style="text-align: center; padding: 56px 24px;">
        <div style="width: 56px; height: 56px; border-radius: 50%; background: var(--color-bg-subtle); display: inline-flex; align-items: center; justify-content: center; color: var(--color-text-subtle); margin-bottom: 16px;">
            <?= icon('zap', 24) ?>
        </div>
        <h3 style="font-size: 16px; font-weight: 700; color: var(--color-text-main); margin: 0 0 8px 0;">Sin productos registrados</h3>
        <p style="color: var(--color-text-muted); font-size: 14px; margin: 0 0 20px 0;">
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

<div class="kpi-grid" style="margin-bottom: 24px;">
    <div class="kpi-card">
        <div class="kpi-label">
            <span>Disponibles</span>
            <?= icon('check', 16) ?>
        </div>
        <div class="kpi-value" style="color: var(--color-success);"><?= count($disponibles) ?></div>
        <div class="kpi-subtext">Visibles en la carta digital</div>
    </div>
    <div class="kpi-card">
        <div class="kpi-label">
            <span>Agotados</span>
            <?= icon('x', 16) ?>
        </div>
        <div class="kpi-value" style="color: var(--color-danger);"><?= count($agotados) ?></div>
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

<div class="card" style="padding: 0; overflow: hidden;">
    <div style="padding: 18px 24px; border-bottom: 1px solid var(--color-border-subtle); display: flex; justify-content: space-between; align-items: center; background: var(--color-bg-subtle);">
        <div>
            <span style="font-size: 14px; font-weight: 700; color: var(--color-text-main);">Todos los Platos</span>
            <span style="font-size: 12px; color: var(--color-text-muted); margin-left: 8px;">Toca el toggle para cambiar el estado al instante</span>
        </div>
        <div style="display: flex; gap: 8px; align-items: center;">
            <span class="badge badge-success" style="font-size: 11px;"><?= count($disponibles) ?> disponibles</span>
            <span class="badge badge-danger" style="font-size: 11px;"><?= count($agotados) ?> agotados</span>
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
                        <span class="badge badge-neutral" style="font-size: 11px;"><?= e($prod['categoria_nombre'] ?? 'Sin sección') ?></span>
                        <span style="font-weight: 700; color: var(--color-text-secondary); font-variant-numeric: tabular-nums;"><?= format_cop($prod['precio']) ?></span>
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
    const name  = document.getElementById('txt_prod_' + prodId);

    btn.disabled = true;
    btn.style.opacity = '0.5';

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
                name.style.textDecoration = 'none';
                name.style.color = '';
                pill.className = 'avail-pill avail-pill--on';
                pill.innerHTML = '<span class="avail-dot"></span> Disponible';
            } else {
                btn.className = 'avail-toggle avail-toggle--off';
                label.textContent = 'OFF';
                row.className = 'avail-row avail-row--off';
                name.style.textDecoration = 'line-through';
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
        btn.style.opacity = '1';
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
