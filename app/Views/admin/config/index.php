<div style="margin-bottom: 24px;">
    <h2>Configuración de Marca y Operación</h2>
    <p style="color: var(--brand-gray-muted); font-size: 14px;">
        Personaliza los colores, logo, métodos de entrega y horarios comerciales de tu restaurante.
    </p>
</div>

<form method="POST" action="<?= e(app_url('/admin/configuracion')) ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <!-- 1. IDENTIDAD DE MARCA -->
    <div class="card">
        <h3 style="border-bottom: 1px solid var(--brand-gray-border); padding-bottom: 10px; margin-bottom: 16px;">
            1. Identidad y Apariencia Visual
        </h3>

        <div class="form-group">
            <label for="nombre" class="form-label">Nombre Comercial del Restaurante *</label>
            <input type="text" id="nombre" name="nombre" class="form-control" value="<?= e($tenant['nombre']) ?>" required>
        </div>

        <div class="form-group">
            <label for="descripcion" class="form-label">Descripción Corta (se muestra bajo el nombre en el menú)</label>
            <textarea id="descripcion" name="descripcion" class="form-control" rows="2"><?= e($tenant['descripcion'] ?? '') ?></textarea>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 16px;">
            <div class="form-group">
                <label for="logo" class="form-label">Logo del Restaurante (JPG, PNG o WebP, máx 3 MB)</label>
                <?php if (!empty($tenant['logo_path'])): ?>
                    <div style="margin-bottom: 8px;">
                        <img src="<?= e(app_url($tenant['logo_path'])) ?>" alt="Logo actual" style="max-height: 60px; border-radius: var(--radius-sm); border: 1px solid var(--brand-gray-border); padding: 4px; background: #fff;">
                    </div>
                <?php endif; ?>
                <input type="file" id="logo" name="logo" class="form-control" accept="image/jpeg,image/png,image/webp">
            </div>

            <div class="form-group">
                <label for="banner" class="form-label">Banner de Portada (JPG, PNG o WebP, máx 3 MB)</label>
                <?php if (!empty($tenant['banner_path'])): ?>
                    <div style="margin-bottom: 8px;">
                        <img src="<?= e(app_url($tenant['banner_path'])) ?>" alt="Banner actual" style="max-height: 60px; max-width: 100%; border-radius: var(--radius-sm); border: 1px solid var(--brand-gray-border); object-fit: cover;">
                    </div>
                <?php endif; ?>
                <input type="file" id="banner" name="banner" class="form-control" accept="image/jpeg,image/png,image/webp">
            </div>
        </div>

        <!-- Paletas Predefinidas (Presets de la Sección 15.5) -->
        <div style="margin-top: 20px; padding: 16px; background: var(--brand-gray-light); border-radius: var(--radius-md);">
            <label class="form-label" style="margin-bottom: 8px;">Paletas Predefinidas (Haz clic para aplicar y previsualizar):</label>
            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                <button type="button" class="btn btn-secondary btn-sm" onclick="aplicarPreset('#E85D04', '#FFB703', '#FFF7EA', '#1E1B18')">Sazón (Naranja)</button>
                <button type="button" class="btn btn-secondary btn-sm" onclick="aplicarPreset('#2A9D5C', '#FFB703', '#F6FBF4', '#14281D')">Fresca (Verde)</button>
                <button type="button" class="btn btn-secondary btn-sm" onclick="aplicarPreset('#C1121F', '#1E1B18', '#FFFFFF', '#1E1B18')">Parrilla (Rojo)</button>
                <button type="button" class="btn btn-secondary btn-sm" onclick="aplicarPreset('#6F4E37', '#D4A373', '#FAF3E8', '#2B1B12')">Café (Marrón)</button>
                <button type="button" class="btn btn-secondary btn-sm" onclick="aplicarPreset('#E5527A', '#FFC8DD', '#FFF5F8', '#3A1F2B')">Dulce (Rosa)</button>
                <button type="button" class="btn btn-secondary btn-sm" onclick="aplicarPreset('#0077B6', '#90E0EF', '#F4FBFE', '#0B2A3A')">Mar (Azul)</button>
                <button type="button" class="btn btn-secondary btn-sm" onclick="aplicarPreset('#FFB703', '#E85D04', '#16130F', '#FFF7EA')">Noche (Oscuro)</button>
            </div>
        </div>

        <!-- Selectores de color -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; margin-top: 20px;">
            <div class="form-group">
                <label for="color_primario" class="form-label">Color Primario (Botones) *</label>
                <div style="display: flex; gap: 8px; align-items: center;">
                    <input type="color" id="picker_primario" value="<?= e($tenant['color_primario'] ?? '#E85D04') ?>" style="width: 44px; height: 44px; border: none; cursor: pointer;" oninput="syncColor('color_primario', this.value)">
                    <input type="text" id="color_primario" name="color_primario" class="form-control" value="<?= e($tenant['color_primario'] ?? '#E85D04') ?>" pattern="^#[0-9a-fA-F]{6}$" required oninput="actualizarPreview()">
                </div>
            </div>

            <div class="form-group">
                <label for="color_secundario" class="form-label">Color Secundario (Acentos) *</label>
                <div style="display: flex; gap: 8px; align-items: center;">
                    <input type="color" id="picker_secundario" value="<?= e($tenant['color_secundario'] ?? '#FFB703') ?>" style="width: 44px; height: 44px; border: none; cursor: pointer;" oninput="syncColor('color_secundario', this.value)">
                    <input type="text" id="color_secundario" name="color_secundario" class="form-control" value="<?= e($tenant['color_secundario'] ?? '#FFB703') ?>" pattern="^#[0-9a-fA-F]{6}$" required oninput="actualizarPreview()">
                </div>
            </div>

            <div class="form-group">
                <label for="color_fondo" class="form-label">Fondo de Menú *</label>
                <div style="display: flex; gap: 8px; align-items: center;">
                    <input type="color" id="picker_fondo" value="<?= e($tenant['color_fondo'] ?? '#FFF7EA') ?>" style="width: 44px; height: 44px; border: none; cursor: pointer;" oninput="syncColor('color_fondo', this.value)">
                    <input type="text" id="color_fondo" name="color_fondo" class="form-control" value="<?= e($tenant['color_fondo'] ?? '#FFF7EA') ?>" pattern="^#[0-9a-fA-F]{6}$" required oninput="actualizarPreview()">
                </div>
            </div>

            <div class="form-group">
                <label for="color_texto" class="form-label">Color de Texto *</label>
                <div style="display: flex; gap: 8px; align-items: center;">
                    <input type="color" id="picker_texto" value="<?= e($tenant['color_texto'] ?? '#1E1B18') ?>" style="width: 44px; height: 44px; border: none; cursor: pointer;" oninput="syncColor('color_texto', this.value)">
                    <input type="text" id="color_texto" name="color_texto" class="form-control" value="<?= e($tenant['color_texto'] ?? '#1E1B18') ?>" pattern="^#[0-9a-fA-F]{6}$" required oninput="actualizarPreview()">
                </div>
            </div>
        </div>

        <!-- Vista previa en vivo -->
        <div style="margin-top: 16px; padding: 18px; border-radius: var(--radius-md); border: 1px dashed var(--brand-gray-border);" id="preview_box">
            <span style="font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Vista Previa de Botones y Texto:</span>
            <div style="margin-top: 12px; display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
                <button type="button" id="preview_btn" class="btn" style="background-color: <?= e($tenant['color_primario'] ?? '#E85D04') ?>; color: #fff;">
                    Botón Confirmar Pedido
                </button>
                <span id="preview_text" style="color: <?= e($tenant['color_texto'] ?? '#1E1B18') ?>; font-weight: 600;">
                    Texto de prueba: Bandeja Paisa — $ 38.000
                </span>
            </div>
        </div>
    </div>

    <!-- 2. CONTACTO Y OPERACIÓN -->
    <div class="card">
        <h3 style="border-bottom: 1px solid var(--brand-gray-border); padding-bottom: 10px; margin-bottom: 16px;">
            2. Contacto y Estado de Recepción
        </h3>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label for="whatsapp" class="form-label">WhatsApp Receptor de Pedidos * (código país ej: 573001234567)</label>
                <input type="tel" id="whatsapp" name="whatsapp" class="form-control" value="<?= e($tenant['whatsapp']) ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label">Interruptor Maestro de Recepción</label>
                <div style="padding: 10px 0;">
                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                        <input type="checkbox" name="recibiendo_pedidos" value="1" <?= !empty($tenant['recibiendo_pedidos']) ? 'checked' : '' ?> style="width: 22px; height: 22px;">
                        <span style="font-weight: 600; font-size: 14px;">Abierto para recibir pedidos en vivo</span>
                    </label>
                    <small style="color: var(--brand-gray-muted); display: block; margin-top: 4px;">
                        Si está apagado, los clientes pueden ver el menú pero no podrán enviar pedidos.
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. MODALIDADES DE PEDIDO -->
    <div class="card">
        <h3 style="border-bottom: 1px solid var(--brand-gray-border); padding-bottom: 10px; margin-bottom: 16px;">
            3. Modalidades de Pedido Aceptadas
        </h3>
        <p style="font-size: 13px; color: var(--brand-gray-muted); margin-bottom: 16px;">
            Activa o desactiva las opciones de servicio que ofreces (al menos una obligatoria):
        </p>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 16px;">
            <div style="border: 1px solid var(--brand-gray-border); border-radius: var(--radius-md); padding: 16px;">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-weight: 600;">
                    <input type="checkbox" name="acepta_mesa" value="1" <?= !empty($tenant['acepta_mesa']) ? 'checked' : '' ?> style="width: 20px; height: 20px;">
                    Consumo en Mesa (Códigos QR)
                </label>
                <small style="color: var(--brand-gray-muted); display: block; margin-top: 6px;">
                    Para clientes que escanean la mesa dentro del restaurante.
                </small>
            </div>

            <div style="border: 1px solid var(--brand-gray-border); border-radius: var(--radius-md); padding: 16px;">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-weight: 600;">
                    <input type="checkbox" name="acepta_domicilio" value="1" <?= !empty($tenant['acepta_domicilio']) ? 'checked' : '' ?> style="width: 20px; height: 20px;">
                    Servicio a Domicilio
                </label>
                <div style="margin-top: 12px;">
                    <label class="form-label" style="font-size: 12px;">Costo fijo de envío (COP):</label>
                    <input type="number" name="costo_envio" class="form-control" value="<?= (int)($tenant['costo_envio'] ?? 0) ?>" min="0" step="500">
                </div>
                <div style="margin-top: 8px;">
                    <label class="form-label" style="font-size: 12px;">Pedido mínimo para envío (COP):</label>
                    <input type="number" name="pedido_minimo" class="form-control" value="<?= (int)($tenant['pedido_minimo'] ?? 0) ?>" min="0" step="1000">
                </div>
            </div>

            <div style="border: 1px solid var(--brand-gray-border); border-radius: var(--radius-md); padding: 16px;">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-weight: 600;">
                    <input type="checkbox" name="acepta_recoger" value="1" <?= !empty($tenant['acepta_recoger']) ? 'checked' : '' ?> style="width: 20px; height: 20px;">
                    Para Recoger en el Local (Takeout)
                </label>
                <small style="color: var(--brand-gray-muted); display: block; margin-top: 6px;">
                    El cliente solicita recoger su pedido y define la hora estimada.
                </small>
            </div>
        </div>
    </div>

    <!-- 4. MÉTODOS DE PAGO INFORMATIVOS -->
    <div class="card">
        <h3 style="border-bottom: 1px solid var(--brand-gray-border); padding-bottom: 10px; margin-bottom: 16px;">
            4. Métodos de Pago Aceptados (Informativos)
        </h3>
        <p style="font-size: 13px; color: var(--brand-gray-muted); margin-bottom: 16px;">
            Selecciona qué medios de pago aceptas. Esto se le informa al cliente al enviar el pedido por WhatsApp:
        </p>

        <div style="display: flex; gap: 20px; flex-wrap: wrap; margin-bottom: 16px;">
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                <input type="checkbox" name="metodos_pago[]" value="efectivo" <?= in_array('efectivo', $metodos, true) ? 'checked' : '' ?>>
                Efectivo
            </label>
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                <input type="checkbox" name="metodos_pago[]" value="transferencia" <?= in_array('transferencia', $metodos, true) ? 'checked' : '' ?>>
                Transferencia (Nequi, Daviplata, Bancolombia)
            </label>
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                <input type="checkbox" name="metodos_pago[]" value="datafono" <?= in_array('datafono', $metodos, true) ? 'checked' : '' ?>>
                Datáfono contra entrega
            </label>
        </div>

        <div class="form-group">
            <label for="info_pago_texto" class="form-label">Datos de Transferencia (Números de cuenta / Nequi que verá el cliente):</label>
            <textarea id="info_pago_texto" name="info_pago_texto" class="form-control" rows="2" placeholder="Ej: Nequi o Daviplata al 300 123 4567 a nombre de..."><?= e($tenant['info_pago_texto'] ?? '') ?></textarea>
        </div>
    </div>

    <!-- 5. HORARIOS DE ATENCIÓN -->
    <div class="card">
        <h3 style="border-bottom: 1px solid var(--brand-gray-border); padding-bottom: 10px; margin-bottom: 16px;">
            5. Horarios de Atención por Día
        </h3>

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
                <div style="display: flex; align-items: center; gap: 16px; padding: 8px 12px; background: var(--brand-cream); border-radius: var(--radius-sm); flex-wrap: wrap;">
                    <div style="width: 120px;">
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-weight: 600;">
                            <input type="checkbox" name="dias[<?= $numDia ?>][activo]" value="1" <?= !empty($h['activo']) ? 'checked' : '' ?>>
                            <?= $nombreDia ?>
                        </label>
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="font-size: 13px;">Abre:</span>
                        <input type="time" name="dias[<?= $numDia ?>][abre]" value="<?= e($h['abre']) ?>" class="form-control" style="width: 120px; min-height: 36px; padding: 4px 8px;">
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <span style="font-size: 13px;">Cierra:</span>
                        <input type="time" name="dias[<?= $numDia ?>][cierra]" value="<?= e($h['cierra']) ?>" class="form-control" style="width: 120px; min-height: 36px; padding: 4px 8px;">
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div style="display: flex; justify-content: flex-end; gap: 12px; margin-bottom: 40px;">
        <button type="submit" class="btn btn-primary" style="padding: 12px 28px; font-size: 16px;">
            Guardar Configuración
        </button>
    </div>
</form>

<script nonce="<?= e($cspNonce) ?>">
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
        // Cálculo automático de contraste (Sección 6.1)
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
