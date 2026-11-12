<div class="content-header" style="margin-bottom: 24px;">
    <div>
        <h1 class="page-title" style="font-size: 24px; margin-bottom: 4px;">
            <?= e($tenant['nombre']) ?>
        </h1>
        <p class="page-subtitle" style="font-size: 14px; color: var(--color-text-muted); margin: 0; display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
            <span>Menú digital:</span>
            <a href="<?= e(app_url('/' . $tenant['slug'])) ?>" target="_blank" rel="noopener" style="color: var(--color-brand); font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px;">
                <span><?= e(app_url('/' . $tenant['slug'])) ?></span>
                <?= icon('external-link', 12) ?>
            </a>
        </p>
    </div>

    <div class="content-actions" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
        <!-- Botón rápido de estado: Abrir / Pausar pedidos -->
        <form method="POST" action="<?= e(app_url('/admin/toggle-recibiendo-pedidos')) ?>" style="margin: 0; display: inline-flex; align-items: center; gap: 8px;">
            <?= csrf_field() ?>
            <?php if (!empty($tenant['recibiendo_pedidos'])): ?>
                <span class="badge-pill success">
                    <span style="width: 7px; height: 7px; background: #059669; border-radius: 50%; display: inline-block;"></span>
                    <span>Recibiendo pedidos</span>
                </span>
                <button type="submit" class="btn btn-secondary btn-sm" style="font-weight: 600;" title="Pausar la recepción de pedidos en el menú digital">
                    <?= icon('clock', 13) ?>
                    <span>Pausar</span>
                </button>
            <?php else: ?>
                <span class="badge-pill danger">
                    <span style="width: 7px; height: 7px; background: #DC2626; border-radius: 50%; display: inline-block;"></span>
                    <span>Recepción pausada</span>
                </span>
                <button type="submit" class="btn btn-primary btn-sm" style="font-weight: 600;" title="Activar pedidos en el menú digital">
                    <?= icon('check', 13) ?>
                    <span>Abrir carta</span>
                </button>
            <?php endif; ?>
        </form>

        <!-- Botón directo al módulo de pedidos -->
        <a href="<?= e(app_url('/admin/pedidos')) ?>" class="btn btn-primary btn-sm" style="font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
            <?= icon('cart', 15) ?>
            <span>Ver Pedidos</span>
            <?php if (!empty($metricasMes['pendientes']) && $metricasMes['pendientes'] > 0): ?>
                <span style="background: #FFFFFF; color: var(--color-brand); font-size: 11px; font-weight: 800; border-radius: 999px; padding: 1px 7px; margin-left: 2px;">
                    <?= (int)$metricasMes['pendientes'] ?>
                </span>
            <?php endif; ?>
        </a>
    </div>
</div>

<?php if (!empty($estadoSuscripcion['en_gracia'])): ?>
    <div class="alert alert-warning" style="margin-bottom: 24px; display: flex; align-items: center; gap: 12px; background: #fff3cd; border: 1px solid #ffeeba; color: #856404; padding: 14px 18px; border-radius: var(--radius-md);">
        <?= icon('alert', 20) ?>
        <div>
            <strong>Período de Gracia Activo:</strong> Tu suscripción venció el <strong><?= !empty($tenant['fecha_vencimiento']) ? date('d/m/Y', strtotime($tenant['fecha_vencimiento'])) : 'recientemente' ?></strong>. Tienes <strong><?= (int)($estadoSuscripcion['dias_gracia_restantes'] ?? 0) ?> días</strong> restantes para enviar el soporte de pago antes de la suspensión automática del servicio.
        </div>
    </div>
<?php elseif (!empty($estadoSuscripcion['por_vencer'])): ?>
    <div class="alert alert-info" style="margin-bottom: 24px; display: flex; align-items: center; gap: 12px; background: #e8f4fd; border: 1px solid #b8daff; color: #004085; padding: 14px 18px; border-radius: var(--radius-md);">
        <?= icon('alert', 20) ?>
        <div>
            <strong>Recordatorio de Pago:</strong> Tu suscripción vencerá el <strong><?= date('d/m/Y', strtotime($tenant['fecha_vencimiento'])) ?></strong> (<?= (int)$estadoSuscripcion['dias_restantes'] === 0 ? 'hoy' : 'en ' . (int)$estadoSuscripcion['dias_restantes'] . ' días' ?>). Realiza la transferencia a tiempo para mantener tu catálogo activo.
        </div>
    </div>
<?php endif; ?>

