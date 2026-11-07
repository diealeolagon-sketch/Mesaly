<div class="content-header">
    <div>
        <h1 class="page-title">Disponibilidad Rápida</h1>
        <p class="page-subtitle">
            Control de agotados en tiempo real. Apaga o enciende platos en un toque durante las horas pico de servicio.
        </p>
    </div>
</div>

<div class="card" style="padding: 0; overflow: hidden;">
    <div style="padding: 16px 20px; border-bottom: 1px solid var(--color-border-subtle); background: var(--color-bg-subtle); display: flex; justify-content: space-between; align-items: center;">
        <span style="font-size: 13px; font-weight: 600; color: var(--color-text-muted);">
            Total: <?= count($productos) ?> platos
        </span>
        <span style="font-size: 12px; color: var(--color-text-subtle);">
            Los cambios se guardan al instante
        </span>
    </div>

    <?php if (empty($productos)): ?>
        <div style="text-align: center; padding: 48px 20px;">
            <p style="color: var(--color-text-muted); font-size: 14px; margin: 0 0 16px 0;">
                No tienes productos registrados en tu catálogo.
            </p>
            <a href="<?= e(app_url('/admin/catalogo')) ?>" class="btn btn-primary btn-sm">
                Ir al Catálogo
            </a>
        </div>
    <?php else: ?>
        <div style="display: flex; flex-direction: column;" id="lista_disponibilidad">
            <?php foreach ($productos as $prod): ?>
                <?php $isDisponible = !empty($prod['disponible']); ?>
                <div class="producto-fila" style="display: flex; justify-content: space-between; align-items: center; padding: 14px 20px; border-bottom: 1px solid var(--color-border-subtle); background: <?= $isDisponible ? 'var(--color-surface)' : 'var(--color-danger-subtle)' ?>; transition: background 150ms var(--ease-fluid); gap: 16px;">
                    <div style="display: flex; align-items: center; gap: 14px; min-width: 0;">
                        <?php if (!empty($prod['imagen_path'])): ?>
                            <img src="<?= e(app_url($prod['imagen_path'])) ?>" alt="" style="width: 44px; height: 44px; border-radius: var(--radius-sm); object-fit: cover; border: 1px solid var(--color-border); flex-shrink: 0;">
                        <?php else: ?>
                            <div style="width: 44px; height: 44px; border-radius: var(--radius-sm); background: var(--color-bg-subtle); border: 1px dashed var(--color-border); display: flex; align-items: center; justify-content: center; color: var(--color-text-subtle); flex-shrink: 0;">
                                <?= icon('image', 18) ?>
                            </div>
                        <?php endif; ?>
                        <div style="min-width: 0;">
                            <span style="font-size: 14px; font-weight: 600; display: block; color: <?= $isDisponible ? 'var(--color-text-main)' : 'var(--color-danger)' ?>; <?= !$isDisponible ? 'text-decoration: line-through;' : '' ?>; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" id="txt_prod_<?= $prod['id'] ?>">
                                <?= e($prod['nombre']) ?>
                            </span>
                            <span style="font-size: 12.5px; color: var(--color-text-muted); display: block; margin-top: 2px;">
                                <?= e($prod['categoria_nombre'] ?? 'Sin sección') ?> · <strong style="font-variant-numeric: tabular-nums;"><?= format_cop($prod['precio']) ?></strong>
                            </span>
                        </div>
                    </div>

                    <div style="flex-shrink: 0;">
                        <form method="POST" action="<?= e(app_url('/admin/disponibilidad/toggle')) ?>" onsubmit="return toggleAjax(event, this, <?= (int)$prod['id'] ?>)">
                            <?= csrf_field() ?>
                            <input type="hidden" name="id" value="<?= (int)$prod['id'] ?>">
                            <button type="submit" class="badge <?= $isDisponible ? 'badge-success' : 'badge-danger' ?>" id="btn_prod_<?= $prod['id'] ?>" style="min-width: 120px; padding: 8px 14px; font-weight: 700; font-size: 12px; cursor: pointer; border: none; display: inline-flex; align-items: center; justify-content: center; gap: 6px;">
                                <?= $isDisponible ? icon('check', 13) . ' Disponible' : icon('x', 13) . ' Agotado' ?>
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script nonce="<?= e($cspNonce) ?>">
async function toggleAjax(event, form, prodId) {
    if (!window.fetch) return true;
    event.preventDefault();

    const btn = document.getElementById('btn_prod_' + prodId);
    const txt = document.getElementById('txt_prod_' + prodId);
    const originalHtml = btn.innerHTML;
    btn.innerText = 'Cambiando...';
    btn.disabled = true;

    try {
        const formData = new FormData(form);
        const res = await fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const data = await res.json();
        if (data.success) {
            const fila = btn.closest('.producto-fila');
            if (data.disponible == 1) {
                btn.className = 'badge badge-success';
                btn.innerHTML = '<?= icon('check', 13) ?> Disponible';
                btn.style.cssText = 'min-width: 120px; padding: 8px 14px; font-weight: 700; font-size: 12px; cursor: pointer; border: none; display: inline-flex; align-items: center; justify-content: center; gap: 6px;';
                fila.style.background = 'var(--color-surface)';
                txt.style.textDecoration = 'none';
                txt.style.color = 'var(--color-text-main)';
            } else {
                btn.className = 'badge badge-danger';
                btn.innerHTML = '<?= icon('x', 13) ?> Agotado';
                btn.style.cssText = 'min-width: 120px; padding: 8px 14px; font-weight: 700; font-size: 12px; cursor: pointer; border: none; display: inline-flex; align-items: center; justify-content: center; gap: 6px;';
                fila.style.background = 'var(--color-danger-subtle)';
                txt.style.textDecoration = 'line-through';
                txt.style.color = 'var(--color-danger)';
            }
        } else {
            alert(data.message || 'Error al cambiar estado.');
            btn.innerHTML = originalHtml;
        }
    } catch (e) {
        form.submit();
    } finally {
        btn.disabled = false;
    }

    return false;
}
</script>
