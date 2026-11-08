<div class="page-header">
    <div>
        <h1 class="page-title"><?= e($tenant['nombre']) ?></h1>
        <p class="page-subtitle">
            Menú digital para tus clientes: 
            <a href="<?= e(app_url('/' . $tenant['slug'])) ?>" target="_blank" style="color: var(--color-brand); font-weight: 600; text-decoration: none;">
                <?= e(app_url('/' . $tenant['slug'])) ?> &rarr;
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

<!-- KPI CARDS -->
<div class="kpi-grid">
    <div class="kpi-card">
        <div class="kpi-label">
            <span>Secciones activas</span>
            <?= icon('catalog', 16) ?>
        </div>
        <div class="kpi-value"><?= (int)$stats['categorias'] ?></div>
        <div class="kpi-subtext">Ilimitadas</div>
    </div>

    <div class="kpi-card">
        <div class="kpi-label">
            <span>Platos y bebidas</span>
            <?= icon('store', 16) ?>
        </div>
        <div class="kpi-value" style="color: var(--color-brand);"><?= (int)$stats['productos'] ?></div>
        <div class="kpi-subtext">Ilimitados</div>
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
            <span class="badge-pill" style="font-size: 11px; padding: 2px 8px; color: <?= e($estadoSuscripcion['color']) ?>; background: <?= e($estadoSuscripcion['bg_color']) ?>;">
                <?= e($estadoSuscripcion['label']) ?>
            </span>
        </div>
        <div class="kpi-value" style="font-size: 18px; margin-top: 4px;"><?= format_cop((int)($tenant['precio'] ?? 49000)) ?></div>
        <div class="kpi-subtext">
            Vence: <?= !empty($tenant['fecha_vencimiento']) ? date('d/m/Y', strtotime($tenant['fecha_vencimiento'])) : 'Sin fecha' ?>
        </div>
    </div>
</div>

<!-- GESTIÓN OPERATIVA EN TARJETAS LIMPIAS -->
<div class="card" style="padding: 0; overflow: hidden;">
    <div style="padding: 20px 24px; border-bottom: 1px solid var(--color-border-subtle); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px;">
        <div>
            <h2 style="font-size: 16px; font-weight: 700; color: var(--color-text-main); margin: 0 0 2px 0;">
                Módulos de Gestión
            </h2>
            <p style="font-size: 13px; color: var(--color-text-muted); margin: 0;">
                Herramientas principales para administrar tu restaurante
            </p>
        </div>
    </div>

    <div style="padding: 24px;">
        <div class="modules-grid">
            <a href="<?= e(app_url('/admin/catalogo')) ?>" class="module-card">
                <div>
                    <div class="module-card-icon">
                        <?= icon('catalog', 26) ?>
                    </div>
                    <h3 class="module-card-title">Catálogo de Menú</h3>
                    <p class="module-card-desc">
                        Organiza tus categorías de platos y sube tus productos con fotografías, descripción y precios en pesos colombianos.
                    </p>
                </div>
                <span class="module-card-link">Abrir catálogo &rarr;</span>
            </a>

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
