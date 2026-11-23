<div class="content-header mb-5">
    <div>
        <div class="flex items-center gap-2 mb-1">
            <a href="<?= e(app_url('/superadmin')) ?>" class="text-xs text-muted inline-flex items-center gap-1">
                <?= icon('arrow-left', 14) ?>
                <span>Volver a Vencimientos</span>
            </a>
            <span class="text-subtle">/</span>
            <span class="text-brand text-xs fw-600">Métricas de Plataforma</span>
        </div>
        <h1 class="page-title text-2xl mb-1">
            Métricas Globales de la Red Mesaly
        </h1>
        <p class="page-subtitle text-base text-muted m-0">
            Monitoreo en tiempo real de ingresos recurrentes (MRR), adopción de catálogo y salud financiera de la cartera.
        </p>
    </div>

    <div class="content-actions">
        <a href="<?= e(app_url('/superadmin/pagos')) ?>" class="btn btn-secondary fw-600">
            <?= icon('cash', 14) ?>
            <span>Auditoría de Pagos</span>
        </a>
    </div>
</div>

<!-- 1. BLOQUE DE FINANZAS Y MRR -->
<div class="kpi-grid mb-5">
    <div class="kpi-card-modern">
        <div class="kpi-icon-badge kpi-icon-brand">
            <?= icon('cash', 20) ?>
        </div>
        <span class="text-xs fw-600 text-muted">MRR (Mensual Recurrente)</span>
        <strong class="kpi-val-28 text-brand">
            <?= format_cop($mrr) ?>
        </strong>
        <div class="flex items-center justify-between mt-1">
            <span class="text-xs text-muted">Tarifa promedio/negocio:</span>
            <strong class="text-xs text-main tabular"><?= format_cop($arpu) ?></strong>
        </div>
    </div>

    <div class="kpi-card-modern">
        <div class="kpi-icon-badge kpi-icon-success">
            <?= icon('chart', 20) ?>
        </div>
        <span class="text-xs fw-600 text-muted">ARR Proyectado (Anual)</span>
        <strong class="kpi-val-28 text-success">
            <?= format_cop($arr) ?>
        </strong>
        <div class="flex items-center justify-between mt-1">
            <span class="text-xs text-muted">Base activa anualizada:</span>
            <span class="text-xs text-success fw-600">12 meses</span>
        </div>
    </div>

    <div class="kpi-card-modern">
        <div class="kpi-icon-badge">
            <?= icon('coin', 20) ?>
        </div>
        <span class="text-xs fw-600 text-muted">Recaudado este Mes</span>
        <strong class="kpi-val-28 text-main">
            <?= format_cop($recaudadoMes) ?>
        </strong>
        <div class="flex items-center justify-between mt-1">
            <span class="text-xs text-muted">Mes en curso:</span>
            <span class="text-xs fw-600 text-main"><?= date('m/Y') ?></span>
        </div>
    </div>

    <div class="kpi-card-modern">
        <div class="kpi-icon-badge">
            <?= icon('check', 20) ?>
        </div>
        <span class="text-xs fw-600 text-muted">Salud de Cartera</span>
        <strong class="kpi-val-28 text-warning">
            <?= $tasaCobranza ?>%
        </strong>
        <div class="flex items-center justify-between mt-1">
            <span class="text-xs text-muted">Restaurantes al día:</span>
            <strong class="text-xs text-success"><?= (int)$conteoEstados['al_dia'] ?> / <?= (int)$totalNegocios ?></strong>
        </div>
    </div>
</div>

<!-- 2. GRÁFICOS VISUALES CHART.JS -->
<div class="config-grid-2 mb-5">
    
    <!-- Gráfico 1: Tendencia Histórica de Recaudación -->
    <div class="card card-p-6">
        <div class="card-header-bordered flex items-center justify-between mb-4 pb-3">
            <div>
                <h2 class="text-md fw-700 text-main m-0">Recaudación de Suscripciones (Últimos 6 Meses)</h2>
                <span class="text-xs text-muted">Evolución de cobros manuales asentados en plataforma (COP)</span>
            </div>
            <span class="badge badge-subtle-brand">
                COP / Mes
            </span>
        </div>
        <div class="chart-container-wrap">
            <canvas id="chartRecaudacion"></canvas>
        </div>
    </div>

    <!-- Gráfico 2: Distribución de Suscripciones (Cartera) -->
    <div class="card card-p-6">
        <div class="card-header-bordered flex items-center justify-between mb-4 pb-3">
            <div>
                <h2 class="text-md fw-700 text-main m-0">Estado de la Cartera</h2>
                <span class="text-xs text-muted">Distribución por estado de cobro</span>
            </div>
            <strong class="text-sm text-main"><?= (int)$totalNegocios ?> negocios</strong>
        </div>
        <div class="chart-doughnut-wrap">
            <canvas id="chartCartera"></canvas>
        </div>
    </div>

