<div class="vencida-container">
    <div class="vencida-icon-box">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
        </svg>
    </div>

    <h1 class="vencida-title">
        Suscripción Pausada
    </h1>

    <p class="vencida-subtitle">
        El servicio para <strong><?= e($tenant['nombre'] ?? 'tu restaurante') ?></strong> se encuentra temporalmente suspendido por vencimiento del período activo.
    </p>

    <!-- Caja de Resumen de la Suscripción -->
    <div class="vencida-card-details">
        <div class="vencida-row">
            <span class="vencida-row-label">Estado de Cuenta</span>
            <span class="vencida-badge-danger">
                <?= e($estado['label'] ?? 'Bloqueado por falta de pago') ?>
            </span>
        </div>
        <div class="vencida-row">
            <span class="vencida-row-label">Fecha de Vencimiento</span>
            <strong class="vencida-row-val text-danger">
                <?= !empty($tenant['fecha_vencimiento']) ? date('d/m/Y', strtotime($tenant['fecha_vencimiento'])) : 'Sin registro' ?>
            </strong>
        </div>
        <div class="vencida-row">
            <span class="vencida-row-label">Tarifa de Renovación</span>
            <div class="text-right">
                <span class="vencida-price"><?= format_cop((int)($tenant['precio'] ?? 49000)) ?></span>
                <span class="vencida-cycle">/ 30 Días</span>
            </div>
        </div>
    </div>

    <!-- Tira de Logotipos de Métodos de Pago Oficiales -->
    <div class="vencida-logos-section">
        <span class="vencida-logos-title">Medios de Pago Habilitados en Wompi</span>
        <div class="vencida-logos-grid">
            <div class="vencida-logo-box" title="Visa">
                <img src="<?= e(asset_url('/assets/img/payments/visa.svg')) ?>" alt="Visa" class="vencida-logo-img">
            </div>
            <div class="vencida-logo-box" title="Mastercard">
                <img src="<?= e(asset_url('/assets/img/payments/mastercard.svg')) ?>" alt="Mastercard" class="vencida-logo-img">
            </div>
            <div class="vencida-logo-box" title="PSE">
                <img src="<?= e(asset_url('/assets/img/payments/pse.svg')) ?>" alt="PSE" class="vencida-logo-img">
            </div>
            <div class="vencida-logo-box" title="Nequi">
                <img src="<?= e(asset_url('/assets/img/payments/nequi.svg')) ?>" alt="Nequi" class="vencida-logo-img">
            </div>
            <div class="vencida-logo-box" title="Bancolombia">
                <img src="<?= e(asset_url('/assets/img/payments/bancolombia.svg')) ?>" alt="Bancolombia" class="vencida-logo-img">
            </div>
            <div class="vencida-logo-box" title="Daviplata">
                <img src="<?= e(asset_url('/assets/img/payments/daviplata.svg')) ?>" alt="Daviplata" class="vencida-logo-img">
            </div>
        </div>
    </div>

    <!-- Botón Principal: Pagar con Wompi -->
    <a href="https://checkout.wompi.co/l/VPOS_8rydsK" 
       target="_blank" 
       rel="noopener noreferrer" 
       class="btn-vencida-wompi">
        <span>Pagar con Wompi (Link Oficial)</span>
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="7" y1="17" x2="17" y2="7"></line>
            <polyline points="7 7 17 7 17 17"></polyline>
        </svg>
    </a>

    <!-- Tarjeta Secundaria: Enviar Comprobante por WhatsApp -->
    <div class="vencida-wa-card">
        <div class="vencida-wa-text">
            <strong>¿Ya realizaste tu transferencia o pago?</strong>
            <p>Envíanos el comprobante por WhatsApp para reactivar tu menú y mesas en minutos.</p>
        </div>
        <?php 
            $msgWa = "Hola equipo Mesaly, acabo de pagar la suscripción de 30 días para " . ($tenant['nombre'] ?? 'Mi Restaurante') . " (" . format_cop((int)($tenant['precio'] ?? 49000)) . "). Adjunto mi soporte para reactivar el servicio.";
        ?>
        <a href="<?= e(support_whatsapp_url($msgWa)) ?>" 
           target="_blank" 
           rel="noopener noreferrer" 
           class="btn-vencida-wa">
            <?= icon('whatsapp', 16) ?>
            <span>Enviar Soporte</span>
        </a>
    </div>

    <!-- Botón de Salir / Cerrar Sesión -->
    <form method="POST" action="<?= e(app_url('/logout')) ?>" class="mt-4">
        <?= csrf_field() ?>
        <button type="submit" class="btn-vencida-logout">
            <?= icon('logout', 14) ?>
            <span>Cerrar Sesión</span>
        </button>
    </form>