<!-- 1. TARJETAS DE KPIS FINANCIEROS Y OPERATIVOS -->
<div class="kpi-grid" style="margin-bottom: 24px;">
    <!-- Ventas del Mes -->
    <div class="kpi-card">
        <div class="kpi-label">
            <span>Ventas del Mes</span>
            <div style="color: #059669;"><?= icon('coin', 18) ?></div>
        </div>
        <div class="kpi-value" style="color: #059669;">
            <?= format_cop((int)($metricasMes['ventas_mes'] ?? 0)) ?>
        </div>
        <div class="kpi-subtext">Mes en curso (excluye cancelados)</div>
    </div>

    <!-- Pedidos del Mes -->
    <div class="kpi-card">
        <div class="kpi-label">
            <span>Pedidos del Mes</span>
            <div style="color: var(--color-brand);"><?= icon('cart', 18) ?></div>
        </div>
        <div class="kpi-value">
            <?= (int)($metricasMes['pedidos_mes'] ?? 0) ?>
        </div>
        <div class="kpi-subtext">Comandas recibidas y procesadas</div>
    </div>

    <!-- Ticket Promedio -->
    <div class="kpi-card">
        <div class="kpi-label">
            <span>Ticket Promedio</span>
            <div style="color: var(--color-text-secondary);"><?= icon('chart', 18) ?></div>
        </div>
        <div class="kpi-value">
            <?= format_cop((int)($metricasMes['ticket_promedio'] ?? 0)) ?>
        </div>
        <div class="kpi-subtext">Consumo promedio por comanda</div>
    </div>

    <!-- Pedidos Pendientes -->
    <div class="kpi-card" style="<?= !empty($metricasMes['pendientes']) && $metricasMes['pendientes'] > 0 ? 'border-color: #FCD34D; background: #FFFDF5;' : '' ?>">
        <div class="kpi-label">
            <span>En Preparación / Por Atender</span>
            <div style="color: <?= !empty($metricasMes['pendientes']) && $metricasMes['pendientes'] > 0 ? '#D97706' : 'var(--color-text-muted)' ?>;">
                <?= icon('clock', 18) ?>
            </div>
        </div>
        <div class="kpi-value" style="color: <?= !empty($metricasMes['pendientes']) && $metricasMes['pendientes'] > 0 ? '#D97706' : 'inherit' ?>;">
            <?= (int)($metricasMes['pendientes'] ?? 0) ?>
        </div>
        <div class="kpi-subtext">
            <?= !empty($metricasMes['pendientes']) && $metricasMes['pendientes'] > 0 ? 'Requieren acción inmediata en cocina' : 'Todo al día en cocina' ?>
        </div>
    </div>
</div>

<!-- 2. SECCIÓN DE GRÁFICOS ANALÍTICOS (CHART.JS) -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(420px, 1fr)); gap: 20px; margin-bottom: 24px;">
    <!-- Gráfico 1: Ventas Diarias de los últimos 30 días -->
    <div class="card" style="padding: 22px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid var(--color-border-subtle);">
            <div>
                <h2 style="font-size: 15px; font-weight: 700; color: var(--color-text-main); margin: 0 0 2px 0;">
                    Ventas Diarias (Últimos 30 días)
                </h2>
                <span style="font-size: 12.5px; color: var(--color-text-muted);">
                    Evolución de ingresos diarios por pedidos completados (COP)
                </span>
            </div>
            <span class="badge" style="background: var(--color-brand-subtle); color: var(--color-brand); font-weight: 700; padding: 4px 10px; border-radius: var(--radius-full); font-size: 11.5px;">
                COP / Día
            </span>
        </div>
        <div style="position: relative; height: 260px; width: 100%;">
            <canvas id="chartVentasDiarias"></canvas>
        </div>
    </div>

    <!-- Gráfico 2: Pedidos por Día de la Semana -->
    <div class="card" style="padding: 22px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid var(--color-border-subtle);">
            <div>
                <h2 style="font-size: 15px; font-weight: 700; color: var(--color-text-main); margin: 0 0 2px 0;">
                    Demanda por Día de la Semana
                </h2>
                <span style="font-size: 12.5px; color: var(--color-text-muted);">
                    Distribución de pedidos para identificar tus días de mayor afluencia
                </span>
            </div>
            <span class="badge" style="background: var(--color-bg-subtle); color: var(--color-text-secondary); font-weight: 700; padding: 4px 10px; border-radius: var(--radius-full); font-size: 11.5px;">
                Total Pedidos
            </span>
        </div>
        <div style="position: relative; height: 260px; width: 100%;">
            <canvas id="chartDiasSemana"></canvas>
        </div>
    </div>
