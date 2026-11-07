<!-- GUÍA DE INICIO Y ESTADO DEL RESTAURANTE -->
<div class="modern-card" style="background: linear-gradient(135deg, #FFF9F2 0%, #FFFFFF 100%); border-left: 6px solid var(--brand-primary);">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <span style="font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; color: var(--brand-primary);">
                Panel Administrativo
            </span>
            <h1 style="font-size: 28px; margin: 4px 0 6px 0; color: var(--admin-text-main);">
                <?= e($tenant['nombre']) ?>
            </h1>
            <p style="color: var(--admin-text-muted); font-size: 14px; margin: 0;">
                Tu enlace de menú digital para compartir en Instagram y WhatsApp: 
                <a href="<?= e(app_url('/' . $tenant['slug'])) ?>" target="_blank" style="font-weight: 700; color: var(--brand-primary); text-decoration: underline;">
                    <?= e(app_url('/' . $tenant['slug'])) ?> ↗
                </a>
            </p>
        </div>

        <div>
            <?php if (!empty($tenant['recibiendo_pedidos'])): ?>
                <span class="badge-status active" style="font-size: 14px; padding: 8px 16px;">
                    <span style="width: 8px; height: 8px; background: #10B981; border-radius: 50%; display: inline-block;"></span>
                    Menú Abierto (Recibiendo Pedidos)
                </span>
            <?php else: ?>
                <span class="badge-status inactive" style="font-size: 14px; padding: 8px 16px;">
                    <span style="width: 8px; height: 8px; background: #EF4444; border-radius: 50%; display: inline-block;"></span>
                    Menú Cerrado Temporalmente
                </span>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- EXPLAINER: CÓMO FUNCIONA EL SISTEMA -->
<div class="guide-banner">
    <div class="guide-icon">💡</div>
    <div>
        <h3 class="guide-title">¿Cómo funciona tu menú digital en Mesaly?</h3>
        <p class="guide-desc">
            <strong>1. Configura:</strong> Sube tu logo, ajusta tus colores y verifica que tu número de WhatsApp esté correcto.<br>
            <strong>2. Carga tus platos:</strong> Organiza tu carta por categorías y sube fotos de tus platos con precio en pesos colombianos.<br>
            <strong>3. Recibe pedidos:</strong> Tus clientes escanean tu QR o abren tu enlace, eligen sus platos en el carrito y te envían el pedido estructurado directamente a tu WhatsApp.
        </p>
    </div>
</div>

<!-- MÉTRICAS EN TARJETAS LIMPIAS (PC GRID) -->
<div class="stats-grid-pc">
    <div class="stat-card-modern">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
            <span style="font-size: 13px; font-weight: 600; color: var(--admin-text-muted);">Categorías Activas</span>
            <span style="font-size: 20px;">📂</span>
        </div>
        <div style="font-size: 34px; font-weight: 800; color: var(--admin-text-main); line-height: 1.1;">
            <?= (int)$stats['categorias'] ?>
        </div>
        <div style="font-size: 12.5px; color: var(--admin-text-subtle); margin-top: 6px;">
            Secciones en tu menú (Máx: <?= (int)($plan['max_categorias'] ?? 0) ?>)
        </div>
    </div>

    <div class="stat-card-modern">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
            <span style="font-size: 13px; font-weight: 600; color: var(--admin-text-muted);">Platos y Bebidas</span>
            <span style="font-size: 20px;">🍽️</span>
        </div>
        <div style="font-size: 34px; font-weight: 800; color: var(--brand-primary); line-height: 1.1;">
            <?= (int)$stats['productos'] ?>
        </div>
        <div style="font-size: 12.5px; color: var(--admin-text-subtle); margin-top: 6px;">
            Total en la carta (Máx: <?= (int)($plan['max_productos'] ?? 0) ?>)
        </div>
    </div>

    <div class="stat-card-modern">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
            <span style="font-size: 13px; font-weight: 600; color: var(--admin-text-muted);">Modificadores y Extras</span>
            <span style="font-size: 20px;">⚙️</span>
        </div>
        <div style="font-size: 34px; font-weight: 800; color: var(--admin-text-main); line-height: 1.1;">
            <?= (int)$stats['grupos'] ?>
        </div>
        <div style="font-size: 12.5px; color: var(--admin-text-subtle); margin-top: 6px;">
            Grupos de adiciones y términos
        </div>
    </div>

    <div class="stat-card-modern">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
            <span style="font-size: 13px; font-weight: 600; color: var(--admin-text-muted);">Tu Suscripción</span>
            <span style="font-size: 20px;">💎</span>
        </div>
        <div style="font-size: 20px; font-weight: 800; color: var(--admin-text-main); line-height: 1.2; margin: 4px 0;">
            <?= e($plan['nombre'] ?? 'Plan Pro') ?>
        </div>
        <div style="font-size: 12.5px; color: #047857; font-weight: 600;">
            ✓ Al día · Vence: <?= e($tenant['fecha_vencimiento'] ?? 'Activo') ?>
        </div>
    </div>
