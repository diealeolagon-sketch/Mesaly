<div class="content-header mb-5">
    <div>
        <h1 class="page-title text-2xl mb-1">
            Vencimientos y Suscripciones
        </h1>
        <p class="page-subtitle text-base text-muted m-0">
            Monitoreo en tiempo real de contratos, cobranzas manuales y estado operativo de la red gastronómica.
        </p>
    </div>

    <div class="content-actions">
        <a href="<?= e(app_url('/superadmin/negocios/crear')) ?>" class="btn btn-primary btn-register-negocio">
            <?= icon('plus', 16) ?>
            <span>Registrar Nuevo Negocio</span>
        </a>
    </div>
</div>

<!-- 1. KPI CARDS GLOBALES -->
<div class="kpi-grid mb-5">
    <div class="kpi-card-modern">
        <div class="kpi-icon-badge kpi-icon-brand">
            <?= icon('cash', 20) ?>
        </div>
        <span class="text-xs fw-600 text-muted">MRR Estimado</span>
        <strong class="text-2xl text-brand tabular">
            <?= format_cop($conteo['mrr_estimado']) ?>
        </strong>
        <span class="text-xs text-muted">Recurrencia mensual pactada</span>
    </div>

    <div class="kpi-card-modern">
        <div class="kpi-icon-badge kpi-icon-success">
            <?= icon('store', 20) ?>
        </div>
        <span class="text-xs fw-600 text-muted">Restaurantes Activos</span>
        <strong class="text-2xl text-main tabular">
            <?= (int)$conteo['todos'] ?>
        </strong>
        <span class="text-xs text-success fw-600">
            <?= (int)$conteo['al_dia'] ?> al día · <?= (int)$conteo['cancelados'] ?> cancelados
        </span>
    </div>

    <div class="kpi-card-modern">
        <div class="kpi-icon-badge">
            <?= icon('alert', 20) ?>
        </div>
        <span class="text-xs fw-600 text-muted">Vencen Esta Semana</span>
        <strong class="text-2xl text-warning tabular">
            <?= (int)$conteo['semana'] ?>
        </strong>
        <span class="text-xs text-muted">Recordatorio preventivo recomendado</span>
    </div>

    <div class="kpi-card-modern">
        <div class="kpi-icon-badge">
            <?= icon('lock', 20) ?>
        </div>
        <span class="text-xs fw-600 text-muted">En Gracia / Bloqueados</span>
        <strong class="text-2xl text-danger tabular">
            <?= (int)$conteo['gracia'] ?> <span class="text-base fw-500 text-subtle">/</span> <?= (int)$conteo['bloqueados'] ?>
        </strong>
        <span class="text-xs text-danger fw-600">Gestión de cobranza requerida</span>
    </div>
</div>