</div>

<!-- 3. TOP 5 PLATOS MÁS VENDIDOS & RESUMEN DE LA CARTA -->
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 24px;" class="dashboard-split-grid">
    <!-- Tabla Top 5 Platos -->
    <div class="card" style="padding: 0; overflow: hidden;">
        <div style="padding: 18px 22px; border-bottom: 1px solid var(--color-border-subtle); display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h2 style="font-size: 15px; font-weight: 700; color: var(--color-text-main); margin: 0 0 2px 0;">
                    Top 5 Platos Más Vendidos
                </h2>
                <span style="font-size: 12.5px; color: var(--color-text-muted);">
                    Los platos y bebidas favoritos de tus comensales
                </span>
            </div>
            <a href="<?= e(app_url('/admin/catalogo')) ?>" style="font-size: 12.5px; color: var(--color-brand); font-weight: 600; text-decoration: none;">
                Ver catálogo &rarr;
            </a>
        </div>

        <?php if (empty($topProductos)): ?>
            <div style="text-align: center; padding: 48px 20px;">
                <div style="width: 44px; height: 44px; border-radius: 50%; background: var(--color-bg-subtle); display: inline-flex; align-items: center; justify-content: center; color: var(--color-brand); margin-bottom: 12px;">
                    <?= icon('cutlery', 20) ?>
                </div>
                <h3 style="font-size: 15px; font-weight: 700; color: var(--color-text-main); margin: 0 0 4px 0;">
                    Sin datos de ventas aún
                </h3>
                <p style="color: var(--color-text-muted); font-size: 13px; margin: 0;">
                    El ranking se calculará automáticamente conforme tus clientes confirmen pedidos.
                </p>
            </div>
        <?php else: ?>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 60px; text-align: center;">#</th>
                            <th>Plato o Bebida</th>
                            <th style="text-align: right; width: 130px;">Unidades</th>
                            <th style="text-align: right; width: 150px;">Total Ventas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($topProductos as $idx => $prod): ?>
                            <tr>
                                <td style="text-align: center;">
                                    <?php if ($idx === 0): ?>
                                        <span style="width: 24px; height: 24px; border-radius: 50%; background: #FEF3C7; color: #D97706; font-weight: 800; font-size: 12px; display: inline-flex; align-items: center; justify-content: center;">1</span>
                                    <?php elseif ($idx === 1): ?>
                                        <span style="width: 24px; height: 24px; border-radius: 50%; background: #F1F5F9; color: #475569; font-weight: 800; font-size: 12px; display: inline-flex; align-items: center; justify-content: center;">2</span>
                                    <?php elseif ($idx === 2): ?>
                                        <span style="width: 24px; height: 24px; border-radius: 50%; background: #FFEDD5; color: #C2410C; font-weight: 800; font-size: 12px; display: inline-flex; align-items: center; justify-content: center;">3</span>
                                    <?php else: ?>
                                        <span style="width: 24px; height: 24px; border-radius: 50%; background: #F8FAFC; color: var(--color-text-muted); font-weight: 700; font-size: 12px; display: inline-flex; align-items: center; justify-content: center;"><?= $idx + 1 ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong style="color: var(--color-text-main); font-size: 13.5px;"><?= e($prod['nombre']) ?></strong>
                                </td>
                                <td style="text-align: right; font-weight: 700; color: var(--color-text-main);">
                                    <?= (int)$prod['cantidad_vendida'] ?> un.
                                </td>
                                <td style="text-align: right; font-weight: 700; color: #059669;">
                                    <?= format_cop((int)$prod['total_ventas']) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Resumen de Infraestructura & Suscripción -->
    <div class="card" style="padding: 22px; display: flex; flex-direction: column; justify-content: space-between;">
        <div>
            <h2 style="font-size: 15px; font-weight: 700; color: var(--color-text-main); margin: 0 0 4px 0;">
                Estado del Restaurante
            </h2>
            <span style="font-size: 12.5px; color: var(--color-text-muted);">
                Capacidad y suscripción activa
            </span>

            <div style="margin-top: 18px; display: flex; flex-direction: column; gap: 12px;">
                <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 10px; border-bottom: 1px solid var(--color-border-subtle);">
                    <span style="font-size: 13px; color: var(--color-text-muted); display: inline-flex; align-items: center; gap: 6px;">
                        <?= icon('catalog', 14) ?> Categorías en carta
                    </span>
                    <strong style="font-size: 13.5px; color: var(--color-text-main);"><?= (int)$stats['categorias'] ?></strong>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 10px; border-bottom: 1px solid var(--color-border-subtle);">
                    <span style="font-size: 13px; color: var(--color-text-muted); display: inline-flex; align-items: center; gap: 6px;">
                        <?= icon('store', 14) ?> Platos y bebidas
                    </span>
                    <strong style="font-size: 13.5px; color: var(--color-text-main);"><?= (int)$stats['productos'] ?></strong>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 10px; border-bottom: 1px solid var(--color-border-subtle);">
                    <span style="font-size: 13px; color: var(--color-text-muted); display: inline-flex; align-items: center; gap: 6px;">
                        <?= icon('cutlery', 14) ?> Mesas con código QR
                    </span>
                    <strong style="font-size: 13.5px; color: var(--color-text-main);"><?= (int)$stats['mesas'] ?></strong>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; padding-bottom: 10px; border-bottom: 1px solid var(--color-border-subtle);">
                    <span style="font-size: 13px; color: var(--color-text-muted); display: inline-flex; align-items: center; gap: 6px;">
                        <?= icon('modifiers', 14) ?> Grupos de opciones
                    </span>
                    <strong style="font-size: 13.5px; color: var(--color-text-main);"><?= (int)$stats['grupos'] ?></strong>
                </div>
            </div>
        </div>

        <div style="margin-top: 18px; padding-top: 14px; border-top: 1px solid var(--color-border-subtle); display: flex; justify-content: space-between; align-items: center;">
            <div>
                <span style="font-size: 11px; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em; color: var(--color-text-subtle); display: block;">Suscripción</span>
                <span style="font-size: 12px; color: var(--color-text-muted);">
                    <?= !empty($tenant['fecha_vencimiento']) ? 'Vence: ' . date('d/m/Y', strtotime($tenant['fecha_vencimiento'])) : 'Sin vencimiento' ?>
                </span>
            </div>
            <span class="badge-pill" style="font-size: 11.5px; padding: 3px 10px; color: <?= e($estadoSuscripcion['color']) ?>; background: <?= e($estadoSuscripcion['bg_color']) ?>;">
                <?= e($estadoSuscripcion['label']) ?>
            </span>
        </div>
    </div>
