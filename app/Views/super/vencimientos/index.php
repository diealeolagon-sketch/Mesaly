<div class="content-header" style="margin-bottom: 24px;">
    <div>
        <h1 class="page-title" style="font-size: 24px; margin-bottom: 4px;">
            Vencimientos y Suscripciones
        </h1>
        <p class="page-subtitle" style="font-size: 14px; color: var(--color-text-muted); margin: 0;">
            Monitoreo en tiempo real de contratos, cobranzas manuales y estado operativo de la red gastronómica.
        </p>
    </div>

    <div class="content-actions">
        <a href="<?= e(app_url('/superadmin/negocios/crear')) ?>" class="btn btn-primary" style="padding: 10px 20px; font-weight: 700;">
            <?= icon('plus', 16) ?>
            <span>Registrar Nuevo Negocio</span>
        </a>
    </div>
</div>

<!-- 1. KPI CARDS GLOBALES -->
<div class="kpi-grid" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); margin-bottom: 24px;">
    <div class="kpi-card-modern">
        <div class="kpi-icon-badge" style="background: var(--color-brand-subtle); color: var(--color-brand);">
            <?= icon('cash', 20) ?>
        </div>
        <span style="font-size: 13px; font-weight: 600; color: var(--color-text-muted);">MRR Estimado</span>
        <strong style="font-size: 24px; color: var(--color-brand); font-variant-numeric: tabular-nums;">
            <?= format_cop($conteo['mrr_estimado']) ?>
        </strong>
        <span style="font-size: 12px; color: var(--color-text-muted);">Recurrencia mensual pactada</span>
    </div>

    <div class="kpi-card-modern">
        <div class="kpi-icon-badge" style="background: #ECFDF5; color: #059669;">
            <?= icon('store', 20) ?>
        </div>
        <span style="font-size: 13px; font-weight: 600; color: var(--color-text-muted);">Restaurantes Activos</span>
        <strong style="font-size: 24px; color: var(--color-text-main); font-variant-numeric: tabular-nums;">
            <?= (int)$conteo['todos'] ?>
        </strong>
        <span style="font-size: 12px; color: var(--color-success); font-weight: 600;">
            <?= (int)$conteo['al_dia'] ?> al día · <?= (int)$conteo['cancelados'] ?> cancelados
        </span>
    </div>

    <div class="kpi-card-modern">
        <div class="kpi-icon-badge" style="background: #FFFBEB; color: #D97706;">
            <?= icon('alert', 20) ?>
        </div>
        <span style="font-size: 13px; font-weight: 600; color: var(--color-text-muted);">Vencen Esta Semana</span>
        <strong style="font-size: 24px; color: #D97706; font-variant-numeric: tabular-nums;">
            <?= (int)$conteo['semana'] ?>
        </strong>
        <span style="font-size: 12px; color: var(--color-text-muted);">Recordatorio preventivo recomendado</span>
    </div>

    <div class="kpi-card-modern">
        <div class="kpi-icon-badge" style="background: #FEF2F2; color: #DC2626;">
            <?= icon('lock', 20) ?>
        </div>
        <span style="font-size: 13px; font-weight: 600; color: var(--color-text-muted);">En Gracia / Bloqueados</span>
        <strong style="font-size: 24px; color: #DC2626; font-variant-numeric: tabular-nums;">
            <?= (int)$conteo['gracia'] ?> <span style="font-size: 16px; font-weight: 500; color: var(--color-text-subtle);">/</span> <?= (int)$conteo['bloqueados'] ?>
        </strong>
        <span style="font-size: 12px; color: #DC2626; font-weight: 600;">Gestión de cobranza requerida</span>
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
<div class="card" style="padding: 0; overflow: hidden;">
    <?php if (empty($negocios)): ?>
        <div style="text-align: center; padding: 60px 24px;">
            <div style="width: 52px; height: 52px; border-radius: 50%; background: var(--color-bg-subtle); display: inline-flex; align-items: center; justify-content: center; color: var(--color-text-subtle); margin-bottom: 16px;">
                <?= icon('store', 24) ?>
            </div>
            <h3 style="font-size: 16px; font-weight: 700; color: var(--color-text-main); margin: 0 0 6px 0;">
                No se encontraron negocios con estos criterios
            </h3>
            <p style="color: var(--color-text-muted); font-size: 14px; margin: 0 auto 20px auto;">
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
                        <th>Precio Acordado</th>
                        <th>Vencimiento</th>
                        <th>Último Pago</th>
                        <th style="text-align: right; min-width: 220px;">Acciones</th>
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
                                <div style="display: flex; flex-direction: column; gap: 3px;">
                                    <div style="display: flex; align-items: center; gap: 8px;">
                                        <strong style="font-size: 14.5px; color: var(--color-text-main);">
                                            <?= e($neg['nombre']) ?>
                                        </strong>
                                        <?php if (!empty($neg['notas_internas'])): ?>
                                            <span style="font-size: 11px; padding: 2px 6px; border-radius: 4px; background: #FEF3C7; color: #92400E; font-weight: 600;" title="<?= e($neg['notas_internas']) ?>">
                                                Nota
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <div style="display: flex; align-items: center; gap: 6px; font-size: 12px; color: var(--color-text-muted);">
                                        <a href="<?= e(app_url('/' . $neg['slug'])) ?>" target="_blank" rel="noopener" style="color: var(--color-brand); text-decoration: none; font-weight: 600;">
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

                            <!-- Precio Acordado -->
                            <td>
                                <strong style="font-size: 14.5px; color: var(--color-text-main); font-variant-numeric: tabular-nums;">
                                    <?= format_cop((int)($neg['precio'] ?? 49000)) ?>
                                </strong>
                                <span style="display: block; font-size: 11.5px; color: var(--color-text-muted); text-transform: capitalize;">
                                    Ciclo <?= e($neg['ciclo'] ?? 'mensual') ?>
                                </span>
                            </td>

                            <!-- Fecha Vencimiento y Días Restantes -->
                            <td>
                                <div style="display: flex; flex-direction: column; gap: 2px;">
                                    <strong style="font-size: 13.5px; color: <?= $est['dias_restantes'] < 0 ? '#DC2626' : 'inherit' ?>; font-variant-numeric: tabular-nums;">
                                        <?= e($vencStr) ?>
                                    </strong>
                                    <span style="font-size: 12px; color: var(--color-text-muted);">
                                        <?php if ($est['dias_restantes'] > 0): ?>
                                            Faltan <?= (int)$est['dias_restantes'] ?> días
                                        <?php elseif ($est['dias_restantes'] === 0): ?>
                                            <span style="color: #D97706; font-weight: 700;">Vence hoy</span>
                                        <?php else: ?>
                                            <span style="color: #DC2626; font-weight: 600;">Venció hace <?= abs((int)$est['dias_restantes']) ?> días</span>
                                        <?php endif; ?>
                                    </span>
                                </div>
                            </td>

                            <!-- Último Pago Registrado -->
                            <td>
                                <?php if (!empty($neg['ultimo_pago_fecha'])): ?>
                                    <div style="display: flex; flex-direction: column; gap: 2px;">
                                        <strong style="font-size: 13px; color: #059669; font-variant-numeric: tabular-nums;">
                                            <?= format_cop((int)$neg['ultimo_pago_monto']) ?>
                                        </strong>
                                        <span style="font-size: 11.5px; color: var(--color-text-muted);">
                                            <?= date('d/m/Y', strtotime($neg['ultimo_pago_fecha'])) ?> (<?= e(ucfirst($neg['ultimo_pago_metodo'] ?? 'transferencia')) ?>)
                                        </span>
                                    </div>
                                <?php else: ?>
                                    <span style="color: var(--color-text-subtle); font-size: 12.5px;">Sin pagos</span>
                                <?php endif; ?>
                            </td>

                            <!-- Acciones por fila limpias y modernas -->
                            <td style="text-align: right;">
                                <div style="display: inline-flex; align-items: center; justify-content: flex-end; gap: 6px;">
                                    
                                    <!-- 1. Botón Primario: Registrar Pago -->
                                    <button type="button" 
                                            class="btn btn-primary btn-sm btn-modal-pago" 
                                            data-id="<?= (int)$neg['id'] ?>" 
                                            data-nombre="<?= e($neg['nombre']) ?>" 
                                            data-precio="<?= (int)($neg['precio'] ?? 49000) ?>"
                                            data-ciclo="<?= e($neg['ciclo'] ?? 'mensual') ?>"
                                            title="Registrar pago manual de suscripción">
                                        <?= icon('cash', 13) ?>
                                        <span>Pagar</span>
                                    </button>

                                    <!-- 2. Botón WhatsApp: Cobrar / Recordatorio -->
                                    <a href="<?= e($waEnlace) ?>" 
                                       target="_blank" 
                                       rel="noopener" 
                                       class="btn btn-secondary btn-sm" 
                                       style="color: #25D366; border-color: #A7F3D0; background: #F0FDF4;"
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
                                            <form method="POST" action="<?= e(app_url('/superadmin/negocios/' . $neg['id'] . '/ver-como')) ?>" style="margin: 0;">
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
                                            <form method="POST" action="<?= e(app_url('/superadmin/negocios/' . $neg['id'] . '/estado')) ?>" style="margin: 0;"
                                                  data-confirm="¿Seguro que deseas <?= $esSuspendido ? 'reactivar' : 'suspender temporalmente' ?> este restaurante?">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="estado" value="<?= $esSuspendido ? '' : 'suspendido' ?>">
                                                <button type="submit" class="dropdown-item <?= $esSuspendido ? '' : 'text-danger' ?>">
                                                    <?= icon('lock', 14) ?>
                                                    <span><?= $esSuspendido ? 'Reactivar acceso' : 'Suspender acceso' ?></span>
                                                </button>
                                            </form>

                                            <!-- Resetear Contraseña -->
                                            <form method="POST" action="<?= e(app_url('/superadmin/negocios/' . $neg['id'] . '/reset-password')) ?>" style="margin: 0;"
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
<div class="modal-backdrop-modern" id="modal_pago">
    <div class="modal-dialog-modern">
        <div class="modal-header-modern">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 38px; height: 38px; border-radius: var(--radius-sm); background: #ECFDF5; color: #059669; display: flex; align-items: center; justify-content: center;">
                    <?= icon('cash', 20) ?>
                </div>
                <div>
                    <h2 style="font-size: 16px; font-weight: 700; margin: 0; color: var(--color-text-main);">
                        Registrar Pago Manual
                    </h2>
                    <span id="pago_nombre_negocio" style="font-size: 13px; color: var(--color-text-muted); font-weight: 600;"></span>
                </div>
            </div>
            <button type="button" class="btn btn-secondary btn-sm" id="btnCerrarModalPago" style="padding: 4px 8px; font-size: 18px; line-height: 1;">&times;</button>
        </div>

        <form method="POST" id="formRegistrarPago" action="">
            <?= csrf_field() ?>
            <div class="modal-body-modern">
                <div class="form-group">
                    <label for="pago_monto" class="form-label">Monto Recibido (COP) *</label>
                    <input type="number" id="pago_monto" name="monto" class="form-control" required min="1000" step="500" style="font-size: 18px; font-weight: 700; color: #059669;">
                    <span class="form-hint">Monto cobrado por fuera de la plataforma (Nequi, Daviplata, transferencia, etc.).</span>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;" class="config-grid-2">
                    <div class="form-group">
                        <label for="pago_fecha" class="form-label">Fecha del Pago *</label>
                        <input type="date" id="pago_fecha" name="fecha_pago" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="pago_periodos" class="form-label">Cantidad de Períodos *</label>
                        <select id="pago_periodos" name="periodos" class="form-control" required>
                            <option value="1" selected>1 período</option>
                            <option value="2">2 períodos</option>
                            <option value="3">3 períodos</option>
                            <option value="6">6 períodos (semestral)</option>
                            <option value="12">12 períodos (anual)</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="pago_metodo" class="form-label">Método Utilizado por el Cliente *</label>
                    <select id="pago_metodo" name="metodo" class="form-control" required>
                        <?php foreach ($metodosPago as $k => $lbl): ?>
                            <option value="<?= e($k) ?>" <?= $k === 'transferencia' ? 'selected' : '' ?>><?= e($lbl) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="pago_referencia" class="form-label">Comprobante o Referencia de Pago</label>
                    <input type="text" id="pago_referencia" name="referencia" class="form-control" placeholder="Ej: Nequi M1234567, Transf. #98721">
                </div>

                <div class="form-group">
                    <label for="pago_nota" class="form-label">Nota Interna (Opcional)</label>
                    <input type="text" id="pago_nota" name="nota" class="form-control" placeholder="Ej: Pago adelantado del mes con descuento promocional">
                </div>
            </div>

            <div class="modal-footer-modern">
                <button type="button" class="btn btn-secondary" id="btnCancelarPago">
                    Cancelar
                </button>
                <button type="submit" class="btn btn-primary" style="font-weight: 700; background: #059669; border-color: #059669;">
                    <?= icon('check', 16) ?>
                    <span>Asentar Pago y Renovar</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════════════════ -->