<!-- 2. BARRA DE FILTROS Y BÚSQUEDA -->
<div class="filter-bar-card">
    <!-- Pestañas de estado -->
    <div class="filter-pills-group">
        <a href="<?= e(app_url('/superadmin?filtro=todos' . ($busqueda ? '&q=' . urlencode($busqueda) : ''))) ?>" 
           class="filter-pill <?= $filtroActivo === 'todos' ? 'active' : '' ?>">
            <span>Todos</span>
            <span class="filter-count"><?= (int)$conteo['todos'] ?></span>
        </a>
        <a href="<?= e(app_url('/superadmin?filtro=semana' . ($busqueda ? '&q=' . urlencode($busqueda) : ''))) ?>" 
           class="filter-pill <?= $filtroActivo === 'semana' ? 'active' : '' ?>">
            <span>Vencen esta semana</span>
            <span class="filter-count"><?= (int)$conteo['semana'] ?></span>
        </a>
        <a href="<?= e(app_url('/superadmin?filtro=gracia' . ($busqueda ? '&q=' . urlencode($busqueda) : ''))) ?>" 
           class="filter-pill <?= $filtroActivo === 'gracia' ? 'active' : '' ?>">
            <span>En Gracia</span>
            <span class="filter-count"><?= (int)$conteo['gracia'] ?></span>
        </a>
        <a href="<?= e(app_url('/superadmin?filtro=bloqueados' . ($busqueda ? '&q=' . urlencode($busqueda) : ''))) ?>" 
           class="filter-pill <?= $filtroActivo === 'bloqueados' ? 'active' : '' ?>">
            <span>Bloqueados</span>
            <span class="filter-count"><?= (int)$conteo['bloqueados'] ?></span>
        </a>
        <a href="<?= e(app_url('/superadmin?filtro=al_dia' . ($busqueda ? '&q=' . urlencode($busqueda) : ''))) ?>" 
           class="filter-pill <?= $filtroActivo === 'al_dia' ? 'active' : '' ?>">
            <span>Al Día</span>
            <span class="filter-count"><?= (int)$conteo['al_dia'] ?></span>
        </a>
        <a href="<?= e(app_url('/superadmin?filtro=cancelados' . ($busqueda ? '&q=' . urlencode($busqueda) : ''))) ?>" 
           class="filter-pill <?= $filtroActivo === 'cancelados' ? 'active' : '' ?>">
            <span>Cancelados</span>
            <span class="filter-count"><?= (int)$conteo['cancelados'] ?></span>
        </a>
    </div>

    <!-- Buscador -->
    <form method="GET" action="<?= e(app_url('/superadmin')) ?>" class="filter-search-form">
        <input type="hidden" name="filtro" value="<?= e($filtroActivo) ?>">
        <div class="filter-search-box">
            <span class="filter-search-icon">
                <?= icon('search', 14) ?>
            </span>
            <input type="text" name="q" value="<?= e($busqueda) ?>" class="filter-search-input" placeholder="Buscar negocio o slug...">
            <?php if (!empty($busqueda)): ?>
                <a href="<?= e(app_url('/superadmin?filtro=' . urlencode($filtroActivo))) ?>" class="filter-search-clear" title="Limpiar búsqueda">
                    &times;
                </a>
            <?php endif; ?>
        </div>
        <button type="submit" class="btn btn-secondary btn-sm filter-search-btn">
            Buscar
        </button>
    </form>
</div>

