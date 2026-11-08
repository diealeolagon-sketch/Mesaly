<div class="content-header">
    <div>
        <h1 class="page-title">Grupos de Modificadores</h1>
        <p class="page-subtitle">
            Personaliza opciones para tus platos: términos de cocción, adiciones con costo extra o ingredientes a excluir.
        </p>
    </div>
    <div class="content-actions">
        <button type="button" class="btn btn-primary btn-abrir-grupo" id="btnNuevoGrupo">
            <?= icon('plus', 16) ?>
            <span>Nuevo Grupo de Modificadores</span>
        </button>
    </div>
</div>

<?php if (empty($grupos)): ?>
    <div class="card" style="text-align: center; padding: 64px 24px;">
        <div style="width: 56px; height: 56px; border-radius: 50%; background: var(--color-brand-subtle); color: var(--color-brand); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 16px;">
            <?= icon('modifiers', 26) ?>
        </div>
        <h3 style="font-size: 17px; font-weight: 700; color: var(--color-text-main); margin: 0 0 8px 0;">
            Aún no tienes grupos de modificadores
        </h3>
        <p style="color: var(--color-text-muted); font-size: 14px; max-width: 480px; margin: 0 auto 24px auto; line-height: 1.5;">
            Los modificadores permiten que tus clientes personalicen sus platos (ej: <em>"Término de la carne"</em>, <em>"Tipo de salsa"</em> o <em>"Adición de queso / tocineta"</em>).
        </p>
        <button type="button" class="btn btn-primary btn-abrir-grupo">
            <?= icon('plus', 16) ?>
            <span>Crear Primer Grupo</span>
        </button>
    </div>
