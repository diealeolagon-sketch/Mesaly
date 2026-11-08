<div class="content-header" style="margin-bottom: 24px;">
    <div>
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
            <a href="<?= e(app_url('/superadmin')) ?>" style="color: var(--color-text-muted); font-size: 13px; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                <?= icon('arrow-left', 14) ?>
                <span>Volver a Vencimientos</span>
            </a>
            <span style="color: var(--color-text-subtle);">/</span>
            <span style="color: var(--color-brand); font-size: 13px; font-weight: 600;">Métricas de Plataforma</span>
        </div>
        <h1 class="page-title" style="font-size: 24px; margin-bottom: 4px;">
            Métricas Globales de la Red Mesaly
        </h1>
        <p class="page-subtitle" style="font-size: 14px; color: var(--color-text-muted); margin: 0;">
            Monitoreo en tiempo real de ingresos recurrentes (MRR), adopción de catálogo y salud financiera de la cartera.
        </p>
    </div>

    <div class="content-actions">
        <a href="<?= e(app_url('/superadmin/pagos')) ?>" class="btn btn-secondary" style="font-weight: 600;">
            <?= icon('cash', 14) ?>
            <span>Auditoría de Pagos</span>
        </a>
    </div>
</div>

<!-- 1. BLOQUE DE FINANZAS Y MRR -->
<div class="kpi-grid" style="grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); margin-bottom: 24px;">
    <div class="kpi-card-modern">
        <div class="kpi-icon-badge" style="background: var(--color-brand-subtle); color: var(--color-brand);">
            <?= icon('cash', 20) ?>
        </div>
        <span style="font-size: 13px; font-weight: 600; color: var(--color-text-muted);">MRR (Mensual Recurrente)</span>
        <strong style="font-size: 28px; color: var(--color-brand); font-variant-numeric: tabular-nums;">
            <?= format_cop($mrr) ?>
        </strong>
        <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 4px;">
            <span style="font-size: 12px; color: var(--color-text-muted);">Tarifa promedio/negocio:</span>
            <strong style="font-size: 12.5px; color: var(--color-text-main); font-variant-numeric: tabular-nums;"><?= format_cop($arpu) ?></strong>
        </div>
    </div>

    <div class="kpi-card-modern">
        <div class="kpi-icon-badge" style="background: #ECFDF5; color: #059669;">
            <?= icon('chart', 20) ?>
        </div>
        <span style="font-size: 13px; font-weight: 600; color: var(--color-text-muted);">ARR Proyectado (Anual)</span>
        <strong style="font-size: 28px; color: #059669; font-variant-numeric: tabular-nums;">
            <?= format_cop($arr) ?>
        </strong>
        <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 4px;">
            <span style="font-size: 12px; color: var(--color-text-muted);">Base activa anualizada:</span>
            <span style="font-size: 12px; color: #059669; font-weight: 600;">12 meses</span>
        </div>
    </div>

    <div class="kpi-card-modern">
        <div class="kpi-icon-badge" style="background: #EFF6FF; color: #2563EB;">
            <?= icon('coin', 20) ?>
        </div>
        <span style="font-size: 13px; font-weight: 600; color: var(--color-text-muted);">Recaudado este Mes</span>
        <strong style="font-size: 28px; color: #2563EB; font-variant-numeric: tabular-nums;">
            <?= format_cop($recaudadoMes) ?>
        </strong>
        <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 4px;">
            <span style="font-size: 12px; color: var(--color-text-muted);">Mes en curso:</span>
            <span style="font-size: 12px; font-weight: 600; color: var(--color-text-main);"><?= date('m/Y') ?></span>
        </div>
    </div>

    <div class="kpi-card-modern">
        <div class="kpi-icon-badge" style="background: #FFFBEB; color: #D97706;">
            <?= icon('check', 20) ?>
        </div>
        <span style="font-size: 13px; font-weight: 600; color: var(--color-text-muted);">Salud de Cartera</span>
        <strong style="font-size: 28px; color: #D97706; font-variant-numeric: tabular-nums;">
            <?= $tasaCobranza ?>%
        </strong>
        <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 4px;">
            <span style="font-size: 12px; color: var(--color-text-muted);">Restaurantes al día:</span>
            <strong style="font-size: 12px; color: #059669;"><?= (int)$conteoEstados['al_dia'] ?> / <?= (int)$totalNegocios ?></strong>
        </div>
    </div>
</div>