<!-- 3. TABLA DE NEGOCIOS Y VENCIMIENTOS -->
<div class="card p-0 overflow-hidden">
    <?php if (empty($negocios)): ?>
        <div class="empty-state-box p-6">
            <div class="icon-bubble-lg mx-auto mb-4">
                <?= icon('store', 24) ?>
            </div>
            <h3 class="text-md fw-700 text-main m-0 mb-1">
                No se encontraron negocios con estos criterios
            </h3>
            <p class="text-muted text-sm max-w-md mx-auto mb-5">
                Prueba seleccionando otro filtro o registra un nuevo restaurante.
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
                        <th>Restaurante / Slug</th>
                        <th>Estado</th>
                        <th>Tarifa</th>
                        <th>Vencimiento</th>
                        <th>Último Pago</th>
                        <th class="text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($negocios as $neg): ?>
                        <?php
                        $est = $neg['estado_suscripcion'];
                        $vencStr = !empty($neg['fecha_vencimiento']) ? date('d/m/Y', strtotime($neg['fecha_vencimiento'])) : 'Sin fecha';
                        $waEnlace = \App\Services\SuscripcionService::generarEnlaceRecordatorioWhatsApp($neg);
                        $pillClass = match ($est['codigo']) {
                            \App\Services\SuscripcionService::ESTADO_AL_DIA => 'al-dia',
                            \App\Services\SuscripcionService::ESTADO_POR_VENCER => 'por-vencer',
                            \App\Services\SuscripcionService::ESTADO_EN_GRACIA => 'en-gracia',
                            \App\Services\SuscripcionService::ESTADO_BLOQUEADO => 'bloqueado',
                            \App\Services\SuscripcionService::ESTADO_SUSPENDIDO => 'suspendido',
                            default => 'cancelado',
                        };
                        ?>
                        <tr>
                            <!-- Nombre, Slug y Admin -->
                            <td>
                                <div class="stack gap-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <strong class="text-main text-sm">
                                            <?= e($neg['nombre']) ?>
                                        </strong>
                                        <span class="badge badge-subtle-brand text-xs py-0 px-1 capitalize">
                                            <?= e($neg['plan'] ?? 'Pro') ?><?= !empty($neg['limite_mesas']) ? ": {$neg['limite_mesas']} mesas" : '' ?>
                                        </span>
                                        <?php if (!empty($neg['notas_internas'])): ?>
                                            <span class="badge badge-warning text-xs py-0 px-1" title="<?= e($neg['notas_internas']) ?>">
                                                Nota
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex items-center gap-1 text-xs text-muted">
                                        <a href="<?= e(app_url('/' . $neg['slug'])) ?>" target="_blank" rel="noopener" class="text-brand fw-600">
                                            /<?= e($neg['slug']) ?> &rarr;
                                        </a>
                                        <span>&bull;</span>
                                        <span><?= e($neg['admin_email'] ?? 'Sin admin') ?></span>
                                    </div>
                                </div>
                            </td>

                            <!-- Semáforo / Estado de Suscripción -->
                            <td>
                                <div class="status-pill <?= $pillClass ?>">
                                    <span class="status-pill-dot"></span>
                                    <span><?= e($est['label']) ?></span>
                                </div>
                            </td>

                            <!-- Tarifa: Precio Acordado (Básico) vs Precio Fijo (Pro / Cadena) -->
                            <td>
                                <strong class="text-sm text-main tabular">
                                    <?= format_cop((int)($neg['precio'] ?? 79000)) ?>
                                </strong>
                                <div class="mt-1">
                                    <?php if (($neg['plan'] ?? '') === 'basico'): ?>
                                        <span class="badge badge-warning text-xs font-semibold" title="Tarifa acordada según número de mesas (Básico)">
                                            Precio Acordado
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-subtle-brand text-xs font-semibold" title="Tarifa estándar fija de catálogo">
                                            Precio Fijo
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <span class="block text-xs text-muted capitalize mt-1">
                                    Ciclo <?= e($neg['ciclo'] ?? 'mensual') ?>
                                </span>
                            </td>

                            <!-- Fecha Vencimiento y Días Restantes -->
                            <td>
                                <div class="stack gap-1">
                                    <strong class="text-sm tabular <?= $est['dias_restantes'] < 0 ? 'text-danger' : 'text-main' ?>">
                                        <?= e($vencStr) ?>
                                    </strong>
                                    <span class="text-xs text-muted">
                                        <?php if ($est['dias_restantes'] > 0): ?>
                                            Faltan <?= (int)$est['dias_restantes'] ?> días
                                        <?php elseif ($est['dias_restantes'] === 0): ?>
                                            <span class="text-warning fw-700">Vence hoy</span>
                                        <?php else: ?>
                                            <span class="text-danger fw-600">Venció hace <?= abs((int)$est['dias_restantes']) ?> días</span>
                                        <?php endif; ?>
                                    </span>
                                </div>
                            </td>

                            <!-- Último Pago Registrado -->
                            <td>
                                <?php if (!empty($neg['ultimo_pago_fecha'])): ?>
                                    <div class="stack gap-1">
                                        <strong class="text-xs text-success tabular">
                                            <?= format_cop((int)$neg['ultimo_pago_monto']) ?>
                                        </strong>
                                        <span class="text-xs text-muted">
                                            <?= date('d/m/Y', strtotime($neg['ultimo_pago_fecha'])) ?> (<?= e(ucfirst($neg['ultimo_pago_metodo'] ?? 'transferencia')) ?>)
                                        </span>
                                    </div>
                                <?php else: ?>
                                    <span class="text-subtle text-xs">Sin pagos</span>
                                <?php endif; ?>
                            </td>

                            <!-- Acciones por fila limpias y modernas -->
                            <td class="text-right">
                                <div class="inline-flex items-center justify-end gap-1">
                                    
                                    <!-- 1. Botón Primario: Renovar (+1 Mes) -->
                                    <button type="button" 
                                            class="btn btn-primary btn-sm btn-modal-pago fw-600 inline-flex items-center gap-1" 
                                            data-id="<?= (int)$neg['id'] ?>" 
                                            data-nombre="<?= e($neg['nombre']) ?>" 
                                            data-precio="<?= (int)($neg['precio'] ?? 49000) ?>"
                                            data-ciclo="<?= e($neg['ciclo'] ?? 'mensual') ?>"
                                            title="Registrar pago y renovar suscripción (+30 días)">
                                        <?= icon('cash', 13) ?>
                                        <span>Renovar (+30 Días)</span>
                                    </button>

                                    <!-- 2. Botón WhatsApp: Cobrar / Recordatorio -->
                                    <a href="<?= e($waEnlace) ?>" 
                                       target="_blank" 
                                       rel="noopener" 
                                       class="btn btn-secondary btn-sm btn-cobrar-wa" 
                                       title="Enviar recordatorio de cobro prellenado a WhatsApp">
                                        <?= icon('whatsapp', 13) ?>
                                        <span>Cobrar</span>
                                    </a>

                                    <!-- 3. Menú Kebab de Opciones Secundarias -->
                                    <div class="dropdown-wrap">
                                        <button type="button" class="dropdown-trigger" title="Más opciones">
                                            <?= icon('more', 16) ?>
                                        </button>
                                        <div class="dropdown-menu">
                                            <!-- Ver como (Soporte) -->
                                            <form method="POST" action="<?= e(app_url('/superadmin/negocios/' . $neg['id'] . '/ver-como')) ?>" class="m-0">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="dropdown-item">
                                                    <?= icon('sparkles', 14) ?>
                                                    <span>Ver como restaurante</span>
                                                </button>
                                            </form>

                                            <!-- Extender días -->
                                            <button type="button" 
                                                    class="dropdown-item btn-modal-extender" 
                                                    data-id="<?= (int)$neg['id'] ?>" 
                                                    data-nombre="<?= e($neg['nombre']) ?>">
                                                <?= icon('plus', 14) ?>
                                                <span>Extender días</span>
                                            </button>

                                            <!-- Historial de pagos del restaurante -->
                                            <button type="button" 
                                                    class="dropdown-item btn-modal-historial" 
                                                    data-id="<?= (int)$neg['id'] ?>" 
                                                    data-nombre="<?= e($neg['nombre']) ?>">
                                                <?= icon('cash', 14) ?>
                                                <span>Historial de pagos</span>
                                            </button>

                                            <!-- Editar configuración -->
                                            <a href="<?= e(app_url('/superadmin/negocios/' . $neg['id'] . '/editar')) ?>" class="dropdown-item">
                                                <?= icon('edit', 14) ?>
                                                <span>Editar datos y precio</span>
                                            </a>

                                            <div class="dropdown-divider"></div>

                                            <!-- Pausar / Reactivar Toggle -->
                                            <?php $esSuspendido = ($neg['estado_manual'] === 'suspendido'); ?>
                                            <form method="POST" action="<?= e(app_url('/superadmin/negocios/' . $neg['id'] . '/estado')) ?>" class="m-0"
                                                  data-confirm="¿Seguro que deseas <?= $esSuspendido ? 'reactivar' : 'suspender temporalmente' ?> este restaurante?">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="estado" value="<?= $esSuspendido ? '' : 'suspendido' ?>">
                                                <button type="submit" class="dropdown-item <?= $esSuspendido ? '' : 'text-danger' ?>">
                                                    <?= icon('lock', 14) ?>
                                                    <span><?= $esSuspendido ? 'Reactivar acceso' : 'Suspender acceso' ?></span>
                                                </button>
                                            </form>

                                            <!-- Resetear Contraseña -->
                                            <form method="POST" action="<?= e(app_url('/superadmin/negocios/' . $neg['id'] . '/reset-password')) ?>" class="m-0"
                                                  data-confirm="¿Restablecer la contraseña del admin de <?= e($neg['nombre']) ?>? Se generará una nueva clave temporal.">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <?= icon('lock', 14) ?>
                                                    <span>Resetear contraseña</span>
                                                </button>
                                            </form>
                                        </div>
                                    </div>

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
<div class="modal-backdrop modal-backdrop-modern" id="modal_pago">
    <div class="modal-dialog modal-dialog-modern">
        <div class="modal-header-modern">
            <div class="flex items-center gap-3">
                <div class="section-icon-box section-icon-success">
                    <?= icon('cash', 20) ?>
                </div>
                <div>
                    <h2 class="text-sm fw-700 text-main m-0">
                        Registrar Pago Manual
                    </h2>
                    <span id="pago_nombre_negocio" class="text-xs text-muted fw-600"></span>
                </div>
            </div>
            <button type="button" class="btn btn-secondary btn-sm p-1 leading-none" id="btnCerrarModalPago">&times;</button>
        </div>

        <form method="POST" id="formRegistrarPago" action="">
            <?= csrf_field() ?>
            <div class="modal-body-modern">
                <div class="form-group">
                    <label for="pago_monto" class="form-label">Monto Recibido (COP) *</label>
                    <input type="number" id="pago_monto" name="monto" class="form-control input-price-super" required min="1000" step="500">
                    <span class="form-hint">Monto cobrado por fuera de la plataforma (Nequi, Daviplata, transferencia, etc.).</span>
                </div>

                <div class="config-grid-2">
                    <div class="form-group">
                        <label for="pago_fecha" class="form-label">Fecha del Pago *</label>
                        <input type="date" id="pago_fecha" name="fecha_pago" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="pago_periodos" class="form-label">Cantidad de Períodos *</label>
                        <select id="pago_periodos" name="periodos" class="form-control" required>
                            <option value="1" selected>1 período (30 días)</option>
                            <option value="2">2 períodos (60 días)</option>
                            <option value="3">3 períodos (90 días)</option>
                            <option value="6">6 períodos (semestral - 180 días)</option>
                            <option value="12">12 períodos (anual - 360 días)</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="pago_metodo" class="form-label">Método Utilizado por el Cliente *</label>
                    <select id="pago_metodo" name="metodo" class="form-control" required>
                        <?php foreach ($metodosPago as $k => $lbl): ?>
                            <option value="<?= e($k) ?>" <?= $k === 'wompi' ? 'selected' : '' ?>><?= e($lbl) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="pago_referencia" class="form-label">Comprobante o Referencia de Pago</label>
                    <input type="text" id="pago_referencia" name="referencia" class="form-control" placeholder="Ej: Wompi #12345, Nequi M1234567, Transf. #98721">
                </div>

                <div class="form-group">
                    <label for="pago_nota" class="form-label">Nota Interna (Opcional)</label>
                    <input type="text" id="pago_nota" name="nota" class="form-control" placeholder="Ej: Pago recibido vía Wompi verificado por WhatsApp">
                </div>
            </div>

            <div class="modal-footer-modern">
                <button type="button" class="btn btn-secondary" id="btnCancelarPago">
                    Cancelar
                </button>
                <button type="submit" class="btn btn-primary btn-submit-pago">
                    <?= icon('check', 16) ?>
                    <span>Asentar Pago y Renovar (+30 Días)</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════════════ -->
