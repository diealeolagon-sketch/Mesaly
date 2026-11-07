<div style="margin-bottom: 24px;">
    <span style="font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; color: var(--brand-primary);">
        Personalización y Operación
    </span>
    <h1 style="font-size: 26px; margin: 4px 0 6px 0; color: var(--admin-text-main);">
        Configuración de tu Restaurante
    </h1>
    <p style="color: var(--admin-text-muted); font-size: 14px; margin: 0;">
        Personaliza los colores, logo, métodos de entrega y horarios comerciales de tu negocio.
    </p>
</div>

<form method="POST" action="<?= e(app_url('/admin/configuracion')) ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <!-- 1. IDENTIDAD DE MARCA Y COLORES -->
    <div class="modern-card">
        <h3 style="font-size: 18px; margin-bottom: 6px; color: var(--admin-text-main);">
            1. Marca y Apariencia Visual
        </h3>
        <p style="font-size: 13.5px; color: var(--admin-text-muted); margin-bottom: 20px;">
            Estos colores y logos se inyectan automáticamente en el menú que verán tus comensales.
        </p>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;" class="config-grid-2">
            <div class="form-group">
                <label for="nombre" class="form-label">Nombre Comercial del Restaurante *</label>
                <input type="text" id="nombre" name="nombre" class="form-control" value="<?= e($tenant['nombre']) ?>" required>
            </div>

            <div class="form-group">
                <label for="fuente" class="form-label">Tipografía del Menú</label>
                <select id="fuente" name="fuente" class="form-control">
                    <option value="Inter" <?= ($tenant['fuente'] ?? '') === 'Inter' ? 'selected' : '' ?>>Inter (Limpia y moderna)</option>
                    <option value="Poppins" <?= ($tenant['fuente'] ?? '') === 'Poppins' ? 'selected' : '' ?>>Poppins (Geométrica y amigable)</option>
                    <option value="Outfit" <?= ($tenant['fuente'] ?? '') === 'Outfit' ? 'selected' : '' ?>>Outfit (Contemporánea)</option>
                    <option value="Playfair" <?= ($tenant['fuente'] ?? '') === 'Playfair' ? 'selected' : '' ?>>Playfair (Elegante / Gourmet)</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="descripcion" class="form-label">Descripción Corta / Eslogan</label>
            <textarea id="descripcion" name="descripcion" class="form-control" rows="2" placeholder="Ej: Carnes asadas al carbón, hamburguesas artesanales y picadas típicas."><?= e($tenant['descripcion'] ?? '') ?></textarea>
        </div>

        <!-- SUBIDA DE LOGO Y BANNER CON DROPZONE Y PREVIEW -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px;" class="config-grid-2">
            <div>
                <label class="form-label">Logo del Restaurante</label>
                <div class="image-dropzone" onclick="document.getElementById('logo_input').click()">
                    <input type="file" id="logo_input" name="logo" accept="image/jpeg,image/png,image/webp" onchange="previewLogo(this)">
                    <?php if (!empty($tenant['logo_path'])): ?>
                        <img src="<?= e(app_url($tenant['logo_path'])) ?>" id="preview_logo" class="dropzone-preview" alt="Logo actual" style="max-height: 80px; object-fit: contain;">
                        <span style="font-size: 12px; font-weight: 600; color: var(--brand-primary); display: block;">🔄 Haz clic para cambiar logo</span>
                    <?php else: ?>
                        <img src="" id="preview_logo" class="dropzone-preview" alt="Logo" style="display: none; max-height: 80px; object-fit: contain;">
                        <span style="font-size: 32px; display: block; margin-bottom: 6px;">🏷️</span>
                        <strong style="font-size: 13.5px; display: block; color: var(--admin-text-main);">Subir Logo</strong>
                        <span style="font-size: 11.5px; color: var(--admin-text-subtle);">JPG, PNG o WebP</span>
                    <?php endif; ?>
                </div>
            </div>

            <div>
                <label class="form-label">Banner de Portada</label>
                <div class="image-dropzone" onclick="document.getElementById('banner_input').click()">
                    <input type="file" id="banner_input" name="banner" accept="image/jpeg,image/png,image/webp" onchange="previewBanner(this)">
                    <?php if (!empty($tenant['banner_path'])): ?>
                        <img src="<?= e(app_url($tenant['banner_path'])) ?>" id="preview_banner" class="dropzone-preview" alt="Banner actual" style="max-height: 80px; object-fit: cover;">
                        <span style="font-size: 12px; font-weight: 600; color: var(--brand-primary); display: block;">🔄 Haz clic para cambiar banner</span>
                    <?php else: ?>
                        <img src="" id="preview_banner" class="dropzone-preview" alt="Banner" style="display: none; max-height: 80px; object-fit: cover;">
                        <span style="font-size: 32px; display: block; margin-bottom: 6px;">🖼️</span>
                        <strong style="font-size: 13.5px; display: block; color: var(--admin-text-main);">Subir Portada / Banner</strong>
                        <span style="font-size: 11.5px; color: var(--admin-text-subtle);">JPG, PNG o WebP</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- PALETAS PREDEFINIDAS -->
        <div style="margin-top: 24px; padding: 18px; background: #F8FAFC; border-radius: var(--radius-md); border: 1px solid var(--admin-border);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                <label class="form-label" style="margin: 0;">🎨 Paletas Predefinidas Recomendadas:</label>
                <span style="font-size: 12px; color: var(--admin-text-muted);">Toca una para probar</span>
            </div>
            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                <button type="button" class="btn-modern btn-modern-secondary btn-modern-sm" onclick="aplicarPreset('#E85D04', '#FFB703', '#FFF7EA', '#1E1B18')">🟠 Sazón (Naranja)</button>
                <button type="button" class="btn-modern btn-modern-secondary btn-modern-sm" onclick="aplicarPreset('#2A9D5C', '#FFB703', '#F6FBF4', '#14281D')">🟢 Fresca (Verde)</button>
                <button type="button" class="btn-modern btn-modern-secondary btn-modern-sm" onclick="aplicarPreset('#C1121F', '#1E1B18', '#FFFFFF', '#1E1B18')">🔴 Parrilla (Rojo)</button>
                <button type="button" class="btn-modern btn-modern-secondary btn-modern-sm" onclick="aplicarPreset('#6F4E37', '#D4A373', '#FAF3E8', '#2B1B12')">🟤 Café (Marrón)</button>
                <button type="button" class="btn-modern btn-modern-secondary btn-modern-sm" onclick="aplicarPreset('#E5527A', '#FFC8DD', '#FFF5F8', '#3A1F2B')">🌸 Dulce (Rosa)</button>
                <button type="button" class="btn-modern btn-modern-secondary btn-modern-sm" onclick="aplicarPreset('#0077B6', '#90E0EF', '#F4FBFE', '#0B2A3A')">🔵 Mar (Azul)</button>
                <button type="button" class="btn-modern btn-modern-secondary btn-modern-sm" onclick="aplicarPreset('#FFB703', '#E85D04', '#16130F', '#FFF7EA')">⚫ Noche (Oscuro)</button>
            </div>
        </div>

        <!-- SELECTORES DE COLOR CON VISTA PREVIA DINÁMICA -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; margin-top: 20px;">
            <div class="form-group">
                <label for="color_primario" class="form-label">Color Primario (Botones) *</label>
                <div style="display: flex; gap: 8px; align-items: center;">
                    <input type="color" id="picker_primario" value="<?= e($tenant['color_primario'] ?? '#E85D04') ?>" style="width: 44px; height: 44px; border: none; cursor: pointer; border-radius: var(--radius-sm);" oninput="syncColor('color_primario', this.value)">
                    <input type="text" id="color_primario" name="color_primario" class="form-control" value="<?= e($tenant['color_primario'] ?? '#E85D04') ?>" pattern="^#[0-9a-fA-F]{6}$" required oninput="actualizarPreview()">
                </div>
            </div>

            <div class="form-group">
                <label for="color_secundario" class="form-label">Color Secundario (Acentos) *</label>
                <div style="display: flex; gap: 8px; align-items: center;">
                    <input type="color" id="picker_secundario" value="<?= e($tenant['color_secundario'] ?? '#FFB703') ?>" style="width: 44px; height: 44px; border: none; cursor: pointer; border-radius: var(--radius-sm);" oninput="syncColor('color_secundario', this.value)">
                    <input type="text" id="color_secundario" name="color_secundario" class="form-control" value="<?= e($tenant['color_secundario'] ?? '#FFB703') ?>" pattern="^#[0-9a-fA-F]{6}$" required oninput="actualizarPreview()">
                </div>
            </div>

            <div class="form-group">
                <label for="color_fondo" class="form-label">Fondo de Menú *</label>
                <div style="display: flex; gap: 8px; align-items: center;">
                    <input type="color" id="picker_fondo" value="<?= e($tenant['color_fondo'] ?? '#FFF7EA') ?>" style="width: 44px; height: 44px; border: none; cursor: pointer; border-radius: var(--radius-sm);" oninput="syncColor('color_fondo', this.value)">
                    <input type="text" id="color_fondo" name="color_fondo" class="form-control" value="<?= e($tenant['color_fondo'] ?? '#FFF7EA') ?>" pattern="^#[0-9a-fA-F]{6}$" required oninput="actualizarPreview()">
                </div>
            </div>

            <div class="form-group">
                <label for="color_texto" class="form-label">Color de Texto *</label>
                <div style="display: flex; gap: 8px; align-items: center;">
                    <input type="color" id="picker_texto" value="<?= e($tenant['color_texto'] ?? '#1E1B18') ?>" style="width: 44px; height: 44px; border: none; cursor: pointer; border-radius: var(--radius-sm);" oninput="syncColor('color_texto', this.value)">
                    <input type="text" id="color_texto" name="color_texto" class="form-control" value="<?= e($tenant['color_texto'] ?? '#1E1B18') ?>" pattern="^#[0-9a-fA-F]{6}$" required oninput="actualizarPreview()">
                </div>
            </div>
        </div>

        <!-- CAJA DE VISTA PREVIA EN VIVO CON CÁLCULO DE CONTRASTE -->
        <div style="margin-top: 16px; padding: 20px; border-radius: var(--radius-md); border: 1px solid var(--admin-border); background: <?= e($tenant['color_fondo'] ?? '#FFF7EA') ?>;" id="preview_box">
            <span style="font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.08em; color: var(--admin-text-subtle); display: block; margin-bottom: 8px;">
                Vista previa del botón de tu menú con contraste inteligente:
            </span>
            <div style="display: flex; gap: 16px; align-items: center; flex-wrap: wrap;">
                <button type="button" id="preview_btn" class="btn-modern" style="background-color: <?= e($tenant['color_primario'] ?? '#E85D04') ?>; color: #fff; padding: 12px 24px; font-size: 15px;">
                    🛒 Confirmar Pedido ($ 42.000)
                </button>
                <span id="preview_text" style="color: <?= e($tenant['color_texto'] ?? '#1E1B18') ?>; font-weight: 700; font-size: 15px;">
                    Churrasco al Carbón — $ 42.000
                </span>
            </div>
        </div>
    </div>

    <!-- 2. CONTACTO Y OPERACIÓN -->
    <div class="modern-card">
        <h3 style="font-size: 18px; margin-bottom: 6px; color: var(--admin-text-main);">
            2. Contacto y Recepción de Pedidos
        </h3>
        <p style="font-size: 13.5px; color: var(--admin-text-muted); margin-bottom: 20px;">
            Aquí configuras a qué número llegan los pedidos por WhatsApp y el horario en que atiendes.
        </p>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;" class="config-grid-2">
            <div class="form-group">
                <label for="whatsapp" class="form-label">WhatsApp Receptor de Pedidos * (con código país, ej: 573001234567)</label>
                <input type="tel" id="whatsapp" name="whatsapp" class="form-control" value="<?= e($tenant['whatsapp']) ?>" placeholder="573001234567" required style="font-size: 15px; font-weight: 600;">
                <small style="color: var(--admin-text-muted); display: block; margin-top: 4px;">
                    A este número se enviarán automáticamente los pedidos de tus clientes por WhatsApp.
                </small>
            </div>

            <div class="form-group">
                <label class="form-label">Interruptor Maestro de Recepción</label>
                <div style="padding: 6px 0;">
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                        <input type="checkbox" name="recibiendo_pedidos" value="1" <?= !empty($tenant['recibiendo_pedidos']) ? 'checked' : '' ?> style="width: 22px; height: 22px;">
                        <span style="font-weight: 700; font-size: 14.5px; color: var(--admin-text-main);">Restaurante Abierto en Vivo</span>
                    </label>
                    <small style="color: var(--admin-text-muted); display: block; margin-top: 6px;">
                        Si lo apagas, los clientes podrán mirar la carta pero el botón de confirmar pedido estará deshabilitado.
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. MODALIDADES DE PEDIDO -->
    <div class="modern-card">
        <h3 style="font-size: 18px; margin-bottom: 6px; color: var(--admin-text-main);">
            3. Modalidades de Entrega Aceptadas
        </h3>
        <p style="font-size: 13.5px; color: var(--admin-text-muted); margin-bottom: 20px;">
            Elige los tipos de servicio que ofreces (debes tener al menos uno activo):
        </p>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 16px;">
            <div style="border: 1px solid var(--admin-border); border-radius: var(--radius-md); padding: 18px; background: #FAFBFD;">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-weight: 700; font-size: 15px;">
                    <input type="checkbox" name="acepta_mesa" value="1" <?= !empty($tenant['acepta_mesa']) ? 'checked' : '' ?> style="width: 20px; height: 20px;">
                    🍽️ Consumo en Mesa (QR)
                </label>
                <p style="color: var(--admin-text-muted); font-size: 13px; margin: 8px 0 0 0; line-height: 1.4;">
                    Para comensales sentados en tu local que escanean el código QR pegado en la mesa.
                </p>
            </div>

            <div style="border: 1px solid var(--admin-border); border-radius: var(--radius-md); padding: 18px; background: #FAFBFD;">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-weight: 700; font-size: 15px;">
                    <input type="checkbox" name="acepta_domicilio" value="1" <?= !empty($tenant['acepta_domicilio']) ? 'checked' : '' ?> style="width: 20px; height: 20px;">
                    🛵 Pedidos a Domicilio
                </label>
                <div style="margin-top: 14px;">
                    <label class="form-label" style="font-size: 12.5px;">Costo de envío (COP):</label>
                    <input type="number" name="costo_envio" class="form-control" value="<?= (int)($tenant['costo_envio'] ?? 0) ?>" min="0" step="500">
                </div>
                <div style="margin-top: 10px;">
                    <label class="form-label" style="font-size: 12.5px;">Pedido mínimo para envío (COP):</label>
                    <input type="number" name="pedido_minimo" class="form-control" value="<?= (int)($tenant['pedido_minimo'] ?? 0) ?>" min="0" step="1000">
                </div>
            </div>

            <div style="border: 1px solid var(--admin-border); border-radius: var(--radius-md); padding: 18px; background: #FAFBFD;">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-weight: 700; font-size: 15px;">
                    <input type="checkbox" name="acepta_recoger" value="1" <?= !empty($tenant['acepta_recoger']) ? 'checked' : '' ?> style="width: 20px; height: 20px;">
                    🥡 Para Recoger en el Local
                </label>
                <p style="color: var(--admin-text-muted); font-size: 13px; margin: 8px 0 0 0; line-height: 1.4;">
                    El cliente pide para llevar y el sistema le solicita la hora estimada de recogida.
                </p>
            </div>
        </div>
    </div>

    <!-- 4. MEDIOS DE PAGO INFORMATIVOS -->
    <div class="modern-card">
        <h3 style="font-size: 18px; margin-bottom: 6px; color: var(--admin-text-main);">
            4. Métodos de Pago Informados al Cliente
        </h3>
        <p style="font-size: 13.5px; color: var(--admin-text-muted); margin-bottom: 20px;">
            Mesaly no cobra comisiones ni procesa dinero. Esta información se incluye en el mensaje de WhatsApp para que el cliente sepa cómo pagarte:
        </p>

        <div style="display: flex; gap: 24px; flex-wrap: wrap; margin-bottom: 18px;">
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 14px; font-weight: 600;">
                <input type="checkbox" name="metodos_pago[]" value="efectivo" <?= in_array('efectivo', $metodos, true) ? 'checked' : '' ?> style="width: 18px; height: 18px;">
                💵 Efectivo
            </label>
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 14px; font-weight: 600;">
                <input type="checkbox" name="metodos_pago[]" value="transferencia" <?= in_array('transferencia', $metodos, true) ? 'checked' : '' ?> style="width: 18px; height: 18px;">
                📲 Transferencia (Nequi, Daviplata, Bancolombia)
            </label>
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 14px; font-weight: 600;">
                <input type="checkbox" name="metodos_pago[]" value="datafono" <?= in_array('datafono', $metodos, true) ? 'checked' : '' ?> style="width: 18px; height: 18px;">
                💳 Datáfono contra entrega
            </label>
        </div>

        <div class="form-group">
            <label for="info_pago_texto" class="form-label">Datos e Instrucciones de Pago:</label>
            <textarea id="info_pago_texto" name="info_pago_texto" class="form-control" rows="2" placeholder="Ej: Transferencias por Nequi o Daviplata al 300 123 4567. También recibimos efectivo y datáfono."><?= e($tenant['info_pago_texto'] ?? '') ?></textarea>
        </div>
    </div>

    <!-- 5. HORARIOS DE ATENCIÓN -->
    <div class="modern-card">
        <h3 style="font-size: 18px; margin-bottom: 6px; color: var(--admin-text-main);">
            5. Horarios de Apertura y Cierre
        </h3>
        <p style="font-size: 13.5px; color: var(--admin-text-muted); margin-bottom: 16px;">
            Marca los días en que abres y el horario de servicio:
        </p>

        <?php
        $nombresDias = [
            0 => 'Domingo',
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado',
        ];
        ?>

        <div style="display: grid; gap: 10px;">
            <?php foreach ($nombresDias as $numDia => $nombreDia): ?>
                <?php $h = $horarios[$numDia] ?? ['abre' => '11:00', 'cierra' => '22:00', 'activo' => 0]; ?>
                <div style="display: flex; align-items: center; gap: 16px; padding: 10px 16px; background: #FAFBFD; border-radius: var(--radius-sm); border: 1px solid var(--admin-border); flex-wrap: wrap;">
                    <div style="width: 140px;">
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-weight: 700; font-size: 14px;">
                            <input type="checkbox" name="dias[<?= $numDia ?>][activo]" value="1" <?= !empty($h['activo']) ? 'checked' : '' ?> style="width: 18px; height: 18px;">
                            <?= $nombreDia ?>
                        </label>
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="font-size: 13px; color: var(--admin-text-muted);">Abre:</span>
                        <input type="time" name="dias[<?= $numDia ?>][abre]" value="<?= e($h['abre']) ?>" class="form-control" style="width: 130px; min-height: 38px; padding: 4px 8px;">
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="font-size: 13px; color: var(--admin-text-muted);">Cierra:</span>
                        <input type="time" name="dias[<?= $numDia ?>][cierra]" value="<?= e($h['cierra']) ?>" class="form-control" style="width: 130px; min-height: 38px; padding: 4px 8px;">
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- BOTÓN DE GUARDADO PRINCIPAL -->
    <div style="display: flex; justify-content: flex-end; gap: 14px; margin-bottom: 48px;">
        <button type="submit" class="btn-modern btn-modern-primary" style="padding: 14px 36px; font-size: 16px;">
            💾 Guardar Todos los Cambios
        </button>
    </div>
