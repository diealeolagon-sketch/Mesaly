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
        <button type="button" class="btn btn-secondary btn-sm fw-600 inline-flex items-center gap-2 btn-abrir-wompi" title="Pagar suscripción mensual a Mesaly">
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
        <button type="button" class="btn btn-primary btn-sm fw-600 inline-flex items-center gap-2 btn-abrir-wompi">
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
<div class="modal-backdrop modal-backdrop-modern" id="modalPagarMesaly" role="dialog" aria-modal="true">
    <div class="modal-dialog modal-wompi-dialog">
        <div class="modal-content m-0">
            <!-- CABECERA DE MARCA MESALY + WOMPI -->
            <div class="modal-wompi-header">
                <div class="modal-wompi-brand-group">
                    <img src="<?= e(asset_url('/assets/img/logo-horizontal.svg')) ?>" alt="Mesaly" class="modal-wompi-mesaly-logo">
                    <span class="modal-wompi-divider"></span>
                    <img src="<?= e(asset_url('/assets/img/payments/wompi.svg')) ?>" alt="Wompi" class="modal-wompi-wompi-logo">
                </div>
                <button type="button" class="modal-wompi-close-btn btn-cerrar-wompi" aria-label="Cerrar modal" title="Cerrar">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            <div class="modal-body modal-wompi-body">
                <!-- TARJETA PRINCIPAL DE PLAN Y TARIFA -->
                <div class="modal-wompi-plan-box">
                    <div class="modal-wompi-plan-info">
                        <span class="modal-wompi-plan-badge">Suscripción SaaS Activa</span>
                        <h4 class="modal-wompi-restaurant-name">
                            <?= icon('store', 16) ?>
                            <span><?= e($tenant['nombre']) ?></span>
                        </h4>
                    </div>
                    <div class="modal-wompi-price-block">
                        <span class="modal-wompi-price-amount"><?= format_cop((int)($tenant['precio'] ?? 49000)) ?></span>
                        <span class="modal-wompi-price-cycle">/ 1 Mes Calendario</span>
                    </div>
                </div>

                <!-- TIRA DE LOGOTIPOS OFICIALES DE MEDIOS DE PAGO -->
                <div class="modal-wompi-section">
                    <span class="modal-wompi-section-title">Medios de Pago Habilitados en Wompi</span>
                    <div class="modal-wompi-logos-grid">
                        <div class="payment-logo-card" title="Visa">
                            <img src="<?= e(asset_url('/assets/img/payments/visa.svg')) ?>" alt="Visa" class="payment-logo-img">
                        </div>
                        <div class="payment-logo-card" title="Mastercard">
                            <img src="<?= e(asset_url('/assets/img/payments/mastercard.svg')) ?>" alt="Mastercard" class="payment-logo-img">
                        </div>
                        <div class="payment-logo-card" title="PSE">
                            <img src="<?= e(asset_url('/assets/img/payments/pse.svg')) ?>" alt="PSE" class="payment-logo-img">
                        </div>
                        <div class="payment-logo-card" title="Nequi">
                            <img src="<?= e(asset_url('/assets/img/payments/nequi.svg')) ?>" alt="Nequi" class="payment-logo-img">
                        </div>
                        <div class="payment-logo-card" title="Bancolombia">
                            <img src="<?= e(asset_url('/assets/img/payments/bancolombia.svg')) ?>" alt="Bancolombia" class="payment-logo-img">
                        </div>
                        <div class="payment-logo-card" title="Daviplata">
                            <img src="<?= e(asset_url('/assets/img/payments/daviplata.svg')) ?>" alt="Daviplata" class="payment-logo-img">
                        </div>
                    </div>
                </div>

                <!-- BOTÓN PRINCIPAL DE PAGO WOMPI -->
                <div class="modal-wompi-section">
                    <a href="https://checkout.wompi.co/l/VPOS_8rydsK" 
                       target="_blank" 
                       rel="noopener noreferrer" 
                       class="btn-wompi-primary-pay">
                        <span>Pagar con Wompi (Link Oficial)</span>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="7" y1="17" x2="17" y2="7"></line>
                            <polyline points="7 7 17 7 17 17"></polyline>
                        </svg>
                    </a>
                    <span class="modal-wompi-security-note">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                        <span>Se abrirá el checkout seguro de Wompi Bancolombia en una pestaña nueva.</span>
                    </span>
                </div>

                <!-- SECCIÓN DE NOTIFICACIÓN WHATSAPP -->
                <div class="modal-wompi-wa-card">
                    <div class="modal-wompi-wa-text">
                        <strong>¿Ya completaste tu pago?</strong>
                        <p>Notifícanos por WhatsApp con tu soporte para renovar tu suscripción de inmediato.</p>
                    </div>
                    <?php 
                        $msgWa = "Hola equipo Mesaly, acabo de realizar el pago de la suscripción mensual para el restaurante " . ($tenant['nombre'] ?? 'Mi Restaurante') . " por valor de " . format_cop((int)($tenant['precio'] ?? 49000)) . " a través de Wompi. Adjunto el comprobante para la renovación.";
                    ?>
                    <a href="<?= e(support_whatsapp_url($msgWa)) ?>" 
                       target="_blank" 
                       rel="noopener noreferrer" 
                       class="btn-wompi-wa-link">
                        <?= icon('whatsapp', 16) ?>
                        <span>Enviar Comprobante</span>
                    </a>
                </div>
            </div>

            <div class="modal-wompi-footer">
                <span class="modal-wompi-footer-ssl">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                    <span>Cifrado SSL 256-bit Wompi Bancolombia</span>
                </span>
                <button type="button" class="btn btn-secondary btn-sm btn-cerrar-wompi modal-wompi-btn-close-footer">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<style nonce="<?= e($cspNonce) ?>">
