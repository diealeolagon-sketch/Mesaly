<?php $fromPedidos = (($_GET['from'] ?? '') === 'pedidos'); ?>
<?php if ($fromPedidos): ?>
    <div class="mb-3">
        <a href="<?= e(app_url('/admin/pedidos')) ?>" class="btn btn-secondary btn-sm fw-600 inline-flex items-center gap-1.5" title="Regresar al panel de pedidos en vivo">
            <?= icon('arrow-left', 14) ?>
            <span>Volver a Pedidos en Vivo</span>
        </a>
    </div>
<?php endif; ?>

<div class="content-header mb-5">
    <div>
        <h1 class="page-title text-2xl mb-1 flex items-center gap-2">
            <span class="text-brand flex items-center"><?= icon('chart', 24) ?></span>
            <span>Reportes y Rendimiento Financiero</span>
        </h1>
        <p class="page-subtitle text-base text-muted m-0">
            Inteligencia de ventas, horas pico, ranking de platos y auditoría granular de comandas.
        </p>
    </div>

    <!-- Acciones Rápidas de Descarga e Impresión -->
    <div class="content-actions flex items-center gap-2 flex-wrap">
        <!-- Grupo de Exportación Rápida -->
        <div class="dropdown-quick-export flex items-center gap-1 bg-white border rounded-lg p-1 shadow-xs">
            <a href="<?= e(app_url('/admin/reportes/exportar?periodo=' . urlencode($periodo) . '&fecha_desde=' . urlencode($desde) . '&fecha_hasta=' . urlencode($hasta) . (!empty($filtros['modalidad']) ? '&modalidad=' . urlencode($filtros['modalidad']) : ''))) ?>" 
               class="btn btn-primary btn-sm fw-700 flex items-center gap-1.5" id="btnExportarExcel" title="Descargar datos del rango actual en formato CSV para Excel">
                <?= icon('download', 14) ?>
                <span>Descargar CSV</span>
            </a>

            <!-- Accesos rápidos a Semana y Mes -->
            <a href="<?= e(app_url('/admin/reportes/exportar?preset=semana')) ?>" 
               class="btn btn-secondary btn-sm text-xs px-2 py-1 fw-600" title="Descargar ventas de esta semana">
                Semana
            </a>
            <a href="<?= e(app_url('/admin/reportes/exportar?preset=mes')) ?>" 
               class="btn btn-secondary btn-sm text-xs px-2 py-1 fw-600" title="Descargar ventas de este mes">
                Mes
            </a>
        </div>

        <button type="button" class="btn btn-secondary btn-sm fw-600 flex items-center gap-2" id="btnPrintReporte" title="Imprimir cierre o guardar PDF">
            <?= icon('printer', 15) ?>
            <span>Imprimir Cierre</span>
        </button>
    </div>
</div>

<!-- 1. PESTAÑAS PRINCIPALES: GRÁFICAS DE NEGOCIO vs. TABLA DE DATOS -->
<div class="admin-order-views-tabs mb-4">
    <a href="<?= e(app_url('/admin/reportes?tab=graficas&periodo=' . urlencode($periodo) . '&fecha_desde=' . urlencode($desde) . '&fecha_hasta=' . urlencode($hasta) . (!empty($filtros['modalidad']) ? '&modalidad=' . urlencode($filtros['modalidad']) : '') . ($fromPedidos ? '&from=pedidos' : ''))) ?>" 
       class="admin-order-view-tab <?= ($tab === 'graficas') ? 'active' : '' ?>">
        <?= icon('chart', 16) ?>
        <span>Gráficas &amp; Analítica Visual</span>
    </a>

    <a href="<?= e(app_url('/admin/reportes?tab=datos&periodo=' . urlencode($periodo) . '&fecha_desde=' . urlencode($desde) . '&fecha_hasta=' . urlencode($hasta) . (!empty($filtros['modalidad']) ? '&modalidad=' . urlencode($filtros['modalidad']) : '') . ($fromPedidos ? '&from=pedidos' : ''))) ?>" 
       class="admin-order-view-tab <?= ($tab === 'datos') ? 'active' : '' ?>">
        <?= icon('list', 16) ?>
        <span>Tabla de Datos &amp; Cierre</span>
        <span class="tab-badge"><?= count($pedidos) ?></span>
    </a>
