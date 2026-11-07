<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
    <div>
        <h2>Grupos de Modificadores y Opciones</h2>
        <p style="color: var(--brand-gray-muted); font-size: 14px;">
            Configura opciones reutilizables para tus platos (ej: término de carne, salsas, o adiciones con costo extra).
        </p>
    </div>
    <button type="button" class="btn btn-primary" onclick="abrirGrupoModal()">
        + Nuevo Grupo de Modificadores
    </button>
</div>

<?php if (empty($grupos)): ?>
    <div class="card" style="text-align: center; padding: 36px 20px;">
        <span style="font-size: 40px; display: block; margin-bottom: 12px;">⚙️</span>
        <h3>Aún no tienes grupos de modificadores</h3>
        <p style="color: var(--brand-gray-muted); font-size: 14px; max-width: 480px; margin: 8px auto 20px auto;">
            Crea grupos como "Término de la carne" o "Adicionales extra" para que tus clientes puedan personalizar sus pedidos desde el carrito.
        </p>
        <button type="button" class="btn btn-primary" onclick="abrirGrupoModal()">
            Crear Mi Primer Grupo
        </button>
    </div>
<?php else: ?>
    <div style="display: grid; gap: 20px;">
        <?php foreach ($grupos as $grupo): ?>
            <div class="card" style="margin-bottom: 0;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1px solid var(--brand-gray-border); padding-bottom: 14px; margin-bottom: 16px; flex-wrap: wrap; gap: 10px;">
                    <div>
                        <h3 style="font-size: 18px; display: inline-block;"><?= e($grupo['nombre']) ?></h3>
                        <span class="badge" style="background: <?= $grupo['tipo'] === 'unica' ? '#EEF2FF' : '#FEF3C7' ?>; color: <?= $grupo['tipo'] === 'unica' ? '#3730A3' : '#92400E' ?>; margin-left: 8px;">
                            <?= $grupo['tipo'] === 'unica' ? 'Selección Única' : 'Selección Múltiple' ?>
                        </span>
                        <?php if (!empty($grupo['obligatorio'])): ?>
                            <span class="badge" style="background: #FEE2E2; color: #991B1B; margin-left: 4px;">
                                Obligatorio
                            </span>
                        <?php else: ?>
                            <span class="badge" style="background: var(--brand-gray-light); color: var(--brand-gray-muted); margin-left: 4px;">
                                Opcional
                            </span>
                        <?php endif; ?>

                        <?php if ($grupo['tipo'] === 'multiple'): ?>
                            <small style="color: var(--brand-gray-muted); display: block; margin-top: 4px;">
                                Mínimo: <?= (int)$grupo['min_selecciones'] ?> · Máximo: <?= (int)$grupo['max_selecciones'] ?> selecciones
                            </small>
                        <?php endif; ?>
                    </div>

                    <div style="display: flex; gap: 8px;">
                        <button type="button" class="btn btn-secondary btn-sm" onclick="editarGrupo(<?= e(json_encode($grupo)) ?>)">
                            Editar Grupo
                        </button>
                        <form method="POST" action="<?= e(app_url('/admin/modificadores/grupo/' . $grupo['id'] . '/eliminar')) ?>" onsubmit="return confirm('¿Seguro que deseas eliminar este grupo y todas sus opciones?');">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-secondary btn-sm" style="color: #DC2626;">
                                Eliminar
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Lista de opciones del grupo -->
                <div style="margin-bottom: 16px;">
                    <strong style="font-size: 13px; color: var(--brand-gray-muted); text-transform: uppercase; letter-spacing: 0.5px;">Opciones configuradas:</strong>

                    <?php if (empty($grupo['opciones'])): ?>
                        <p style="color: var(--brand-gray-muted); font-size: 13px; margin-top: 8px;">
                            Este grupo aún no tiene opciones. Agrega una abajo (ej: Bien cocido, o Tocineta +$3.000).
                        </p>
                    <?php else: ?>
                        <div style="display: grid; gap: 8px; margin-top: 10px;">
                            <?php foreach ($grupo['opciones'] as $op): ?>
                                <div style="display: flex; justify-content: space-between; align-items: center; background: var(--brand-cream); padding: 8px 14px; border-radius: var(--radius-sm); border: 1px solid var(--brand-gray-border);">
                                    <div>
                                        <span style="font-weight: 600; font-size: 14px;"><?= e($op['nombre']) ?></span>
                                        <?php if ((int)$op['precio_extra'] > 0): ?>
                                            <span style="color: var(--brand-primary); font-weight: 700; font-size: 13px; margin-left: 6px;">
                                                + <?= format_cop($op['precio_extra']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span style="color: var(--brand-gray-muted); font-size: 12px; margin-left: 6px;">(Sin costo extra)</span>
                                        <?php endif; ?>
                                    </div>
                                    <form method="POST" action="<?= e(app_url('/admin/modificadores/opcion/' . $op['id'] . '/eliminar')) ?>" onsubmit="return confirm('¿Eliminar esta opción?');">
                                        <?= csrf_field() ?>
                                        <button type="submit" style="background: none; border: none; color: #DC2626; cursor: pointer; font-size: 12px;">
                                            ✕ Quitar
                                        </button>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Formulario en línea para agregar opción rápida -->
                <form method="POST" action="<?= e(app_url('/admin/modificadores/opcion/guardar')) ?>" style="display: flex; gap: 10px; background: var(--brand-gray-light); padding: 10px 14px; border-radius: var(--radius-sm); flex-wrap: wrap;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="grupo_id" value="<?= e($grupo['id']) ?>">
                    <input type="hidden" name="id" value="0">

                    <input type="text" name="nombre" class="form-control" placeholder="Nombre de la opción (ej: Tocineta extra)" style="flex: 2; min-width: 180px; min-height: 36px; padding: 6px 12px;" required>
                    <div style="display: flex; align-items: center; gap: 4px; flex: 1; min-width: 130px;">
                        <span style="font-size: 12px; color: var(--brand-gray-muted);">+$</span>
                        <input type="number" name="precio_extra" class="form-control" placeholder="0" min="0" step="500" value="0" style="min-height: 36px; padding: 6px 10px;">
                    </div>
                    <button type="submit" class="btn btn-secondary btn-sm" style="background: #fff;">
                        + Agregar Opción
                    </button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- MODAL GRUPO -->
<div id="modal_grupo" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); align-items: center; justify-content: center; z-index: 1000; padding: 20px;">
    <div class="card" style="max-width: 480px; width: 100%; margin: 0;">
        <h3 id="grupo_modal_title" style="margin-bottom: 16px;">Nuevo Grupo de Modificadores</h3>
        <form method="POST" action="<?= e(app_url('/admin/modificadores/grupo/guardar')) ?>">
            <?= csrf_field() ?>
            <input type="hidden" id="grupo_id" name="id" value="0">

            <div class="form-group">
                <label for="grupo_nombre" class="form-label">Nombre del Grupo *</label>
                <input type="text" id="grupo_nombre" name="nombre" class="form-control" placeholder="Ej: Término de la carne" required>
            </div>

            <div class="form-group">
                <label for="grupo_tipo" class="form-label">Tipo de Selección *</label>
                <select id="grupo_tipo" name="tipo" class="form-control" onchange="toggleTipoGrupo(this.value)" required>
                    <option value="unica">Selección Única (el cliente escoge exactamente una opción)</option>
                    <option value="multiple">Selección Múltiple (adicionales, salsas, ingredientes extra)</option>
                </select>
            </div>

            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                    <input type="checkbox" id="grupo_obligatorio" name="obligatorio" value="1" style="width: 18px; height: 18px;">
                    <span>Es obligatorio responder este grupo antes de agregar al carrito</span>
                </label>
            </div>

            <div id="box_multi_limites" style="display: none; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 12px;">
                <div class="form-group">
                    <label for="grupo_min" class="form-label">Mínimo de opciones</label>
                    <input type="number" id="grupo_min" name="min_selecciones" class="form-control" value="0" min="0">
                </div>
                <div class="form-group">
                    <label for="grupo_max" class="form-label">Máximo de opciones</label>
                    <input type="number" id="grupo_max" name="max_selecciones" class="form-control" value="3" min="1">
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px;">
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