<?php else: ?>
    <div style="display: flex; flex-direction: column; gap: 24px;">
        <?php foreach ($grupos as $grupo): ?>
            <div class="modifier-group-card">
                <!-- Header del Grupo -->
                <div class="modifier-group-header">
                    <div class="modifier-group-title-wrap">
                        <div style="width: 36px; height: 36px; border-radius: var(--radius-sm); background: #FFFFFF; border: 1px solid var(--color-border); display: flex; align-items: center; justify-content: center; color: var(--color-brand);">
                            <?= icon('modifiers', 18) ?>
                        </div>
                        <div>
                            <h2 class="modifier-group-title">
                                <?= e($grupo['nombre']) ?>
                            </h2>
                            <div style="display: flex; align-items: center; gap: 8px; margin-top: 4px; flex-wrap: wrap;">
                                <span class="badge <?= $grupo['tipo'] === 'unica' ? 'badge-neutral' : 'badge-warning' ?>">
                                    <?= $grupo['tipo'] === 'unica' ? 'Selección Única' : 'Selección Múltiple' ?>
                                </span>
                                <?php if (!empty($grupo['obligatorio'])): ?>
                                    <span class="badge badge-danger">Obligatorio</span>
                                <?php else: ?>
                                    <span class="badge badge-neutral">Opcional</span>
                                <?php endif; ?>

                                <?php if ($grupo['tipo'] === 'multiple'): ?>
                                    <span style="font-size: 12px; color: var(--color-text-muted);">
                                        (El cliente puede elegir de <strong><?= (int)$grupo['min_selecciones'] ?></strong> a <strong><?= (int)$grupo['max_selecciones'] ?></strong> opciones)
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="modifier-group-actions">
                        <button type="button" class="btn btn-secondary btn-sm btn-editar-grupo" data-grupo="<?= e(json_encode($grupo)) ?>">
                            <?= icon('edit', 14) ?>
                            <span>Editar Grupo</span>
                        </button>
                        <form method="POST" action="<?= e(app_url('/admin/modificadores/grupo/' . $grupo['id'] . '/eliminar')) ?>" data-confirm="¿Seguro que deseas eliminar este grupo y todas sus opciones?" style="display: inline;">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-secondary btn-sm" style="color: var(--color-danger); border-color: var(--color-border);" title="Eliminar grupo completo">
                                <?= icon('trash', 14) ?>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Lista de Opciones -->
                <div class="modifier-options-list">
                    <span style="font-size: 11.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-text-subtle); display: block; margin-bottom: 4px;">
                        Opciones configuradas (<?= count($grupo['opciones'] ?? []) ?>)
                    </span>

                    <?php if (empty($grupo['opciones'])): ?>
                        <div style="background: var(--color-bg-subtle); border: 1.5px dashed var(--color-border); padding: 18px 20px; border-radius: var(--radius-md); font-size: 13.5px; color: var(--color-text-muted); text-align: center;">
                            Este grupo aún no tiene opciones. Escribe la primera abajo y presiona <strong>Agregar Opción</strong>.
                        </div>
                    <?php else: ?>
                        <div style="display: flex; flex-direction: column; gap: 8px;">
                            <?php foreach ($grupo['opciones'] as $op): ?>
                                <div class="modifier-option-item">
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <span style="width: 8px; height: 8px; border-radius: 50%; background: var(--color-brand); display: inline-block;"></span>
                                        <span class="modifier-option-name">
                                            <?= e($op['nombre']) ?>
                                        </span>
                                        <?php if ((int)$op['precio_extra'] > 0): ?>
                                            <span class="badge badge-warning" style="font-size: 11.5px; font-weight: 700;">
                                                + <?= format_cop($op['precio_extra']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span style="color: var(--color-text-subtle); font-size: 12px; font-weight: 500;">(Sin costo adicional)</span>
                                        <?php endif; ?>
                                    </div>
                                    <form method="POST" action="<?= e(app_url('/admin/modificadores/opcion/' . $op['id'] . '/eliminar')) ?>" data-confirm="¿Eliminar esta opción?" style="display: inline;">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-secondary btn-sm" style="color: var(--color-danger); padding: 4px 8px;" title="Quitar opción">
                                            <?= icon('trash', 13) ?>
                                        </button>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Formulario en línea para agregar opción rápida -->
                <form method="POST" action="<?= e(app_url('/admin/modificadores/opcion/guardar')) ?>" class="modifier-add-option-form">
                    <?= csrf_field() ?>
                    <input type="hidden" name="grupo_id" value="<?= e($grupo['id']) ?>">
                    <input type="hidden" name="id" value="0">

                    <span class="modifier-add-option-title">
                        <?= icon('plus', 14) ?>
                        <span>Nueva Opción:</span>
                    </span>

                    <input type="text" name="nombre" class="form-control" placeholder="Ej: Tocineta crujiente, Término medio, Queso cheddar" style="flex: 2; min-width: 220px;" required>

                    <div style="display: flex; align-items: center; position: relative; flex: 1; min-width: 150px;">
                        <span style="position: absolute; left: 12px; font-size: 13px; font-weight: 700; color: var(--color-text-subtle);">+$</span>
                        <input type="number" name="precio_extra" class="form-control" placeholder="0" min="0" step="100" value="0" style="padding-left: 32px; font-weight: 600; font-variant-numeric: tabular-nums;" title="Costo extra en pesos colombianos">
                    </div>

                    <button type="submit" class="btn btn-primary btn-sm" style="padding: 10px 18px; font-weight: 700;">
                        <?= icon('plus', 14) ?>
                        <span>Agregar Opción</span>
                    </button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- MODAL GRUPO -->
<div id="modal_grupo" class="modal-backdrop">
    <div class="modal-dialog" style="max-width: 520px;">
        <h3 id="grupo_modal_title" class="modal-title">
            Nuevo Grupo de Modificadores
        </h3>
        <p style="font-size: 13px; color: var(--color-text-muted); margin: -10px 0 20px 0;">
            Configura el tipo de pregunta y reglas que verá el comensal.
        </p>

        <form method="POST" action="<?= e(app_url('/admin/modificadores/grupo/guardar')) ?>">
            <?= csrf_field() ?>
            <input type="hidden" id="grupo_id" name="id" value="0">

            <div class="form-group">
                <label for="grupo_nombre" class="form-label">Nombre del Grupo *</label>
                <input type="text" id="grupo_nombre" name="nombre" class="form-control" placeholder="Ej: Término de la carne, Tipo de salsa, Adicionales" required>
                <span class="form-hint">Este título aparecerá en el menú interactivo para el cliente.</span>
            </div>

            <div class="form-group">
                <label for="grupo_tipo" class="form-label">Tipo de Selección *</label>
                <select id="grupo_tipo" name="tipo" class="form-control" required>
                    <option value="unica">Selección Única (El cliente escoge exactamente una opción)</option>
                    <option value="multiple">Selección Múltiple (El cliente puede marcar varias adiciones)</option>
                </select>
            </div>

            <div class="form-group" style="padding: 12px 14px; background: var(--color-bg-subtle); border-radius: var(--radius-md); border: 1px solid var(--color-border-subtle);">
                <label style="display: flex; align-items: flex-start; gap: 10px; cursor: pointer; margin: 0;">
                    <input type="checkbox" id="grupo_obligatorio" name="obligatorio" value="1" style="width: 18px; height: 18px; margin-top: 2px; accent-color: var(--color-brand);">
                    <div>
                        <span style="font-size: 13.5px; font-weight: 600; color: var(--color-text-main); display: block;">Respuesta obligatoria</span>
                        <span class="form-hint" style="margin-top: 2px;">El cliente no podrá agregar el plato al pedido sin elegir una opción.</span>
                    </div>
                </label>
            </div>

            <div id="box_multi_limites" style="display: none; grid-template-columns: 1fr 1fr; gap: 16px; margin-top: 18px; padding: 14px; background: var(--color-bg-subtle); border-radius: var(--radius-md);">
                <div class="form-group" style="margin: 0;">
                    <label for="grupo_min" class="form-label">Mínimo de opciones</label>
                    <input type="number" id="grupo_min" name="min_selecciones" class="form-control" value="0" min="0">
                </div>
                <div class="form-group" style="margin: 0;">
                    <label for="grupo_max" class="form-label">Máximo de opciones</label>
                    <input type="number" id="grupo_max" name="max_selecciones" class="form-control" value="3" min="1">
                </div>
            </div>

            <div class="modal-footer" style="margin-top: 28px; padding-top: 20px;">
                <button type="button" class="btn btn-secondary" id="btnCancelarGrupoModal">Cancelar</button>
                <button type="submit" class="btn btn-primary" style="padding: 10px 22px;">Guardar Grupo</button>
            </div>
        </form>
    </div>
</div>

<script nonce="<?= e($cspNonce) ?>">
function abrirGrupoModal() {
    const modal = document.getElementById('modal_grupo');
    document.getElementById('grupo_modal_title').innerText = 'Nuevo Grupo de Modificadores';
    document.getElementById('grupo_id').value = '0';
    document.getElementById('grupo_nombre').value = '';
    document.getElementById('grupo_tipo').value = 'unica';
    document.getElementById('grupo_obligatorio').checked = false;
    document.getElementById('grupo_min').value = '0';
    document.getElementById('grupo_max').value = '3';
    toggleTipoGrupo('unica');
    modal.classList.add('is-open');
    modal.style.setProperty('display', 'flex', 'important');
    setTimeout(() => {
        const inp = document.getElementById('grupo_nombre');
        if (inp) inp.focus();
    }, 60);
}

function editarGrupo(g) {
    const modal = document.getElementById('modal_grupo');
    document.getElementById('grupo_modal_title').innerText = 'Editar Grupo de Modificadores';
    document.getElementById('grupo_id').value = g.id;
    document.getElementById('grupo_nombre').value = g.nombre;
    document.getElementById('grupo_tipo').value = g.tipo;
    document.getElementById('grupo_obligatorio').checked = g.obligatorio == 1;
    document.getElementById('grupo_min').value = g.min_selecciones;
    document.getElementById('grupo_max').value = g.max_selecciones;
    toggleTipoGrupo(g.tipo);
    modal.classList.add('is-open');
    modal.style.setProperty('display', 'flex', 'important');
    setTimeout(() => {
        const inp = document.getElementById('grupo_nombre');
        if (inp) inp.focus();
    }, 60);
}

function cerrarGrupoModal() {
    const modal = document.getElementById('modal_grupo');
    modal.classList.remove('is-open');
    modal.style.setProperty('display', 'none', 'important');
}

function toggleTipoGrupo(tipo) {
    document.getElementById('box_multi_limites').style.display = tipo === 'multiple' ? 'grid' : 'none';
}

document.getElementById('modal_grupo')?.addEventListener('click', function(e) {
    if (e.target === this) {
        cerrarGrupoModal();
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        cerrarGrupoModal();
    }
});

// Enlace directo de eventos (compatible con CSP)
document.getElementById('grupo_tipo')?.addEventListener('change', function() { toggleTipoGrupo(this.value); });
document.querySelectorAll('.btn-abrir-grupo').forEach(btn => btn.addEventListener('click', abrirGrupoModal));
document.getElementById('btnCancelarGrupoModal')?.addEventListener('click', cerrarGrupoModal);
document.querySelectorAll('.btn-editar-grupo').forEach(btn => {
    btn.addEventListener('click', function() {
        try {
            const raw = this.getAttribute('data-grupo');
            if (raw) editarGrupo(JSON.parse(raw));
        } catch(e) {
            console.error('Error parseando grupo:', e);
        }
    });
});
</script>
