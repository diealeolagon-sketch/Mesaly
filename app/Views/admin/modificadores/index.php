<div class="content-header">
    <div>
        <h1 class="page-title">Grupos de Modificadores</h1>
        <p class="page-subtitle">
            Personaliza opciones para tus platos: términos de cocción, adiciones con costo extra o ingredientes a excluir.
        </p>
    </div>
    <div class="content-actions">
        <button type="button" class="btn btn-primary" onclick="abrirGrupoModal()">
            <?= icon('plus', 16) ?>
            <span>Nuevo Grupo</span>
        </button>
    </div>
</div>

<?php if (empty($grupos)): ?>
    <div class="card" style="text-align: center; padding: 56px 24px;">
        <div style="width: 48px; height: 48px; border-radius: 50%; background: var(--color-bg-subtle); display: inline-flex; align-items: center; justify-content: center; color: var(--color-text-subtle); margin-bottom: 16px;">
            <?= icon('modifiers', 22) ?>
        </div>
        <h3 style="font-size: 16px; font-weight: 700; color: var(--color-text-main); margin: 0 0 6px 0;">
            Aún no tienes grupos de modificadores
        </h3>
        <p style="color: var(--color-text-muted); font-size: 14px; max-width: 460px; margin: 0 auto 20px auto;">
            Crea grupos como <em>"Término de la carne"</em> o <em>"Adiciones parrilleras"</em> para que tus clientes personalicen sus pedidos.
        </p>
        <button type="button" class="btn btn-primary" onclick="abrirGrupoModal()">
            <?= icon('plus', 16) ?>
            <span>Crear Primer Grupo</span>
        </button>
    </div>
