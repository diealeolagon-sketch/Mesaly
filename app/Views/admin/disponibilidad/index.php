<div style="margin-bottom: 24px;">
    <span style="font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; color: #DC2626;">
        Operación en Vivo
    </span>
    <h1 style="font-size: 26px; margin: 4px 0 6px 0; color: var(--admin-text-main);">
        ⚡ Disponibilidad Rápida (Control de Agotados)
    </h1>
    <p style="color: var(--admin-text-muted); font-size: 14px; margin: 0;">
        Pensado para usarse desde el celular durante el servicio: apaga o enciende platos en 1 toque.
    </p>
</div>

<!-- EXPLAINER: PARA QUÉ SIRVE -->
<div class="guide-banner" style="background: linear-gradient(135deg, rgba(239, 68, 68, 0.05) 0%, rgba(254, 242, 242, 0.5) 100%); border-color: rgba(239, 68, 68, 0.2);">
    <div class="guide-icon" style="color: #DC2626;">⚡</div>
    <div>
        <h3 class="guide-title" style="color: #991B1B;">¿Cuándo usar esta pantalla?</h3>
        <p class="guide-desc" style="color: #7F1D1D;">
            Durante el servicio en horas pico, si se termina un ingrediente o plato en la cocina, simplemente toca el botón del plato. Pasará inmediatamente a <strong>AGOTADO</strong> y tus clientes no podrán agregarlo al carrito, evitando reclamos y devoluciones.
        </p>
    </div>
</div>

<div class="modern-card" style="padding: 16px;">
    <?php if (empty($productos)): ?>
        <div style="text-align: center; padding: 36px 16px;">
            <p style="color: var(--admin-text-muted); font-size: 14px;">
                No tienes productos registrados en tu catálogo. Ve a la sección <a href="<?= e(app_url('/admin/catalogo')) ?>">Catálogo</a> para crearlos.
            </p>
        </div>
    <?php else: ?>
        <div style="display: grid; gap: 12px;" id="lista_disponibilidad">
            <?php foreach ($productos as $prod): ?>
                <?php $isDisponible = !empty($prod['disponible']); ?>
                <div class="producto-fila" style="display: flex; justify-content: space-between; align-items: center; padding: 14px 18px; border-radius: var(--radius-md); border: 1px solid <?= $isDisponible ? 'var(--admin-border)' : 'rgba(239, 68, 68, 0.25)' ?>; background: <?= $isDisponible ? '#FFFFFF' : '#FEF2F2' ?>; transition: all 150ms var(--ease-fluid);">
                    <div style="display: flex; align-items: center; gap: 14px;">
                        <?php if (!empty($prod['imagen_path'])): ?>
                            <img src="<?= e(app_url($prod['imagen_path'])) ?>" alt="" style="width: 50px; height: 50px; border-radius: var(--radius-sm); object-fit: cover; border: 1px solid var(--admin-border);">
                        <?php else: ?>
                            <div style="width: 50px; height: 50px; border-radius: var(--radius-sm); background: #F1F5F9; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                                🍽️
                            </div>
                        <?php endif; ?>
                        <div>
                            <strong style="font-size: 15.5px; display: block; color: <?= $isDisponible ? 'var(--admin-text-main)' : '#991B1B' ?>; <?= !$isDisponible ? 'text-decoration: line-through;' : '' ?>" id="txt_prod_<?= $prod['id'] ?>">
                                <?= e($prod['nombre']) ?>
                            </strong>
                            <span style="font-size: 13px; color: var(--admin-text-muted);">
                                <?= e($prod['categoria_nombre'] ?? 'Sin categoría') ?> · <strong><?= format_cop($prod['precio']) ?></strong>
                            </span>
                        </div>
                    </div>

                    <div>
                        <form method="POST" action="<?= e(app_url('/admin/disponibilidad/toggle')) ?>" onsubmit="return toggleAjax(event, this, <?= (int)$prod['id'] ?>)">
                            <?= csrf_field() ?>
                            <input type="hidden" name="id" value="<?= (int)$prod['id'] ?>">
                            <button type="submit" class="btn-modern btn-modern-sm" id="btn_prod_<?= $prod['id'] ?>" style="min-width: 130px; font-weight: 800; font-size: 13px; background: <?= $isDisponible ? 'var(--brand-success-light)' : '#DC2626' ?>; color: <?= $isDisponible ? '#047857' : '#FFFFFF' ?>; border: 1px solid <?= $isDisponible ? 'rgba(16, 185, 129, 0.25)' : '#B91C1C' ?>;">
                                <?= $isDisponible ? '✓ DISPONIBLE' : '✕ AGOTADO' ?>
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
    const originalText = btn.innerText;
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
                btn.style.background = 'var(--brand-success-light)';
                btn.style.color = '#047857';
                btn.style.border = '1px solid rgba(16, 185, 129, 0.25)';
                btn.innerText = '✓ DISPONIBLE';
                fila.style.background = '#FFFFFF';
                fila.style.borderColor = 'var(--admin-border)';
                txt.style.textDecoration = 'none';
                txt.style.color = 'var(--admin-text-main)';
            } else {
                btn.style.background = '#DC2626';
                btn.style.color = '#FFFFFF';
                btn.style.border = '1px solid #B91C1C';
                btn.innerText = '✕ AGOTADO';
                fila.style.background = '#FEF2F2';
                fila.style.borderColor = 'rgba(239, 68, 68, 0.25)';
                txt.style.textDecoration = 'line-through';
                txt.style.color = '#991B1B';
            }
        } else {
            alert(data.message || 'Error al cambiar estado.');
            btn.innerText = originalText;
        }
    } catch (e) {
        form.submit();
    } finally {
        btn.disabled = false;
    }

    return false;
}
</script>
