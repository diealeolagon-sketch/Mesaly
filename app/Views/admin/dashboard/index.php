<div class="content-header mb-5">
    <div>
        <h1 class="page-title text-2xl mb-1">
            <?= e($tenant['nombre']) ?>
        </h1>
        <p class="page-subtitle text-base text-muted m-0 flex items-center gap-2 flex-wrap">
            <span>Menú digital:</span>
            <a href="<?= e(app_url('/' . $tenant['slug'])) ?>" target="_blank" rel="noopener" class="text-brand fw-600 flex items-center gap-1">
                <span><?= e(app_url('/' . $tenant['slug'])) ?></span>
                <?= icon('external-link', 12) ?>
            </a>
        </p>
    </div>

    <div class="content-actions flex items-center gap-2 flex-wrap">
        <!-- Botón rápido de estado: Abrir / Pausar pedidos -->
        <form method="POST" action="<?= e(app_url('/admin/toggle-recibiendo-pedidos')) ?>" class="m-0 inline-flex items-center gap-2">
            <?= csrf_field() ?>
            <?php if (!empty($tenant['recibiendo_pedidos'])): ?>
                <span class="badge-pill success">
                    <span class="dot-indicator dot-indicator-success"></span>
                    <span>Recibiendo pedidos</span>
                </span>
                <button type="submit" class="btn btn-secondary btn-sm fw-600" title="Pausar la recepción de pedidos en el menú digital">
                    <?= icon('clock', 13) ?>
                    <span>Pausar</span>
                </button>
            <?php else: ?>
                <span class="badge-pill danger">
                    <span class="dot-indicator dot-indicator-danger"></span>
                    <span>Recepción pausada</span>
                </span>
                <button type="submit" class="btn btn-primary btn-sm fw-600" title="Activar pedidos en el menú digital">
                    <?= icon('check', 13) ?>
                    <span>Abrir carta</span>
                </button>
            <?php endif; ?>
        </form>

        <!-- Botón directo al módulo de pedidos -->
        <a href="<?= e(app_url('/admin/pedidos')) ?>" class="btn btn-primary btn-sm fw-600 inline-flex items-center gap-2">
            <?= icon('cart', 15) ?>
            <span>Ver Pedidos</span>
            <?php if (!empty($metricasHoy['pendientes']) && $metricasHoy['pendientes'] > 0): ?>
                <span class="badge-counter">
                    <?= (int)$metricasHoy['pendientes'] ?>
                </span>
            <?php endif; ?>
        </a>

        <!-- Botón para pagar suscripción al dueño de Mesaly con Wompi -->
        <button type="button" class="btn btn-secondary btn-sm fw-600 inline-flex items-center gap-2" onclick="abrirModalPagarMesaly()" title="Pagar suscripción mensual a Mesaly">
            <?= icon('cash', 14) ?>
            <span>Pagar a Mesaly</span>
        </button>
    </div>
</div>

<!-- TARJETA DESTACADA DE SUSCRIPCIÓN SAAS Y BOTÓN WOMPI -->
<div class="card p-4 mb-5 border-brand-light bg-subtle flex items-center justify-between flex-wrap gap-4">
    <div class="flex items-center gap-3">
        <div class="kpi-icon-badge kpi-icon-brand">
            <?= icon('cash', 20) ?>
        </div>
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="fw-700 text-main text-base">Suscripción SaaS Mesaly</span>
                <span class="badge-pill <?= !empty($estadoSuscripcion['bloqueado']) ? 'danger' : (!empty($estadoSuscripcion['en_gracia']) ? 'warning' : 'success') ?>">
                    <?= e($estadoSuscripcion['label'] ?? 'Al Día') ?>
                </span>
            </div>
            <p class="text-xs text-muted m-0">
                Tu plan actual vence el <strong><?= !empty($tenant['fecha_vencimiento']) ? date('d/m/Y', strtotime($tenant['fecha_vencimiento'])) : 'N/A' ?></strong> 
                (<?= (int)($estadoSuscripcion['dias_restantes'] ?? 30) ?> días restantes). Tarifa: <strong><?= format_cop((int)($tenant['precio'] ?? 49000)) ?> COP / mes</strong>.
            </p>
        </div>
    </div>
    <div>
        <button type="button" class="btn btn-primary btn-sm fw-600 inline-flex items-center gap-2" onclick="abrirModalPagarMesaly()">
            <?= icon('check', 14) ?>
            <span>Pagar Suscripción (Wompi)</span>
        </button>
    </div>