</div>

<!-- SECCIONES EXPLICADAS CON BOTONES CLAROS -->
<div class="modern-card">
    <h3 style="font-size: 18px; margin-bottom: 6px;">Gestión del Restaurante</h3>
    <p style="color: var(--admin-text-muted); font-size: 13.5px; margin-bottom: 24px;">
        Haz clic en cualquiera de las secciones para administrar tu operación diaria:
    </p>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 16px;">
        <a href="<?= e(app_url('/admin/catalogo')) ?>" style="text-decoration: none; padding: 20px; border-radius: var(--radius-md); border: 1px solid var(--admin-border); background: #FAFBFD; display: flex; flex-direction: column; justify-content: space-between; transition: transform 120ms ease, box-shadow 120ms ease;" class="btn-action-card">
            <div>
                <span style="font-size: 28px; display: block; margin-bottom: 8px;">📋</span>
                <strong style="font-size: 16px; color: var(--admin-text-main); display: block;">Catálogo de Menú</strong>
                <p style="font-size: 13px; color: var(--admin-text-muted); margin-top: 6px; line-height: 1.4;">
                    Crea tus categorías (ej: Entradas, Carnes, Bebidas) y agrega tus platos con fotos, precios y descripción.
                </p>
            </div>
            <span style="color: var(--brand-primary); font-weight: 700; font-size: 13px; margin-top: 14px;">Ir al Catálogo →</span>
        </a>

        <a href="<?= e(app_url('/admin/disponibilidad')) ?>" style="text-decoration: none; padding: 20px; border-radius: var(--radius-md); border: 1px solid rgba(239, 68, 68, 0.2); background: #FFFBFB; display: flex; flex-direction: column; justify-content: space-between; transition: transform 120ms ease, box-shadow 120ms ease;" class="btn-action-card">
            <div>
                <span style="font-size: 28px; display: block; margin-bottom: 8px;">⚡</span>
                <strong style="font-size: 16px; color: #DC2626; display: block;">Control Rápido de Agotados</strong>
                <p style="font-size: 13px; color: var(--admin-text-muted); margin-top: 6px; line-height: 1.4;">
                    ¿Se agotó un ingrediente en la cocina? Apaga el plato en 1 segundo desde tu celular para que nadie lo pida.
                </p>
            </div>
            <span style="color: #DC2626; font-weight: 700; font-size: 13px; margin-top: 14px;">Gestionar Agotados →</span>
        </a>

        <a href="<?= e(app_url('/admin/modificadores')) ?>" style="text-decoration: none; padding: 20px; border-radius: var(--radius-md); border: 1px solid var(--admin-border); background: #FAFBFD; display: flex; flex-direction: column; justify-content: space-between; transition: transform 120ms ease, box-shadow 120ms ease;" class="btn-action-card">
            <div>
                <span style="font-size: 28px; display: block; margin-bottom: 8px;">⚙️</span>
                <strong style="font-size: 16px; color: var(--admin-text-main); display: block;">Modificadores y Adiciones</strong>
                <p style="font-size: 13px; color: var(--admin-text-muted); margin-top: 6px; line-height: 1.4;">
                    Configura opciones para que el cliente personalice (términos de carne, salsas, o adiciones con costo extra).
                </p>
            </div>
            <span style="color: var(--brand-primary); font-weight: 700; font-size: 13px; margin-top: 14px;">Configurar Extras →</span>
        </a>

        <a href="<?= e(app_url('/admin/configuracion')) ?>" style="text-decoration: none; padding: 20px; border-radius: var(--radius-md); border: 1px solid var(--admin-border); background: #FAFBFD; display: flex; flex-direction: column; justify-content: space-between; transition: transform 120ms ease, box-shadow 120ms ease;" class="btn-action-card">
            <div>
                <span style="font-size: 28px; display: block; margin-bottom: 8px;">🎨</span>
                <strong style="font-size: 16px; color: var(--admin-text-main); display: block;">Marca, Colores y WhatsApp</strong>
                <p style="font-size: 13px; color: var(--admin-text-muted); margin-top: 6px; line-height: 1.4;">
                    Sube tu logo, banner, elige los colores de tu negocio, horarios y define el WhatsApp receptor de pedidos.
                </p>
            </div>
            <span style="color: var(--brand-primary); font-weight: 700; font-size: 13px; margin-top: 14px;">Editar Configuración →</span>
        </a>
    </div>
</div>
