<div class="page-header">
    <div>
        <h1 class="page-title"><?= e($tenant['nombre']) ?></h1>
        <p class="page-subtitle">
            Menú digital para tus clientes: 
            <a href="<?= e(app_url('/' . $tenant['slug'])) ?>" target="_blank" style="color: var(--brand-primary); font-weight: 600; text-decoration: none;">
                <?= e(app_url('/' . $tenant['slug'])) ?> ↗
            </a>
        </p>
    </div>

    <div>
        <?php if (!empty($tenant['recibiendo_pedidos'])): ?>
            <span class="badge-pill success">
                <span style="width: 7px; height: 7px; background: #059669; border-radius: 50%; display: inline-block;"></span>
                <span>Recibiendo pedidos</span>
            </span>
        <?php else: ?>
            <span class="badge-pill danger">
                <span style="width: 7px; height: 7px; background: #DC2626; border-radius: 50%; display: inline-block;"></span>
                <span>Recepción pausada</span>
            </span>
        <?php endif; ?>
    </div>
</div>

<!-- KPI CARDS (DENSITY & CLARITY) -->
<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-label">
            <span>Categorías activas</span>
            <?= icon('catalog', 16) ?>
        </div>
        <div class="kpi-value"><?= (int)$stats['categorias'] ?></div>
        <div class="kpi-subtext">Límite del plan: <?= (int)($plan['max_categorias'] ?? 0) ?></div>
    </div>

    <div class="kpi-card">
        <div class="kpi-label">
            <span>Platos y bebidas</span>
            <?= icon('store', 16) ?>
        </div>
        <div class="kpi-value" style="color: var(--brand-primary);"><?= (int)$stats['productos'] ?></div>
        <div class="kpi-subtext">Límite del plan: <?= (int)($plan['max_productos'] ?? 0) ?></div>
    </div>

    <div class="kpi-card">
        <div class="kpi-label">
            <span>Modificadores</span>
            <?= icon('modifiers', 16) ?>
        </div>
        <div class="kpi-value"><?= (int)$stats['grupos'] ?></div>
        <div class="kpi-subtext">Grupos de opciones y extras</div>
    </div>

    <div class="kpi-card">
        <div class="kpi-label">
            <span>Suscripción</span>
            <span class="badge-pill success" style="font-size: 11px; padding: 2px 8px;">Al día</span>
        </div>
        <div class="kpi-value" style="font-size: 18px; margin-top: 4px;"><?= e($plan['nombre'] ?? 'Plan Pro') ?></div>
        <div class="kpi-subtext">Vence: <?= e($tenant['fecha_vencimiento'] ?? 'Activo') ?></div>
    </div>
</div>

<!-- GESTIÓN OPERATIVA EN TARJETAS LIMPIAS (PC GRID) -->
<div class="ui-panel">
    <div class="ui-panel-header">
        <h2 class="ui-panel-title">Módulos de Gestión</h2>
        <span style="font-size: 13px; color: var(--text-muted);">Acceso directo a las herramientas del restaurante</span>
    </div>

    <div class="ui-panel-body" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
        <a href="<?= e(app_url('/admin/catalogo')) ?>" style="text-decoration: none; padding: 20px; border: 1px solid var(--border-subtle); border-radius: var(--radius-md); background: #FFFFFF; display: flex; flex-direction: column; justify-content: space-between; transition: border-color 140ms ease;">
            <div>
                <div style="color: var(--brand-primary); margin-bottom: 12px;">
                    <?= icon('catalog', 24) ?>
                </div>
                <h3 style="font-size: 16px; font-weight: 600; color: var(--text-primary); margin-bottom: 6px;">Catálogo de Menú</h3>
                <p style="font-size: 13.5px; color: var(--text-muted); line-height: 1.45; margin: 0;">
                    Administra tus categorías de platos y sube tus productos con fotografías, descripción y precios en pesos colombianos.
                </p>
            </div>
            <span style="color: var(--brand-primary); font-weight: 600; font-size: 13px; margin-top: 16px;">Abrir catálogo →</span>
        </a>

        <a href="<?= e(app_url('/admin/disponibilidad')) ?>" style="text-decoration: none; padding: 20px; border: 1px solid rgba(239, 68, 68, 0.2); border-radius: var(--radius-md); background: #FFFBFB; display: flex; flex-direction: column; justify-content: space-between; transition: border-color 140ms ease;">
            <div>
                <div style="color: #DC2626; margin-bottom: 12px;">
                    <?= icon('zap', 24) ?>
                </div>
                <h3 style="font-size: 16px; font-weight: 600; color: #DC2626; margin-bottom: 6px;">Control de Agotados</h3>
                <p style="font-size: 13.5px; color: var(--text-muted); line-height: 1.45; margin: 0;">
                    Diseñado para la hora pico: apaga un plato en 1 segundo cuando se agote un ingrediente en cocina para que los clientes no puedan pedirlo.
                </p>
            </div>
            <span style="color: #DC2626; font-weight: 600; font-size: 13px; margin-top: 16px;">Ver disponibles y agotados →</span>
        </a>

        <a href="<?= e(app_url('/admin/modificadores')) ?>" style="text-decoration: none; padding: 20px; border: 1px solid var(--border-subtle); border-radius: var(--radius-md); background: #FFFFFF; display: flex; flex-direction: column; justify-content: space-between; transition: border-color 140ms ease;">
            <div>
                <div style="color: var(--text-secondary); margin-bottom: 12px;">
                    <?= icon('modifiers', 24) ?>
                </div>
                <h3 style="font-size: 16px; font-weight: 600; color: var(--text-primary); margin-bottom: 6px;">Modificadores y Adiciones</h3>
                <p style="font-size: 13.5px; color: var(--text-muted); line-height: 1.45; margin: 0;">
                    Configura preguntas obligatorias (ej: término de carne) o adiciones con costo adicional (ej: tocineta o queso extra).
                </p>
            </div>
            <span style="color: var(--brand-primary); font-weight: 600; font-size: 13px; margin-top: 16px;">Gestionar modificadores →</span>
        </a>

        <a href="<?= e(app_url('/admin/configuracion')) ?>" style="text-decoration: none; padding: 20px; border: 1px solid var(--border-subtle); border-radius: var(--radius-md); background: #FFFFFF; display: flex; flex-direction: column; justify-content: space-between; transition: border-color 140ms ease;">
            <div>
                <div style="color: var(--text-secondary); margin-bottom: 12px;">
                    <?= icon('settings', 24) ?>
                </div>
                <h3 style="font-size: 16px; font-weight: 600; color: var(--text-primary); margin-bottom: 6px;">Configuración y Marca</h3>
                <p style="font-size: 13.5px; color: var(--text-muted); line-height: 1.45; margin: 0;">
                    Define tu número de WhatsApp para recibir pedidos, sube tu logo, personaliza tus colores, horarios y modalidades de entrega.
                </p>
            </div>
            <span style="color: var(--brand-primary); font-weight: 600; font-size: 13px; margin-top: 16px;">Editar configuración →</span>
        </a>
    </div>
</div>
