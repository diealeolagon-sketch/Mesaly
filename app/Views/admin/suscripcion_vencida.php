<div class="auth-card" style="max-width: 500px; text-align: center;">
    <div style="width: 60px; height: 60px; border-radius: 50%; background: #fee2e2; color: #dc2626; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 20px;">
        <?= icon('alert', 30) ?>
    </div>

    <h1 style="font-size: 22px; font-weight: 700; color: var(--color-text-main); margin: 0 0 8px 0;">
        Suscripción Pausada
    </h1>

    <p style="color: var(--color-text-muted); font-size: 14.5px; line-height: 1.5; margin: 0 0 24px 0;">
        La suscripción de <strong><?= e($tenant['nombre']) ?></strong> se encuentra suspendida temporalmente por vencimiento de pago.
    </p>

    <div style="background: var(--color-bg-subtle); border: 1px solid var(--color-border); border-radius: var(--radius-md); padding: 18px 20px; text-align: left; margin-bottom: 24px; font-size: 13.5px;">
        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
            <span style="color: var(--color-text-muted);">Estado:</span>
            <strong style="color: <?= e($estado['color']) ?>;"><?= e($estado['label']) ?></strong>
        </div>
        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
            <span style="color: var(--color-text-muted);">Fecha de corte:</span>
            <strong><?= !empty($tenant['fecha_vencimiento']) ? date('d/m/Y', strtotime($tenant['fecha_vencimiento'])) : 'Sin registro' ?></strong>
        </div>
        <div style="display: flex; justify-content: space-between;">
            <span style="color: var(--color-text-muted);">Valor acordado:</span>
            <strong style="color: var(--color-brand);"><?= format_cop((int)($tenant['precio'] ?? 49000)) ?> COP</strong>
        </div>
    </div>

    <p style="font-size: 13.5px; color: var(--color-text-muted); margin-bottom: 24px;">
        Para reactivar tu carta digital de inmediato, envía tu comprobante de pago por WhatsApp al equipo de soporte de Mesaly:
    </p>

    <a href="https://wa.me/573001234567?text=<?= rawurlencode('Hola Mesaly, quiero renovar la suscripción de mi restaurante: ' . $tenant['nombre'] . ' (slug: ' . $tenant['slug'] . ')') ?>" 
       target="_blank" 
       rel="noopener" 
       class="btn btn-primary" 
       style="width: 100%; justify-content: center; padding: 12px 20px; font-size: 15px; margin-bottom: 16px;">
        <?= icon('whatsapp', 18) ?>
        <span>Contactar a Soporte por WhatsApp</span>
    </a>

    <form method="POST" action="<?= e(app_url('/logout')) ?>">
        <?= csrf_field() ?>
        <button type="submit" class="btn btn-secondary" style="width: 100%; justify-content: center;">
            Cerrar Sesión
        </button>
    </form>
</div>
