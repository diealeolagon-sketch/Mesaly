<div class="content-header mb-4">
    <div>
        <h1 class="page-title text-2xl mb-1 flex items-center gap-2">
            <span>Gestión de Pedidos en Vivo</span>
            <span class="live-dot-pulse" title="Actualización en tiempo real"></span>
        </h1>
        <p class="page-subtitle text-base text-muted m-0">
            Turno operativo de hoy (<?= e(date('d/m/Y')) ?>). Control de comandas de salón, despachos de domicilio y notificaciones automáticas.
        </p>
    </div>

    <div class="content-actions flex items-center gap-2 flex-wrap">
        <span class="badge badge-subtle-brand py-1 px-3 inline-flex items-center gap-2 text-sm fw-700" id="badgePedidosActivos">
            <span class="dot-indicator dot-indicator-success"></span>
            <span data-count-key="pendientes"><?= (int)($conteos['pendientes'] ?? 0) ?></span> pedido(s) activos
        </span>

        <a href="<?= e(app_url('/admin/reportes?tab=datos&from=pedidos')) ?>" class="btn btn-secondary btn-sm fw-600 flex items-center gap-1.5" title="Ver auditoría de ventas, reportes y comandas históricas">
            <?= icon('chart', 14) ?>
            <span>Ver Reportes y Cierre &rarr;</span>
        </a>

        <a href="<?= e(app_url('/' . $tenant['slug'])) ?>" target="_blank" rel="noopener" class="btn btn-secondary btn-sm fw-600">
            <?= icon('eye', 14) ?>
            <span>Ver Menú Digital</span>
        </a>
    </div>
</div>

<!-- 1. PESTAÑAS DE VISTA OPERATIVA: SEPARACIÓN DOMICILIOS VS SALÓN -->
<div class="admin-order-views-tabs">
    <a href="<?= e(app_url('/admin/pedidos?vista=domicilios' . (!empty($filtros['estado']) ? '&estado=' . urlencode($filtros['estado']) : ''))) ?>" 
       class="admin-order-view-tab <?= ($vista === 'domicilios') ? 'active' : '' ?>">
        <?= icon('motorcycle', 16) ?>
        <span>Despacho de Domicilios</span>
        <span class="tab-badge" data-count-key="modalidad_domicilio"><?= (int)($conteos['modalidad_domicilio'] ?? 0) ?></span>
    </a>

    <a href="<?= e(app_url('/admin/pedidos?vista=local' . (!empty($filtros['estado']) ? '&estado=' . urlencode($filtros['estado']) : ''))) ?>" 
       class="admin-order-view-tab <?= ($vista === 'local') ? 'active' : '' ?>">
        <?= icon('cutlery', 16) ?>
        <span>Salón en Mesa</span>
        <span class="tab-badge" data-count-key="modalidad_local"><?= (int)($conteos['modalidad_local'] ?? 0) ?></span>
    </a>

    <a href="<?= e(app_url('/admin/pedidos?vista=todos' . (!empty($filtros['estado']) ? '&estado=' . urlencode($filtros['estado']) : ''))) ?>" 
       class="admin-order-view-tab <?= ($vista === 'todos') ? 'active' : '' ?>">
        <?= icon('list', 16) ?>
        <span>Todos los Pedidos</span>
        <span class="tab-badge" data-count-key="pendientes"><?= (int)($conteos['pendientes'] ?? 0) ?></span>
    </a>
</div>

<!-- 2. PESTAÑAS DE FILTRO RÁPIDO POR ESTADO (5 ESTADOS SIMPLIFICADOS) -->
<div class="filter-bar-card mb-4">
    <div class="filter-pills-group">
        <a href="<?= e(app_url('/admin/pedidos?vista=' . urlencode($vista) . (!empty($filtros['q']) ? '&q=' . urlencode($filtros['q']) : ''))) ?>" 
           class="filter-pill <?= empty($filtros['estado']) ? 'active' : '' ?>"
           data-filtro-estado="">
            <span>Todos los estados</span>
            <span class="filter-count" data-count-key="todos"><?= (int)$conteos['todos'] ?></span>
        </a>

        <?php foreach ($estadosConfig as $codigoEst => $cfg): ?>
            <?php 
            $queryUrl = '?vista=' . urlencode($vista) . '&estado=' . urlencode($codigoEst);
            if (!empty($filtros['q'])) $queryUrl .= '&q=' . urlencode($filtros['q']);
            ?>
            <a href="<?= e(app_url('/admin/pedidos' . $queryUrl)) ?>" 
               class="filter-pill <?= ($filtros['estado'] ?? '') === $codigoEst ? 'active' : '' ?>"
               data-filtro-estado="<?= e($codigoEst) ?>">
                <span><?= e($cfg['label']) ?></span>
                <span class="filter-count" data-count-key="<?= e($codigoEst) ?>"><?= (int)($conteos[$codigoEst] ?? 0) ?></span>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<!-- 3. BARRA DE BÚSQUEDA RÁPIDA EN PEDIDOS DE HOY -->
