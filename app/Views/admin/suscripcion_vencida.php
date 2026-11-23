<div class="auth-card card-suscripcion-vencida">
    <div class="suscripcion-icon-bubble">
        <?= icon('alert', 30) ?>
    </div>

    <h1 class="text-xl fw-700 text-main mb-2">
        Suscripción Pausada
    </h1>

    <p class="text-muted text-base leading-normal mb-5">
        La suscripción de <strong><?= e($tenant['nombre']) ?></strong> se encuentra suspendida temporalmente por vencimiento de pago.
    </p>

    <div class="suscripcion-details-box">
        <div class="suscripcion-detail-row">
            <span class="text-muted">Estado:</span>
            <strong class="text-danger"><?= e($estado['label']) ?></strong>
        </div>
        <div class="suscripcion-detail-row">
            <span class="text-muted">Fecha de corte:</span>
            <strong><?= !empty($tenant['fecha_vencimiento']) ? date('d/m/Y', strtotime($tenant['fecha_vencimiento'])) : 'Sin registro' ?></strong>
        </div>
        <div class="suscripcion-detail-row">
            <span class="text-muted">Valor acordado:</span>
            <strong class="text-brand"><?= format_cop((int)($tenant['precio'] ?? 49000)) ?> COP</strong>
        </div>
    </div>

    <p class="text-sm text-muted mb-5">
        Para reactivar tu carta digital de inmediato, envía tu comprobante de pago por WhatsApp al equipo de soporte de Mesaly:
    </p>

    <a href="<?= e(support_whatsapp_url('Hola Mesaly, quiero renovar la suscripción de mi restaurante: ' . ($tenant['nombre'] ?? '') . ' (slug: ' . ($tenant['slug'] ?? '') . ')')) ?>" 
       target="_blank" 
       rel="noopener" 
       class="btn btn-primary w-full justify-center py-3 text-base mb-4">
        <?= icon('whatsapp', 18) ?>
        <span>Contactar a Soporte por WhatsApp (+57 322 899 1339)</span>
    </a>

    <form method="POST" action="<?= e(app_url('/logout')) ?>">
        <?= csrf_field() ?>
        <button type="submit" class="btn btn-secondary w-full justify-center">
            Cerrar Sesión
        </button>
    </form>
</div>
