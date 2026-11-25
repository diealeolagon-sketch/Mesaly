<div class="content-header mb-5">
    <div>
        <div class="flex items-center gap-2 mb-1">
            <a href="<?= e(app_url('/superadmin')) ?>" class="text-xs text-muted">
                Super Admin &rarr;
            </a>
            <span class="text-subtle text-xs">Logs del Sistema</span>
        </div>
        <h1 class="page-title text-2xl mb-1">
            Logs del Sistema &amp; Auditoría
        </h1>
        <p class="page-subtitle text-base text-muted m-0">
            Registro cronológico inmutable de actividades, pagos de suscripción, inicios de sesión y cambios de estado en toda la red.
        </p>
    </div>

    <div class="content-actions flex items-center gap-2">
        <a href="<?= e(app_url('/superadmin')) ?>" class="btn btn-secondary btn-sm">
            <?= icon('arrow-left', 14) ?>
            <span>Volver a Vencimientos</span>
        </a>
    </div>
</div>

<!-- KPI CARDS -->
<div class="kpi-grid mb-5">
    <div class="kpi-card-modern">
        <div class="kpi-icon-badge kpi-icon-brand">
            <?= icon('settings', 20) ?>
        </div>
        <span class="text-xs fw-600 text-muted">Total Eventos Registrados</span>
        <strong class="text-2xl text-main tabular">
            <?= number_format($stats['total_logs']) ?>
        </strong>
        <span class="text-xs text-muted">
            Auditoría global persistida
        </span>
    </div>

    <div class="kpi-card-modern">
        <div class="kpi-icon-badge kpi-icon-success">
            <?= icon('zap', 20) ?>
        </div>
        <span class="text-xs fw-600 text-muted">Eventos Hoy</span>
        <strong class="text-2xl text-success tabular">
            <?= number_format($stats['logs_hoy']) ?>
        </strong>
        <span class="text-xs text-muted">
            Actividad en las últimas 24 horas
        </span>
    </div>

    <div class="kpi-card-modern">
        <div class="kpi-icon-badge kpi-icon-info">
            <?= icon('lock', 20) ?>
        </div>
        <span class="text-xs fw-600 text-muted">Inicios de Sesión Hoy</span>
        <strong class="text-2xl text-main tabular">
            <?= (int)$stats['logins_exitosos'] ?>
            <span class="text-xs text-muted fw-400">exitosos</span>
        </strong>
        <span class="text-xs <?= $stats['logins_fallidos'] > 0 ? 'text-danger fw-600' : 'text-muted' ?>">
            <?= (int)$stats['logins_fallidos'] ?> intentos fallidos
        </span>
    </div>

    <div class="kpi-card-modern">
        <div class="kpi-icon-badge kpi-icon-cash">
            <?= icon('cash', 20) ?>
        </div>
        <span class="text-xs fw-600 text-muted">Pagos Auditados</span>
        <strong class="text-2xl text-brand tabular">
            <?= (int)$stats['total_pagos'] ?>
        </strong>
        <span class="text-xs text-muted">
            Transacciones Wompi / Suscripción
        </span>
    </div>
</div>

