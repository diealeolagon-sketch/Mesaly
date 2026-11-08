<div class="content-header">
    <div>
        <h1 class="page-title">Vencimientos y Negocios</h1>
        <p class="page-subtitle">
            Monitoreo en tiempo real de suscripciones, cobros manuales y estado operativo de restaurantes.
        </p>
    </div>

    <div class="content-actions">
        <a href="<?= e(app_url('/superadmin/negocios/crear')) ?>" class="btn btn-primary">
            <?= icon('plus', 16) ?>
            <span>Registrar Nuevo Negocio</span>
        </a>
    </div>
</div>

<!-- 1. KPI CARDS GLOBALES -->
<div class="kpi-grid" style="margin-bottom: 24px;">
    <div class="kpi-card">
        <div class="kpi-label">
            <span>MRR Estimado</span>
            <?= icon('cash', 16) ?>
        </div>
        <div class="kpi-value" style="color: var(--color-brand); font-size: 22px;">
            <?= format_cop($conteo['mrr_estimado']) ?>
        </div>
        <div class="kpi-subtext">Ingreso mensual recurrente pactado</div>
    </div>

    <div class="kpi-card">
        <div class="kpi-label">
            <span>Total Restaurantes</span>
            <?= icon('store', 16) ?>
        </div>
        <div class="kpi-value"><?= (int)$conteo['todos'] ?></div>
        <div class="kpi-subtext"><?= (int)$conteo['al_dia'] ?> al día · <?= (int)$conteo['cancelados'] ?> cancelados</div>
    </div>

    <div class="kpi-card">
        <div class="kpi-label">
            <span>Vencen Esta Semana</span>
            <?= icon('alert', 16) ?>
        </div>
        <div class="kpi-value" style="color: #f59f00;"><?= (int)$conteo['semana'] ?></div>
        <div class="kpi-subtext">Recordar pago preventivo</div>
    </div>

    <div class="kpi-card">
        <div class="kpi-label">
            <span>En Gracia / Bloqueados</span>
            <?= icon('lock', 16) ?>
        </div>
        <div class="kpi-value" style="color: #dc3545;">
            <?= (int)$conteo['gracia'] ?> / <?= (int)$conteo['bloqueados'] ?>
        </div>
        <div class="kpi-subtext">Atención y seguimiento urgente</div>
    </div>
</div>

<!-- 2. BARRA DE FILTROS Y BÚSQUEDA -->
<div class="card" style="padding: 16px 20px; margin-bottom: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px;">
        <!-- Pestañas de estado -->
        <div class="cat-chips-wrap" style="margin: 0;">
            <a href="<?= e(app_url('/superadmin?filtro=todos' . ($busqueda ? '&q=' . urlencode($busqueda) : ''))) ?>" 
               class="cat-chip <?= $filtroActivo === 'todos' ? 'active' : '' ?>">
                Todos (<?= (int)$conteo['todos'] ?>)
            </a>
            <a href="<?= e(app_url('/superadmin?filtro=semana' . ($busqueda ? '&q=' . urlencode($busqueda) : ''))) ?>" 
               class="cat-chip <?= $filtroActivo === 'semana' ? 'active' : '' ?>" style="color: #f59f00;">
                Vencen esta semana (<?= (int)$conteo['semana'] ?>)
            </a>
            <a href="<?= e(app_url('/superadmin?filtro=gracia' . ($busqueda ? '&q=' . urlencode($busqueda) : ''))) ?>" 
               class="cat-chip <?= $filtroActivo === 'gracia' ? 'active' : '' ?>" style="color: #fd7e14;">
                En Gracia (<?= (int)$conteo['gracia'] ?>)
            </a>
            <a href="<?= e(app_url('/superadmin?filtro=bloqueados' . ($busqueda ? '&q=' . urlencode($busqueda) : ''))) ?>" 
               class="cat-chip <?= $filtroActivo === 'bloqueados' ? 'active' : '' ?>" style="color: #dc3545;">
                Bloqueados (<?= (int)$conteo['bloqueados'] ?>)
            </a>
            <a href="<?= e(app_url('/superadmin?filtro=al_dia' . ($busqueda ? '&q=' . urlencode($busqueda) : ''))) ?>" 
               class="cat-chip <?= $filtroActivo === 'al_dia' ? 'active' : '' ?>" style="color: #28a745;">
                Al Día (<?= (int)$conteo['al_dia'] ?>)
            </a>
            <a href="<?= e(app_url('/superadmin?filtro=cancelados' . ($busqueda ? '&q=' . urlencode($busqueda) : ''))) ?>" 
               class="cat-chip <?= $filtroActivo === 'cancelados' ? 'active' : '' ?>">
                Cancelados (<?= (int)$conteo['cancelados'] ?>)
            </a>
        </div>

        <!-- Buscador -->
        <form method="GET" action="<?= e(app_url('/superadmin')) ?>" style="display: flex; gap: 8px;">
            <input type="hidden" name="filtro" value="<?= e($filtroActivo) ?>">
            <input type="text" name="q" value="<?= e($busqueda) ?>" class="form-control" placeholder="Buscar negocio o slug..." style="width: 220px; padding: 7px 12px; font-size: 13px;">
            <button type="submit" class="btn btn-secondary btn-sm" style="padding: 7px 14px;">
                <?= icon('search', 14) ?>
            </button>
            <?php if (!empty($busqueda)): ?>
                <a href="<?= e(app_url('/superadmin?filtro=' . urlencode($filtroActivo))) ?>" class="btn btn-secondary btn-sm" title="Limpiar búsqueda">
                    &times;
                </a>
            <?php endif; ?>
        </form>
    </div>