</div>

<?php if (!empty($estadoSuscripcion['en_gracia'])): ?>
    <div class="alert alert-warning mb-5">
        <?= icon('alert', 20) ?>
        <div>
            <strong>Período de Gracia Activo:</strong> Tu suscripción venció el <strong><?= !empty($tenant['fecha_vencimiento']) ? date('d/m/Y', strtotime($tenant['fecha_vencimiento'])) : 'recientemente' ?></strong>. Tienes <strong><?= (int)($estadoSuscripcion['dias_gracia_restantes'] ?? 0) ?> días</strong> restantes para enviar el soporte de pago antes de la suspensión automática del servicio.
        </div>
    </div>
<?php elseif (!empty($estadoSuscripcion['por_vencer'])): ?>
    <div class="alert alert-info mb-5">
        <?= icon('alert', 20) ?>
        <div>
            <strong>Recordatorio de Pago:</strong> Tu suscripción vencerá el <strong><?= date('d/m/Y', strtotime($tenant['fecha_vencimiento'])) ?></strong> (<?= (int)$estadoSuscripcion['dias_restantes'] === 0 ? 'hoy' : 'en ' . (int)$estadoSuscripcion['dias_restantes'] . ' días' ?>). Realiza la transferencia a tiempo para mantener tu catálogo activo.
        </div>
    </div>
<?php endif; ?>

<!-- 1. TARJETAS DE KPIS OPERATIVOS DE HOY (SUPER RÁPIDO & TIEMPO REAL) -->
<div class="kpi-grid mb-5">
    <!-- Ventas de Hoy -->
    <div class="kpi-card">
        <div class="kpi-label">
            <span>Ventas de Hoy</span>
            <div class="kpi-icon-success"><?= icon('coin', 18) ?></div>
        </div>
        <div class="kpi-value text-success" data-metric-hoy="ventas_hoy">
            <?= format_cop((int)($metricasHoy['ventas_hoy'] ?? 0)) ?>
        </div>
        <div class="kpi-subtext">Caja acumulada del día (pedidos no cancelados)</div>
    </div>

    <!-- Pedidos de Hoy -->
    <div class="kpi-card">
        <div class="kpi-label">
            <span>Pedidos de Hoy</span>
            <div class="kpi-icon-brand"><?= icon('cart', 18) ?></div>
        </div>
        <div class="kpi-value" data-metric-hoy="pedidos_hoy">
            <?= (int)($metricasHoy['pedidos_hoy'] ?? 0) ?>
        </div>
        <div class="kpi-subtext">Comandas procesadas en la jornada</div>
    </div>

    <!-- Ticket Promedio Hoy -->
    <div class="kpi-card">
        <div class="kpi-label">
            <span>Ticket Promedio Hoy</span>
            <div class="kpi-icon-muted"><?= icon('chart', 18) ?></div>
        </div>
        <div class="kpi-value" data-metric-hoy="ticket_promedio_hoy">
            <?= format_cop((int)($metricasHoy['ticket_promedio_hoy'] ?? 0)) ?>
        </div>
        <div class="kpi-subtext">Consumo promedio por comanda de hoy</div>
    </div>

    <!-- Mesas Ocupadas Ahora -->
    <div class="kpi-card <?= !empty($metricasHoy['mesas_ocupadas']) && $metricasHoy['mesas_ocupadas'] > 0 ? 'kpi-card--warning' : '' ?>">
        <div class="kpi-label">
            <span>Mesas Ocupadas</span>
            <div class="kpi-icon-brand"><?= icon('cutlery', 18) ?></div>
        </div>
        <div class="kpi-value">
            <span data-metric-hoy="mesas_ocupadas"><?= (int)($metricasHoy['mesas_ocupadas'] ?? 0) ?></span> <span class="text-xs text-muted fw-500">/ <?= (int)($metricasHoy['total_mesas'] ?? 0) ?> activas</span>
        </div>
        <div class="kpi-subtext">
            <?= !empty($metricasHoy['mesas_ocupadas']) && $metricasHoy['mesas_ocupadas'] > 0 ? 'Comensales consumiendo en el salón' : 'Todas las mesas disponibles' ?>
        </div>
    </div>
