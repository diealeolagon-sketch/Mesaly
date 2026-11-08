<div class="content-header" style="margin-bottom: 24px;">
    <div>
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
            <a href="<?= e(app_url('/superadmin')) ?>" style="color: var(--color-text-muted); font-size: 13px; text-decoration: none;">
                Super Admin &rarr;
            </a>
            <span style="color: var(--color-text-subtle);">Métricas Globales</span>
        </div>
        <h1 class="page-title" style="font-size: 24px; margin-bottom: 4px;">
            Métricas Globales de la Red Mesaly
        </h1>
        <p class="page-subtitle" style="font-size: 14px; color: var(--color-text-muted); margin: 0;">
            Rendimiento del negocio, ingresos recurrentes (MRR), adopción de catálogo y volumen transaccional de restaurantes.
        </p>
    </div>

    <div class="content-actions">
        <a href="<?= e(app_url('/superadmin/pagos')) ?>" class="btn btn-secondary">
            <?= icon('cash', 14) ?>
            <span>Ver Historial de Pagos</span>
        </a>
    </div>
</div>

<!-- 1. BLOQUE DE FINANZAS Y MRR -->
<div style="margin-bottom: 28px;">
    <h2 style="font-size: 15px; font-weight: 700; color: var(--color-text-main); margin: 0 0 14px 0; display: flex; align-items: center; gap: 8px;">
        <?= icon('chart', 16) ?>
        <span>Ingresos Recurrentes y Recaudación</span>
    </h2>

    <div class="kpi-grid" style="grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));">
        <div class="kpi-card-modern">
            <div class="kpi-icon-badge" style="background: var(--color-brand-subtle); color: var(--color-brand);">
                <?= icon('cash', 20) ?>
            </div>
            <span style="font-size: 13px; font-weight: 600; color: var(--color-text-muted);">MRR (Mensual Recurrente)</span>
            <strong style="font-size: 26px; color: var(--color-brand); font-variant-numeric: tabular-nums;">
                <?= format_cop($mrr) ?>
            </strong>
            <span style="font-size: 12px; color: var(--color-text-muted);">
                Suma de tarifas pactadas activas en base mensual
            </span>
        </div>

        <div class="kpi-card-modern">
            <div class="kpi-icon-badge" style="background: #ECFDF5; color: #059669;">
                <?= icon('dashboard', 20) ?>
            </div>
            <span style="font-size: 13px; font-weight: 600; color: var(--color-text-muted);">ARR Proyectado (Anual)</span>
            <strong style="font-size: 26px; color: #059669; font-variant-numeric: tabular-nums;">
                <?= format_cop($arr) ?>
            </strong>
            <span style="font-size: 12px; color: var(--color-text-muted);">
                Proyección a 12 meses sobre la base actual
            </span>
        </div>

        <div class="kpi-card-modern">
            <div class="kpi-icon-badge" style="background: #EFF6FF; color: #2563EB;">
                <?= icon('check', 20) ?>
            </div>
            <span style="font-size: 13px; font-weight: 600; color: var(--color-text-muted);">Recaudado en <?= date('F') ?></span>
            <strong style="font-size: 26px; color: #2563EB; font-variant-numeric: tabular-nums;">
                <?= format_cop($recaudadoMes) ?>
            </strong>
            <span style="font-size: 12px; color: var(--color-text-muted);">
                Pagos manuales asentados este mes calendario
            </span>
        </div>
    </div>
</div>