</div>

<!-- 2. BARRA DE FILTROS TEMPORALES Y MEDIOS DE PAGO -->
<div class="card card-p-4 mb-5 border-brand-subtle">
    <form method="GET" action="<?= e(app_url('/admin/reportes')) ?>" id="formFiltroReportes" class="flex flex-col gap-3">
        <input type="hidden" name="tab" value="<?= e($tab) ?>">
        <input type="hidden" name="periodo" id="inputPeriodo" value="<?= e($periodo) ?>">
        <?php if ($fromPedidos): ?>
            <input type="hidden" name="from" value="pedidos">
        <?php endif; ?>
        
        <!-- Controles de Filtro en Línea -->
        <div class="reportes-filter-row">
            <div class="filter-pills-group m-0">
                <a href="<?= e(app_url('/admin/reportes?tab=' . urlencode($tab) . '&periodo=hoy')) ?>" class="filter-pill <?= $periodo === 'hoy' ? 'active' : '' ?>">
                    Hoy
                </a>
                <a href="<?= e(app_url('/admin/reportes?tab=' . urlencode($tab) . '&periodo=ayer')) ?>" class="filter-pill <?= $periodo === 'ayer' ? 'active' : '' ?>">
                    Ayer
                </a>
                <a href="<?= e(app_url('/admin/reportes?tab=' . urlencode($tab) . '&periodo=esta_semana')) ?>" class="filter-pill <?= $periodo === 'esta_semana' ? 'active' : '' ?>">
                    Esta Semana
                </a>
                <a href="<?= e(app_url('/admin/reportes?tab=' . urlencode($tab) . '&periodo=este_mes')) ?>" class="filter-pill <?= $periodo === 'este_mes' ? 'active' : '' ?>">
                    Este Mes
                </a>
                <a href="<?= e(app_url('/admin/reportes?tab=' . urlencode($tab) . '&periodo=mes_anterior')) ?>" class="filter-pill <?= $periodo === 'mes_anterior' ? 'active' : '' ?>">
                    Mes Anterior
                </a>
            </div>

            <!-- Rango de Fechas con Calendario Flatpickr y Selects -->
            <div class="reportes-controls-inline">
                <div class="date-range-picker-wrapper flex items-center bg-white border rounded-lg px-2.5 py-1 shadow-xs">
                    <span class="text-brand mr-2 flex items-center">
                        <?= icon('calendar', 15) ?>
                    </span>
                    <input type="text" id="dateRangePicker" class="form-control-plaintext text-xs fw-600 cursor-pointer" placeholder="Seleccionar fechas..." readonly>
                    <input type="hidden" name="fecha_desde" id="fechaDesde" value="<?= e($desde) ?>">
                    <input type="hidden" name="fecha_hasta" id="fechaHasta" value="<?= e($hasta) ?>">
                </div>

                <!-- Filtro por Modalidad -->
                <select name="modalidad" class="reportes-select-control" id="selectModalidad">
                    <option value="">Todas las modalidades</option>
                    <option value="mesa" <?= ($filtros['modalidad'] === 'mesa') ? 'selected' : '' ?>>En Mesa (Salón)</option>
                    <option value="domicilio" <?= ($filtros['modalidad'] === 'domicilio') ? 'selected' : '' ?>>Domicilio</option>
                </select>

                <button type="submit" class="btn btn-secondary btn-sm fw-600" id="btnSubmitFiltros">
                    Aplicar
                </button>
            </div>
        </div>

        <div class="flex items-center justify-between text-xs text-muted pt-2 border-t">
            <span>Período consultado: <strong><?= date('d/m/Y', strtotime($desde)) ?></strong> al <strong><?= date('d/m/Y', strtotime($hasta)) ?></strong> (<?= (int)$metricas['total_pedidos'] ?> pedidos completados)</span>
            <?php if ($periodo !== 'este_mes' || !empty($filtros['modalidad'])): ?>
                <a href="<?= e(app_url('/admin/reportes?tab=' . urlencode($tab) . '&periodo=este_mes')) ?>" class="text-danger hover:underline">
                    &times; Restablecer a Este Mes
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- ══════════════════════════════════════════════════════════════════════════════
     VISTA 1: ANALÍTICA VISUAL & GRÁFICAS DE NEGOCIO
     ══════════════════════════════════════════════════════════════════════════════ -->