</div>

<style nonce="<?= e($cspNonce) ?>">
.vencida-container {
    width: 100%;
    max-width: 440px;
    margin: 0 auto;
    padding: 8px 0;
}
.vencida-icon-box {
    width: 64px;
    height: 64px;
    background: #FEF2F2;
    border: 1px solid #FECACA;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 16px auto;
    box-shadow: 0 4px 12px rgba(220, 38, 38, 0.12);
}
.vencida-title {
    font-size: 24px;
    font-weight: 800;
    color: #0F172A;
    text-align: center;
    margin: 0 0 8px 0;
    letter-spacing: -0.5px;
}
.vencida-subtitle {
    font-size: 13.5px;
    color: #64748B;
    text-align: center;
    line-height: 1.5;
    margin: 0 0 20px 0;
}
.vencida-card-details {
    background: #F8FAFC;
    border: 1px solid #E2E8F0;
    border-radius: 16px;
    padding: 16px;
    margin-bottom: 20px;
}
.vencida-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px dashed #E2E8F0;
}
.vencida-row:last-child {
    border-bottom: none;
    padding-bottom: 0;
}
.vencida-row:first-child {
    padding-top: 0;
}
.vencida-row-label {
    font-size: 13px;
    color: #64748B;
    font-weight: 500;
}
.vencida-row-val {
    font-size: 13.5px;
    font-weight: 700;
}
.vencida-badge-danger {
    background: #FEE2E2;
    color: #991B1B;
    font-size: 11.5px;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 20px;
    border: 1px solid #FCA5A5;
}
.vencida-price {
    font-size: 17px;
    font-weight: 800;
    color: #EA580C;
    display: block;
    line-height: 1.1;
}
.vencida-cycle {
    font-size: 11px;
    font-weight: 600;
    color: #9A3412;
    display: block;
}
.vencida-logos-section {
    margin-bottom: 18px;
}
.vencida-logos-title {
    display: block;
    font-size: 11.5px;
    font-weight: 700;
    color: #475569;
    margin-bottom: 8px;
    text-align: center;
}
.vencida-logos-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 6px;
}
.vencida-logo-box {
    background: #FFFFFF;
    border: 1px solid #E2E8F0;
    border-radius: 8px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 4px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
}
.vencida-logo-img {
    max-height: 20px;
    max-width: 88%;
    object-fit: contain;
}
.btn-vencida-wompi {
    background: linear-gradient(135deg, #EA580C 0%, #C2410C 100%);
    color: #FFFFFF !important;
    border-radius: 12px;
    padding: 13px 18px;
    font-size: 14.5px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    text-decoration: none !important;
    box-shadow: 0 4px 16px rgba(234, 88, 12, 0.3);
    transition: transform 0.15s ease, box-shadow 0.15s ease;
    width: 100%;
    margin-bottom: 14px;
}
.btn-vencida-wompi:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(234, 88, 12, 0.4);
}
.vencida-wa-card {
    background: #F0FDF4;
    border: 1px solid #BBF7D0;
    border-radius: 12px;
    padding: 12px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    margin-bottom: 12px;
}
.vencida-wa-text strong {
    font-size: 12px;
    color: #166534;
    display: block;
    margin-bottom: 2px;
}
.vencida-wa-text p {
    font-size: 11px;
    color: #15803D;
    margin: 0;
    line-height: 1.3;
}
.btn-vencida-wa {
    background: #25D366;
    color: #FFFFFF !important;
    border-radius: 8px;
    padding: 7px 11px;
    font-size: 11.5px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    text-decoration: none !important;
    flex-shrink: 0;
    box-shadow: 0 2px 6px rgba(37, 211, 102, 0.25);
    transition: background 0.15s ease;
}
.btn-vencida-wa:hover {
    background: #1EBE5D;
}
.btn-vencida-logout {
    width: 100%;
    background: #F8FAFC;
    color: #64748B;
    border: 1px solid #E2E8F0;
    border-radius: 10px;
    padding: 9px;
    font-size: 12.5px;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    cursor: pointer;
    transition: all 0.15s ease;
}
.btn-vencida-logout:hover {
    background: #F1F5F9;
    color: #0F172A;
    border-color: #CBD5E1;
}
</style>