<?php else: ?>
    <div style="display: flex; flex-direction: column; gap: 20px;">
        <?php foreach ($grupos as $grupo): ?>
            <div class="card">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; padding-bottom: 16px; border-bottom: 1px solid var(--color-border-subtle); flex-wrap: wrap; gap: 12px;">
                    <div>
                        <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                            <h2 style="font-size: 16px; font-weight: 700; color: var(--color-text-main); margin: 0;">
                                <?= e($grupo['nombre']) ?>
                            </h2>
                            <span class="badge <?= $grupo['tipo'] === 'unica' ? 'badge-neutral' : 'badge-warning' ?>" style="font-weight: 600;">
                                <?= $grupo['tipo'] === 'unica' ? 'Selección Única' : 'Selección Múltiple' ?>
                            </span>
                            <?php if (!empty($grupo['obligatorio'])): ?>
                                <span class="badge badge-danger">Obligatorio</span>
                            <?php else: ?>
                                <span class="badge badge-neutral">Opcional</span>
                            <?php endif; ?>
                        </div>

                        <?php if ($grupo['tipo'] === 'multiple'): ?>
                            <p style="color: var(--color-text-muted); font-size: 12.5px; margin: 6px 0 0 0;">
                                El cliente puede elegir entre <strong><?= (int)$grupo['min_selecciones'] ?></strong> y <strong><?= (int)$grupo['max_selecciones'] ?></strong> opciones.
                            </p>
                        <?php endif; ?>
                    </div>

                    <div style="display: flex; gap: 8px;">
                        <button type="button" class="btn btn-secondary btn-sm" onclick="editarGrupo(<?= e(json_encode($grupo)) ?>)">
                            <?= icon('edit', 14) ?>
                            <span>Editar</span>
                        </button>
                        <form method="POST" action="<?= e(app_url('/admin/modificadores/grupo/' . $grupo['id'] . '/eliminar')) ?>" onsubmit="return confirm('¿Seguro que deseas eliminar este grupo y todas sus opciones?');" style="display: inline;">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-secondary btn-sm" style="color: var(--color-danger); border-color: var(--color-border);" title="Eliminar grupo">
                                <?= icon('trash', 14) ?>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Opciones configuradas -->
                <div style="padding: 16px 0 20px 0;">
                    <span style="font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; color: var(--color-text-muted); display: block; margin-bottom: 10px;">
                        Opciones para el cliente
                    </span>

                    <?php if (empty($grupo['opciones'])): ?>
                        <div style="background: var(--color-bg-subtle); border: 1px dashed var(--color-border); padding: 12px 16px; border-radius: var(--radius-md); font-size: 13px; color: var(--color-text-muted);">
                            Este grupo aún no tiene opciones. Agrega la primera en el formulario siguiente.
                        </div>
                    <?php else: ?>
                        <div style="display: flex; flex-direction: column; gap: 6px;">
                            <?php foreach ($grupo['opciones'] as $op): ?>
                                <div style="display: flex; justify-content: space-between; align-items: center; background: var(--color-bg-subtle); padding: 8px 14px; border-radius: var(--radius-sm); border: 1px solid var(--color-border-subtle);">
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <span style="font-size: 13.5px; font-weight: 600; color: var(--color-text-main);">
                                            <?= e($op['nombre']) ?>
                                        </span>
                                        <?php if ((int)$op['precio_extra'] > 0): ?>
                                            <span style="font-weight: 700; font-size: 12.5px; color: var(--color-brand); font-variant-numeric: tabular-nums;">
                                                + <?= format_cop($op['precio_extra']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span style="color: var(--color-text-subtle); font-size: 12px;">(Sin costo)</span>
                                        <?php endif; ?>
                                    </div>
                                    <form method="POST" action="<?= e(app_url('/admin/modificadores/opcion/' . $op['id'] . '/eliminar')) ?>" onsubmit="return confirm('¿Eliminar esta opción?');" style="display: inline;">
                                        <?= csrf_field() ?>
                                        <button type="submit" style="background: none; border: none; color: var(--color-text-subtle); cursor: pointer; padding: 4px; display: inline-flex; align-items: center;" title="Quitar opción">
                                            <?= icon('x', 14) ?>
                                        </button>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Formulario en línea para agregar opción rápida -->
                <form method="POST" action="<?= e(app_url('/admin/modificadores/opcion/guardar')) ?>" style="display: flex; gap: 10px; background: var(--color-bg-subtle); padding: 12px 16px; border-radius: var(--radius-md); border: 1px solid var(--color-border-subtle); align-items: center; flex-wrap: wrap;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="grupo_id" value="<?= e($grupo['id']) ?>">
                    <input type="hidden" name="id" value="0">

                    <span style="font-size: 13px; font-weight: 600; color: var(--color-text-main); white-space: nowrap;">
                        + Opción:
                    </span>
                    <input type="text" name="nombre" class="form-control" placeholder="Ej: Tocineta ahumada crujiente" style="flex: 2; min-width: 180px;" required>
                    <div style="display: flex; align-items: center; position: relative; flex: 1; min-width: 130px;">
                        <span style="position: absolute; left: 10px; font-size: 12px; font-weight: 600; color: var(--color-text-subtle);">+$</span>
                        <input type="number" name="precio_extra" class="form-control" placeholder="0" min="0" step="100" value="0" style="padding-left: 28px; font-variant-numeric: tabular-nums;" title="Costo adicional en COP">
                    </div>
                    <button type="submit" class="btn btn-secondary btn-sm" style="background: var(--color-surface); font-weight: 600;">
                        Agregar
                    </button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- MODAL GRUPO -->
<div id="modal_grupo" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.45); backdrop-filter: blur(4px); align-items: center; justify-content: center; z-index: 1000; padding: 20px;">
    <div class="card" style="max-width: 480px; width: 100%; margin: 0; box-shadow: var(--shadow-modal);">
        <h3 id="grupo_modal_title" style="font-size: 17px; font-weight: 700; margin: 0 0 16px 0; color: var(--color-text-main);">
            Nuevo Grupo de Modificadores
        </h3>
        <form method="POST" action="<?= e(app_url('/admin/modificadores/grupo/guardar')) ?>">
            <?= csrf_field() ?>
            <input type="hidden" id="grupo_id" name="id" value="0">

            <div class="form-group">
                <label for="grupo_nombre" class="form-label">Nombre del Grupo *</label>
                <input type="text" id="grupo_nombre" name="nombre" class="form-control" placeholder="Ej: Término de la carne, Adiciones, Salsas" required autofocus>
            </div>

            <div class="form-group">
                <label for="grupo_tipo" class="form-label">Tipo de Selección del Cliente *</label>
                <select id="grupo_tipo" name="tipo" class="form-control" onchange="toggleTipoGrupo(this.value)" required>
                    <option value="unica">Selección Única (El comensal escoge exactamente una opción)</option>
                    <option value="multiple">Selección Múltiple (El comensal puede marcar varias adiciones)</option>
                </select>
            </div>

            <div class="form-group" style="margin-top: 14px;">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 13.5px; color: var(--color-text-main);">
                    <input type="checkbox" id="grupo_obligatorio" name="obligatorio" value="1" style="width: 16px; height: 16px; accent-color: var(--color-brand);">
                    <span>Selección obligatoria para agregar el plato al pedido</span>
                </label>
            </div>

            <div id="box_multi_limites" style="display: none; grid-template-columns: 1fr 1fr; gap: 14px; margin-top: 14px;">
                <div class="form-group">
                    <label for="grupo_min" class="form-label">Mínimo de opciones</label>
                    <input type="number" id="grupo_min" name="min_selecciones" class="form-control" value="0" min="0">
                </div>
                <div class="form-group">
                    <label for="grupo_max" class="form-label">Máximo de opciones</label>
                    <input type="number" id="grupo_max" name="max_selecciones" class="form-control" value="3" min="1">
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px; padding-top: 16px; border-top: 1px solid var(--color-border-subtle);">
                <button type="button" class="btn btn-secondary" onclick="cerrarGrupoModal()">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar Grupo</button>
            </div>
        </form>
    </div>
</div>

<script nonce="<?= e($cspNonce) ?>">
function abrirGrupoModal() {
    document.getElementById('grupo_modal_title').innerText = 'Nuevo Grupo de Modificadores';
    document.getElementById('grupo_id').value = '0';
    document.getElementById('grupo_nombre').value = '';
    document.getElementById('grupo_tipo').value = 'unica';
    document.getElementById('grupo_obligatorio').checked = false;
    document.getElementById('grupo_min').value = '0';
    document.getElementById('grupo_max').value = '3';
    toggleTipoGrupo('unica');
    document.getElementById('modal_grupo').style.display = 'flex';
}

function editarGrupo(g) {
    document.getElementById('grupo_modal_title').innerText = 'Editar Grupo de Modificadores';
    document.getElementById('grupo_id').value = g.id;
    document.getElementById('grupo_nombre').value = g.nombre;
    document.getElementById('grupo_tipo').value = g.tipo;
    document.getElementById('grupo_obligatorio').checked = g.obligatorio == 1;
    document.getElementById('grupo_min').value = g.min_selecciones;
    document.getElementById('grupo_max').value = g.max_selecciones;
    toggleTipoGrupo(g.tipo);
    document.getElementById('modal_grupo').style.display = 'flex';
}

function cerrarGrupoModal() {
    document.getElementById('modal_grupo').style.display = 'none';
}

function toggleTipoGrupo(tipo) {
    document.getElementById('box_multi_limites').style.display = tipo === 'multiple' ? 'grid' : 'none';
}
</script>