<div class="card card-p-4 mb-4">
    <form method="GET" action="<?= e(app_url('/admin/pedidos')) ?>" class="flex items-center justify-between gap-3 flex-wrap">
        <input type="hidden" name="vista" value="<?= e($vista) ?>">
        <?php if (!empty($filtros['estado'])): ?>
            <input type="hidden" name="estado" value="<?= e($filtros['estado']) ?>">
        <?php endif; ?>

        <div class="flex items-center gap-3 flex-wrap flex-1 min-w-280">
            <div class="price-input-wrapper max-w-sm w-full">
                <input type="text" name="q" value="<?= e($filtros['q']) ?>" class="form-control price-input-field" placeholder="Buscar en pedidos de hoy (#código, cliente o teléfono)...">
                <span class="price-prefix flex items-center">
                    <?= icon('search', 14) ?>
                </span>
            </div>

            <button type="submit" class="btn btn-secondary btn-sm fw-600">
                <span>Buscar</span>
            </button>

            <?php if (!empty($filtros['q']) || !empty($filtros['estado'])): ?>
                <a href="<?= e(app_url('/admin/pedidos?vista=' . urlencode($vista))) ?>" 
                   class="btn btn-secondary btn-sm text-danger inline-flex items-center" title="Limpiar búsqueda">
                    &times; Limpiar
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- 4. CONTENIDO SEGÚN LA VISTA SELECCIONADA -->
<div id="pedidosTablesWrapper">
<?php if ($vista === 'domicilios'): ?>
    <!-- ═══ VISTA 1: TABLA ESPECIALIZADA DE DOMICILIOS ═══ -->
    <div class="card p-0 overflow-hidden mb-5">
        <div class="table-header-domicilio">
            <div class="flex items-center gap-2">
                <span class="text-brand flex items-center"><?= icon('motorcycle', 18) ?></span>
                <h3 class="text-sm fw-700 text-main m-0">Despacho de Domicilios</h3>
            </div>
            <span class="badge badge-orange-subtle">
                <?= count($pedidos) ?> despacho(s) en esta vista
            </span>
        </div>

        <?php if (empty($pedidos)): ?>
            <div class="empty-state-box p-6">
                <div class="icon-bubble-lg mx-auto mb-3">
                    <?= icon('motorcycle', 24) ?>
                </div>
                <h3 class="text-md fw-700 text-main m-0 mb-1">
                    No hay pedidos de domicilio
                </h3>
                <p class="text-muted text-sm max-w-md mx-auto m-0">
                    Los pedidos solicitados para entrega a domicilio con dirección y teléfono aparecerán en esta tabla.
                </p>
            </div>
        <?php else: ?>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th># Pedido / Hora</th>
                            <th>Cliente &amp; Contacto</th>
                            <th>Dirección de Entrega</th>
                            <th>Total</th>
                            <th>Estado del Reparto</th>
                            <th class="text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pedidos as $p): ?>
                            <tr id="pedido_fila_<?= (int)$p['id'] ?>">
                                <!-- Código y Hora -->
                                <td>
                                    <div class="stack gap-1">
                                        <strong class="table-row-code">
                                            #<?= e($p['codigo']) ?>
                                        </strong>
                                        <span class="text-xs text-muted">
                                            <?= date('d/m/Y h:i A', strtotime($p['created_at'])) ?>
                                        </span>
                                    </div>
                                </td>

                                <!-- Cliente & Teléfono -->
                                <td>
                                    <div class="stack gap-1">
                                        <strong class="text-sm text-main">
                                            <?= e($p['cliente_nombre']) ?>
                                        </strong>
                                        <?php if (!empty($p['cliente_telefono'])): ?>
                                            <div class="flex items-center gap-1">
                                                <span class="text-xs text-muted tabular">
                                                    <?= e($p['cliente_telefono']) ?>
                                                </span>
                                                <?php if (!empty($p['wa_enlace'])): ?>
                                                    <a href="<?= e($p['wa_enlace']) ?>" target="_blank" rel="noopener" class="text-success inline-flex items-center" title="Escribir al cliente por WhatsApp">
                                                        <?= icon('whatsapp', 13) ?>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </td>

                                <!-- Dirección y Barrio -->
                                <td>
                                    <div class="stack gap-1">
                                        <strong class="text-xs text-main">
                                            <?= e($p['direccion'] ?: 'Sin dirección') ?>
                                        </strong>
                                        <?php if (!empty($p['barrio'])): ?>
                                            <span class="text-xs text-muted">
                                                Barrio: <?= e($p['barrio']) ?>
                                            </span>
                                        <?php endif; ?>
                                        <?php if (!empty($p['notas_entrega'])): ?>
                                            <span class="text-xs text-orange-bold">
                                                Indicación: <?= e($p['notas_entrega']) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </td>

                                <!-- Total -->
                                <td>
                                    <strong class="table-row-total">
                                        <?= format_cop((int)$p['total']) ?>
                                    </strong>
                                </td>

                                <!-- Estado con Aviso en "En Camino" -->
                                <td>
                                    <form method="POST" action="<?= e(app_url('/admin/pedidos/' . $p['id'] . '/estado')) ?>" class="form-cambio-estado m-0">
                                        <?= csrf_field() ?>
                                        <select name="estado" class="form-control select-estado-pedido estado-sel--<?= e($p['estado']) ?>" data-id="<?= (int)$p['id'] ?>">
                                            <?php foreach ($estadosConfig as $eCod => $eCfg): ?>
                                                <option value="<?= e($eCod) ?>" <?= $p['estado'] === $eCod ? 'selected' : '' ?>>
                                                    <?= e($eCfg['label']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </form>
                                </td>

                                <!-- Acciones -->
                                <td class="text-right">
                                    <div class="inline-flex items-center justify-end gap-2">
                                        <button type="button" class="btn btn-secondary btn-sm btn-ver-detalle" data-id="<?= (int)$p['id'] ?>" title="Ver platos del pedido">
                                            <?= icon('eye', 13) ?>
                                            <span>Detalle</span>
                                        </button>
                                        <?php if (!empty($p['wa_enlace'])): ?>
                                            <a href="<?= e($p['wa_enlace']) ?>" target="_blank" rel="noopener" class="btn btn-secondary btn-sm btn-wa-notify-inline" title="Avisar al cliente por WhatsApp">
                                                <?= icon('whatsapp', 14) ?>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

<?php elseif ($vista === 'local'): ?>
    <!-- ═══ VISTA 2: TABLA ESPECIALIZADA DE SALÓN EN MESA ═══ -->
    <div class="card p-0 overflow-hidden mb-5">
        <div class="table-header-salon">
            <div class="flex items-center gap-2">
                <span class="text-indigo flex items-center"><?= icon('cutlery', 18) ?></span>
                <h3 class="text-sm fw-700 text-main m-0">Salón en Mesa</h3>
            </div>
            <span class="badge badge-blue-subtle">
                <?= count($pedidos) ?> orden(es) en esta vista
            </span>
        </div>

        <?php if (empty($pedidos)): ?>
            <div class="empty-state-box p-6">
                <div class="icon-bubble-lg mx-auto mb-3">
                    <?= icon('cutlery', 24) ?>
                </div>
                <h3 class="text-md fw-700 text-main m-0 mb-1">
                    No hay comandas de mesa en este momento
                </h3>
                <p class="text-muted text-sm max-w-md mx-auto m-0">
                    Las órdenes tomadas por los comensales directamente en sus mesas físicas se mostrarán aquí.
                </p>
            </div>
        <?php else: ?>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th># Pedido / Hora</th>
                            <th>Ubicación / Modalidad</th>
                            <th>Cliente / Comensal</th>
                            <th>Total</th>
                            <th>Estado de Cocina</th>
                            <th class="text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pedidos as $p): ?>
                            <tr id="pedido_fila_<?= (int)$p['id'] ?>">
                                <td>
                                    <div class="stack gap-1">
                                        <strong class="table-row-code">
                                            #<?= e($p['codigo']) ?>
                                        </strong>
                                        <span class="text-xs text-muted">
                                            <?= date('d/m/Y h:i A', strtotime($p['created_at'])) ?>
                                        </span>
                                    </div>
                                </td>

                                <td>
                                    <?php if ($p['modalidad'] === 'mesa'): ?>
                                        <span class="badge badge-subtle-brand py-1 px-2 inline-flex items-center gap-1 text-xs fw-700">
                                            <?= icon('cutlery', 13) ?>
                                            <span>Mesa <?= !empty($p['mesa_numero_rel']) ? (int)$p['mesa_numero_rel'] : (!empty($p['mesa_numero']) ? (int)$p['mesa_numero'] : '—') ?></span>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-neutral py-1 px-2 inline-flex items-center gap-1 text-xs fw-700">
                                            <?= icon('shopping-bag', 13) ?>
                                            <span>Para Llevar</span>
                                        </span>
                                        <?php if (!empty($p['hora_recogida'])): ?>
                                            <span class="text-xs text-muted block mt-1">
                                                <?= e($p['hora_recogida']) ?>
                                            </span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <div class="stack gap-1">
                                        <strong class="text-sm text-main">
                                            <?= e($p['cliente_nombre']) ?>
                                        </strong>
                                        <?php if (!empty($p['cliente_telefono'])): ?>
                                            <span class="text-xs text-muted">
                                                <?= e($p['cliente_telefono']) ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </td>

                                <td>
                                    <strong class="table-row-total">
                                        <?= format_cop((int)$p['total']) ?>
                                    </strong>
                                </td>

                                <td>
                                    <form method="POST" action="<?= e(app_url('/admin/pedidos/' . $p['id'] . '/estado')) ?>" class="form-cambio-estado m-0">
                                        <?= csrf_field() ?>
                                        <select name="estado" class="form-control select-estado-pedido estado-sel--<?= e($p['estado']) ?>" data-id="<?= (int)$p['id'] ?>">
                                            <?php foreach ($estadosConfig as $eCod => $eCfg): ?>
                                                <option value="<?= e($eCod) ?>" <?= $p['estado'] === $eCod ? 'selected' : '' ?>>
                                                    <?= e($eCfg['label']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </form>
                                </td>

                                <td class="text-right">
                                    <div class="inline-flex items-center justify-end gap-2">
                                        <button type="button" class="btn btn-secondary btn-sm btn-ver-detalle" data-id="<?= (int)$p['id'] ?>" title="Ver platos y notas de cocina">
                                            <?= icon('eye', 13) ?>
                                            <span>Detalle</span>
                                        </button>
                                        <?php if (!empty($p['wa_enlace'])): ?>
                                            <a href="<?= e($p['wa_enlace']) ?>" target="_blank" rel="noopener" class="btn btn-secondary btn-sm btn-wa-notify-inline" title="WhatsApp del cliente">
                                                <?= icon('whatsapp', 14) ?>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

<?php else: ?>
    <!-- ═══ VISTA 3: AMBAS TABLAS SEPARADAS ORDENADAMENTE ═══ -->

    <!-- TABLA 1: DESPACHOS A DOMICILIO -->
    <div class="card p-0 overflow-hidden mb-5">
        <div class="table-header-domicilio">
            <div class="flex items-center gap-2">
                <span class="text-brand flex items-center"><?= icon('motorcycle', 18) ?></span>
                <h3 class="text-sm fw-700 text-main m-0">Tabla de Domicilios</h3>
            </div>
            <a href="<?= e(app_url('/admin/pedidos?vista=domicilios')) ?>" class="btn btn-secondary btn-sm text-xs py-1 px-3">
                Ver solo Domicilios (<?= count($pedidosDomicilio) ?>)
            </a>
        </div>

        <?php if (empty($pedidosDomicilio)): ?>
            <div class="empty-state-box p-5 text-muted text-sm">
                No hay pedidos de domicilio en este rango.
            </div>
        <?php else: ?>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th># Pedido</th>
                            <th>Cliente &amp; Teléfono</th>
                            <th>Dirección de Entrega</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th class="text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pedidosDomicilio as $p): ?>
                            <tr id="pedido_fila_<?= (int)$p['id'] ?>">
                                <td>
                                    <strong class="table-row-code">#<?= e($p['codigo']) ?></strong>
                                    <span class="text-xs text-muted block"><?= date('d/m h:i A', strtotime($p['created_at'])) ?></span>
                                </td>
                                <td>
                                    <strong class="text-sm text-main"><?= e($p['cliente_nombre']) ?></strong>
                                    <span class="text-xs text-muted block"><?= e($p['cliente_telefono'] ?: 'Sin teléfono') ?></span>
                                </td>
                                <td>
                                    <span class="text-xs text-main fw-600"><?= e($p['direccion'] ?: '—') ?></span>
                                    <?php if (!empty($p['barrio'])): ?>
                                        <span class="text-xs text-muted block"><?= e($p['barrio']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong class="table-row-total"><?= format_cop((int)$p['total']) ?></strong>
                                </td>
                                <td>
                                    <form method="POST" action="<?= e(app_url('/admin/pedidos/' . $p['id'] . '/estado')) ?>" class="form-cambio-estado m-0">
                                        <?= csrf_field() ?>
                                        <select name="estado" class="form-control select-estado-pedido estado-sel--<?= e($p['estado']) ?>" data-id="<?= (int)$p['id'] ?>">
                                            <?php foreach ($estadosConfig as $eCod => $eCfg): ?>
                                                <option value="<?= e($eCod) ?>" <?= $p['estado'] === $eCod ? 'selected' : '' ?>>
                                                    <?= e($eCfg['label']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </form>
                                </td>
                                <td class="text-right">
                                    <button type="button" class="btn btn-secondary btn-sm btn-ver-detalle" data-id="<?= (int)$p['id'] ?>">Detalle</button>
                                    <?php if (!empty($p['wa_enlace'])): ?>
                                        <a href="<?= e($p['wa_enlace']) ?>" target="_blank" rel="noopener" class="btn btn-secondary btn-sm btn-wa-notify-sm">
                                            <?= icon('whatsapp', 13) ?>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- TABLA 2: SALÓN EN MESA -->
    <div class="card p-0 overflow-hidden mb-5">
        <div class="table-header-salon">
            <div class="flex items-center gap-2">
                <span class="text-indigo flex items-center"><?= icon('cutlery', 18) ?></span>
                <h3 class="text-sm fw-700 text-main m-0">Tabla de Salón en Mesa</h3>
            </div>
            <a href="<?= e(app_url('/admin/pedidos?vista=local')) ?>" class="btn btn-secondary btn-sm text-xs py-1 px-3">
                Ver solo Salón (<?= count($pedidosLocal) ?>)
            </a>
        </div>

        <?php if (empty($pedidosLocal)): ?>
            <div class="empty-state-box p-5 text-muted text-sm">
                No hay órdenes de salón en este rango.
            </div>
        <?php else: ?>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th># Pedido</th>
                            <th>Ubicación</th>
                            <th>Cliente</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th class="text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pedidosLocal as $p): ?>
                            <tr id="pedido_fila_<?= (int)$p['id'] ?>">
                                <td>
                                    <strong class="table-row-code">#<?= e($p['codigo']) ?></strong>
                                    <span class="text-xs text-muted block"><?= date('d/m h:i A', strtotime($p['created_at'])) ?></span>
                                </td>
                                <td>
                                    <?php if ($p['modalidad'] === 'mesa'): ?>
                                        <span class="badge badge-subtle-brand py-1 px-2 text-xs fw-700">
                                            Mesa <?= !empty($p['mesa_numero_rel']) ? (int)$p['mesa_numero_rel'] : (!empty($p['mesa_numero']) ? (int)$p['mesa_numero'] : '—') ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-neutral py-1 px-2 text-xs fw-700">
                                            Para Llevar
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong class="text-sm text-main"><?= e($p['cliente_nombre']) ?></strong>
                                </td>
                                <td>
                                    <strong class="table-row-total"><?= format_cop((int)$p['total']) ?></strong>
                                </td>
                                <td>
                                    <form method="POST" action="<?= e(app_url('/admin/pedidos/' . $p['id'] . '/estado')) ?>" class="form-cambio-estado m-0">
                                        <?= csrf_field() ?>
                                        <select name="estado" class="form-control select-estado-pedido estado-sel--<?= e($p['estado']) ?>" data-id="<?= (int)$p['id'] ?>">
                                            <?php foreach ($estadosConfig as $eCod => $eCfg): ?>
                                                <option value="<?= e($eCod) ?>" <?= $p['estado'] === $eCod ? 'selected' : '' ?>>
                                                    <?= e($eCfg['label']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </form>
                                </td>
                                <td class="text-right">
                                    <button type="button" class="btn btn-secondary btn-sm btn-ver-detalle" data-id="<?= (int)$p['id'] ?>">Detalle</button>
                                    <?php if (!empty($p['wa_enlace'])): ?>
                                        <a href="<?= e($p['wa_enlace']) ?>" target="_blank" rel="noopener" class="btn btn-secondary btn-sm btn-wa-notify-sm">
                                            <?= icon('whatsapp', 13) ?>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<!-- PAGINACIÓN GENERAL -->
<?php if ($totalPaginas > 1): ?>
    <div class="pagination-bar">
        <span class="text-xs text-muted">
            Página <strong><?= (int)$pagina ?></strong> de <strong><?= (int)$totalPaginas ?></strong> (<?= (int)$totalPedidos ?> pedidos en total)
        </span>
        <div class="flex gap-2">
            <?php if ($pagina > 1): ?>
                <a href="<?= e(app_url('/admin/pedidos?' . http_build_query(array_merge($filtros, ['vista' => $vista, 'page' => $pagina - 1])))) ?>" class="btn btn-secondary btn-sm">
                    &larr; Anterior
                </a>
            <?php endif; ?>
            <?php if ($pagina < $totalPaginas): ?>
                <a href="<?= e(app_url('/admin/pedidos?' . http_build_query(array_merge($filtros, ['vista' => $vista, 'page' => $pagina + 1])))) ?>" class="btn btn-secondary btn-sm">
                    Siguiente &rarr;
                </a>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>
</div>

<!-- ═══ MODAL: AVISO WHATSAPP CUANDO SALE EN CAMINO ═══ -->
<div class="modal-backdrop modal-backdrop-modern" id="modalAvisoWhatsApp">
    <div class="modal-dialog modal-aviso-wa-dialog">
        <div class="modal-aviso-wa-header">
            <div class="modal-brand-header">
                <div class="modal-brand-header-left">
                    <img src="<?= e(asset_url('/assets/img/logo-horizontal.svg')) ?>" alt="Mesaly" class="modal-brand-mesaly-logo">
                    <span class="modal-brand-divider"></span>
                    <div class="modal-brand-titles">
                        <h3 class="modal-brand-title">¡Pedido en Camino!</h3>
                        <span class="modal-brand-subtitle" id="avisoWaSubtitulo">Avisar al cliente por WhatsApp</span>
                    </div>
                </div>
                <button type="button" class="modal-btn-close-circle" id="btnCerrarModalAvisoWa" aria-label="Cerrar modal" title="Cerrar">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
        </div>

        <p class="modal-aviso-wa-text">
            El pedido <strong id="avisoWaCodigo">#0000</strong> de <strong id="avisoWaCliente">Cliente</strong> salió a despacho. ¿Deseas avisarle con el mensaje precargado?
        </p>

        <div class="modal-aviso-wa-card">
            <span class="text-xs fw-700 text-success uppercase block mb-1">
                Mensaje a enviar:
            </span>
            <div class="modal-aviso-wa-bubble" id="avisoWaMensaje">
                <!-- Inyectado vía JS -->
            </div>
        </div>

        <div class="modal-aviso-wa-footer">
            <button type="button" class="btn btn-secondary" id="btnCancelarAvisoWa">
                Continuar sin avisar
            </button>
            <a href="#" id="btnAbrirAvisoWa" target="_blank" rel="noopener" class="btn btn-primary btn-wa-notify">
                <?= icon('whatsapp', 16) ?>
                <span>Abrir WhatsApp y Notificar</span>
            </a>
        </div>
    </div>
</div>

<!-- ═══ MODAL: DETALLE COMPLETO DEL PEDIDO ═══ -->
<div class="modal-backdrop modal-backdrop-modern" id="modalDetallePedido">
    <div class="modal-dialog order-modal-dialog">
        
        <div class="order-detail-header modal-header-modern">
            <div class="modal-brand-header">
                <div class="modal-brand-header-left">
                    <img src="<?= e(asset_url('/assets/img/logo-horizontal.svg')) ?>" alt="Mesaly" class="modal-brand-mesaly-logo">
                    <div class="modal-brand-divider"></div>
                    <div class="modal-brand-titles">
                        <div class="flex items-center gap-2">
                            <h3 class="modal-brand-title font-mono" id="detCodigo" style="font-size: 16px; color: var(--color-brand, #E85D04);">
                                #PED-0000
                            </h3>
                            <span id="detEstadoBadge" class="badge">
                                Recibido
                            </span>
                        </div>
                        <span class="modal-brand-subtitle" id="detFecha">
                            01/01/2026 12:00 PM
                        </span>
                    </div>
                </div>
                <button type="button" class="modal-btn-close-circle" id="btnCerrarModalDetalle" aria-label="Cerrar modal" title="Cerrar">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
        </div>

        <div id="detLoading" class="empty-state-box p-6 text-muted">
            Cargando detalle del pedido...
        </div>

        <div id="detContent" class="order-detail-content-wrap is-hidden">
            <div class="modal-body-modern order-detail-body">
                <!-- Banner de Mesa y Liberación (Solo para pedidos en salón) -->
                <div id="detMesaCardWrap" class="p-3.5 rounded-xl border flex items-center justify-between flex-wrap gap-3 is-hidden" style="background: #FFFBF7; border-color: #FED7AA;">
                    <div class="flex items-center gap-3">
                        <div style="background: rgba(232, 93, 4, 0.12); color: #E85D04; padding: 10px; border-radius: 10px; display: flex; align-items: center;">
                            <?= icon('cutlery', 20) ?>
                        </div>
                        <div>
                            <strong class="text-base text-main" id="detMesaNumeroTxt">Mesa 1</strong>
                            <div class="mt-1">
                                <span id="detMesaStatusTxt" class="order-mesa-occupancy-pill is-occupied">Comensales en mesa (Consumiendo)</span>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn-liberar-mesa-modal" id="btnLiberarMesaModal" title="Marcar la mesa como desocupada para nuevos clientes">
                        <?= icon('check', 14) ?>
                        <span>Liberar Mesa Ahora</span>
                    </button>
                </div>

                <!-- Datos del Cliente y Entrega -->
                <div class="order-detail-customer-box">
                    <div class="order-detail-field-col">
                        <span class="order-detail-label">Cliente / Solicitante</span>
                        <strong class="order-detail-val-strong" id="detClienteNombre">—</strong>
                        <div id="detClienteTelWrap" class="order-detail-val-muted">—</div>
                    </div>

                    <div class="order-detail-field-col">
                        <span class="order-detail-label">Modalidad de Orden</span>
                        <strong class="order-detail-val-strong" id="detModalidad">—</strong>
                        <div id="detModalidadExtra" class="order-detail-val-muted">—</div>
                    </div>

                    <div class="order-detail-field-col full-col" id="detNotasWrap">
                        <span class="order-detail-label">Instrucciones / Notas Especiales</span>
                        <p class="order-detail-notes-box" id="detNotas">
                            —
                        </p>
                    </div>
                </div>

                <!-- Tabla de Ítems del Pedido -->
                <div>
                    <h4 class="text-sm fw-700 text-main m-0 mb-2">
                        Platos y Bebidas Ordenadas
                    </h4>
                    <div class="order-detail-table-card">
                        <table class="order-detail-table">
                            <thead>
                                <tr>
                                    <th class="col-left">Cant · Plato / Especificación</th>
                                    <th class="col-right">Total</th>
                                </tr>
                            </thead>
                            <tbody id="detItemsTbody">
                                <!-- Inyectado vía JS -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Resumen Financiero -->
                <div class="order-detail-totals-box">
                    <div class="order-detail-total-row">
                        <span>Subtotal:</span>
                        <strong class="text-main" id="detSubtotal">$ 0</strong>
                    </div>
                    <div class="order-detail-total-row" id="detEnvioRow">
                        <span>Costo de Envío:</span>
                        <strong class="text-main" id="detEnvio">$ 0</strong>
                    </div>
                    <div class="order-detail-total-row grand-row">
                        <span>Total a Cobrar:</span>
                        <span class="text-success fw-700 text-lg" id="detTotal">$ 0</span>
                    </div>
                </div>
            </div>

            <!-- Botones de Acción en el Modal -->
            <div class="order-detail-actions-row flex items-center justify-between flex-wrap gap-2">
                <div class="flex items-center gap-2">
                    <button type="button" class="btn btn-secondary btn-sm flex items-center gap-1.5" id="btnImprimirComandaModal" title="Imprimir ticket para cocina o comanda de mesa">
                        <?= icon('printer', 14) ?>
                        <span>Imprimir Comanda</span>
                    </button>
                    <a href="#" id="detBtnWa" target="_blank" rel="noopener" class="btn btn-primary btn-sm btn-wa-notify flex items-center gap-1.5">
                        <?= icon('whatsapp', 14) ?>
                        <span>WhatsApp</span>
                    </a>
                </div>

                <button type="button" class="btn btn-secondary btn-sm" id="btnCerrarModalDetalleFooter">
                    Cerrar
                </button>
            </div>

        </div>

    </div>
</div>

<script nonce="<?= e($cspNonce ?? '') ?>">
document.addEventListener('DOMContentLoaded', function () {
    const baseUrl = <?= json_encode(app_url(''), JSON_UNESCAPED_UNICODE) ?>;
    const filtroActivo = <?= json_encode($filtros['estado'] ?? '') ?>;
    const modal = document.getElementById('modalDetallePedido');
    const loading = document.getElementById('detLoading');
    const content = document.getElementById('detContent');
    const modalAvisoWa = document.getElementById('modalAvisoWhatsApp');

    // Función para actualizar los contadores en las pestañas en tiempo real
    function actualizarConteos(conteos) {
        if (!conteos) return;
        document.querySelectorAll('[data-count-key]').forEach(function (el) {
            const key = el.getAttribute('data-count-key');
            if (typeof conteos[key] !== 'undefined') {
                el.textContent = conteos[key];
            }
        });
    }

    // 1. Enlazar eventos de tabla (cambio de estado y ver detalle)
    function bindPedidosTableEvents() {
        document.querySelectorAll('.select-estado-pedido').forEach(function (select) {
            let estadoPrevio = select.value;
            select.addEventListener('focus', function () {
                estadoPrevio = this.value;
            });

            select.addEventListener('change', function () {
                const form = this.closest('form');
                const nuevoEstado = this.value;
                const selectEl = this;
                const row = selectEl.closest('tr');
                const codigoEl = row ? row.querySelector('.table-row-code') : null;
                const codigo = codigoEl ? codigoEl.textContent.trim() : '';

                const formData = new FormData(form);
                formData.set('estado', nuevoEstado);
                selectEl.disabled = true;

                fetch(form.action, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                })
                .then(r => r.json())
                .then(data => {
                    selectEl.disabled = false;
                    if (data.success) {
                        estadoPrevio = nuevoEstado;
                        selectEl.className = 'form-control select-estado-pedido estado-sel--' + nuevoEstado;

                        if (data.conteos) {
                            actualizarConteos(data.conteos);
                        }

                        if (typeof window.mostrarAdminToast === 'function') {
                            window.mostrarAdminToast('Pedido ' + codigo + ' marcado como ' + (data.label || nuevoEstado));
                        }

                        if (filtroActivo && nuevoEstado !== filtroActivo && row) {
                            row.classList.add('order-row--exit');
                            setTimeout(function () {
                                const tbody = row.closest('tbody');
                                row.remove();
                                if (tbody && tbody.querySelectorAll('tr').length === 0) {
                                    tbody.innerHTML = '<tr><td colspan="7" class="text-center py-5 text-muted">No hay más pedidos en este estado.</td></tr>';
                                }
                            }, 250);
                        }

                        if (data.aviso_whatsapp && data.aviso_whatsapp.wa_url) {
                            abrirModalAvisoWhatsApp(data.aviso_whatsapp);
                        }
                    } else {
                        selectEl.value = estadoPrevio;
                        alert(data.error || 'No se pudo actualizar el estado.');
                    }
                })
                .catch(() => {
                    selectEl.disabled = false;
                    selectEl.value = estadoPrevio;
                    alert('Error al comunicarse con el servidor.');
                });
            });
        });

        function cargarYMostrarPedido(id) {
            if (!id) return;
            modal.classList.add('is-open', 'is-active');
            loading.classList.remove('is-hidden');
            content.classList.add('is-hidden');

            fetch(baseUrl + '/admin/pedidos/' + id + '/detalle-json', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(data => {
                loading.classList.add('is-hidden');
                if (!data.success || !data.pedido) {
                    alert(data.error || 'Error al obtener pedido');
                    cerrarDetalle();
                    return;
                }

                const p = data.pedido;
                content.classList.remove('is-hidden');

                document.getElementById('detCodigo').innerText = '#' + p.codigo;
                const badge = document.getElementById('detEstadoBadge');
                badge.innerText = p.estado_info.label;
                badge.className = 'badge estado-badge--' + p.estado;
                document.getElementById('detFecha').innerText = p.fecha_formateada;

                // Banner de Mesa y Liberación
                const mesaCardWrap = document.getElementById('detMesaCardWrap');
                const btnLiberarMesaModal = document.getElementById('btnLiberarMesaModal');
                if (p.modalidad === 'mesa') {
                    mesaCardWrap.classList.remove('is-hidden');
                    const mesaNum = p.mesa_numero_rel || p.mesa_numero || 'S/N';
                    document.getElementById('detMesaNumeroTxt').innerText = 'Mesa ' + mesaNum;
                    const mesaStatusText = document.getElementById('detMesaStatusTxt');
                    if (p.mesa_liberada_at) {
                        mesaStatusText.innerText = 'Mesa Liberada (Desocupada)';
                        mesaStatusText.className = 'order-mesa-occupancy-pill is-free';
                        btnLiberarMesaModal.classList.add('is-hidden');
                    } else {
                        mesaStatusText.innerText = 'Comensales consumiendo en salón (Mesa Ocupada)';
                        mesaStatusText.className = 'order-mesa-occupancy-pill is-occupied';
                        btnLiberarMesaModal.classList.remove('is-hidden');
                        btnLiberarMesaModal.setAttribute('data-id', p.id);
                    }
                } else {
                    mesaCardWrap.classList.add('is-hidden');
                }

                document.getElementById('detClienteNombre').innerText = p.cliente_nombre;
                const telWrap = document.getElementById('detClienteTelWrap');
                if (p.cliente_telefono) {
                    telWrap.innerText = p.cliente_telefono;
                } else {
                    telWrap.innerText = 'Sin teléfono registrado';
                }

                document.getElementById('detModalidad').innerText = p.modalidad_label;
                const extraWrap = document.getElementById('detModalidadExtra');
                if (p.modalidad === 'mesa') {
                    extraWrap.innerText = 'Atención en salón · Mesa ' + (p.mesa_numero_rel || p.mesa_numero || '');
                } else if (p.modalidad === 'domicilio') {
                    extraWrap.innerText = (p.direccion || '') + (p.barrio ? ' (' + p.barrio + ')' : '');
                } else {
                    extraWrap.innerText = p.hora_recogida ? 'Hora acordada: ' + p.hora_recogida : 'Pasa a recoger al local';
                }

                const notasWrap = document.getElementById('detNotasWrap');
                if (p.notas_entrega) {
                    notasWrap.classList.remove('is-hidden');
                    document.getElementById('detNotas').innerText = p.notas_entrega;
                } else {
                    notasWrap.classList.add('is-hidden');
                }

                const tbody = document.getElementById('detItemsTbody');
                tbody.innerHTML = '';
                p.items.forEach(function (it) {
                    let opcionesHtml = '';
                    if (it.opciones && it.opciones.length > 0) {
                        opcionesHtml = '<div class="order-item-options-list mt-1">';
                        it.opciones.forEach(function (op) {
                            opcionesHtml += '<span class="order-item-option-row text-xs text-muted block">&bull; ' + op.nombre_grupo + ': <strong class="text-main">' + op.nombre_opcion + '</strong> (' + op.precio_extra_formateado + ')</span>';
                        });
                        opcionesHtml += '</div>';
                    }

                    let notaHtml = '';
                    if (it.nota) {
                        notaHtml = '<div class="order-item-note mt-1 text-xs text-warning fw-600 bg-amber-50 p-1.5 rounded border border-amber-200">Nota del comensal: ' + it.nota + '</div>';
                    }

                    const tr = document.createElement('tr');
                    tr.className = 'order-item-table-row border-b';
                    tr.innerHTML = `
                        <td class="order-item-cell-name py-2.5 px-3">
                            <span class="badge badge-subtle-brand text-xs font-bold mr-1.5">${it.cantidad}x</span>
                            <strong class="order-item-title text-main text-sm">${it.nombre_producto}</strong>
                            ${opcionesHtml}
                            ${notaHtml}
                        </td>
                        <td class="order-item-cell-total py-2.5 px-3 text-right font-bold text-main">
                            ${it.total_linea_formateado}
                        </td>
                    `;
                    tbody.appendChild(tr);
                });

                document.getElementById('detSubtotal').innerText = p.subtotal_formateado;
                const envioRow = document.getElementById('detEnvioRow');
                if (p.costo_envio > 0) {
                    envioRow.classList.remove('is-hidden');
                    document.getElementById('detEnvio').innerText = p.costo_envio_formateado;
                } else {
                    envioRow.classList.add('is-hidden');
                }
                document.getElementById('detTotal').innerText = p.total_formateado;

                const btnWa = document.getElementById('detBtnWa');
                if (p.cliente_telefono) {
                    btnWa.classList.remove('is-hidden');
                    let phone = p.cliente_telefono.replace(/\D/g, '');
                    if (phone.length === 10 && phone.startsWith('3')) phone = '57' + phone;
                    const msg = encodeURIComponent('Hola ' + p.cliente_nombre + ', te escribimos con relación a tu pedido #' + p.codigo + '.');
                    btnWa.href = 'https://wa.me/' + phone + '?text=' + msg;
                } else {
                    btnWa.classList.add('is-hidden');
                }
            })
            .catch(() => {
                alert('Error al cargar detalle del pedido');
                cerrarDetalle();
            });
        }

        document.querySelectorAll('.btn-ver-detalle').forEach(function (btn) {
            btn.addEventListener('click', function () {
                cargarYMostrarPedido(this.getAttribute('data-id'));
            });
        });

        // Auto-abrir pedido si viene en el query string (ej: ?pedido=16)
        const urlParams = new URLSearchParams(window.location.search);
        const pedidoParam = urlParams.get('pedido');
        if (pedidoParam) {
            cargarYMostrarPedido(pedidoParam);
        }

        // Acción de Liberar Mesa desde el Modal
        document.getElementById('btnLiberarMesaModal')?.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            if (!id) return;
            if (!confirm('¿Deseas marcar la mesa como liberada? El salón se actualizará y la mesa quedará disponible.')) return;

            fetch(baseUrl + '/admin/pedidos/' + id + '/liberar-mesa', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '<?= csrf_token() ?>'
                }
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    if (window.mostrarAdminToast) window.mostrarAdminToast('Mesa liberada correctamente');
                    cerrarDetalle();
                    setTimeout(() => window.location.reload(), 300);
                } else {
                    alert(data.error || 'Error al liberar mesa');
                }
            })
            .catch(() => alert('Error de conexión al liberar mesa'));
        });

        // Imprimir Comanda
        document.getElementById('btnImprimirComandaModal')?.addEventListener('click', function () {
            window.print();
        });
    }

    // 2. Exponer función de recarga en vivo para nuevos pedidos detectados por polling
    window.recargarPedidosEnVivo = function (nuevosIds) {
        const isModalOpen = document.querySelector('.modal-backdrop.is-active, .modal-backdrop.is-open');
        if (isModalOpen) return;

        const refreshUrl = new URL(window.location.href);
        refreshUrl.searchParams.set('_nocache', Date.now());

        fetch(refreshUrl.toString(), {
            cache: 'no-store',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            const oldShell = document.querySelector('.admin-shell');
            const newShell = doc.querySelector('.admin-shell');
            if (oldShell && newShell) {
                // Guardar los modales existentes para no perder sus referencias
                const oldModals = Array.from(oldShell.querySelectorAll('.modal-backdrop'));
                oldShell.innerHTML = newShell.innerHTML;

                // Re-anexar modales en caso de que la vista dependa de ellos
                oldModals.forEach(m => {
                    if (!oldShell.querySelector('#' + m.id)) {
                        oldShell.appendChild(m);
                    }
                });
            }

            bindPedidosTableEvents();

            if (Array.isArray(nuevosIds)) {
                nuevosIds.forEach(id => {
                    const row = document.getElementById('pedido_fila_' + id);
                    if (row) {
                        row.classList.add('order-row--new');
                        row.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }
                });
            }
        })
        .catch(err => console.error('Error al actualizar pedidos en vivo:', err));
    };

    window.actualizarConteosPedidos = actualizarConteos;

    // Enlazar eventos al cargar
    bindPedidosTableEvents();

    // Inicializar Flatpickr en pedidos
    if (typeof flatpickr !== 'undefined') {
        const fpD = "<?= e($filtros['fecha_desde'] ?? '') ?>";
        const fpH = "<?= e($filtros['fecha_hasta'] ?? '') ?>";
        flatpickr("#dateRangePickerPedidos", {
            mode: "range",
            dateFormat: "Y-m-d",
            defaultDate: (fpD && fpH) ? [fpD, fpH] : [],
            locale: flatpickr.l10ns.es || "es",
            altInput: true,
            altFormat: "d M Y",
            onClose: function (selectedDates, dateStr, instance) {
                if (selectedDates.length === 2) {
                    document.getElementById('fechaDesdePedidos').value = instance.formatDate(selectedDates[0], "Y-m-d");
                    document.getElementById('fechaHastaPedidos').value = instance.formatDate(selectedDates[1], "Y-m-d");
                } else if (selectedDates.length === 0) {
                    document.getElementById('fechaDesdePedidos').value = '';
                    document.getElementById('fechaHastaPedidos').value = '';
                }
            }
        });
    }

    // Cerrar Modal Detalle
    function cerrarDetalle() {
        modal.classList.remove('is-open', 'is-active');
    }
    document.getElementById('btnCerrarModalDetalle')?.addEventListener('click', cerrarDetalle);
    document.getElementById('btnCerrarModalDetalleFooter')?.addEventListener('click', cerrarDetalle);
    modal?.addEventListener('click', function (e) {
        if (e.target === this) cerrarDetalle();
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            cerrarDetalle();
            cerrarModalAvisoWhatsApp();
        }
    });
});
</script>