<!-- MODAL 2: EXTENDER DÍAS DE GRACIA                                   -->
<!-- ═══════════════════════════════════════════════════════════════════ -->
<div class="modal-backdrop modal-backdrop-modern" id="modal_extender">
    <div class="modal-dialog modal-dialog-modern modal-dialog-md">
        <div class="modal-header-modern">
            <div class="flex items-center gap-3">
                <div class="section-icon-box">
                    <?= icon('plus', 20) ?>
                </div>
                <div>
                    <h2 class="text-sm fw-700 text-main m-0">
                        Extender Días de Suscripción
                    </h2>
                    <span id="extender_nombre_negocio" class="text-xs text-muted fw-600"></span>
                </div>
            </div>
            <button type="button" class="btn btn-secondary btn-sm p-1 leading-none" id="btnCerrarModalExtender">&times;</button>
        </div>

        <form method="POST" id="formExtenderDias" action="">
            <?= csrf_field() ?>
            <div class="modal-body-modern">
                <div class="form-group">
                    <label class="form-label">Selección Rápida</label>
                    <div class="flex gap-2 mb-3">
                        <button type="button" class="btn btn-secondary btn-sm btn-quick-days" data-days="3">+3 días</button>
                        <button type="button" class="btn btn-secondary btn-sm btn-quick-days" data-days="7">+7 días</button>
                        <button type="button" class="btn btn-secondary btn-sm btn-quick-days" data-days="15">+15 días</button>
                        <button type="button" class="btn btn-secondary btn-sm btn-quick-days" data-days="30">+30 días</button>
                    </div>
                </div>

                <div class="form-group">
                    <label for="extender_dias" class="form-label">Días a Añadir *</label>
                    <input type="number" id="extender_dias" name="dias" class="form-control text-base fw-700" value="7" min="1" max="90" required>
                    <span class="form-hint">Se sumarán directamente a la fecha de vencimiento actual.</span>
                </div>

                <div class="form-group">
                    <label for="extender_motivo" class="form-label">Motivo de la Extensión</label>
                    <input type="text" id="extender_motivo" name="motivo" class="form-control" placeholder="Ej: Cortesía por apertura, promesa de pago fin de mes">
                </div>
            </div>

            <div class="modal-footer-modern">
                <button type="button" class="btn btn-secondary" id="btnCancelarExtender">
                    Cancelar
                </button>
                <button type="submit" class="btn btn-primary fw-700">
                    <?= icon('check', 16) ?>
                    <span>Aplicar Extensión</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════════════ -->