</div>

<!-- 4. ACCESO DIRECTO A MÓDULOS DE GESTIÓN -->
<div class="card" style="padding: 0; overflow: hidden; margin-bottom: 24px;">
    <div style="padding: 20px 24px; border-bottom: 1px solid var(--color-border-subtle); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;">
        <div>
            <h2 style="font-size: 16px; font-weight: 700; color: var(--color-text-main); margin: 0 0 2px 0;">
                Módulos de Gestión del Restaurante
            </h2>
            <p style="font-size: 13px; color: var(--color-text-muted); margin: 0;">
                Acceso a todas las herramientas de operación diaria y configuración
            </p>
        </div>
    </div>

    <div style="padding: 24px;">
        <div class="modules-grid">
            <!-- Pedidos en Vivo -->
            <a href="<?= e(app_url('/admin/pedidos')) ?>" class="module-card">
                <div>
                    <div class="module-card-icon">
                        <?= icon('cart', 26) ?>
                    </div>
                    <h3 class="module-card-title">Gestión de Pedidos</h3>
                    <p class="module-card-desc">
                        Control en tiempo real de órdenes de comensales, actualización de cocina (generado, en preparación, listo) y enlace directo a WhatsApp.
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
                    <div class="module-card-icon" style="color: #4F46E5;">
                        <?= icon('cutlery', 26) ?>
                    </div>
                    <h3 class="module-card-title">Mesas &amp; Códigos QR</h3>
                    <p class="module-card-desc">
                        Genera códigos QR de alta resolución para cada mesa del local y descarga el PDF imprimible listo para recortar y laminar.
                    </p>
                </div>
                <span class="module-card-link" style="color: #4F46E5;">Gestionar mesas y QR &rarr;</span>
            </a>

            <!-- Control de Agotados -->
            <a href="<?= e(app_url('/admin/disponibilidad')) ?>" class="module-card" style="border-color: rgba(239, 68, 68, 0.2); background: #FFFBFB;">
                <div>
                    <div class="module-card-icon" style="color: var(--color-danger);">
                        <?= icon('zap', 26) ?>
                    </div>
                    <h3 class="module-card-title" style="color: var(--color-danger);">Control de Agotados</h3>
                    <p class="module-card-desc">
                        Diseñado para la hora pico: apaga un plato en 1 segundo cuando se agote un ingrediente en cocina para que los clientes no puedan pedirlo.
                    </p>
                </div>
                <span class="module-card-link" style="color: var(--color-danger);">Ver disponibles y agotados &rarr;</span>
            </a>

            <!-- Modificadores -->
            <a href="<?= e(app_url('/admin/modificadores')) ?>" class="module-card">
                <div>
                    <div class="module-card-icon" style="color: var(--color-text-secondary);">
                        <?= icon('modifiers', 26) ?>
                    </div>
                    <h3 class="module-card-title">Modificadores y Adiciones</h3>
                    <p class="module-card-desc">
                        Configura preguntas obligatorias (ej: término de carne) o adiciones con costo adicional (ej: tocineta o queso extra).
                    </p>
                </div>
                <span class="module-card-link">Gestionar modificadores &rarr;</span>
            </a>

            <!-- Configuración -->
            <a href="<?= e(app_url('/admin/configuracion')) ?>" class="module-card">
                <div>
                    <div class="module-card-icon" style="color: var(--color-text-secondary);">
                        <?= icon('settings', 26) ?>
                    </div>
                    <h3 class="module-card-title">Configuración y Marca</h3>
                    <p class="module-card-desc">
                        Define tu número de WhatsApp para recibir pedidos, sube tu logo, personaliza tus colores, horarios y modalidades de entrega.
                    </p>
                </div>
                <span class="module-card-link">Editar configuración &rarr;</span>
            </a>
        </div>
    </div>