</form>

<style nonce="<?= e($cspNonce) ?>">
@media (max-width: 800px) {
    .config-grid-2 {
        grid-template-columns: 1fr !important;
    }
}
</style>

<script nonce="<?= e($cspNonce) ?>">
function previewLogo(input) {
    if (input.files && input.files[0]) {
        const preview = document.getElementById('preview_logo');
        preview.src = URL.createObjectURL(input.files[0]);
        preview.style.display = 'block';
    }
}

function previewBanner(input) {
    if (input.files && input.files[0]) {
        const preview = document.getElementById('preview_banner');
        preview.src = URL.createObjectURL(input.files[0]);
        preview.style.display = 'block';
    }
}

function syncColor(targetId, val) {
    document.getElementById(targetId).value = val;
    actualizarPreview();
}

function aplicarPreset(primario, secundario, fondo, texto) {
    document.getElementById('color_primario').value = primario;
    document.getElementById('picker_primario').value = primario;
    document.getElementById('color_secundario').value = secundario;
    document.getElementById('picker_secundario').value = secundario;
    document.getElementById('color_fondo').value = fondo;
    document.getElementById('picker_fondo').value = fondo;
    document.getElementById('color_texto').value = texto;
    document.getElementById('picker_texto').value = texto;
    actualizarPreview();
}

function getLuminance(hex) {
    hex = hex.replace('#', '');
    if (hex.length === 3) {
        hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2];
    }
    const r = parseInt(hex.substr(0, 2), 16) / 255;
    const g = parseInt(hex.substr(2, 2), 16) / 255;
    const b = parseInt(hex.substr(4, 2), 16) / 255;
    return (0.299 * r + 0.587 * g + 0.114 * b);
}

function actualizarPreview() {
    const primario = document.getElementById('color_primario').value;
    const fondo = document.getElementById('color_fondo').value;
    const texto = document.getElementById('color_texto').value;

    const btn = document.getElementById('preview_btn');
    const txt = document.getElementById('preview_text');
    const box = document.getElementById('preview_box');

    if (/^#[0-9a-fA-F]{6}$/.test(primario)) {
        btn.style.backgroundColor = primario;
        btn.style.color = getLuminance(primario) > 0.55 ? '#1E1B18' : '#FFFFFF';
    }
    if (/^#[0-9a-fA-F]{6}$/.test(fondo)) {
        box.style.backgroundColor = fondo;
    }
    if (/^#[0-9a-fA-F]{6}$/.test(texto)) {
        txt.style.color = texto;
    }
}
actualizarPreview();
</script>
