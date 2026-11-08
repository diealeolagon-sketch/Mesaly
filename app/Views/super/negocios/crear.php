<div style="max-width: 820px; margin: 0 auto;">
    <div style="margin-bottom: 28px;">
        <a href="<?= e(app_url('/superadmin')) ?>" style="display: inline-flex; align-items: center; gap: 8px; font-size: 13.5px; font-weight: 600; color: var(--color-text-muted); text-decoration: none; margin-bottom: 12px; transition: color 150ms var(--ease-fluid);">
            <?= icon('arrow-left', 16) ?>
            <span>Volver a Vencimientos</span>
        </a>
        <h1 class="page-title" style="font-size: 26px; margin-bottom: 6px;">
            Registrar Nuevo Restaurante
        </h1>
        <p class="page-subtitle" style="font-size: 14px; color: var(--color-text-muted); margin: 0;">
            Habilita un catálogo interactivo con uso ilimitado (productos, categorías y mesas sin tope) y pacta el precio acordado.
        </p>
    </div>

    <div class="card" style="padding: 32px;">
        <form method="POST" action="<?= e(app_url('/superadmin/negocios/crear')) ?>">
            <?= csrf_field() ?>

            <!-- SECCIÓN 1: DATOS DEL COMERCIO -->
            <div style="margin-bottom: 32px;">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px; padding-bottom: 14px; border-bottom: 1px solid var(--color-border-subtle);">
                    <div style="width: 34px; height: 34px; border-radius: var(--radius-sm); background: var(--color-brand-subtle); color: var(--color-brand); display: flex; align-items: center; justify-content: center;">
                        <?= icon('store', 18) ?>
                    </div>
                    <div>
                        <h2 style="font-size: 16px; font-weight: 700; color: var(--color-text-main); margin: 0;">
                            1. Datos Comerciales del Negocio
                        </h2>
                        <span style="font-size: 12.5px; color: var(--color-text-muted);">Información visible para los clientes y URL de acceso</span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="nombre" class="form-label">Nombre Comercial del Negocio *</label>
                    <input type="text" id="nombre" name="nombre" class="form-control" placeholder="Ej: Hamburguesas La 80" required autofocus style="font-size: 15px; font-weight: 600;">
                    <span class="form-hint">Nombre que encabezará la carta digital y los mensajes de WhatsApp.</span>
                </div>

                <div class="form-group">
                    <label for="slug" class="form-label">
                        Slug de la URL * 
                        <span style="font-weight: 400; color: var(--color-text-muted);">(se genera automáticamente o puedes editarlo)</span>
                    </label>
                    <div class="input-prefix-wrap">
                        <span class="input-prefix">mesaly.com/</span>
                        <input type="text" id="slug" name="slug" data-slug-from="nombre" class="form-control" placeholder="hamburguesas-la-80" required pattern="^[a-z0-9]+(-[a-z0-9]+)*$" style="font-weight: 600; color: var(--color-brand);">
                    </div>
                    <span class="form-hint">Solo letras minúsculas, números y guiones. Los códigos QR apuntarán a esta dirección.</span>
                </div>

                <div class="form-group">
                    <label for="whatsapp" class="form-label">WhatsApp Oficial Receptor de Pedidos *</label>
                    <div class="input-prefix-wrap">
                        <span class="input-prefix">+</span>
                        <input type="tel" id="whatsapp" name="whatsapp" class="form-control" placeholder="573001234567" required style="font-weight: 600; font-variant-numeric: tabular-nums;">
                    </div>
                    <span class="form-hint">Incluye el código de país sin espacios ni símbolos (ej: 57 para Colombia).</span>
                </div>
            </div>

            <!-- SECCIÓN 2: ACUERDO COMERCIAL Y TARIFA -->
            <div style="margin-bottom: 32px; padding-top: 28px; border-top: 1px solid var(--color-border-subtle);">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px; padding-bottom: 14px; border-bottom: 1px solid var(--color-border-subtle);">
                    <div style="width: 34px; height: 34px; border-radius: var(--radius-sm); background: #ECFDF5; color: #059669; display: flex; align-items: center; justify-content: center;">
                        <?= icon('cash', 18) ?>
                    </div>
                    <div>
                        <h2 style="font-size: 16px; font-weight: 700; color: var(--color-text-main); margin: 0;">
                            2. Acuerdo Comercial y Suscripción
                        </h2>
                        <span style="font-size: 12.5px; color: var(--color-text-muted);">Sin planes rígidos: tarifa pactada a la medida del cliente</span>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;" class="config-grid-2">
                    <div class="form-group">
                        <label for="precio" class="form-label">Precio Acordado (COP) *</label>
                        <input type="number" id="precio" name="precio" class="form-control" value="49000" min="10000" step="1000" required style="font-size: 16px; font-weight: 700; color: #059669;">
                        <span class="form-hint">Monto que cobrarás manualmente por período.</span>
                    </div>

                    <div class="form-group">
                        <label for="ciclo" class="form-label">Ciclo de Cobro *</label>
                        <select id="ciclo" name="ciclo" class="form-control" required style="font-weight: 600;">
                            <option value="mensual" selected>Mensual (Cada 30 días)</option>
                            <option value="anual">Anual (Cada 365 días)</option>
                        </select>
                        <span class="form-hint">Frecuencia acordada para la renovación.</span>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;" class="config-grid-2">
                    <div class="form-group">
                        <label for="dias_gracia" class="form-label">Días de Gracia tras Vencimiento</label>
                        <input type="number" id="dias_gracia" name="dias_gracia" class="form-control" value="3" min="1" max="30" required style="font-weight: 600;">
                        <span class="form-hint">Margen de cortesía antes del bloqueo de la carta.</span>
                    </div>

                    <div class="form-group">
                        <label for="notas_internas" class="form-label">Notas Internas (Solo Super Admin)</label>
                        <input type="text" id="notas_internas" name="notas_internas" class="form-control" placeholder="Ej: Paga por Nequi, promo primer mes">
                        <span class="form-hint">Apuntes privados no visibles para el cliente.</span>
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; gap: 12px; margin-top: 10px;">
                    <label class="toggle-option-card">
                        <input type="checkbox" name="en_prueba" value="1" checked style="width: 18px; height: 18px; margin-top: 2px; accent-color: var(--color-brand); flex-shrink: 0;">
                        <div>
                            <span class="toggle-option-title">Activar periodo de prueba inicial (15 días de cortesía)</span>
                            <span class="toggle-option-desc">Permite al restaurante usar la plataforma de forma completa antes del primer cobro.</span>
                        </div>
                    </label>

                    <label class="toggle-option-card">
                        <input type="checkbox" name="mostrar_marca" value="1" checked style="width: 18px; height: 18px; margin-top: 2px; accent-color: var(--color-brand); flex-shrink: 0;">
                        <div>
                            <span class="toggle-option-title">Mostrar pie de marca "Menú digital por Mesaly"</span>
                            <span class="toggle-option-desc">Si lo desmarcas, la carta pública se mostrará en modo marca blanca (white-label).</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- SECCIÓN 3: CUENTA DEL ADMINISTRADOR -->
            <div style="margin-top: 36px; padding-top: 28px; border-top: 1px solid var(--color-border-subtle);">
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px; padding-bottom: 14px; border-bottom: 1px solid var(--color-border-subtle);">
                    <div style="width: 34px; height: 34px; border-radius: var(--radius-sm); background: #EFF6FF; color: #2563EB; display: flex; align-items: center; justify-content: center;">
                        <?= icon('settings', 18) ?>
                    </div>
                    <div>
                        <h2 style="font-size: 16px; font-weight: 700; color: var(--color-text-main); margin: 0;">
                            3. Cuenta del Administrador del Restaurante
                        </h2>
                        <span style="font-size: 12.5px; color: var(--color-text-muted);">Credenciales con las que el dueño del restaurante ingresará a su panel</span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="admin_nombre" class="form-label">Nombre del Contacto o Encargado *</label>
                    <input type="text" id="admin_nombre" name="admin_nombre" class="form-control" placeholder="Ej: Carlos Gómez" required>
                </div>

                <div class="form-group">
                    <label for="admin_email" class="form-label">Correo Electrónico de Acceso *</label>
                    <input type="email" id="admin_email" name="admin_email" class="form-control" placeholder="gerencia@hamburguesasla80.com" required style="font-family: monospace;">
                    <span class="form-hint">Se generará una contraseña temporal automática que se mostrará en pantalla al guardar.</span>
                </div>
            </div>

            <div style="margin-top: 36px; padding-top: 24px; border-top: 1px solid var(--color-border-subtle); display: flex; justify-content: flex-end; gap: 14px;">
                <a href="<?= e(app_url('/superadmin')) ?>" class="btn btn-secondary" style="padding: 11px 22px;">
                    Cancelar
                </a>
                <button type="submit" class="btn btn-primary" style="padding: 11px 32px; font-size: 14.5px; font-weight: 700;">
                    <?= icon('check', 16) ?>
                    <span>Registrar Negocio y Generar Acceso</span>
                </button>
            </div>
        </form>
    </div>
</div>