<!-- FILTROS DE BÚSQUEDA -->
<div class="card mb-5 p-4">
    <form method="GET" action="<?= e(app_url('/superadmin/logs')) ?>" class="flex items-end gap-3 flex-wrap">
        <div class="form-group mb-0 flex-1 min-w-200">
            <label class="form-label text-xs fw-600 text-muted mb-1" for="filterQ">Buscar en logs</label>
            <input type="text" id="filterQ" name="q" class="form-input form-input-sm" 
                   placeholder="Acción, usuario, email, IP..." 
                   value="<?= e($filtros['q'] ?? '') ?>">
        </div>

        <div class="form-group mb-0 min-w-180">
            <label class="form-label text-xs fw-600 text-muted mb-1" for="filterRest">Restaurante</label>
            <select id="filterRest" name="restaurante_id" class="form-select form-select-sm">
                <option value="">Todos los restaurantes</option>
                <?php foreach ($restaurantes as $r): ?>
                    <option value="<?= (int)$r['id'] ?>" <?= (!empty($filtros['restaurante_id']) && (int)$filtros['restaurante_id'] === (int)$r['id']) ? 'selected' : '' ?>>
                        <?= e($r['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group mb-0 min-w-160">
            <label class="form-label text-xs fw-600 text-muted mb-1" for="filterAccion">Tipo de Evento</label>
            <select id="filterAccion" name="accion" class="form-select form-select-sm">
                <option value="">Todas las acciones</option>
                <?php foreach ($accionesUnicas as $acc): ?>
                    <option value="<?= e($acc) ?>" <?= (!empty($filtros['accion']) && $filtros['accion'] === $acc) ? 'selected' : '' ?>>
                        <?= e($acc) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group mb-0">
            <label class="form-label text-xs fw-600 text-muted mb-1" for="filterDesde">Desde</label>
            <input type="date" id="filterDesde" name="desde" class="form-input form-input-sm" 
                   value="<?= e($filtros['desde'] ?? '') ?>">
        </div>

        <div class="form-group mb-0">
            <label class="form-label text-xs fw-600 text-muted mb-1" for="filterHasta">Hasta</label>
            <input type="date" id="filterHasta" name="hasta" class="form-input form-input-sm" 
                   value="<?= e($filtros['hasta'] ?? '') ?>">
        </div>

        <div class="flex items-center gap-2">
            <button type="submit" class="btn btn-primary btn-sm fw-600">
                <?= icon('search', 14) ?>
                <span>Filtrar</span>
            </button>
            <?php if (!empty($filtros['q']) || !empty($filtros['restaurante_id']) || !empty($filtros['accion']) || !empty($filtros['desde']) || !empty($filtros['hasta'])): ?>
                <a href="<?= e(app_url('/superadmin/logs')) ?>" class="btn btn-secondary btn-sm" title="Limpiar filtros">
                    <?= icon('x', 14) ?>
                    <span>Limpiar</span>
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- TABS DE NAVEGACIÓN DE LOGS -->
<div class="card p-0 mb-5 overflow-hidden">
    <div class="flex items-center justify-between border-b px-4 py-3 bg-subtle">
        <div class="flex items-center gap-3">
            <button type="button" class="btn btn-sm btn-tab active" id="tabAuditoriaBtn" onclick="switchLogsTab('auditoria')">
                <?= icon('file-text', 14) ?>
                <span>Auditoría de Eventos (<?= count($logs) ?>)</span>
            </button>
            <button type="button" class="btn btn-sm btn-tab" id="tabLoginBtn" onclick="switchLogsTab('login')">
                <?= icon('lock', 14) ?>
                <span>Intentos de Login (<?= count($loginLogs) ?>)</span>
            </button>
        </div>
        <span class="text-xs text-muted">Mostrando hasta 200 registros más recientes</span>
    </div>

    <!-- SECCIÓN 1: AUDITORÍA GENERAL -->
    <div id="seccionAuditoria" class="table-responsive">
        <?php if (empty($logs)): ?>
            <div class="p-6 text-center text-muted">
                <p class="m-0">No se encontraron eventos de auditoría con los filtros seleccionados.</p>
            </div>
        <?php else: ?>
            <table class="table table-hover align-middle m-0">
                <thead>
                    <tr class="text-xs text-muted text-uppercase">
                        <th>Fecha &amp; Hora</th>
                        <th>Acción</th>
                        <th>Entidad</th>
                        <th>Restaurante</th>
                        <th>Usuario Responsable</th>
                        <th>IP</th>
                        <th class="text-right">Detalle</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    <?php foreach ($logs as $log): ?>
                        <?php
                        $accionLower = strtolower($log['accion']);
                        $badgeClass = 'badge-pill';
                        if (str_contains($accionLower, 'pago') || str_contains($accionLower, 'crear') || str_contains($accionLower, 'activado')) {
                            $badgeClass .= ' success';
                        } elseif (str_contains($accionLower, 'eliminar') || str_contains($accionLower, 'bloqueado') || str_contains($accionLower, 'suspendido') || str_contains($accionLower, 'cancelado')) {
                            $badgeClass .= ' danger';
                        } elseif (str_contains($accionLower, 'vencimiento') || str_contains($accionLower, 'extender') || str_contains($accionLower, 'alerta')) {
                            $badgeClass .= ' warning';
                        } else {
                            $badgeClass .= ' info';
                        }
                        ?>
                        <tr>
                            <td class="tabular whitespace-nowrap text-xs text-muted">
                                <?= date('d/m/Y H:i:s', strtotime($log['created_at'])) ?>
                            </td>
                            <td>
                                <span class="<?= $badgeClass ?>">
                                    <?= e($log['accion']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="fw-600 text-main"><?= e($log['entidad']) ?></span>
                                <?php if (!empty($log['entidad_id'])): ?>
                                    <span class="text-xs text-muted">#<?= (int)$log['entidad_id'] ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($log['restaurante_nombre'])): ?>
                                    <span class="fw-600 text-brand"><?= e($log['restaurante_nombre']) ?></span>
                                <?php else: ?>
                                    <span class="text-muted text-xs">Global / Plataforma</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($log['usuario_nombre'])): ?>
                                    <div>
                                        <span class="fw-600 text-main"><?= e($log['usuario_nombre']) ?></span>
                                        <span class="text-xs text-muted block"><?= e($log['usuario_email']) ?></span>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted text-xs">Sistema / Anónimo</span>
                                <?php endif; ?>
                            </td>
                            <td class="tabular text-xs text-muted">
                                <?= e($log['ip']) ?>
                            </td>
                            <td class="text-right">
                                <?php if (!empty($log['despues']) || !empty($log['antes'])): ?>
                                    <button type="button" class="btn btn-secondary btn-xs" 
                                            onclick='verDetalleLog(<?= json_encode($log, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'>
                                        <?= icon('eye', 13) ?>
                                        <span>Ver Datos</span>
                                    </button>
                                <?php else: ?>
                                    <span class="text-xs text-muted">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- SECCIÓN 2: INTENTOS DE LOGIN -->
    <div id="seccionLogin" class="table-responsive is-hidden">
        <?php if (empty($loginLogs)): ?>
            <div class="p-6 text-center text-muted">
                <p class="m-0">No hay registros recientes de inicio de sesión.</p>
            </div>
        <?php else: ?>
            <table class="table table-hover align-middle m-0">
                <thead>
                    <tr class="text-xs text-muted text-uppercase">
                        <th>Fecha &amp; Hora</th>
                        <th>Email Ingresado</th>
                        <th>Resultado</th>
                        <th>IP de Origen</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    <?php foreach ($loginLogs as $ll): ?>
                        <tr>
                            <td class="tabular whitespace-nowrap text-xs text-muted">
                                <?= date('d/m/Y H:i:s', strtotime($ll['created_at'])) ?>
                            </td>
                            <td class="fw-600 text-main">
                                <?= e($ll['email']) ?>
                            </td>
                            <td>
                                <?php if (!empty($ll['exitoso'])): ?>
                                    <span class="badge-pill success">
                                        <?= icon('check', 12) ?>
                                        <span>Exitoso</span>
                                    </span>
                                <?php else: ?>
                                    <span class="badge-pill danger">
                                        <?= icon('x', 12) ?>
                                        <span>Fallido / Credencial inválida</span>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="tabular text-xs text-muted">
                                <?= e($ll['ip']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<!-- MODAL DE DETALLE DE AUDITORÍA -->
<div class="modal-backdrop modal-backdrop-modern is-hidden" id="modalDetalleLog" role="dialog" aria-modal="true" onclick="if(event.target===this) cerrarDetalleLog()">
    <div class="modal-dialog-modern max-w-600">
        <div class="modal-header-modern">
            <div class="modal-brand-header">
                <div class="modal-brand-header-left">
                    <img src="<?= e(asset_url('/assets/img/logo-horizontal.svg')) ?>" alt="Mesaly" class="modal-brand-mesaly-logo">
                    <span class="modal-brand-divider"></span>
                    <div class="modal-brand-titles">
                        <h3 class="modal-brand-title" id="modalLogTitulo">Detalle de Auditoría</h3>
                        <span class="modal-brand-subtitle" id="modalLogSubtitulo"></span>
                    </div>
                </div>
                <button type="button" class="modal-btn-close-circle" onclick="cerrarDetalleLog()" aria-label="Cerrar modal" title="Cerrar">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
        </div>
        <div class="modal-body-modern">
            <div id="modalLogAntesWrap" class="mb-3 is-hidden">
                <label class="text-xs fw-600 text-muted block mb-1">Estado Anterior (Antes):</label>
                <pre id="modalLogAntes" class="code-block text-xs p-3 bg-subtle border rounded overflow-x-auto"></pre>
            </div>
            <div id="modalLogDespuesWrap">
                <label class="text-xs fw-600 text-muted block mb-1">Carga Registrada (Después / Payload):</label>
                <pre id="modalLogDespues" class="code-block text-xs p-3 bg-subtle border rounded overflow-x-auto"></pre>
            </div>
        </div>
        <div class="modal-footer-modern">
            <button type="button" class="btn btn-secondary btn-sm" onclick="cerrarDetalleLog()">Cerrar</button>
        </div>
    </div>
</div>

<script nonce="<?= e($cspNonce) ?>">
function switchLogsTab(tab) {
    const secAud = document.getElementById('seccionAuditoria');
    const secLog = document.getElementById('seccionLogin');
    const btnAud = document.getElementById('tabAuditoriaBtn');
    const btnLog = document.getElementById('tabLoginBtn');

    if (tab === 'auditoria') {
        secAud.classList.remove('is-hidden');
        secLog.classList.add('is-hidden');
        btnAud.classList.add('active');
        btnLog.classList.remove('active');
    } else {
        secAud.classList.add('is-hidden');
        secLog.classList.remove('is-hidden');
        btnAud.classList.remove('active');
        btnLog.classList.add('active');
    }
}

function verDetalleLog(log) {
    document.getElementById('modalLogTitulo').textContent = 'Evento: ' + log.accion;
    document.getElementById('modalLogSubtitulo').textContent = 'Entidad: ' + log.entidad + ' #' + (log.entidad_id || '') + ' | ' + log.created_at;

    const antesWrap = document.getElementById('modalLogAntesWrap');
    const antesPre = document.getElementById('modalLogAntes');
    if (log.antes) {
        try {
            antesPre.textContent = JSON.stringify(JSON.parse(log.antes), null, 2);
        } catch(e) {
            antesPre.textContent = log.antes;
        }
        antesWrap.classList.remove('is-hidden');
    } else {
        antesWrap.classList.add('is-hidden');
    }

    const despuesPre = document.getElementById('modalLogDespues');
    if (log.despues) {
        try {
            despuesPre.textContent = JSON.stringify(JSON.parse(log.despues), null, 2);
        } catch(e) {
            despuesPre.textContent = log.despues;
        }
    } else {
        despuesPre.textContent = '(Sin datos adicionales)';
    }

    const modal = document.getElementById('modalDetalleLog');
    modal.classList.remove('is-hidden');
    modal.classList.add('is-open', 'is-active');
}

function cerrarDetalleLog() {
    const modal = document.getElementById('modalDetalleLog');
    modal.classList.add('is-hidden');
    modal.classList.remove('is-open', 'is-active');
}
</script>