</div>

<!-- 2. OPERACIÓN EN VIVO: PLANO DE SALÓN (MESAS) & COLAS DE COCINA -->
<div class="dashboard-operative-grid mb-5">
    <!-- Columna Izquierda: Plano de Mesas y Ocupación en Vivo -->
    <div class="floor-plan-card">
        <div class="floor-plan-header">
            <div>
                <h2 class="floor-plan-title">
                    <?= icon('cutlery', 16) ?>
                    <span>Salón en Vivo — Ocupación de Mesas</span>
                </h2>
                <span class="text-xs text-muted">Estado actual de comensales y consumo acumulado en mesa</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="floor-plan-summary-badge <?= !empty($metricasHoy['mesas_ocupadas']) ? 'has-occupied' : '' ?>" id="floorPlanSummaryBadge">
                    <span class="fp-ocupadas"><?= (int)($metricasHoy['mesas_ocupadas'] ?? 0) ?> ocupadas</span>
                    <span>&bull;</span>
                    <span class="fp-libres"><?= max(0, (int)($metricasHoy['total_mesas'] ?? 0) - (int)($metricasHoy['mesas_ocupadas'] ?? 0)) ?> libres</span>
                </span>
                <a href="<?= e(app_url('/admin/mesas')) ?>" class="btn btn-secondary btn-xs fw-600">
                    Administrar mesas &rarr;
                </a>
            </div>
        </div>

        <div class="floor-plan-body">
            <?php if (empty($mesasEnVivo)): ?>
                <div class="empty-state-box p-6 text-center">
                    <div class="icon-bubble-lg mx-auto mb-3">
                        <?= icon('cutlery', 20) ?>
                    </div>
                    <h3 class="text-sm fw-700 text-main m-0 mb-1">
                        No hay mesas configuradas aún
                    </h3>
                    <p class="text-xs text-muted m-0 mb-4">
                        Crea las mesas de tu salón para generar códigos QR y monitorear la ocupación en vivo.
                    </p>
                    <a href="<?= e(app_url('/admin/mesas')) ?>" class="btn btn-primary btn-sm fw-600">
                        Crear mesas ahora
                    </a>
                </div>
            <?php else: ?>
                <div class="floor-plan-grid" id="floorPlanGrid">
                    <?php foreach ($mesasEnVivo as $mesa): ?>
                        <div class="table-node-card <?= $mesa['ocupada'] ? 'is-occupied' : 'is-free' ?>"
                             id="table_node_<?= (int)$mesa['id'] ?>"
                             data-mesa-node-id="<?= (int)$mesa['id'] ?>"
                             data-mesa-numero="<?= e($mesa['numero']) ?>">
                            <div class="table-node-top">
                                <span class="table-node-title">
                                    <span>Mesa <?= e($mesa['numero']) ?></span>
                                </span>
                                <?php if ($mesa['ocupada']): ?>
                                    <span class="table-node-badge occupied">Ocupada</span>
                                <?php else: ?>
                                    <span class="table-node-badge free">Libre</span>
                                <?php endif; ?>
                            </div>

                            <div class="table-node-content">
                                <?php if ($mesa['ocupada']): ?>
                                    <div class="table-node-total">
                                        <?= format_cop((int)$mesa['total_mesa']) ?>
                                    </div>
                                    <div class="table-node-meta">
                                        <?= icon('clock', 12) ?>
                                        <span>Hace <?= (int)$mesa['tiempo_min'] ?> min</span>
                                        <span>&bull;</span>
                                        <span class="fw-600 text-main">
                                            <?php if ($mesa['estado_pedido'] === 'entregado'): ?>
                                                Comensales en mesa
                                            <?php elseif ($mesa['estado_pedido'] === 'listo'): ?>
                                                Listo para servir
                                            <?php else: ?>
                                                En cocina
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                <?php else: ?>
                                    <div class="table-node-meta text-muted">
                                        <?= icon('users', 12) ?>
                                        <span><?= !empty($mesa['nombre']) ? e($mesa['nombre']) : 'Lista para ordenar' ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="table-node-actions flex items-center justify-between gap-1">
                                <?php if ($mesa['ocupada'] && !empty($mesa['pedido_id'])): ?>
                                    <a href="<?= e(app_url('/admin/pedidos?pedido=' . $mesa['pedido_id'])) ?>" class="table-node-link">
                                        <span>Comanda</span>
                                        &rarr;
                                    </a>
                                    <button type="button" class="btn-liberar-mesa-node btn-ajax-liberar" data-liberar-url="<?= e(app_url('/admin/mesas/' . $mesa['id'] . '/liberar')) ?>" title="Liberar mesa (marcar como disponible y sin clientes)">
                                        <?= icon('check', 11) ?>
                                        <span>Liberar</span>
                                    </button>
                                <?php else: ?>
                                    <a href="<?= e(app_url('/' . $tenant['slug'] . '?mesa=' . urlencode($mesa['numero']))) ?>" target="_blank" rel="noopener" class="table-node-link text-muted">
                                        <span>Abrir QR</span>
                                        <?= icon('external-link', 10) ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Columna Derecha: Comandas en Cocina / Despacho en Vivo -->
    <div class="live-kitchen-queue">
        <div class="live-kitchen-header">
            <div>
                <h2 class="live-kitchen-title">
                    <?= icon('clock', 16) ?>
                    <span>Cocina y Despacho</span>
                </h2>
                <span class="text-xs text-muted">Comandas activas pendientes</span>
            </div>
            <a href="<?= e(app_url('/admin/pedidos')) ?>" class="btn btn-secondary btn-xs fw-600">
                Ver todos (<?= (int)($metricasHoy['pendientes'] ?? 0) ?>)
            </a>
        </div>

        <div class="live-kitchen-body" id="liveKitchenBody">
            <?php if (empty($comandasActivas)): ?>
                <div class="live-empty-kitchen">
                    <div class="icon-bubble-lg mx-auto mb-3">
                        <?= icon('check', 20) ?>
                    </div>
                    <h3 class="text-sm fw-700 text-main m-0 mb-1">
                        ¡Cocina y barra al día!
                    </h3>
                    <p class="text-xs text-muted m-0">
                        No hay comandas pendientes por preparar o despachar en este momento.
                    </p>
                </div>
            <?php else: ?>
                <?php foreach ($comandasActivas as $cmd): ?>
                    <a href="<?= e(app_url('/admin/pedidos?pedido=' . $cmd['id'])) ?>" class="live-ticket-item text-decoration-none">
                        <div class="live-ticket-left">
                            <span class="live-ticket-code">
                                <?= icon('cart', 13) ?>
                                <span>#<?= e($cmd['consecutivo'] ?? $cmd['codigo']) ?></span>
                            </span>
                            <span class="live-ticket-origin">
                                <?php if ($cmd['modalidad'] === 'mesa'): ?>
                                    Mesa <?= e($cmd['mesa_nombre'] ?? $cmd['mesa_numero'] ?? 'S/N') ?>
                                <?php elseif ($cmd['modalidad'] === 'domicilio'): ?>
                                    Domicilio &bull; <?= e($cmd['cliente_nombre'] ?? 'Cliente') ?>
                                <?php else: ?>
                                    Para Llevar
                                <?php endif; ?>
                                &bull; <?= (int)$cmd['total_items'] ?> platos
                            </span>
                        </div>
                        <div class="live-ticket-right">
                            <span class="live-ticket-status <?= e($cmd['estado']) ?>">
                                <?= ucfirst(e(str_replace('_', ' ', $cmd['estado']))) ?>
                            </span>
                            <span class="live-ticket-time">
                                <?= (int)$cmd['minutos_transcurridos'] === 0 ? 'Justo ahora' : 'Hace ' . (int)$cmd['minutos_transcurridos'] . ' min' ?>
                            </span>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- 3. ACCESO DIRECTO A REPORTES ANALÍTICOS Y MÓDULOS DE GESTIÓN -->
