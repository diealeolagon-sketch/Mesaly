<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 12px;">
    <div>
        <h2><?= e($tenant['nombre']) ?></h2>
        <p style="color: var(--brand-gray-muted); font-size: 14px;">
            Menú público: 
            <a href="<?= e(app_url('/' . $tenant['slug'])) ?>" target="_blank" style="font-weight: 600;">
                <?= e(app_url('/' . $tenant['slug'])) ?> ↗
            </a>
        </p>
    </div>
    <div>
        <span class="badge" style="background: <?= !empty($tenant['recibiendo_pedidos']) ? 'var(--status-al-dia)' : 'var(--status-bloqueado)' ?>; color: #fff; font-size: 13px; padding: 6px 14px;">
            <?= !empty($tenant['recibiendo_pedidos']) ? '● Recibiendo Pedidos' : '○ Cerrado Temporalmente' ?>
        </span>
    </div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-bottom: 24px;">
    <div class="card" style="margin-bottom: 0; text-align: center;">
        <span style="font-size: 13px; color: var(--brand-gray-muted); display: block;">Categorías Activas</span>
        <strong style="font-size: 32px; color: var(--brand-primary);"><?= (int)$stats['categorias'] ?></strong>
        <span style="font-size: 12px; color: var(--brand-gray-muted); display: block; margin-top: 4px;">
            Límite: <?= (int)($plan['max_categorias'] ?? 0) ?>
        </span>
    </div>

    <div class="card" style="margin-bottom: 0; text-align: center;">
        <span style="font-size: 13px; color: var(--brand-gray-muted); display: block;">Productos en Menú</span>
        <strong style="font-size: 32px; color: var(--brand-primary);"><?= (int)$stats['productos'] ?></strong>
        <span style="font-size: 12px; color: var(--brand-gray-muted); display: block; margin-top: 4px;">
            Límite: <?= (int)($plan['max_productos'] ?? 0) ?>
        </span>
    </div>

    <div class="card" style="margin-bottom: 0; text-align: center;">
        <span style="font-size: 13px; color: var(--brand-gray-muted); display: block;">Grupos de Modificadores</span>
        <strong style="font-size: 32px; color: var(--brand-primary);"><?= (int)$stats['grupos'] ?></strong>
        <span style="font-size: 12px; color: var(--brand-gray-muted); display: block; margin-top: 4px;">Personalización</span>
    </div>

    <div class="card" style="margin-bottom: 0; text-align: center;">
        <span style="font-size: 13px; color: var(--brand-gray-muted); display: block;">Plan Actual</span>
        <strong style="font-size: 20px; color: var(--brand-ink); display: block; margin: 6px 0;"><?= e($plan['nombre'] ?? 'Plan Básico') ?></strong>
        <span class="badge" style="background: var(--brand-gray-light); color: var(--brand-ink);">
            Vence: <?= e($tenant['fecha_vencimiento'] ?? 'Activo') ?>
        </span>
    </div>
</div>

<div class="card">
    <h3 style="margin-bottom: 16px;">Accesos Rápidos de Gestión</h3>
    <div style="display: flex; gap: 12px; flex-wrap: wrap;">
        <a href="<?= e(app_url('/admin/catalogo')) ?>" class="btn btn-primary">
            📋 Gestionar Categorías y Productos
        </a>
        <a href="<?= e(app_url('/admin/disponibilidad')) ?>" class="btn btn-secondary" style="color: #DC2626; border-color: #FECACA; background: #FEF2F2;">
            ⚡ Pantalla Rápida de Agotados
        </a>
        <a href="<?= e(app_url('/admin/modificadores')) ?>" class="btn btn-secondary">
            ⚙️ Grupos y Opciones Extra
        </a>
        <a href="<?= e(app_url('/admin/configuracion')) ?>" class="btn btn-secondary">
            🎨 Marca, Colores y Horarios
        </a>
    </div>
</div>
