<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
    <div>
        <span style="font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; color: var(--brand-primary);">
            Personalización de Platos
        </span>
        <h1 style="font-size: 26px; margin: 4px 0 6px 0; color: var(--admin-text-main);">
            Grupos de Modificadores y Adicionales
        </h1>
        <p style="color: var(--admin-text-muted); font-size: 14px; margin: 0;">
            Configura opciones para que tus clientes personalicen sus platos (términos de cocción, salsas, o extras con precio adicional).
        </p>
    </div>
    <button type="button" class="btn-modern btn-modern-primary" onclick="abrirGrupoModal()">
        ⚙️ + Nuevo Grupo de Modificadores
    </button>
</div>

<!-- EXPLAINER: ¿QUÉ ES UN GRUPO DE MODIFICADORES? -->
<div class="guide-banner">
    <div class="guide-icon">💡</div>
    <div>
        <h3 class="guide-title">¿Cómo funcionan los modificadores en tu menú?</h3>
        <p class="guide-desc">
            Son preguntas u opciones que le aparecen al cliente al tocar un plato antes de agregarlo al carrito:<br>
            • <strong>Selección Única:</strong> El cliente debe elegir exactamente una opción (ej: <em>Término de la carne</em> o <em>Tipo de leche en café</em>).<br>
            • <strong>Selección Múltiple:</strong> El cliente puede marcar varias adiciones con precio adicional (ej: <em>+ Tocineta $4.000</em>, <em>+ Queso fundido $3.500</em>).
        </p>
    </div>
</div>

<?php if (empty($grupos)): ?>
    <div class="modern-card" style="text-align: center; padding: 48px 24px;">
        <span style="font-size: 42px; display: block; margin-bottom: 12px;">⚙️</span>
        <h3 style="font-size: 18px; margin-bottom: 6px;">Aún no tienes grupos de modificadores creados</h3>
        <p style="color: var(--admin-text-muted); font-size: 14px; max-width: 480px; margin: 0 auto 20px auto;">
            Crea grupos como "Término de la carne" o "Adicionales parrilleros" para que tus platos sean personalizables y aumentes el ticket promedio.
        </p>
        <button type="button" class="btn-modern btn-modern-primary" onclick="abrirGrupoModal()">
            + Crear Mi Primer Grupo
        </button>
    </div>