</div>

<!-- 3. ACTIVIDAD OPERATIVA DE LA RED DE RESTAURANTES -->
<div class="card card-p-6 mb-5">
    <div class="card-header-bordered flex items-center justify-between mb-4 pb-3">
        <div>
            <h2 class="text-md fw-700 text-main m-0">Actividad Operativa de los Restaurantes</h2>
            <span class="text-sm text-muted">Adopción del producto gastronómico en los negocios activos</span>
        </div>
        <span class="text-xs text-muted">Actualizado en tiempo real</span>
    </div>

    <div class="modalities-grid">
        <div class="stat-box-super">
            <span class="stat-box-title">
                Pedidos Tramitados
            </span>
            <strong class="stat-val-super">
                <?= (int)$totalPedidos ?>
            </strong>
            <span class="text-xs text-subtle">Generados y enviados a WhatsApp</span>
        </div>

        <div class="stat-box-super">
            <span class="stat-box-title">
                Volumen Estimado Ventas
            </span>
            <strong class="stat-val-super text-success">
                <?= format_cop($volumenVentasRed) ?>
            </strong>
            <span class="text-xs text-subtle">Sumatoria de cartas digitales</span>
        </div>

        <div class="stat-box-super">
            <span class="stat-box-title">
                Mesas con QR Activas
            </span>
            <strong class="stat-val-super">
                <?= (int)$totalMesas ?>
            </strong>
            <span class="text-xs text-subtle">Puntos de contacto físico</span>
        </div>

        <div class="stat-box-super">
            <span class="stat-box-title">
                Platos en Catálogos
            </span>
            <strong class="stat-val-super">
                <?= (int)$totalProductos ?>
            </strong>
            <span class="text-xs text-subtle">Productos activos en la plataforma</span>
        </div>
    </div>
</div>

<!-- Scripts de Gráficos con Chart.js compatible con CSP Nonce -->
<script nonce="<?= e($cspNonce ?? '') ?>">
document.addEventListener('DOMContentLoaded', function () {
    if (typeof Chart === 'undefined') {
        console.warn('Chart.js no está disponible.');
        return;
    }

    // Datos del backend
    const historicoData = <?= json_encode($historicoMeses, JSON_UNESCAPED_UNICODE) ?>;
    const conteoEstados = <?= json_encode($conteoEstados, JSON_UNESCAPED_UNICODE) ?>;

    // 1. Gráfico de Barras: Recaudación Histórica
    const ctxRecaudacion = document.getElementById('chartRecaudacion');
    if (ctxRecaudacion) {
        new Chart(ctxRecaudacion, {
            type: 'bar',
            data: {
                labels: historicoData.map(d => d.label),
                datasets: [{
                    label: 'Recaudado (COP)',
                    data: historicoData.map(d => d.total),
                    backgroundColor: 'rgba(232, 93, 4, 0.75)',
                    borderColor: '#E85D04',
                    borderWidth: 1.5,
                    borderRadius: 6,
                    hoverBackgroundColor: '#CF4E00',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return '$ ' + context.parsed.y.toLocaleString('es-CO');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function (val) {
                                if (val >= 1000000) return '$' + (val / 1000000) + 'M';
                                if (val >= 1000) return '$' + (val / 1000) + 'K';
                                return '$' + val;
                            },
                            font: { size: 11 }
                        },
                        grid: { color: 'rgba(15, 23, 42, 0.06)' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 12, weight: '600' } }
                    }
                }
            }
        });
    }

    // 2. Gráfico Doughnut: Estado de la Cartera
    const ctxCartera = document.getElementById('chartCartera');
    if (ctxCartera) {
        new Chart(ctxCartera, {
            type: 'doughnut',
            data: {
                labels: ['Al Día', 'Por Vencer', 'En Gracia', 'Bloqueados', 'Cancelados'],
                datasets: [{
                    data: [
                        conteoEstados.al_dia || 0,
                        conteoEstados.por_vencer || 0,
                        conteoEstados.en_gracia || 0,
                        conteoEstados.bloqueado || 0,
                        conteoEstados.cancelado || 0
                    ],
                    backgroundColor: [
                        '#10B981', // Al día
                        '#F59E0B', // Por vencer
                        '#EA580C', // En gracia
                        '#EF4444', // Bloqueados
                        '#94A3B8'  // Cancelados
                    ],
                    borderWidth: 2,
                    borderColor: '#FFFFFF',
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            padding: 14,
                            font: { size: 12 }
                        }
                    }
                },
                cutout: '68%'
            }
        });
    }
});
</script>