<div class="card p-0 overflow-hidden mb-5">
    <div class="card-header-bar flex items-center justify-between">
        <div>
            <h2 class="text-md fw-700 text-main m-0 mb-1">
                Herramientas y Módulos del Restaurante
            </h2>
            <p class="text-xs text-muted m-0">
                Gestión comercial, carta digital, cierres diarios y analítica histórica
            </p>
        </div>
        <a href="<?= e(app_url('/admin/reportes')) ?>" class="btn btn-secondary btn-sm fw-600 inline-flex items-center gap-1">
            <?= icon('chart', 14) ?>
            <span>Ir a Reportes &amp; Cierre</span>
            &rarr;
        </a>
    </div>

    <div class="card-p-6">
        <div class="modules-grid">
            <!-- Reportes y Cierre -->
            <a href="<?= e(app_url('/admin/reportes')) ?>" class="module-card">
                <div>
                    <div class="module-card-icon">
                        <?= icon('chart', 26) ?>
                    </div>
                    <h3 class="module-card-title">Reportes &amp; Cierre de Ventas</h3>
                    <p class="module-card-desc">
                        Gráficas analíticas de evolución, horas pico, top platos más vendidos, consolidado tributario y exportación a Excel / CSV.
                    </p>
                </div>
                <span class="module-card-link">Ver reportes y cierre &rarr;</span>
            </a>

            <!-- Pedidos en Vivo -->
            <a href="<?= e(app_url('/admin/pedidos')) ?>" class="module-card">
                <div>
                    <div class="module-card-icon">
                        <?= icon('cart', 26) ?>
                    </div>
                    <h3 class="module-card-title">Gestión de Pedidos</h3>
                    <p class="module-card-desc">
                        Control en tiempo real de órdenes, actualización de estado en cocina (generado, en preparación, listo) y enlace a WhatsApp.
                    </p>
                </div>
                <span class="module-card-link">Abrir pedidos en vivo &rarr;</span>
            </a>

            <!-- Catálogo de Menú -->
            <a href="<?= e(app_url('/admin/catalogo')) ?>" class="module-card">
                <div>
                    <div class="module-card-icon">
                        <?= icon('catalog', 26) ?>
                    </div>
                    <h3 class="module-card-title">Catálogo y Platos</h3>
                    <p class="module-card-desc">
                        Organiza tus categorías de comida y bebidas, sube fotos de alta calidad, descripciones y precios en pesos colombianos.
                    </p>
                </div>
                <span class="module-card-link">Abrir catálogo &rarr;</span>
            </a>

            <!-- Mesas y QR -->
            <a href="<?= e(app_url('/admin/mesas')) ?>" class="module-card">
                <div>
                    <div class="module-card-icon icon-indigo">
                        <?= icon('cutlery', 26) ?>
                    </div>
                    <h3 class="module-card-title">Mesas &amp; Códigos QR</h3>
                    <p class="module-card-desc">
                        Genera códigos QR de alta resolución para cada mesa del local y descarga el PDF imprimible listo para recortar y laminar.
                    </p>
                </div>
                <span class="module-card-link text-indigo">Gestionar mesas y QR &rarr;</span>
            </a>

            <!-- Control de Agotados -->
            <a href="<?= e(app_url('/admin/disponibilidad')) ?>" class="module-card module-card-danger">
                <div>
                    <div class="module-card-icon">
                        <?= icon('zap', 26) ?>
                    </div>
                    <h3 class="module-card-title">Control de Agotados</h3>
                    <p class="module-card-desc">
                        Diseñado para la hora pico: apaga un plato en 1 segundo cuando se agote un ingrediente para que los clientes no puedan pedirlo.
                    </p>
                </div>
                <span class="module-card-link">Ver disponibles y agotados &rarr;</span>
            </a>

            <!-- Modificadores -->
            <a href="<?= e(app_url('/admin/modificadores')) ?>" class="module-card">
                <div>
                    <div class="module-card-icon text-muted">
                        <?= icon('modifiers', 26) ?>
                    </div>
                    <h3 class="module-card-title">Modificadores y Adiciones</h3>
                    <p class="module-card-desc">
                        Configura preguntas obligatorias (ej: término de carne) o adiciones con costo adicional (ej: tocineta o queso extra).
                    </p>
                </div>
                <span class="module-card-link">Gestionar modificadores &rarr;</span>
            </a>
        </div>
    </div>