<?php else: ?>
    <div style="display: grid; gap: 24px;">
        <?php foreach ($grupos as $grupo): ?>
            <div class="modern-card" style="margin-bottom: 0;">
                <div class="modern-card-header" style="border-bottom: 1px solid var(--admin-border); padding-bottom: 16px;">
                    <div>
                        <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                            <h3 style="font-size: 18px; margin: 0; color: var(--admin-text-main);"><?= e($grupo['nombre']) ?></h3>
                            <span class="badge" style="background: <?= $grupo['tipo'] === 'unica' ? '#EEF2FF' : '#FEF3C7' ?>; color: <?= $grupo['tipo'] === 'unica' ? '#3730A3' : '#92400E' ?>; font-weight: 700; font-size: 12px;">
                                <?= $grupo['tipo'] === 'unica' ? '🔘 Selección Única' : '☑️ Selección Múltiple (Adiciones)' ?>
                            </span>
                            <?php if (!empty($grupo['obligatorio'])): ?>
                                <span class="badge" style="background: #FEE2E2; color: #991B1B; font-weight: 700; font-size: 12px;">
                                    Obligatorio
                                </span>
                            <?php else: ?>
                                <span class="badge" style="background: #F1F5F9; color: var(--admin-text-muted); font-size: 12px;">
                                    Opcional
                                </span>
                            <?php endif; ?>
                        </div>

                        <?php if ($grupo['tipo'] === 'multiple'): ?>
                            <p style="color: var(--admin-text-muted); font-size: 13px; margin: 6px 0 0 0;">
                                El cliente puede elegir entre <strong><?= (int)$grupo['min_selecciones'] ?></strong> y <strong><?= (int)$grupo['max_selecciones'] ?></strong> opciones.
                            </p>
                        <?php endif; ?>
                    </div>

                    <div style="display: flex; gap: 8px;">
                        <button type="button" class="btn-modern btn-modern-secondary btn-modern-sm" onclick="editarGrupo(<?= e(json_encode($grupo)) ?>)">
                            Editar Grupo
                        </button>
                        <form method="POST" action="<?= e(app_url('/admin/modificadores/grupo/' . $grupo['id'] . '/eliminar')) ?>" onsubmit="return confirm('¿Seguro que deseas eliminar este grupo y todas sus opciones?');">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn-modern btn-modern-danger btn-modern-sm">
                                Eliminar
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Lista de opciones del grupo -->
                <div style="margin-bottom: 20px;">
                    <span style="font-size: 12.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--admin-text-muted); display: block; margin-bottom: 10px;">
                        Opciones disponibles para el cliente:
                    </span>

                    <?php if (empty($grupo['opciones'])): ?>
                        <div style="background: #FFFDF9; border: 1px dashed rgba(232, 93, 4, 0.3); padding: 14px; border-radius: var(--radius-md); font-size: 13.5px; color: var(--admin-text-muted);">
                            Este grupo aún no tiene opciones configuradas. Usa el formulario de abajo para agregar las opciones (ej: <em>Tres cuartos</em> o <em>Queso extra +$3.500</em>).
                        </div>
                    <?php else: ?>
                        <div style="display: grid; gap: 8px;">
                            <?php foreach ($grupo['opciones'] as $op): ?>
                                <div style="display: flex; justify-content: space-between; align-items: center; background: #F8FAFC; padding: 10px 16px; border-radius: var(--radius-sm); border: 1px solid var(--admin-border);">
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <span style="font-size: 14px; font-weight: 600; color: var(--admin-text-main);">
                                            <?= e($op['nombre']) ?>
                                        </span>
                                        <?php if ((int)$op['precio_extra'] > 0): ?>
                                            <span style="color: var(--brand-primary); font-weight: 800; font-size: 13.5px; background: rgba(232, 93, 4, 0.08); padding: 2px 8px; border-radius: var(--radius-pill);">
                                                + <?= format_cop($op['precio_extra']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span style="color: var(--admin-text-subtle); font-size: 12px;">(Sin costo adicional)</span>
                                        <?php endif; ?>
                                    </div>
                                    <form method="POST" action="<?= e(app_url('/admin/modificadores/opcion/' . $op['id'] . '/eliminar')) ?>" onsubmit="return confirm('¿Eliminar esta opción?');">
                                        <?= csrf_field() ?>
                                        <button type="submit" style="background: none; border: none; color: var(--brand-danger); cursor: pointer; font-size: 13px; font-weight: 600; padding: 4px 8px;" title="Quitar opción">
                                            ✕ Eliminar
                                        </button>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Formulario en línea para agregar opción rápida -->
                <form method="POST" action="<?= e(app_url('/admin/modificadores/opcion/guardar')) ?>" style="display: flex; gap: 12px; background: #F1F5F9; padding: 12px 18px; border-radius: var(--radius-md); align-items: center; flex-wrap: wrap;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="grupo_id" value="<?= e($grupo['id']) ?>">
                    <input type="hidden" name="id" value="0">

                    <span style="font-size: 13px; font-weight: 700; color: var(--admin-text-main);">+ Nueva opción:</span>
                    <input type="text" name="nombre" class="form-control" placeholder="Ej: Tocineta ahumada crujiente" style="flex: 2; min-width: 200px; min-height: 38px; padding: 6px 12px;" required>
                    <div style="display: flex; align-items: center; gap: 6px; flex: 1; min-width: 140px;">
                        <span style="font-size: 13px; font-weight: 700; color: var(--admin-text-muted);">+$</span>
                        <input type="number" name="precio_extra" class="form-control" placeholder="0" min="0" step="500" value="0" style="min-height: 38px; padding: 6px 10px;" title="Costo adicional (deja en 0 si no tiene costo extra)">
                    </div>
                    <button type="submit" class="btn-modern btn-modern-primary btn-modern-sm">
                        Guardar Opción
                    </button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- MODAL GRUPO -->
<div id="modal_grupo" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.55); backdrop-filter: blur(4px); align-items: center; justify-content: center; z-index: 1000; padding: 20px;">
    <div class="modern-card" style="max-width: 500px; width: 100%; margin: 0; box-shadow: var(--shadow-elevated);">
        <h3 id="grupo_modal_title" style="font-size: 18px; margin-bottom: 16px;">Nuevo Grupo de Modificadores</h3>
        <form method="POST" action="<?= e(app_url('/admin/modificadores/grupo/guardar')) ?>">
            <?= csrf_field() ?>
            <input type="hidden" id="grupo_id" name="id" value="0">

            <div class="form-group">
                <label for="grupo_nombre" class="form-label">Nombre del Grupo *</label>
                <input type="text" id="grupo_nombre" name="nombre" class="form-control" placeholder="Ej: Término de la carne, Adiciones especiales, Salsas" required autofocus>
            </div>

            <div class="form-group">
                <label for="grupo_tipo" class="form-label">Tipo de Selección del Cliente *</label>
                <select id="grupo_tipo" name="tipo" class="form-control" onchange="toggleTipoGrupo(this.value)" required>
                    <option value="unica">🔘 Selección Única (El cliente escoge exactamente una opción)</option>
                    <option value="multiple">☑️ Selección Múltiple (El cliente puede marcar varias adiciones o ingredientes)</option>
                </select>
            </div>

            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 13.5px;">
                    <input type="checkbox" id="grupo_obligatorio" name="obligatorio" value="1" style="width: 18px; height: 18px;">
                    <span>Es obligatorio que el cliente elija antes de poder agregar al carrito</span>
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

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px;">
                <button type="button" class="btn-modern btn-modern-secondary" onclick="cerrarGrupoModal()">Cancelar</button>
                <button type="submit" class="btn-modern btn-modern-primary">Guardar Grupo</button>
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