</div>

<!-- 5. INICIALIZACIÓN DE GRÁFICOS CHART.JS -->
<script nonce="<?= e($cspNonce) ?>">
document.addEventListener('DOMContentLoaded', function () {
    if (typeof Chart === 'undefined') {
        console.warn('Chart.js no está cargado');
        return;
    }

    const ventasData = <?= json_encode($ventasPorDia, JSON_UNESCAPED_UNICODE) ?>;
    const diasSemanaData = <?= json_encode($pedidosPorDiaSemana, JSON_UNESCAPED_UNICODE) ?>;

    // 1. Gráfico de Ventas Diarias (30 días)
    const ctxVentas = document.getElementById('chartVentasDiarias');
    if (ctxVentas && ventasData && ventasData.length > 0) {
        new Chart(ctxVentas, {
            type: 'line',
            data: {
                labels: ventasData.map(d => d.label),
                datasets: [{
                    label: 'Ventas (COP)',
                    data: ventasData.map(d => d.total),
                    borderColor: '#E85D04',
                    backgroundColor: 'rgba(232, 93, 4, 0.08)',
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.3,
                    pointBackgroundColor: '#E85D04',
                    pointBorderColor: '#FFFFFF',
                    pointBorderWidth: 1.5,
                    pointRadius: 3,
                    pointHoverRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function (ctx) {
                                const val = ctx.parsed.y || 0;
                                return 'Ventas: $ ' + val.toLocaleString('es-CO');
                            },
                            afterLabel: function (ctx) {
                                const item = ventasData[ctx.dataIndex];
                                return (item && item.pedidos) ? 'Pedidos: ' + item.pedidos : '';
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
                        ticks: {
                            font: { size: 11 },
                            maxRotation: 0,
                            autoSkip: true,
                            maxTicksLimit: 10
                        }
                    }
                }
            }
        });
    }

    // 2. Gráfico de Pedidos por Día de la Semana
    const ctxDias = document.getElementById('chartDiasSemana');
    if (ctxDias && diasSemanaData) {
        const labels = Object.keys(diasSemanaData);
        const counts = Object.values(diasSemanaData);

        new Chart(ctxDias, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Pedidos',
                    data: counts,
                    backgroundColor: 'rgba(79, 70, 229, 0.75)',
                    hoverBackgroundColor: '#4338CA',
                    borderRadius: 6,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function (ctx) {
                                const val = ctx.parsed.y || 0;
                                return 'Pedidos: ' + val + (val === 1 ? ' pedido' : ' pedidos');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            precision: 0,
                            font: { size: 11 }
                        },
                        grid: { color: 'rgba(15, 23, 42, 0.06)' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11.5, weight: '600' } }
                    }
                }
            }
        });
    }
});
</script>