<!-- MODAL 2: EXTENDER DÍAS DE GRACIA                                   -->
<!-- ═══════════════════════════════════════════════════════════════════ -->
<div class="modal-backdrop-modern" id="modal_extender">
    <div class="modal-dialog-modern" style="max-width: 460px;">
        <div class="modal-header-modern">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 38px; height: 38px; border-radius: var(--radius-sm); background: #FFFBEB; color: #D97706; display: flex; align-items: center; justify-content: center;">
                    <?= icon('plus', 20) ?>
                </div>
                <div>
                    <h2 style="font-size: 16px; font-weight: 700; margin: 0; color: var(--color-text-main);">
                        Extender Días de Suscripción
                    </h2>
                    <span id="extender_nombre_negocio" style="font-size: 13px; color: var(--color-text-muted); font-weight: 600;"></span>
                </div>
            </div>
            <button type="button" class="btn btn-secondary btn-sm" id="btnCerrarModalExtender" style="padding: 4px 8px; font-size: 18px; line-height: 1;">&times;</button>
        </div>

        <form method="POST" id="formExtenderDias" action="">
            <?= csrf_field() ?>
            <div class="modal-body-modern">
                <div class="form-group">
                    <label class="form-label">Selección Rápida</label>
                    <div style="display: flex; gap: 8px; margin-bottom: 12px;">
                        <button type="button" class="btn btn-secondary btn-sm btn-quick-days" data-days="3">+3 días</button>
                        <button type="button" class="btn btn-secondary btn-sm btn-quick-days" data-days="7">+7 días</button>
                        <button type="button" class="btn btn-secondary btn-sm btn-quick-days" data-days="15">+15 días</button>
                        <button type="button" class="btn btn-secondary btn-sm btn-quick-days" data-days="30">+30 días</button>
                    </div>
                </div>

                <div class="form-group">
                    <label for="extender_dias" class="form-label">Días a Añadir *</label>
                    <input type="number" id="extender_dias" name="dias" class="form-control" value="7" min="1" max="90" required style="font-size: 16px; font-weight: 700;">
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
                <button type="submit" class="btn btn-primary" style="font-weight: 700;">
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
<div class="modal-backdrop-modern" id="modal_historial">
    <div class="modal-dialog-modern" style="max-width: 680px;">
        <div class="modal-header-modern">
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 38px; height: 38px; border-radius: var(--radius-sm); background: #EFF6FF; color: #2563EB; display: flex; align-items: center; justify-content: center;">
                    <?= icon('cash', 20) ?>
                </div>
                <div>
                    <h2 style="font-size: 16px; font-weight: 700; margin: 0; color: var(--color-text-main);">
                        Historial de Pagos del Restaurante
                    </h2>
                    <span id="historial_nombre_negocio" style="font-size: 13px; color: var(--color-text-muted); font-weight: 600;"></span>
                </div>
            </div>
            <button type="button" class="btn btn-secondary btn-sm" id="btnCerrarModalHistorial" style="padding: 4px 8px; font-size: 18px; line-height: 1;">&times;</button>
        </div>

        <div class="modal-body-modern" style="padding: 0;">
            <div id="historial_loading" style="text-align: center; padding: 40px; color: var(--color-text-muted);">
                Cargando historial de pagos...
            </div>
            <div id="historial_empty" style="display: none; text-align: center; padding: 40px; color: var(--color-text-muted);">
                Este restaurante no registra ningún pago previo en la plataforma.
            </div>
            <div id="historial_table_wrap" style="display: none;">
                <table class="table" style="margin: 0;">
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

            modalPago.style.display = 'flex';
            setTimeout(() => document.getElementById('pago_monto').focus(), 50);
        });
    });

    function cerrarPago() { modalPago.style.display = 'none'; }
    document.getElementById('btnCerrarModalPago')?.addEventListener('click', cerrarPago);
    document.getElementById('btnCancelarPago')?.addEventListener('click', cerrarPago);

    // 2. Abrir Modal de Extender Días
    document.querySelectorAll('.btn-modal-extender').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            const nombre = this.getAttribute('data-nombre');

            document.getElementById('extender_nombre_negocio').textContent = nombre;
            formExt.action = baseUrl + '/superadmin/negocios/' + id + '/extender';

            modalExt.style.display = 'flex';
        });
    });

    document.querySelectorAll('.btn-quick-days').forEach(btn => {
        btn.addEventListener('click', function () {
            const days = this.getAttribute('data-days');
            document.getElementById('extender_dias').value = days;
        });
    });

    function cerrarExtender() { modalExt.style.display = 'none'; }
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

            loading.style.display = 'block';
            empty.style.display = 'none';
            tableWrap.style.display = 'none';
            tbody.innerHTML = '';
            modalHist.style.display = 'flex';

            fetch(baseUrl + '/superadmin/negocios/' + id + '/pagos-json')
                .then(r => r.json())
                .then(data => {
                    loading.style.display = 'none';
                    if (!data.pagos || data.pagos.length === 0) {
                        empty.style.display = 'block';
                        return;
                    }

                    tableWrap.style.display = 'block';
                    data.pagos.forEach(p => {
                        const tr = document.createElement('tr');
                        const montoFmt = '$ ' + parseInt(p.monto, 10).toLocaleString('es-CO');
                        tr.innerHTML = `
                            <td><strong>${p.fecha_pago}</strong></td>
                            <td style="color: #059669; font-weight: 700;">${montoFmt}</td>
                            <td style="text-transform: capitalize;">${p.metodo}</td>
                            <td>${p.periodos} período(s)</td>
                            <td><strong>${p.fecha_vencimiento_nueva}</strong></td>
                            <td style="font-size: 12px; color: var(--color-text-muted);">${p.nota || '—'}</td>
                        `;
                        tbody.appendChild(tr);
                    });
                })
                .catch(() => {
                    loading.textContent = 'Error al cargar el historial.';
                });
        });
    });

    function cerrarHistorial() { modalHist.style.display = 'none'; }
    document.getElementById('btnCerrarModalHistorial')?.addEventListener('click', cerrarHistorial);
    document.getElementById('btnCerrarHistorialFooter')?.addEventListener('click', cerrarHistorial);

    // Cerrar modales con Escape y backdrop click
    [modalPago, modalExt, modalHist].forEach(modal => {
        modal?.addEventListener('click', function (e) {
            if (e.target === this) this.style.display = 'none';
        });
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            [modalPago, modalExt, modalHist].forEach(m => {
                if (m) m.style.display = 'none';
            });
        }
    });
});
</script>