<!-- MODAL 3: HISTORIAL DE PAGOS DEL NEGOCIO (AJAX)                      -->
<!-- ═══════════════════════════════════════════════════════════════════ -->
<div class="modal-backdrop modal-backdrop-modern" id="modal_historial">
    <div class="modal-dialog modal-dialog-modern modal-dialog-lg">
        <div class="modal-header-modern">
            <div class="flex items-center gap-3">
                <div class="section-icon-box section-icon-blue">
                    <?= icon('cash', 20) ?>
                </div>
                <div>
                    <h2 class="text-sm fw-700 text-main m-0">
                        Historial de Pagos del Restaurante
                    </h2>
                    <span id="historial_nombre_negocio" class="text-xs text-muted fw-600"></span>
                </div>
            </div>
            <button type="button" class="btn btn-secondary btn-sm p-1 leading-none" id="btnCerrarModalHistorial">&times;</button>
        </div>

        <div class="modal-body-modern p-0">
            <div id="historial_loading" class="empty-state-box p-6 text-muted">
                Cargando historial de pagos...
            </div>
            <div id="historial_empty" class="empty-state-box p-6 text-muted is-hidden">
                Este restaurante no registra ningún pago previo en la plataforma.
            </div>
            <div id="historial_table_wrap" class="is-hidden">
                <table class="table m-0">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Monto</th>
                            <th>Método</th>
                            <th>Períodos</th>
                            <th>Nuevo Vencimiento</th>
                            <th>Nota</th>
                        </tr>
                    </thead>
                    <tbody id="historial_tbody">
                    </tbody>
                </table>
            </div>
        </div>

        <div class="modal-footer-modern">
            <button type="button" class="btn btn-secondary" id="btnCerrarHistorialFooter">
                Cerrar
            </button>
        </div>
    </div>