</div>

<!-- 3. TABLA DE NEGOCIOS Y VENCIMIENTOS -->
<div class="card" style="padding: 0; overflow: hidden;">
    <?php if (empty($negocios)): ?>
        <div style="text-align: center; padding: 60px 24px;">
            <div style="width: 52px; height: 52px; border-radius: 50%; background: var(--color-bg-subtle); display: inline-flex; align-items: center; justify-content: center; color: var(--color-text-subtle); margin-bottom: 16px;">
                <?= icon('store', 24) ?>
            </div>
            <h3 style="font-size: 16px; font-weight: 700; color: var(--color-text-main); margin: 0 0 6px 0;">
                No se encontraron negocios
            </h3>
            <p style="color: var(--color-text-muted); font-size: 14px; margin: 0 auto 20px auto;">
                Prueba ajustando el filtro de búsqueda o registra un nuevo restaurante.
            </p>
            <a href="<?= e(app_url('/superadmin/negocios/crear')) ?>" class="btn btn-primary">
                <?= icon('plus', 16) ?>
                <span>Registrar Nuevo Negocio</span>
            </a>
        </div>
    <?php else: ?>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 32px;"></th>
                        <th>Restaurante</th>
                        <th>Precio Acordado</th>
                        <th>Vencimiento</th>
                        <th>Último Pago</th>
                        <th style="text-align: right;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($negocios as $neg): ?>
                        <?php
                        $est = $neg['estado_suscripcion'];
                        $vencStr = !empty($neg['fecha_vencimiento']) ? date('d/m/Y', strtotime($neg['fecha_vencimiento'])) : 'Sin fecha';
                        $waEnlace = \App\Services\SuscripcionService::generarEnlaceRecordatorioWhatsApp($neg);
                        ?>
                        <tr>
                            <!-- Semáforo de color -->
                            <td style="padding-right: 0;">
                                <span style="width: 10px; height: 10px; border-radius: 50%; background: <?= e($est['color']) ?>; display: inline-block;" 
                                      title="Estado: <?= e($est['label']) ?>"></span>
                            </td>

                            <!-- Nombre y Datos -->
                            <td>
                                <div style="display: flex; flex-direction: column; gap: 2px;">
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <strong style="font-size: 14px; color: var(--color-text-main);">
                                            <?= e($neg['nombre']) ?>
                                        </strong>
                                        <span class="badge" style="background: <?= e($est['bg_color']) ?>; color: <?= e($est['color']) ?>; font-size: 11px; font-weight: 700; padding: 2px 8px;">
                                            <?= e($est['label']) ?>
                                        </span>
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 8px; font-size: 12px; color: var(--color-text-muted);">
                                        <a href="<?= e(app_url('/' . $neg['slug'])) ?>" target="_blank" style="color: var(--color-brand); text-decoration: none;">
                                            mesaly.com/<?= e($neg['slug']) ?> &rarr;
                                        </a>
                                        <span>&bull;</span>
                                        <span><?= e($neg['admin_email'] ?? 'Sin admin') ?></span>
                                        <?php if (!empty($neg['notas_internas'])): ?>
                                            <span>&bull;</span>
                                            <span style="font-style: italic;" title="<?= e($neg['notas_internas']) ?>">
                                                "<?= e(substr($neg['notas_internas'], 0, 30)) ?>..."
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>

                            <!-- Precio Acordado -->
                            <td>
                                <strong style="font-size: 14px; color: var(--color-text-main);">
                                    <?= format_cop((int)($neg['precio'] ?? 49000)) ?>
                                </strong>
                                <span style="display: block; font-size: 12px; color: var(--color-text-muted); text-transform: capitalize;">
                                    Ciclo <?= e($neg['ciclo'] ?? 'mensual') ?>
                                </span>
                            </td>

                            <!-- Fecha Vencimiento y Días Restantes -->
                            <td>
                                <div style="display: flex; flex-direction: column; gap: 2px;">
                                    <strong style="font-size: 13.5px; color: <?= $est['dias_restantes'] < 0 ? '#dc3545' : 'inherit' ?>;">
                                        <?= e($vencStr) ?>
                                    </strong>
                                    <span style="font-size: 12px; color: var(--color-text-muted);">
                                        <?php if ($est['dias_restantes'] > 0): ?>
                                            Faltan <?= (int)$est['dias_restantes'] ?> días
                                        <?php elseif ($est['dias_restantes'] === 0): ?>
                                            <span style="color: #f59f00; font-weight: 700;">Vence hoy</span>
                                        <?php else: ?>
                                            <span style="color: #dc3545; font-weight: 600;">Venció hace <?= abs((int)$est['dias_restantes']) ?> días</span>
                                        <?php endif; ?>
                                    </span>
                                </div>
                            </td>

                            <!-- Último Pago Registrado -->
                            <td>
                                <?php if (!empty($neg['ultimo_pago_fecha'])): ?>
                                    <div style="display: flex; flex-direction: column; gap: 2px;">
                                        <strong style="font-size: 13px; color: var(--color-text-main);">
                                            <?= format_cop((int)$neg['ultimo_pago_monto']) ?>
                                        </strong>
                                        <span style="font-size: 11.5px; color: var(--color-text-muted);">
                                            <?= date('d/m/Y', strtotime($neg['ultimo_pago_fecha'])) ?> (<?= e(ucfirst($neg['ultimo_pago_metodo'] ?? 'transferencia')) ?>)
                                        </span>
                                    </div>
                                <?php else: ?>
                                    <span style="color: var(--color-text-subtle); font-size: 12.5px;">Sin pagos registrados</span>
                                <?php endif; ?>
                            </td>

                            <!-- Acciones por fila -->
                            <td style="text-align: right;">
                                <div class="action-btns" style="justify-content: flex-end; gap: 6px;">
                                    <!-- 1. Registrar Pago -->
                                    <button type="button" 
                                            class="btn btn-primary btn-sm btn-modal-pago" 
                                            data-id="<?= (int)$neg['id'] ?>" 
                                            data-nombre="<?= e($neg['nombre']) ?>" 
                                            data-precio="<?= (int)($neg['precio'] ?? 49000) ?>"
                                            data-ciclo="<?= e($neg['ciclo'] ?? 'mensual') ?>"
                                            title="Registrar pago manual">
                                        <?= icon('cash', 13) ?>
                                        <span>Registrar Pago</span>
                                    </button>

                                    <!-- 2. Recordar Pago (WhatsApp) -->
                                    <a href="<?= e($waEnlace) ?>" 
                                       target="_blank" 
                                       rel="noopener" 
                                       class="btn btn-secondary btn-sm" 
                                       style="color: #25D366; border-color: #25D366;"
                                       title="Enviar recordatorio de cobro por WhatsApp">
                                        <?= icon('whatsapp', 13) ?>
                                        <span>Cobrar</span>
                                    </a>

                                    <!-- 3. Extender Días -->
                                    <button type="button" 
                                            class="btn btn-secondary btn-sm btn-modal-extender" 
                                            data-id="<?= (int)$neg['id'] ?>" 
                                            data-nombre="<?= e($neg['nombre']) ?>" 
                                            title="Extender días de gracia o cortesía">
                                        + Días
                                    </button>

                                    <!-- 4. Suspender / Reactivar Toggle -->
                                    <?php $esSuspendido = ($neg['estado_manual'] === 'suspendido'); ?>
                                    <form method="POST" action="<?= e(app_url('/superadmin/negocios/' . $neg['id'] . '/estado')) ?>" style="display: inline;" 
                                          data-confirm="¿Seguro que deseas <?= $esSuspendido ? 'reactivar' : 'suspender temporalmente' ?> este negocio?">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="estado" value="<?= $esSuspendido ? '' : 'suspendido' ?>">
                                        <button type="submit" class="btn btn-secondary btn-sm" style="<?= $esSuspendido ? 'color: var(--color-success);' : 'color: var(--color-danger);' ?>" 
                                                title="<?= $esSuspendido ? 'Reactivar' : 'Suspender' ?>">
                                            <?= $esSuspendido ? 'Reactivar' : 'Pausar' ?>
                                        </button>
                                    </form>

                                    <!-- 5. Ver Como (Impersonar) -->
                                    <form method="POST" action="<?= e(app_url('/superadmin/negocios/' . $neg['id'] . '/ver-como')) ?>" style="display: inline;">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-secondary btn-sm" title="Ingresar al panel de este restaurante para dar soporte">
                                            Ver como
                                        </button>
                                    </form>

                                    <!-- 6. Editar -->
                                    <a href="<?= e(app_url('/superadmin/negocios/' . $neg['id'] . '/editar')) ?>" class="btn btn-secondary btn-sm" title="Editar configuración">
                                        <?= icon('edit', 13) ?>
                                    </a>

                                    <!-- 7. Reset Password -->
                                    <form method="POST" action="<?= e(app_url('/superadmin/negocios/' . $neg['id'] . '/reset-password')) ?>" style="display: inline;" 
                                          data-confirm="¿Restablecer contraseña del admin de este negocio? Se generará una clave temporal nueva.">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-secondary btn-sm" style="color: var(--color-danger);" title="Reset Clave">
                                            <?= icon('lock', 13) ?>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- ═══════════════════════════════════════════════════════════════════ -->
