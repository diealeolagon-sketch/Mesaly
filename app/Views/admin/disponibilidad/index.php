<div style="margin-bottom: 20px;">
    <h2>⚡ Edición Rápida de Disponibilidad (Agotados)</h2>
    <p style="color: var(--brand-gray-muted); font-size: 14px;">
        Pensado para usar desde el celular durante el servicio: apaga o enciende productos al instante si se acaba un ingrediente.
    </p>
</div>

<div class="card" style="padding: 16px;">
    <?php if (empty($productos)): ?>
        <p style="text-align: center; color: var(--brand-gray-muted); padding: 20px 0;">
            No tienes productos registrados en tu catálogo.
        </p>
    <?php else: ?>
        <div style="display: grid; gap: 12px;" id="lista_disponibilidad">
            <?php foreach ($productos as $prod): ?>
                <?php $isDisponible = !empty($prod['disponible']); ?>
                <div class="producto-fila" style="display: flex; justify-content: space-between; align-items: center; padding: 12px 16px; border-radius: var(--radius-md); border: 1px solid var(--brand-gray-border); background: <?= $isDisponible ? '#fff' : '#FEF2F2' ?>;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <?php if (!empty($prod['imagen_path'])): ?>
                            <img src="<?= e(app_url($prod['imagen_path'])) ?>" alt="" style="width: 44px; height: 44px; border-radius: var(--radius-sm); object-fit: cover;">
                        <?php else: ?>
                            <div style="width: 44px; height: 44px; border-radius: var(--radius-sm); background: var(--brand-gray-light); display: flex; align-items: center; justify-content: center; font-size: 18px;">
                                🍽️
                            </div>
                        <?php endif; ?>
                        <div>
                            <strong style="font-size: 15px; display: block; <?= !$isDisponible ? 'text-decoration: line-through; color: #991B1B;' : '' ?>">
                                <?= e($prod['nombre']) ?>
                            </strong>
                            <small style="color: var(--brand-gray-muted);">
                                <?= e($prod['categoria_nombre'] ?? '') ?> · <?= format_cop($prod['precio']) ?>
                            </small>
                        </div>
                    </div>

                    <div>
                        <form method="POST" action="<?= e(app_url('/admin/disponibilidad/toggle')) ?>" onsubmit="return toggleAjax(event, this, <?= (int)$prod['id'] ?>)">
                            <?= csrf_field() ?>
                            <input type="hidden" name="id" value="<?= (int)$prod['id'] ?>">
                            <button type="submit" class="btn btn-sm" id="btn_prod_<?= $prod['id'] ?>" style="min-width: 110px; font-weight: 700; background: <?= $isDisponible ? '#ECFDF5' : '#DC2626' ?>; color: <?= $isDisponible ? '#065F46' : '#FFFFFF' ?>; border: 1px solid <?= $isDisponible ? '#A7F3D0' : '#B91C1C' ?>;">
                                <?= $isDisponible ? '✓ Disponible' : '✕ AGOTADO' ?>
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
    if (!window.fetch) return true; // Si navegador no soporta fetch, enviar por POST normal
    event.preventDefault();

    const btn = document.getElementById('btn_prod_' + prodId);
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
                btn.style.background = '#ECFDF5';
                btn.style.color = '#065F46';
                btn.style.border = '1px solid #A7F3D0';
                btn.innerText = '✓ Disponible';
                fila.style.background = '#FFFFFF';
            } else {
                btn.style.background = '#DC2626';
                btn.style.color = '#FFFFFF';
                btn.style.border = '1px solid #B91C1C';
                btn.innerText = '✕ AGOTADO';
                fila.style.background = '#FEF2F2';
            }
        } else {
            alert(data.message || 'Error al cambiar estado.');
            btn.innerText = originalText;
        }
    } catch (e) {
        form.submit(); // Fallback
    } finally {
        btn.disabled = false;
    }

    return false;
}
</script>