<?php if ($tab === 'graficas'): ?>

    <!-- 1. Gráficas Principales en Cuadrícula (Evolución Diaria + Horas Pico) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">
        <!-- Gráfica 1: Evolución Diaria de Ventas -->
        <div class="card card-p-4 bg-white border border-gray-200">
            <div class="flex items-center justify-between pb-3 border-b mb-3">
                <div>
                    <h2 class="text-sm font-bold text-main m-0">Evolución Diaria de Ventas</h2>
                    <span class="text-xs text-muted">Ingresos acumulados día por día en el período seleccionado</span>
                </div>
                <span class="badge badge-subtle-brand">COP / Día</span>
            </div>
            <div class="chart-box-reportes">
                <canvas id="chartEvolucionVentas"></canvas>
            </div>
        </div>

        <!-- Gráfica 2: Horas Pico de Demanda -->
        <div class="card card-p-4 bg-white border border-gray-200">
            <div class="flex items-center justify-between pb-3 border-b mb-3">
                <div>
                    <h2 class="text-sm font-bold text-main m-0">Horas Pico de Mayor Demanda</h2>
                    <span class="text-xs text-muted">Distribución de comandas por hora (para organizar cocina y meseros)</span>
                </div>
                <span class="badge badge-subtle-brand">Franjas Horarias</span>
            </div>
            <div class="chart-box-reportes">
                <canvas id="chartHorasPico"></canvas>
            </div>
        </div>
    </div>

    <!-- 2. Segunda Fila: Top 5 Platos + Comparativa Mensual -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-5">
        <!-- Gráfica 3: Top 5 Platos Más Vendidos -->
        <div class="card card-p-4 bg-white border border-gray-200">
            <div class="flex items-center justify-between pb-3 border-b mb-3">
                <div>
                    <h2 class="text-sm font-bold text-main m-0">Top 5 Platos Favoritos</h2>
                    <span class="text-xs text-muted">Platos y bebidas más pedidos por tus clientes</span>
                </div>
                <span class="badge badge-subtle-neutral">Unidades</span>
            </div>
            <div class="chart-box-reportes">
                <canvas id="chartTopProductos"></canvas>
            </div>
        </div>

        <!-- Tarjeta Comparativa Mensual -->
        <div class="card card-p-4 bg-white border border-gray-200 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-3 border-b mb-3">
                <div class="flex items-center gap-2">
                    <span class="text-brand flex items-center"><?= icon('trending-up', 18) ?></span>
                    <strong class="text-sm text-main">Comparativa Mes a Mes</strong>
                </div>
                <span class="text-xs text-muted">Variación acumulada</span>
            </div>

            <div class="grid grid-cols-2 gap-3 mb-2">
                <div class="p-3 bg-gray-50 rounded-lg">
                    <div class="text-xs text-muted mb-1">Este Mes (<?= date('M Y') ?>)</div>
                    <div class="text-lg font-bold text-main">
                        $ <?= number_format($comparativaMeses['actual']['total'], 0, ',', '.') ?>
                    </div>
                    <div class="text-xxs text-muted mt-0.5">
                        <?= (int)$comparativaMeses['actual']['pedidos'] ?> pedidos
                    </div>
                </div>

                <div class="p-3 bg-gray-50 rounded-lg">
                    <div class="text-xs text-muted mb-1">Mes Anterior</div>
                    <div class="text-lg font-bold text-muted">
                        $ <?= number_format($comparativaMeses['anterior']['total'], 0, ',', '.') ?>
                    </div>
                    <div class="text-xxs text-muted mt-0.5">
                        <?= (int)$comparativaMeses['anterior']['pedidos'] ?> pedidos
                    </div>
                </div>

                <div class="p-3 bg-gray-50 rounded-lg">
                    <div class="text-xs text-muted mb-1">Variación Ventas</div>
                    <div class="text-lg font-bold flex items-center gap-1 <?= ($comparativaMeses['diferencia_ventas'] >= 0) ? 'text-success' : 'text-danger' ?>">
                        <?php if (!empty($comparativaMeses['es_primer_mes'])): ?>
                            <?= icon('trending-up', 16) ?>
                            <span>Primer mes</span>
                        <?php elseif ($comparativaMeses['diferencia_ventas'] >= 0): ?>
                            <?= icon('trending-up', 16) ?>
                            <span>+<?= $comparativaMeses['pct_cambio_ventas'] ?>%</span>
                        <?php else: ?>
                            <?= icon('trending-down', 16) ?>
                            <span><?= $comparativaMeses['pct_cambio_ventas'] ?>%</span>
                        <?php endif; ?>
                    </div>
                    <div class="text-xxs text-muted mt-0.5">
                        <?= !empty($comparativaMeses['es_primer_mes']) ? 'Base inicial de ventas' : '$ ' . number_format(abs($comparativaMeses['diferencia_ventas']), 0, ',', '.') ?>
                    </div>
                </div>

                <div class="p-3 bg-gray-50 rounded-lg">
                    <div class="text-xs text-muted mb-1">Ticket Promedio</div>
                    <div class="text-lg font-bold text-main">
                        $ <?= number_format($comparativaMeses['actual']['ticket_promedio'], 0, ',', '.') ?>
                    </div>
                    <div class="text-xxs text-muted mt-0.5">
                        Por comanda
                    </div>
                </div>
            </div>

            <div class="pt-2 text-xxs text-muted border-t text-right">
                Datos calculados contra el mes calendario inmediatamente anterior.
            </div>
        </div>
    </div>