<!-- 2. DESGLOSE DE NEGOCIOS POR ESTADO -->
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 28px;" class="config-grid-2">
    <div class="card" style="padding: 24px;">
        <h3 style="font-size: 16px; font-weight: 700; color: var(--color-text-main); margin: 0 0 16px 0;">
            Estado de Suscripciones en la Red
        </h3>
        
        <div style="display: flex; flex-direction: column; gap: 12px;">
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 14px; background: #ECFDF5; border: 1px solid #A7F3D0; border-radius: var(--radius-sm);">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="width: 8px; height: 8px; border-radius: 50%; background: #10B981;"></span>
                    <strong style="font-size: 13.5px; color: #065F46;">Al Día</strong>
                </div>
                <strong style="font-size: 15px; color: #065F46;"><?= (int)$conteoEstados['al_dia'] ?> restaurantes</strong>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 14px; background: #FFFBEB; border: 1px solid #FDE68A; border-radius: var(--radius-sm);">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="width: 8px; height: 8px; border-radius: 50%; background: #F59E0B;"></span>
                    <strong style="font-size: 13.5px; color: #92400E;">Por Vencer (Esta semana)</strong>
                </div>
                <strong style="font-size: 15px; color: #92400E;"><?= (int)$conteoEstados['por_vencer'] ?> restaurantes</strong>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 14px; background: #FFF7ED; border: 1px solid #FED7AA; border-radius: var(--radius-sm);">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="width: 8px; height: 8px; border-radius: 50%; background: #EA580C;"></span>
                    <strong style="font-size: 13.5px; color: #9A3412;">En Días de Gracia</strong>
                </div>
                <strong style="font-size: 15px; color: #9A3412;"><?= (int)$conteoEstados['en_gracia'] ?> restaurantes</strong>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 14px; background: #FEF2F2; border: 1px solid #FECACA; border-radius: var(--radius-sm);">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="width: 8px; height: 8px; border-radius: 50%; background: #EF4444;"></span>
                    <strong style="font-size: 13.5px; color: #991B1B;">Bloqueados por Mora</strong>
                </div>
                <strong style="font-size: 15px; color: #991B1B;"><?= (int)$conteoEstados['bloqueado'] ?> restaurantes</strong>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 14px; background: #F1F5F9; border: 1px solid #CBD5E1; border-radius: var(--radius-sm);">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="width: 8px; height: 8px; border-radius: 50%; background: #94A3B8;"></span>
                    <strong style="font-size: 13.5px; color: #475569;">Cancelados / Inactivos</strong>
                </div>
                <strong style="font-size: 15px; color: #475569;"><?= (int)$conteoEstados['cancelado'] ?> restaurantes</strong>
            </div>
        </div>
    </div>

    <!-- 3. ACTIVIDAD OPERATIVA DE LA RED -->
    <div class="card" style="padding: 24px;">
        <h3 style="font-size: 16px; font-weight: 700; color: var(--color-text-main); margin: 0 0 16px 0;">
            Volumen Operativo en Restaurantes
        </h3>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div style="padding: 16px; background: #FAFAFC; border: 1px solid var(--color-border); border-radius: var(--radius-sm);">
                <span style="font-size: 12px; color: var(--color-text-muted); font-weight: 600; display: block; margin-bottom: 4px;">Pedidos Tramitados</span>
                <strong style="font-size: 24px; color: var(--color-text-main); font-variant-numeric: tabular-nums;"><?= (int)$totalPedidos ?></strong>
                <span style="font-size: 11px; color: var(--color-text-subtle); display: block; margin-top: 4px;">Enviados por WhatsApp</span>
            </div>

            <div style="padding: 16px; background: #FAFAFC; border: 1px solid var(--color-border); border-radius: var(--radius-sm);">
                <span style="font-size: 12px; color: var(--color-text-muted); font-weight: 600; display: block; margin-bottom: 4px;">Volumen Estimado Ventas</span>
                <strong style="font-size: 20px; color: #059669; font-variant-numeric: tabular-nums;"><?= format_cop($volumenVentasRed) ?></strong>
                <span style="font-size: 11px; color: var(--color-text-subtle); display: block; margin-top: 4px;">Total cartas digitales</span>
            </div>

            <div style="padding: 16px; background: #FAFAFC; border: 1px solid var(--color-border); border-radius: var(--radius-sm);">
                <span style="font-size: 12px; color: var(--color-text-muted); font-weight: 600; display: block; margin-bottom: 4px;">Mesas con QR Generadas</span>
                <strong style="font-size: 24px; color: var(--color-text-main);"><?= (int)$totalMesas ?></strong>
                <span style="font-size: 11px; color: var(--color-text-subtle); display: block; margin-top: 4px;">Códigos QR activos</span>
            </div>

            <div style="padding: 16px; background: #FAFAFC; border: 1px solid var(--color-border); border-radius: var(--radius-sm);">
                <span style="font-size: 12px; color: var(--color-text-muted); font-weight: 600; display: block; margin-bottom: 4px;">Platos en Catálogos</span>
                <strong style="font-size: 24px; color: var(--color-text-main);"><?= (int)$totalProductos ?></strong>
                <span style="font-size: 11px; color: var(--color-text-subtle); display: block; margin-top: 4px;">Total ítems activos</span>
            </div>
        </div>
    </div>
</div>
