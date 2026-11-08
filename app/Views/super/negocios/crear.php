<div style="max-width: 760px; margin: 0 auto;">
    <div style="margin-bottom: 32px;">
        <a href="<?= e(app_url('/superadmin/negocios')) ?>" style="display: inline-flex; align-items: center; gap: 8px; font-size: 13.5px; font-weight: 600; color: var(--color-text-muted); text-decoration: none; margin-bottom: 14px; transition: color 150ms var(--ease-fluid);">
            <?= icon('arrow-left', 16) ?>
            <span>Volver a la lista de negocios</span>
        </a>
        <h1 class="page-title" style="font-size: 24px; margin-bottom: 6px;">
            Registrar Nuevo Restaurante
        </h1>
        <p class="page-subtitle" style="font-size: 14px; color: var(--color-text-muted); margin: 0;">
            Al dar de alta el negocio se creará su base de datos aislada y se generará una contraseña temporal de un solo uso para su administrador.
        </p>
    </div>

    <div class="card" style="padding: 32px;">
        <form method="POST" action="<?= e(app_url('/superadmin/negocios/crear')) ?>">
            <?= csrf_field() ?>

            <!-- SECCIÓN 1 -->
            <div style="margin-bottom: 32px;">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px; padding-bottom: 14px; border-bottom: 1px solid var(--color-border-subtle);">
                    <div style="width: 32px; height: 32px; border-radius: var(--radius-sm); background: var(--color-brand-subtle); color: var(--color-brand); display: flex; align-items: center; justify-content: center;">
                        <?= icon('store', 16) ?>
                    </div>
                    <div>
                        <h2 style="font-size: 16px; font-weight: 700; color: var(--color-text-main); margin: 0;">
                            1. Datos del Restaurante o Comercio
                        </h2>
                        <span style="font-size: 12.5px; color: var(--color-text-muted);">Información visible para los clientes y URL pública</span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="nombre" class="form-label">Nombre Comercial del Negocio *</label>
                    <input type="text" id="nombre" name="nombre" class="form-control" placeholder="Ej: Hamburguesas La 80" required autofocus>
                    <span class="form-hint">Nombre que encabezará la carta digital y los mensajes de WhatsApp.</span>
                </div>

                <div class="form-group">
                    <label for="slug" class="form-label">Slug de la URL * <span style="font-weight: 400; color: var(--color-text-muted);">(identificador único para códigos QR)</span></label>
                    <div class="input-prefix-wrap">
                        <span class="input-prefix">mesaly.com/</span>
                        <input type="text" id="slug" name="slug" class="form-control" placeholder="hamburguesas-la-80" required pattern="^[a-z0-9]+(-[a-z0-9]+)*$" style="border-radius: 0 var(--radius-md) var(--radius-md) 0; font-weight: 600;">
                    </div>
                    <span class="form-hint">Solo letras minúsculas, números y guiones. Los códigos QR apuntarán a esta dirección.</span>
                </div>

                <div class="form-group">
                    <label for="whatsapp" class="form-label">WhatsApp Oficial Receptor de Pedidos *</label>
                    <div class="input-prefix-wrap">
                        <span class="input-prefix">+</span>
                        <input type="tel" id="whatsapp" name="whatsapp" class="form-control" placeholder="573001234567" required style="border-radius: 0 var(--radius-md) var(--radius-md) 0; font-weight: 600; font-variant-numeric: tabular-nums;">
                    </div>
                    <span class="form-hint">Incluye el código de país sin espacios ni símbolos (ej: 57 para Colombia).</span>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;" class="config-grid-2">
                    <div class="form-group">
                        <label for="plan_id" class="form-label">Plan de Suscripción *</label>
                        <select id="plan_id" name="plan_id" class="form-control" required>
                            <?php foreach ($planes as $plan): ?>
                                <option value="<?= e($plan['id']) ?>">
                                    <?= e($plan['nombre']) ?> (<?= format_cop($plan['precio_mensual']) ?>/mes)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <span class="form-hint">Define límites de platos, categorías y funcionalidades.</span>
                    </div>

                    <div class="form-group">
                        <label for="ciclo" class="form-label">Ciclo de Facturación *</label>
                        <select id="ciclo" name="ciclo" class="form-control" required>
                            <option value="mensual">Mensual (Facturación recurrente cada 30 días)</option>
                            <option value="anual">Anual (Facturación cada 365 días)</option>
                        </select>
                        <span class="form-hint">Frecuencia de renovación pactada con el cliente.</span>
                    </div>
                </div>

                <div class="form-group" style="margin-top: 8px;">
                    <label class="toggle-option-card">
                        <input type="checkbox" name="en_prueba" value="1" checked style="width: 18px; height: 18px; margin-top: 2px; accent-color: var(--color-brand); flex-shrink: 0;">
                        <div>
                            <span class="toggle-option-title">Activar periodo de prueba inicial (15 días de cortesía)</span>
                            <span class="toggle-option-desc">Permite al restaurante usar la plataforma de forma completa antes del primer cobro.</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- SECCIÓN 2 -->
            <div style="margin-top: 36px; padding-top: 28px; border-top: 1px solid var(--color-border-subtle);">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px; padding-bottom: 14px; border-bottom: 1px solid var(--color-border-subtle);">
                    <div style="width: 32px; height: 32px; border-radius: var(--radius-sm); background: #E6F9EF; color: #059669; display: flex; align-items: center; justify-content: center;">
                        <?= icon('check', 16) ?>
                    </div>
                    <div>
                        <h2 style="font-size: 16px; font-weight: 700; color: var(--color-text-main); margin: 0;">
                            2. Cuenta del Administrador del Restaurante
                        </h2>
                        <span style="font-size: 12.5px; color: var(--color-text-muted);">Credenciales para el dueño o gerente del local</span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="admin_nombre" class="form-label">Nombre del Contacto / Dueño *</label>
                    <input type="text" id="admin_nombre" name="admin_nombre" class="form-control" placeholder="Ej: Carlos Restrepo" required>
                    <span class="form-hint">Persona responsable del establecimiento.</span>
                </div>

                <div class="form-group">
                    <label for="admin_email" class="form-label">Correo Electrónico de Acceso *</label>
                    <input type="email" id="admin_email" name="admin_email" class="form-control" placeholder="admin@restaurante.com" required>
                    <span class="form-hint">Se usará como usuario para iniciar sesión en el panel del restaurante.</span>
                </div>
            </div>

            <!-- BOTONES -->
            <div style="display: flex; justify-content: flex-end; gap: 14px; margin-top: 36px; padding-top: 24px; border-top: 1px solid var(--color-border-subtle);">
                <a href="<?= e(app_url('/superadmin/negocios')) ?>" class="btn btn-secondary" style="padding: 11px 24px;">
                    Cancelar
                </a>
                <button type="submit" class="btn btn-primary" style="padding: 11px 32px; font-size: 14px; font-weight: 700;">
                    <?= icon('check', 16) ?>
                    <span>Registrar y Generar Clave</span>
                </button>
            </div>
        </form>
    </div>
</div>