.modal-wompi-dialog {
    max-width: 520px !important;
    border-radius: 24px !important;
    box-shadow: 0 30px 70px -15px rgba(15, 23, 42, 0.45) !important;
    border: 1px solid #E2E8F0 !important;
    overflow: hidden !important;
    background: #FFFFFF !important;
    padding: 0 !important;
}
.modal-wompi-header {
    background: #FFFFFF;
    border-bottom: 1px solid #F1F5F9;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 18px 24px;
}
.modal-wompi-brand-group {
    display: flex;
    align-items: center;
    gap: 14px;
}
.modal-wompi-mesaly-logo {
    height: 26px;
    width: auto;
    object-fit: contain;
}
.modal-wompi-divider {
    width: 1px;
    height: 22px;
    background: #CBD5E1;
}
.modal-wompi-wompi-logo {
    height: 22px;
    width: auto;
    object-fit: contain;
}
.modal-wompi-close-btn {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    border: 1px solid #E2E8F0;
    background: #F8FAFC;
    color: #64748B;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
}
.modal-wompi-close-btn:hover {
    background: #E2E8F0;
    color: #0F172A;
}
.modal-wompi-body {
    padding: 22px 24px !important;
}
.modal-wompi-plan-box {
    background: linear-gradient(135deg, #FFF7ED 0%, #FFEDD5 100%);
    border: 1px solid #FED7AA;
    border-radius: 16px;
    padding: 16px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
}
.modal-wompi-plan-badge {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #C2410C;
    display: block;
    margin-bottom: 4px;
}
.modal-wompi-restaurant-name {
    font-size: 16px;
    font-weight: 700;
    color: #1E293B;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 6px;
}
.modal-wompi-price-block {
    text-align: right;
}
.modal-wompi-price-amount {
    font-size: 22px;
    font-weight: 800;
    color: #EA580C;
    display: block;
    line-height: 1.1;
}
.modal-wompi-price-cycle {
    font-size: 11.5px;
    font-weight: 600;
    color: #9A3412;
    display: block;
    margin-top: 2px;
}
.modal-wompi-section {
    margin-bottom: 20px;
}
.modal-wompi-section-title {
    display: block;
    font-size: 12px;
    font-weight: 700;
    color: #475569;
    margin-bottom: 10px;
}
.modal-wompi-logos-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 8px;
}
.payment-logo-card {
    background: #FFFFFF;
    border: 1px solid #E2E8F0;
    border-radius: 10px;
    padding: 6px 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 42px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    transition: transform 0.15s ease, border-color 0.15s ease;
}
.payment-logo-card:hover {
    transform: translateY(-2px);
    border-color: #CBD5E1;
}
.payment-logo-img {
    max-height: 24px;
    max-width: 90%;
    object-fit: contain;
}
.btn-wompi-primary-pay {
    background: linear-gradient(135deg, #EA580C 0%, #C2410C 100%);
    color: #FFFFFF !important;
    border: none;
    border-radius: 14px;
    padding: 14px 20px;
    font-size: 15px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    text-decoration: none !important;
    box-shadow: 0 6px 20px rgba(234, 88, 12, 0.32);
    transition: transform 0.15s ease, box-shadow 0.15s ease;
}
.btn-wompi-primary-pay:hover {
    transform: translateY(-1px);
    box-shadow: 0 8px 24px rgba(234, 88, 12, 0.42);
}
.modal-wompi-security-note {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    font-size: 11.5px;
    color: #64748B;
    margin-top: 8px;
    text-align: center;
}
.modal-wompi-wa-card {
    background: #F0FDF4;
    border: 1px solid #BBF7D0;
    border-radius: 14px;
    padding: 12px 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}
.modal-wompi-wa-text strong {
    font-size: 12.5px;
    color: #166534;
    display: block;
    margin-bottom: 2px;
}
.modal-wompi-wa-text p {
    font-size: 11.5px;
    color: #15803D;
    margin: 0;
    line-height: 1.3;
}
.btn-wompi-wa-link {
    background: #25D366;
    color: #FFFFFF !important;
    border-radius: 10px;
    padding: 8px 12px;
    font-size: 12px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none !important;
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(37, 211, 102, 0.25);
    transition: background 0.15s ease;
}
.btn-wompi-wa-link:hover {
    background: #1EBE5D;
}
.modal-wompi-footer {
    background: #F8FAFC;
    border-top: 1px solid #F1F5F9;
    padding: 14px 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.modal-wompi-footer-ssl {
    font-size: 11.5px;
    color: #64748B;
    display: flex;
    align-items: center;
    gap: 6px;
}
.modal-wompi-btn-close-footer {
    border-radius: 8px;
    font-weight: 600;
}
</style>

<script nonce="<?= e($cspNonce) ?>">
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('modalPagarMesaly');
    if (!modal) return;

    document.querySelectorAll('.btn-abrir-wompi').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            modal.classList.add('is-open', 'is-active');
        });
    });

    document.querySelectorAll('.btn-cerrar-wompi').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            modal.classList.remove('is-open', 'is-active');
        });
    });

    modal.addEventListener('click', function (e) {
        if (e.target === modal) {
            modal.classList.remove('is-open', 'is-active');
        }
    });
});
</script>