<!-- MODAL 1: REGISTRAR PAGO (MANUAL)                                   -->
<!-- ═══════════════════════════════════════════════════════════════════ -->
<div class="modal-backdrop" id="modal_pago" style="display: none; align-items: center; justify-content: center; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; padding: 16px;">
    <div class="card" style="width: 100%; max-width: 520px; box-shadow: var(--shadow-xl); max-height: 90vh; overflow-y: auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div style="width: 36px; height: 36px; border-radius: 50%; background: #e8f5e9; color: #28a745; display: flex; align-items: center; justify-content: center;">
                    <?= icon('cash', 18) ?>
                </div>
                <div>
                    <h2 style="font-size: 16px; font-weight: 700; margin: 0; color: var(--color-text-main);">
                        Registrar Pago de Suscripción
                    </h2>
                    <span id="pago_nombre_negocio" style="font-size: 12.5px; color: var(--color-text-muted);"></span>
                </div>
            </div>
            <button type="button" class="btn btn-secondary btn-sm" id="btnCerrarModalPago" style="padding: 4px 8px; font-size: 16px; line-height: 1;">&times;</button>
        </div>

        <form method="POST" id="formRegistrarPago" action="">
            <?= csrf_field() ?>

            <div class="form-group">
                <label for="pago_monto" class="form-label">Monto Recibido (COP) *</label>
                <input type="number" id="pago_monto" name="monto" class="form-control" required min="1000" step="100" style="font-size: 16px; font-weight: 700;">
                <span class="form-hint">Monto real pagado por el restaurante (Nequi, Daviplata, transferencia o efectivo).</span>
            </div>

            <div class="config-grid-2">
                <div class="form-group">
                    <label for="pago_fecha" class="form-label">Fecha de Pago *</label>
                    <input type="date" id="pago_fecha" name="fecha_pago" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>

                <div class="form-group">
                    <label for="pago_metodo" class="form-label">Método de Pago *</label>
                    <select id="pago_metodo" name="metodo" class="form-control" required>
                        <?php foreach ($metodosPago as $k => $v): ?>
                            <option value="<?= e($k) ?>"><?= e($v) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="config-grid-2">
                <div class="form-group">
                    <label for="pago_periodos" class="form-label">Períodos a Extender *</label>
                    <input type="number" id="pago_periodos" name="periodos" class="form-control" value="1" min="1" max="24" required>
                    <span class="form-hint">1 = un mes o un año (según el ciclo).</span>
                </div>

                <div class="form-group">
                    <label for="pago_referencia" class="form-label">Nro. de Comprobante / Ref</label>
                    <input type="text" id="pago_referencia" name="referencia" class="form-control" placeholder="Ej: M128934">
                </div>
            </div>

            <div class="form-group">
                <label for="pago_nota" class="form-label">Notas Internas (Opcional)</label>
                <textarea id="pago_nota" name="nota" class="form-control" rows="2" placeholder="Ej: Pago adelantado de 2 meses por Nequi."></textarea>
            </div>

            <div class="modal-footer" style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px; padding-top: 18px; border-top: 1px solid var(--color-border-subtle);">
                <button type="button" class="btn btn-secondary" id="btnCancelarPago">Cancelar</button>
                <button type="submit" class="btn btn-primary" style="padding: 10px 22px;">Guardar y Extender Vencimiento</button>
            </div>
        </form>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════════════ -->