</div>

<script nonce="<?= e($cspNonce) ?>">
document.addEventListener('DOMContentLoaded', function () {
    const modalPago = document.getElementById('modal_pago');
    const formPago = document.getElementById('formRegistrarPago');
    const modalExt = document.getElementById('modal_extender');
    const formExt = document.getElementById('formExtenderDias');
    const modalHist = document.getElementById('modal_historial');
    const baseUrl = <?= json_encode(app_url(''), JSON_UNESCAPED_UNICODE) ?>;

    // 1. Abrir Modal de Pago
    document.querySelectorAll('.btn-modal-pago').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const nombre = this.getAttribute('data-nombre');
            const precio = this.getAttribute('data-precio');

            document.getElementById('pago_nombre_negocio').textContent = nombre;
            document.getElementById('pago_monto').value = precio;
            formPago.action = baseUrl + '/superadmin/negocios/' + id + '/pago';

            modalPago.classList.add('is-open', 'is-active');
            setTimeout(() => document.getElementById('pago_monto').focus(), 50);
        });
    });

    function cerrarPago() { modalPago.classList.remove('is-open', 'is-active'); }
    document.getElementById('btnCerrarModalPago')?.addEventListener('click', cerrarPago);
    document.getElementById('btnCancelarPago')?.addEventListener('click', cerrarPago);

    // 2. Abrir Modal de Extender Días
    document.querySelectorAll('.btn-modal-extender').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const nombre = this.getAttribute('data-nombre');

            document.getElementById('extender_nombre_negocio').textContent = nombre;
            formExt.action = baseUrl + '/superadmin/negocios/' + id + '/extender';

            modalExt.classList.add('is-open', 'is-active');
        });
    });

    document.querySelectorAll('.btn-quick-days').forEach(btn => {
        btn.addEventListener('click', function () {
            const days = this.getAttribute('data-days');
            document.getElementById('extender_dias').value = days;
        });
    });

    function cerrarExtender() { modalExt.classList.remove('is-open', 'is-active'); }
    document.getElementById('btnCerrarModalExtender')?.addEventListener('click', cerrarExtender);
    document.getElementById('btnCancelarExtender')?.addEventListener('click', cerrarExtender);

    // 3. Abrir Modal de Historial de Pagos (AJAX)
    document.querySelectorAll('.btn-modal-historial').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const nombre = this.getAttribute('data-nombre');

            document.getElementById('historial_nombre_negocio').textContent = nombre;
            const loading = document.getElementById('historial_loading');
            const empty = document.getElementById('historial_empty');
            const tableWrap = document.getElementById('historial_table_wrap');
            const tbody = document.getElementById('historial_tbody');

            loading.classList.remove('is-hidden');
            empty.classList.add('is-hidden');
            tableWrap.classList.add('is-hidden');
            tbody.innerHTML = '';
            modalHist.classList.add('is-open', 'is-active');

            fetch(baseUrl + '/superadmin/negocios/' + id + '/pagos-json')
                .then(r => r.json())
                .then(data => {
                    loading.classList.add('is-hidden');
                    if (!data.pagos || data.pagos.length === 0) {
                        empty.classList.remove('is-hidden');
                        return;
                    }

                    tableWrap.classList.remove('is-hidden');
                    data.pagos.forEach(p => {
                        const tr = document.createElement('tr');
                        const montoFmt = '$ ' + parseInt(p.monto, 10).toLocaleString('es-CO');
                        tr.innerHTML = `
                            <td><strong>${p.fecha_pago}</strong></td>
                            <td class="text-success fw-700">${montoFmt}</td>
                            <td class="capitalize">${p.metodo}</td>
                            <td>${p.periodos} período(s)</td>
                            <td><strong>${p.fecha_vencimiento_nueva}</strong></td>
                            <td class="text-xs text-muted">${p.nota || '—'}</td>
                        `;
                        tbody.appendChild(tr);
                    });
                })
                .catch(() => {
                    loading.textContent = 'Error al cargar el historial.';
                });
        });
    });

    function cerrarHistorial() { modalHist.classList.remove('is-open', 'is-active'); }
    document.getElementById('btnCerrarModalHistorial')?.addEventListener('click', cerrarHistorial);
    document.getElementById('btnCerrarHistorialFooter')?.addEventListener('click', cerrarHistorial);

    // Cerrar modales con Escape y backdrop click
    [modalPago, modalExt, modalHist].forEach(modal => {
        modal?.addEventListener('click', function (e) {
            if (e.target === this) this.classList.remove('is-open', 'is-active');
        });
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            [modalPago, modalExt, modalHist].forEach(m => {
                if (m) m.classList.remove('is-open', 'is-active');
            });
        }
    });
});
</script>