<!-- 2. GRÁFICOS VISUALES CHART.JS -->
<div style="display: grid; grid-template-columns: 1.6fr 1.2fr; gap: 20px; margin-bottom: 24px;" class="config-grid-2">
    
    <!-- Gráfico 1: Tendencia Histórica de Recaudación -->
    <div class="card" style="padding: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; padding-bottom: 12px; border-bottom: 1px solid var(--color-border-subtle);">
            <div>
                <h2 style="font-size: 16px; font-weight: 700; color: var(--color-text-main); margin: 0;">Recaudación de Suscripciones (Últimos 6 Meses)</h2>
                <span style="font-size: 12.5px; color: var(--color-text-muted);">Evolución de cobros manuales asentados en plataforma (COP)</span>
            </div>
            <span class="badge" style="background: var(--color-brand-subtle); color: var(--color-brand); font-weight: 700; padding: 4px 10px; border-radius: var(--radius-full); font-size: 12px;">
                COP / Mes
            </span>
        </div>
        <div style="position: relative; height: 260px; width: 100%;">
            <canvas id="chartRecaudacion"></canvas>
        </div>
    </div>

    <!-- Gráfico 2: Distribución de Suscripciones (Cartera) -->
    <div class="card" style="padding: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 18px; padding-bottom: 12px; border-bottom: 1px solid var(--color-border-subtle);">
            <div>
                <h2 style="font-size: 16px; font-weight: 700; color: var(--color-text-main); margin: 0;">Estado de la Cartera</h2>
                <span style="font-size: 12.5px; color: var(--color-text-muted);">Distribución por estado de cobro</span>
            </div>
            <strong style="font-size: 14px; color: var(--color-text-main);"><?= (int)$totalNegocios ?> negocios</strong>
        </div>
        <div style="position: relative; height: 260px; width: 100%; display: flex; align-items: center; justify-content: center;">
            <canvas id="chartCartera"></canvas>
        </div>
    </div>

</div>

<!-- 3. ACTIVIDAD OPERATIVA DE LA RED DE RESTAURANTES -->
<div class="card" style="padding: 24px; margin-bottom: 24px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 14px; border-bottom: 1px solid var(--color-border-subtle);">
        <div>
            <h2 style="font-size: 16px; font-weight: 700; color: var(--color-text-main); margin: 0;">Actividad Operativa de los Restaurantes</h2>
            <span style="font-size: 13px; color: var(--color-text-muted);">Adopción del producto gastronómico en los negocios activos</span>
        </div>
        <span style="font-size: 12.5px; color: var(--color-text-muted);">Actualizado en tiempo real</span>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 16px;">
        <div style="padding: 18px; background: #FAFBFD; border: 1px solid var(--color-border); border-radius: var(--radius-md); display: flex; flex-direction: column; gap: 6px;">
            <span style="font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-text-muted);">
                Pedidos Tramitados
            </span>
            <strong style="font-size: 26px; color: var(--color-text-main); font-variant-numeric: tabular-nums;">
                <?= (int)$totalPedidos ?>
            </strong>
            <span style="font-size: 12px; color: var(--color-text-subtle);">Generados y enviados a WhatsApp</span>
        </div>

        <div style="padding: 18px; background: #FAFBFD; border: 1px solid var(--color-border); border-radius: var(--radius-md); display: flex; flex-direction: column; gap: 6px;">
            <span style="font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-text-muted);">
                Volumen Estimado Ventas
            </span>
            <strong style="font-size: 22px; color: #059669; font-variant-numeric: tabular-nums;">
                <?= format_cop($volumenVentasRed) ?>
            </strong>
            <span style="font-size: 12px; color: var(--color-text-subtle);">Sumatoria de cartas digitales</span>
        </div>

        <div style="padding: 18px; background: #FAFBFD; border: 1px solid var(--color-border); border-radius: var(--radius-md); display: flex; flex-direction: column; gap: 6px;">
            <span style="font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-text-muted);">
                Mesas con QR Activas
            </span>
            <strong style="font-size: 26px; color: var(--color-text-main); font-variant-numeric: tabular-nums;">
                <?= (int)$totalMesas ?>
            </strong>
            <span style="font-size: 12px; color: var(--color-text-subtle);">Puntos de contacto físico</span>
        </div>

        <div style="padding: 18px; background: #FAFBFD; border: 1px solid var(--color-border); border-radius: var(--radius-md); display: flex; flex-direction: column; gap: 6px;">
            <span style="font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-text-muted);">
                Platos en Catálogos
            </span>
            <strong style="font-size: 26px; color: var(--color-text-main); font-variant-numeric: tabular-nums;">
                <?= (int)$totalProductos ?>
            </strong>
            <span style="font-size: 12px; color: var(--color-text-subtle);">Productos activos en la plataforma</span>
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