<!-- ══════════════════════════════════════════════════════════════════════════════
     VISTA 2: TABLA DE DATOS DETALLADA & AUDITORÍA DE VENTAS
     ══════════════════════════════════════════════════════════════════════════════ -->
<?php else: ?>

    <!-- Buscador en Vivo y Contador de Comandas -->
    <div class="card card-p-4 mb-4 bg-white border border-gray-200 flex items-center justify-between gap-3 flex-wrap">
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <span class="text-muted flex items-center"><?= icon('search', 16) ?></span>
            <input type="text" id="inputBuscarTabla" class="form-control text-xs py-1.5" placeholder="Filtrar por código, mesa o cliente..." style="width: 280px;">
        </div>

        <div class="flex items-center gap-3 text-xs text-muted">
            <span>Mostrando <strong id="contadorFilas"><?= count($pedidos) ?></strong> comanda(s)</span>
        </div>
    </div>

    <!-- Tabla Detallada -->
    <div class="card p-0 overflow-hidden mb-5 border border-gray-200">
        <div class="table-container">
            <table class="table" id="tablaDetallePedidos">
                <thead>
                    <tr>
                        <th class="col-rank">#</th>
                        <th>Código</th>
                        <th>Fecha / Hora</th>
                        <th>Mesa / Modalidad</th>
                        <th>Cliente</th>
                        <th>Ítems Consumidos</th>
                        <th class="text-right">Subtotal</th>
                        <th class="text-right">Total</th>
                        <th class="text-center">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pedidos)): ?>
                        <tr>
                            <td colspan="9" class="text-center p-6 text-muted">
                                No se encontraron pedidos registrados en el período seleccionado.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($pedidos as $idx => $p): ?>
                            <tr class="fila-pedido-reporte">
                                <td class="text-center font-bold text-muted text-xs">
                                    #<?= str_pad((string)$p['consecutivo'], 4, '0', STR_PAD_LEFT) ?>
                                </td>
                                <td>
                                    <span class="font-mono text-xs fw-700 text-main"><?= e($p['codigo']) ?></span>
                                </td>
                                <td class="text-xs text-muted whitespace-nowrap">
                                    <div><?= date('d/m/Y', strtotime($p['created_at'])) ?></div>
                                    <div class="text-xxs"><?= date('h:i A', strtotime($p['created_at'])) ?></div>
                                </td>
                                <td class="text-xs">
                                    <?php if ($p['modalidad'] === 'domicilio'): ?>
                                        <span class="badge badge-subtle-brand">Domicilio</span>
                                    <?php else: ?>
                                        <span class="badge badge-subtle-neutral">Mesa <?= e($p['mesa_numero'] ?? '—') ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-xs fw-600 text-main">
                                    <?= e($p['cliente_nombre']) ?>
                                    <?php if (!empty($p['cliente_telefono'])): ?>
                                        <div class="text-xxs text-muted"><?= e($p['cliente_telefono']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="text-xs text-muted max-w-xs truncate" title="<?= e($p['items_resumen'] ?? '') ?>">
                                    <?= e($p['items_resumen'] ?? '—') ?>
                                </td>
                                <td class="text-right text-xs text-muted">
                                    $ <?= number_format((int)$p['subtotal'], 0, ',', '.') ?>
                                </td>
                                <td class="text-right text-xs font-bold text-main">
                                    $ <?= number_format((int)$p['total'], 0, ',', '.') ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-subtle-success text-xxs font-bold">
                                        <?= ucfirst(e($p['estado'])) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Consolidado Tributario & Cuadre de Caja -->
    <div class="card card-p-4 bg-white border border-gray-200 mb-5">
        <h3 class="text-sm font-bold text-main mb-3 pb-2 border-b">Consolidado Financiero &amp; Tributario del Período</h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
            <div class="p-3 bg-gray-50 rounded-lg">
                <span class="text-muted block mb-1">Total Facturado</span>
                <strong class="text-base text-main block">$ <?= number_format((int)$metricas['total_ventas'], 0, ',', '.') ?></strong>
            </div>
            <div class="p-3 bg-gray-50 rounded-lg">
                <span class="text-muted block mb-1">Base Gravable Estimada</span>
                <strong class="text-base text-main block">$ <?= number_format((int)round($metricas['total_ventas'] / 1.08), 0, ',', '.') ?></strong>
            </div>
            <div class="p-3 bg-gray-50 rounded-lg">
                <span class="text-muted block mb-1">Impoconsumo 8%</span>
                <strong class="text-base text-brand block">$ <?= number_format((int)($metricas['total_ventas'] - round($metricas['total_ventas'] / 1.08)), 0, ',', '.') ?></strong>
            </div>
        </div>
    </div>

<?php endif; ?>

<!-- Librerías de Visualización y Calendario -->
<script nonce="<?= e($cspNonce) ?>" src="<?= e(asset_url('/assets/vendor/chartjs/chart.umd.js')) ?>"></script>
<script nonce="<?= e($cspNonce) ?>" src="<?= e(asset_url('/assets/vendor/flatpickr/flatpickr.min.js')) ?>"></script>
<script nonce="<?= e($cspNonce) ?>" src="<?= e(asset_url('/assets/vendor/flatpickr/es.js')) ?>"></script>

<script nonce="<?= e($cspNonce) ?>">
document.addEventListener('DOMContentLoaded', function () {
    const defaultDesde = "<?= e($desde) ?>";
    const defaultHasta = "<?= e($hasta) ?>";

    // 1. Inicializar Flatpickr con Branding Mesaly y soporte de auto-submit en 1 o 2 clics
    if (typeof flatpickr !== 'undefined') {
        flatpickr("#dateRangePicker", {
            mode: "range",
            dateFormat: "Y-m-d",
            defaultDate: [defaultDesde, defaultHasta],
            locale: flatpickr.l10ns.es || "es",
            altInput: true,
            altFormat: "d M Y",
            onClose: function (selectedDates, dateStr, instance) {
                if (selectedDates.length > 0) {
                    const d1 = instance.formatDate(selectedDates[0], "Y-m-d");
                    const d2 = selectedDates.length === 2 ? instance.formatDate(selectedDates[1], "Y-m-d") : d1;
                    document.getElementById('fechaDesde').value = d1;
                    document.getElementById('fechaHasta').value = d2;
                    document.getElementById('inputPeriodo').value = 'personalizado';
                    document.getElementById('formFiltroReportes').submit();
                }
            }
        });
    }

    // Auto-submit en cambio de modalidad o método de pago
    document.querySelectorAll('#formFiltroReportes select').forEach(function (sel) {
        sel.addEventListener('change', function () {
            document.getElementById('formFiltroReportes').submit();
        });
    });

    // Impresión limpia
    const btnPrint = document.getElementById('btnPrintReporte');
    if (btnPrint) {
        btnPrint.addEventListener('click', function () {
            window.print();
        });
    }

    // Buscador en tiempo real para la tabla de datos
    const inputBuscar = document.getElementById('inputBuscarTabla');
    if (inputBuscar) {
        inputBuscar.addEventListener('input', function () {
            const query = this.value.toLowerCase().trim();
            const filas = document.querySelectorAll('.fila-pedido-reporte');
            let visibles = 0;
            filas.forEach(function (fila) {
                const texto = fila.textContent.toLowerCase();
                if (texto.includes(query)) {
                    fila.style.display = '';
                    visibles++;
                } else {
                    fila.style.display = 'none';
                }
            });
            const contador = document.getElementById('contadorFilas');
            if (contador) contador.textContent = visibles;
        });
    }

    // ══════════════════════════════════════════════════════════════
    // GRÁFICAS DE ANALÍTICA (Solo se inicializan si estamos en tab graficas)
    // ══════════════════════════════════════════════════════════════
    <?php if ($tab === 'graficas'): ?>
    
    // Gráfica 1: Evolución Diaria de Ventas
    const serieDiaria = <?= json_encode($serieDiaria ?? []) ?>;
    const ctxVentas = document.getElementById('chartEvolucionVentas');
    if (ctxVentas && typeof Chart !== 'undefined') {
        const labels = serieDiaria.map(item => item.label);
        const dataVentas = serieDiaria.map(item => item.total);
        const dataPedidos = serieDiaria.map(item => item.pedidos);

        new Chart(ctxVentas, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Ventas ($)',
                    data: dataVentas,
                    borderColor: '#E85D04',
                    backgroundColor: 'rgba(232, 93, 4, 0.12)',
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#E85D04',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1E1B18',
                        padding: 10,
                        callbacks: {
                            label: function (ctx) {
                                const idx = ctx.dataIndex;
                                return ' $' + Number(ctx.parsed.y).toLocaleString('es-CO') + ' (' + (dataPedidos[idx] || 0) + ' pedidos)';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function (val) {
                                if (val >= 1000000) return '$' + (val / 1000000).toFixed(1) + 'M';
                                if (val >= 1000) return '$' + (val / 1000).toFixed(0) + 'k';
                                return '$' + val;
                            }
                        },
                        grid: { color: '#F3F4F6' }
                    },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    // Gráfica 2: Top Platos Más Vendidos
    const topData = <?= json_encode($topProductos ?? []) ?>;
    const ctxTop = document.getElementById('chartTopProductos');
    if (ctxTop && typeof Chart !== 'undefined') {
        const topLabels = topData.map(p => p.nombre.length > 20 ? p.nombre.substring(0, 18) + '...' : p.nombre);
        const topCantidades = topData.map(p => Number(p.cantidad_vendida));
        const brandColors = ['#E85D04', '#F48C06', '#FAA307', '#FFB703', '#FDC500'];

        new Chart(ctxTop, {
            type: 'bar',
            data: {
                labels: topLabels,
                datasets: [{
                    label: 'Unidades Vendidas',
                    data: topCantidades,
                    backgroundColor: brandColors.slice(0, topLabels.length),
                    borderRadius: 6,
                    borderWidth: 0
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { beginAtZero: true, ticks: { precision: 0 } },
                    y: { grid: { display: false } }
                }
            }
        });
    }

    // Gráfica 3: Horas Pico de Demanda
    const horasData = <?= json_encode($horasPico ?? []) ?>;
    const ctxHoras = document.getElementById('chartHorasPico');
    if (ctxHoras && typeof Chart !== 'undefined') {
        const horasLabels = horasData.map(h => h.hora_label);
        const horasPedidos = horasData.map(h => Number(h.pedidos));

        new Chart(ctxHoras, {
            type: 'bar',
            data: {
                labels: horasLabels,
                datasets: [{
                    label: 'Comandas',
                    data: horasPedidos,
                    backgroundColor: 'rgba(232, 93, 4, 0.75)',
                    hoverBackgroundColor: '#E85D04',
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } },
                    x: { grid: { display: false } }
                }
            }
        });
    }

    <?php endif; ?>
});
</script>