<!-- MODAL 2: EXTENDER DÍAS DE GRACIA                                   -->
<!-- ═══════════════════════════════════════════════════════════════════ -->
<div class="modal-backdrop" id="modal_extender" style="display: none; align-items: center; justify-content: center; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; padding: 16px;">
    <div class="card" style="width: 100%; max-width: 460px; box-shadow: var(--shadow-xl);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <div style="width: 36px; height: 36px; border-radius: 50%; background: #fff3e0; color: #fd7e14; display: flex; align-items: center; justify-content: center;">
                    <?= icon('clock', 18) ?>
                </div>
                <div>
                    <h2 style="font-size: 16px; font-weight: 700; margin: 0; color: var(--color-text-main);">
                        Extender Días de Gracia
                    </h2>
                    <span id="extender_nombre_negocio" style="font-size: 12.5px; color: var(--color-text-muted);"></span>
                </div>
            </div>
            <button type="button" class="btn btn-secondary btn-sm" id="btnCerrarModalExtender" style="padding: 4px 8px; font-size: 16px; line-height: 1;">&times;</button>
        </div>

        <form method="POST" id="formExtenderDias" action="">
            <?= csrf_field() ?>

            <div class="form-group">
                <label for="extender_dias" class="form-label">Días Adicionales a Otorgar *</label>
                <input type="number" id="extender_dias" name="dias" class="form-control" value="7" min="1" max="90" required style="font-size: 16px; font-weight: 700;">
                <span class="form-hint">Días calendario que se sumarán al vencimiento actual sin generar cobro.</span>
            </div>

            <div class="form-group">
                <label for="extender_motivo" class="form-label">Motivo de la Extensión</label>
                <input type="text" id="extender_motivo" name="motivo" class="form-control" placeholder="Ej: Esperando transferencia bancaria el lunes">
            </div>

            <div class="modal-footer" style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 24px; padding-top: 18px; border-top: 1px solid var(--color-border-subtle);">
                <button type="button" class="btn btn-secondary" id="btnCancelarExtender">Cancelar</button>
                <button type="submit" class="btn btn-primary" style="padding: 10px 22px;">Extender Días</button>
            </div>
        </form>
    </div>
