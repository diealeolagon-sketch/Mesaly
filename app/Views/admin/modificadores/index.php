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
    <div class="card empty-state-box p-6">
        <div class="icon-bubble-lg mx-auto mb-4">
            <?= icon('modifiers', 26) ?>
        </div>
        <h3 class="text-md fw-700 text-main m-0 mb-2">
            Aún no tienes grupos de modificadores
        </h3>
        <p class="text-muted text-sm max-w-lg mx-auto mb-5">
            Los modificadores permiten que tus clientes personalicen sus platos (ej: <em>"Término de la carne"</em>, <em>"Tipo de salsa"</em> o <em>"Adición de queso / tocineta"</em>).
        </p>
        <button type="button" class="btn btn-primary btn-abrir-grupo">
            <?= icon('plus', 16) ?>
            <span>Crear Primer Grupo</span>
        </button>
    </div>
<?php else: ?>
    <div class="stack gap-5">
        <?php foreach ($grupos as $grupo): ?>
            <div class="modifier-group-card">
                <!-- Header del Grupo -->
                <div class="modifier-group-header">
                    <div class="modifier-group-title-wrap">
                        <div class="modifier-icon-box">
                            <?= icon('modifiers', 18) ?>
                        </div>
                        <div>
                            <h2 class="modifier-group-title">
                                <?= e($grupo['nombre']) ?>
                            </h2>
                            <div class="flex items-center gap-2 mt-1 flex-wrap">
                                <span class="badge <?= $grupo['tipo'] === 'unica' ? 'badge-neutral' : 'badge-warning' ?>">
                                    <?= $grupo['tipo'] === 'unica' ? 'Selección Única' : 'Selección Múltiple' ?>
                                </span>
                                <?php if (!empty($grupo['obligatorio'])): ?>
                                    <span class="badge badge-danger">Obligatorio</span>
                                <?php else: ?>
                                    <span class="badge badge-neutral">Opcional</span>
                                <?php endif; ?>

                                <?php if ($grupo['tipo'] === 'multiple'): ?>
                                    <span class="text-xs text-muted">
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
                        <form method="POST" action="<?= e(app_url('/admin/modificadores/grupo/' . $grupo['id'] . '/eliminar')) ?>" data-confirm="¿Seguro que deseas eliminar este grupo y todas sus opciones?" class="inline m-0">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-secondary btn-sm text-danger" title="Eliminar grupo completo">
                                <?= icon('trash', 14) ?>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Lista de Opciones -->
                <div class="modifier-options-list">
                    <span class="modifier-options-header">
                        Opciones configuradas (<?= count($grupo['opciones'] ?? []) ?>)
                    </span>

                    <?php if (empty($grupo['opciones'])): ?>
                        <div class="modifier-empty-options">
                            Este grupo aún no tiene opciones. Escribe la primera abajo y presiona <strong>Agregar Opción</strong>.
                        </div>
                    <?php else: ?>
                        <div class="stack gap-2">
                            <?php foreach ($grupo['opciones'] as $op): ?>
                                <div class="modifier-option-item">
                                    <div class="flex items-center gap-3">
                                        <span class="bullet-dot"></span>
                                        <span class="modifier-option-name">
                                            <?= e($op['nombre']) ?>
                                        </span>
                                        <?php if ((int)$op['precio_extra'] > 0): ?>
                                            <span class="badge badge-warning text-xs fw-700">
                                                + <?= format_cop($op['precio_extra']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-subtle text-xs fw-500">(Sin costo adicional)</span>
                                        <?php endif; ?>
                                    </div>
                                    <form method="POST" action="<?= e(app_url('/admin/modificadores/opcion/' . $op['id'] . '/eliminar')) ?>" data-confirm="¿Eliminar esta opción?" class="inline m-0">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-secondary btn-sm text-danger btn-sm-icon" title="Quitar opción">
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

                    <input type="text" name="nombre" class="form-control flex-2 min-w-220" placeholder="Ej: Tocineta crujiente, Término medio, Queso cheddar" required>

                    <div class="price-input-wrapper">
                        <span class="price-prefix">+$</span>
                        <input type="number" name="precio_extra" class="form-control price-input-field tabular" placeholder="0" min="0" step="100" value="0" title="Costo extra en pesos colombianos">
                    </div>

                    <button type="submit" class="btn btn-primary btn-sm btn-action-add">
                        <?= icon('plus', 14) ?>
                        <span>Agregar Opción</span>
                    </button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- MODAL GRUPO -->
<div id="modal_grupo" class="modal-backdrop modal-backdrop-modern">
    <div class="modal-dialog-modern modal-dialog-lg">
        <form method="POST" action="<?= e(app_url('/admin/modificadores/grupo/guardar')) ?>">
            <?= csrf_field() ?>
            <input type="hidden" id="grupo_id" name="id" value="0">

            <div class="modal-header-modern">
                <div class="modal-brand-header">
                    <div class="modal-brand-header-left">
                        <img src="<?= e(asset_url('/assets/img/logo-horizontal.svg')) ?>" alt="Mesaly" class="modal-brand-mesaly-logo">
                        <div class="modal-brand-divider"></div>
                        <div class="modal-brand-titles">
                            <h3 id="grupo_modal_title" class="modal-brand-title">
                                Nuevo Grupo de Modificadores
                            </h3>
                            <p class="modal-brand-subtitle">
                                Configura el tipo de pregunta y reglas que verá el comensal.
                            </p>
                        </div>
                    </div>
                    <button type="button" class="modal-btn-close-circle" onclick="cerrarGrupoModal()" aria-label="Cerrar modal">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="modal-body-modern">
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

                <div class="form-group toggle-card-box">
                    <label class="toggle-card-label">
                        <input type="checkbox" id="grupo_obligatorio" name="obligatorio" value="1" class="form-checkbox">
                        <div>
                            <span class="toggle-card-title">Respuesta obligatoria</span>
                            <span class="form-hint mt-1">El cliente no podrá agregar el plato al pedido sin elegir una opción.</span>
                        </div>
                    </label>
                </div>

                <div id="box_multi_limites" class="multi-limits-grid">
                    <div class="form-group m-0">
                        <label for="grupo_min" class="form-label">Mínimo de opciones</label>
                        <input type="number" id="grupo_min" name="min_selecciones" class="form-control" value="0" min="0">
                    </div>
                    <div class="form-group m-0">
                        <label for="grupo_max" class="form-label">Máximo de opciones</label>
                        <input type="number" id="grupo_max" name="max_selecciones" class="form-control" value="3" min="1">
                    </div>
                </div>
            </div>

            <div class="modal-footer-modern">
                <button type="button" class="btn btn-secondary" id="btnCancelarGrupoModal">Cancelar</button>
                <button type="submit" class="btn btn-primary btn-save-action">Guardar Grupo</button>
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
    modal.classList.add('is-open', 'is-active');
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
    modal.classList.add('is-open', 'is-active');
    setTimeout(() => {
        const inp = document.getElementById('grupo_nombre');
        if (inp) inp.focus();
    }, 60);
}

function cerrarGrupoModal() {
    const modal = document.getElementById('modal_grupo');
    modal.classList.remove('is-open', 'is-active');
}

function toggleTipoGrupo(tipo) {
    const box = document.getElementById('box_multi_limites');
    if (box) {
        box.classList.toggle('is-active', tipo === 'multiple');
    }
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