</div>

<!-- MODAL PARA PAGAR SUSCRIPCIÓN A MESALY (INTEGRACIÓN WOMPI) -->
<div class="modal is-hidden" id="modalPagarMesaly" role="dialog" aria-modal="true">
    <div class="modal-backdrop" onclick="cerrarModalPagarMesaly()"></div>
    <div class="modal-dialog max-w-500">
        <form method="POST" action="<?= e(app_url('/admin/pagar-suscripcion')) ?>" class="m-0">
            <?= csrf_field() ?>
            <div class="modal-header">
                <div>
                    <h3 class="modal-title text-lg fw-700 m-0">Pagar Suscripción a Mesaly</h3>
                    <span class="text-xs text-muted">Pasarela Oficial Wompi Colombia (Bancolombia)</span>
                </div>
                <button type="button" class="btn-close" onclick="cerrarModalPagarMesaly()">&times;</button>
            </div>
            <div class="modal-body p-5">
                <div class="card p-3 mb-4 bg-subtle border flex items-center justify-between">
                    <div>
                        <span class="text-xs text-muted block">Restaurante:</span>
                        <strong class="text-sm text-main"><?= e($tenant['nombre']) ?></strong>
                    </div>
                    <div class="text-right">
                        <span class="text-xs text-muted block">Tarifa mensual:</span>
                        <strong class="text-sm text-brand"><?= format_cop((int)($tenant['precio'] ?? 49000)) ?></strong>
                    </div>
                </div>

                <div class="form-group mb-4">
                    <label class="form-label text-sm fw-600 mb-1" for="periodosSuscripcion">Tiempo a Renovar</label>
                    <select name="periodos" id="periodosSuscripcion" class="form-select" onchange="actualizarTotalWompi()">
                        <option value="1">1 Mes (+30 días) — <?= format_cop((int)($tenant['precio'] ?? 49000)) ?></option>
                        <option value="2">2 Meses (+60 días) — <?= format_cop((int)(($tenant['precio'] ?? 49000) * 2)) ?></option>
                        <option value="3">3 Meses (+90 días) — <?= format_cop((int)(($tenant['precio'] ?? 49000) * 3)) ?></option>
                        <option value="6">6 Meses (+180 días) — <?= format_cop((int)(($tenant['precio'] ?? 49000) * 6)) ?></option>
                    </select>
                </div>

                <div class="form-group mb-4">
                    <label class="form-label text-sm fw-600 mb-1" for="metodoWompi">Método de Pago Wompi</label>
                    <select name="metodo" id="metodoWompi" class="form-select">
                        <option value="Wompi - Tarjeta de Crédito / Débito (Visa, Mastercard, AMEX)">Tarjeta de Crédito / Débito (Visa, Mastercard, AMEX)</option>
                        <option value="Wompi - PSE / Transferencia en Línea">PSE / Débito Bancario en Línea</option>
                        <option value="Wompi - Nequi">Nequi (Pago directo QR / Push)</option>
                        <option value="Wompi - Botón Bancolombia">Botón Bancolombia Transferencia</option>
                    </select>
                </div>

                <div class="alert alert-info p-3 mb-4 text-xs">
                    <?= icon('sparkles', 16) ?>
                    <div>
                        <strong>Procesamiento Inmediato:</strong> Al confirmar el pago, la fecha de vencimiento se extenderá automáticamente y se registrará en el historial de pagos del Super Admin.
                    </div>
                </div>

                <div class="p-3 bg-subtle rounded border flex items-center justify-between">
                    <span class="text-sm fw-600 text-muted">Total a Pagar Wompi:</span>
                    <strong class="text-xl text-brand" id="totalPagarDisplay"><?= format_cop((int)($tenant['precio'] ?? 49000)) ?></strong>
                </div>
            </div>
            <div class="modal-footer p-4 border-t flex items-center justify-between">
                <button type="button" class="btn btn-secondary btn-sm" onclick="cerrarModalPagarMesaly()">Cancelar</button>
                <button type="submit" class="btn btn-primary btn-sm fw-600 inline-flex items-center gap-2">
                    <?= icon('check', 14) ?>
                    <span>Confirmar y Pagar con Wompi</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script nonce="<?= e($cspNonce) ?>">
const precioBaseMensual = <?= (int)($tenant['precio'] ?? 49000) ?>;

function abrirModalPagarMesaly() {
    document.getElementById('modalPagarMesaly').classList.remove('is-hidden');
}

function cerrarModalPagarMesaly() {
    document.getElementById('modalPagarMesaly').classList.add('is-hidden');
}

function actualizarTotalWompi() {
    const sel = document.getElementById('periodosSuscripcion');
    const periodos = parseInt(sel.value, 10) || 1;
    const total = periodos * precioBaseMensual;
    document.getElementById('totalPagarDisplay').textContent = new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        maximumFractionDigits: 0
    }).format(total);
}
</script>