</div>

<script nonce="<?= e($cspNonce) ?>">
document.addEventListener('DOMContentLoaded', function () {
    const modalPago = document.getElementById('modal_pago');
    const formPago = document.getElementById('formRegistrarPago');
    const modalExt = document.getElementById('modal_extender');
    const formExt = document.getElementById('formExtenderDias');
    const baseUrl = <?= json_encode(app_url(''), JSON_UNESCAPED_UNICODE) ?>;

    // Modal Pago
    document.querySelectorAll('.btn-modal-pago').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const nombre = this.getAttribute('data-nombre');
            const precio = this.getAttribute('data-precio');

            document.getElementById('pago_nombre_negocio').textContent = nombre;
            document.getElementById('pago_monto').value = precio;
            formPago.action = baseUrl + '/superadmin/negocios/' + id + '/pago';

            modalPago.style.display = 'flex';
        });
    });

    function cerrarPago() { modalPago.style.display = 'none'; }
    document.getElementById('btnCerrarModalPago')?.addEventListener('click', cerrarPago);
    document.getElementById('btnCancelarPago')?.addEventListener('click', cerrarPago);

    // Modal Extender
    document.querySelectorAll('.btn-modal-extender').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const nombre = this.getAttribute('data-nombre');

            document.getElementById('extender_nombre_negocio').textContent = nombre;
            formExt.action = baseUrl + '/superadmin/negocios/' + id + '/extender';

            modalExt.style.display = 'flex';
        });
    });

    function cerrarExtender() { modalExt.style.display = 'none'; }
    document.getElementById('btnCerrarModalExtender')?.addEventListener('click', cerrarExtender);
    document.getElementById('btnCancelarExtender')?.addEventListener('click', cerrarExtender);

    // Cerrar al clickear backdrop
    [modalPago, modalExt].forEach(modal => {
        modal?.addEventListener('click', function (e) {
            if (e.target === this) this.style.display = 'none';
        });
    });
});
</script>
