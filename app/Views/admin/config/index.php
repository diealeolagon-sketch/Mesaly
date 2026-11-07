<div class="content-header">
    <div>
        <h1 class="page-title">Configuración del Restaurante</h1>
        <p class="page-subtitle">
            Personaliza la identidad visual, métodos de entrega aceptados, número de WhatsApp y horarios de atención.
        </p>
    </div>
</div>

<form method="POST" action="<?= e(app_url('/admin/configuracion')) ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div style="display: flex; flex-direction: column; gap: 24px;">
        
        <!-- 1. IDENTIDAD DE MARCA Y COLORES -->
        <div class="card">
            <h2 style="font-size: 15px; font-weight: 700; color: var(--color-text-main); margin: 0 0 4px 0;">
                1. Marca y Apariencia Visual
            </h2>
            <p style="font-size: 13px; color: var(--color-text-muted); margin: 0 0 20px 0;">
                Estos colores y logotipos se aplican automáticamente en la carta digital vista por tus comensales.
            </p>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;" class="config-grid-2">
                <div class="form-group">
                    <label for="nombre" class="form-label">Nombre Comercial del Restaurante *</label>
                    <input type="text" id="nombre" name="nombre" class="form-control" value="<?= e($tenant['nombre']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="fuente" class="form-label">Tipografía del Menú</label>
                    <select id="fuente" name="fuente" class="form-control">
                        <option value="Inter" <?= ($tenant['fuente'] ?? '') === 'Inter' ? 'selected' : '' ?>>Inter (Limpia y moderna)</option>
                        <option value="Poppins" <?= ($tenant['fuente'] ?? '') === 'Poppins' ? 'selected' : '' ?>>Poppins (Geométrica)</option>
                        <option value="Outfit" <?= ($tenant['fuente'] ?? '') === 'Outfit' ? 'selected' : '' ?>>Outfit (Contemporánea)</option>
                        <option value="Playfair" <?= ($tenant['fuente'] ?? '') === 'Playfair' ? 'selected' : '' ?>>Playfair (Elegante)</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="descripcion" class="form-label">Descripción Corta o Eslogan</label>
                <textarea id="descripcion" name="descripcion" class="form-control" rows="2" placeholder="Ej: Carnes al carbón, cortes premium y hamburguesas artesanales."><?= e($tenant['descripcion'] ?? '') ?></textarea>
            </div>

            <!-- SUBIDA DE LOGO Y BANNER -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 16px;" class="config-grid-2">
                <div>
                    <label class="form-label">Logo del Restaurante</label>
                    <div class="dropzone" onclick="document.getElementById('logo_input').click()" style="padding: 20px 16px;">
                        <input type="file" id="logo_input" name="logo" accept="image/jpeg,image/png,image/webp" onchange="previewLogo(this)">
                        <?php if (!empty($tenant['logo_path'])): ?>
                            <img src="<?= e(app_url($tenant['logo_path'])) ?>" id="preview_logo" class="dropzone-preview" alt="Logo actual" style="max-height: 70px; object-fit: contain;">
                            <span style="font-size: 12px; font-weight: 600; color: var(--color-brand); display: block; margin-top: 8px;">Cambiar logo</span>
                        <?php else: ?>
                            <img src="" id="preview_logo" class="dropzone-preview" alt="Logo" style="display: none; max-height: 70px; object-fit: contain;">
                            <div style="width: 36px; height: 36px; border-radius: 50%; background: var(--color-bg-subtle); display: inline-flex; align-items: center; justify-content: center; color: var(--color-text-subtle); margin-bottom: 8px;">
                                <?= icon('upload', 16) ?>
                            </div>
                            <span style="font-size: 13px; font-weight: 600; display: block; color: var(--color-text-main);">Subir Logo</span>
                            <span style="font-size: 11px; color: var(--color-text-subtle);">JPG, PNG o WebP</span>
                        <?php endif; ?>
                    </div>
                </div>

                <div>
                    <label class="form-label">Banner de Portada</label>
                    <div class="dropzone" onclick="document.getElementById('banner_input').click()" style="padding: 20px 16px;">
                        <input type="file" id="banner_input" name="banner" accept="image/jpeg,image/png,image/webp" onchange="previewBanner(this)">
                        <?php if (!empty($tenant['banner_path'])): ?>
                            <img src="<?= e(app_url($tenant['banner_path'])) ?>" id="preview_banner" class="dropzone-preview" alt="Banner actual" style="max-height: 70px; object-fit: cover;">
                            <span style="font-size: 12px; font-weight: 600; color: var(--color-brand); display: block; margin-top: 8px;">Cambiar portada</span>
                        <?php else: ?>
                            <img src="" id="preview_banner" class="dropzone-preview" alt="Banner" style="display: none; max-height: 70px; object-fit: cover;">
                            <div style="width: 36px; height: 36px; border-radius: 50%; background: var(--color-bg-subtle); display: inline-flex; align-items: center; justify-content: center; color: var(--color-text-subtle); margin-bottom: 8px;">
                                <?= icon('upload', 16) ?>
                            </div>
                            <span style="font-size: 13px; font-weight: 600; display: block; color: var(--color-text-main);">Subir Banner</span>
                            <span style="font-size: 11px; color: var(--color-text-subtle);">JPG, PNG o WebP</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- PALETAS PREDEFINIDAS -->
            <div style="margin-top: 24px; padding: 16px; background: var(--color-bg-subtle); border-radius: var(--radius-md); border: 1px solid var(--color-border-subtle);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; flex-wrap: wrap; gap: 6px;">
                    <span style="font-size: 13px; font-weight: 600; color: var(--color-text-main);">Paletas Predefinidas:</span>
                    <span style="font-size: 12px; color: var(--color-text-muted);">Haz clic para previsualizar</span>
                </div>
                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                    <button type="button" class="btn btn-secondary btn-sm" onclick="aplicarPreset('#E85D04', '#FFB703', '#FFF7EA', '#1E1B18')">
                        <span style="width: 10px; height: 10px; border-radius: 50%; background: #E85D04; display: inline-block;"></span> Sazón (Naranja)
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="aplicarPreset('#2A9D5C', '#FFB703', '#F6FBF4', '#14281D')">
                        <span style="width: 10px; height: 10px; border-radius: 50%; background: #2A9D5C; display: inline-block;"></span> Fresca (Verde)
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="aplicarPreset('#C1121F', '#1E1B18', '#FFFFFF', '#1E1B18')">
                        <span style="width: 10px; height: 10px; border-radius: 50%; background: #C1121F; display: inline-block;"></span> Parrilla (Rojo)
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="aplicarPreset('#6F4E37', '#D4A373', '#FAF3E8', '#2B1B12')">
                        <span style="width: 10px; height: 10px; border-radius: 50%; background: #6F4E37; display: inline-block;"></span> Café (Marrón)
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="aplicarPreset('#E5527A', '#FFC8DD', '#FFF5F8', '#3A1F2B')">
                        <span style="width: 10px; height: 10px; border-radius: 50%; background: #E5527A; display: inline-block;"></span> Dulce (Rosa)
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="aplicarPreset('#0077B6', '#90E0EF', '#F4FBFE', '#0B2A3A')">
                        <span style="width: 10px; height: 10px; border-radius: 50%; background: #0077B6; display: inline-block;"></span> Mar (Azul)
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="aplicarPreset('#FFB703', '#E85D04', '#16130F', '#FFF7EA')">
                        <span style="width: 10px; height: 10px; border-radius: 50%; background: #16130F; border: 1px solid #ccc; display: inline-block;"></span> Noche (Oscuro)
                    </button>
                </div>
            </div>

            <!-- SELECTORES DE COLOR -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; margin-top: 20px;">
                <div class="form-group">
                    <label for="color_primario" class="form-label">Color Primario (Botones) *</label>
                    <div style="display: flex; gap: 8px; align-items: center;">
                        <input type="color" id="picker_primario" value="<?= e($tenant['color_primario'] ?? '#E85D04') ?>" style="width: 40px; height: 40px; border: none; cursor: pointer; border-radius: var(--radius-sm); padding: 0;" oninput="syncColor('color_primario', this.value)">
                        <input type="text" id="color_primario" name="color_primario" class="form-control" value="<?= e($tenant['color_primario'] ?? '#E85D04') ?>" pattern="^#[0-9a-fA-F]{6}$" required oninput="actualizarPreview()">
                    </div>
                </div>

                <div class="form-group">
                    <label for="color_secundario" class="form-label">Color Secundario (Acentos) *</label>
                    <div style="display: flex; gap: 8px; align-items: center;">
                        <input type="color" id="picker_secundario" value="<?= e($tenant['color_secundario'] ?? '#FFB703') ?>" style="width: 40px; height: 40px; border: none; cursor: pointer; border-radius: var(--radius-sm); padding: 0;" oninput="syncColor('color_secundario', this.value)">
                        <input type="text" id="color_secundario" name="color_secundario" class="form-control" value="<?= e($tenant['color_secundario'] ?? '#FFB703') ?>" pattern="^#[0-9a-fA-F]{6}$" required oninput="actualizarPreview()">
                    </div>
                </div>

                <div class="form-group">
                    <label for="color_fondo" class="form-label">Fondo de Menú *</label>
                    <div style="display: flex; gap: 8px; align-items: center;">
                        <input type="color" id="picker_fondo" value="<?= e($tenant['color_fondo'] ?? '#FFF7EA') ?>" style="width: 40px; height: 40px; border: none; cursor: pointer; border-radius: var(--radius-sm); padding: 0;" oninput="syncColor('color_fondo', this.value)">
                        <input type="text" id="color_fondo" name="color_fondo" class="form-control" value="<?= e($tenant['color_fondo'] ?? '#FFF7EA') ?>" pattern="^#[0-9a-fA-F]{6}$" required oninput="actualizarPreview()">
                    </div>
                </div>

                <div class="form-group">
                    <label for="color_texto" class="form-label">Color de Texto *</label>
                    <div style="display: flex; gap: 8px; align-items: center;">
                        <input type="color" id="picker_texto" value="<?= e($tenant['color_texto'] ?? '#1E1B18') ?>" style="width: 40px; height: 40px; border: none; cursor: pointer; border-radius: var(--radius-sm); padding: 0;" oninput="syncColor('color_texto', this.value)">
                        <input type="text" id="color_texto" name="color_texto" class="form-control" value="<?= e($tenant['color_texto'] ?? '#1E1B18') ?>" pattern="^#[0-9a-fA-F]{6}$" required oninput="actualizarPreview()">
                    </div>
                </div>
            </div>

            <!-- VISTA PREVIA DEL BOTÓN Y CONTRASTE -->
            <div style="margin-top: 16px; padding: 20px; border-radius: var(--radius-md); border: 1px solid var(--color-border); background: <?= e($tenant['color_fondo'] ?? '#FFF7EA') ?>;" id="preview_box">
                <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: var(--color-text-subtle); display: block; margin-bottom: 10px;">
                    Vista previa de botón y texto con contraste inteligente:
                </span>
                <div style="display: flex; gap: 16px; align-items: center; flex-wrap: wrap;">
                    <button type="button" id="preview_btn" class="btn" style="background-color: <?= e($tenant['color_primario'] ?? '#E85D04') ?>; color: #fff; padding: 10px 20px; font-weight: 600;">
                        Confirmar Pedido (COP $ 42.000)
                    </button>
                    <span id="preview_text" style="color: <?= e($tenant['color_texto'] ?? '#1E1B18') ?>; font-weight: 600; font-size: 14px;">
                        Churrasco al Carbón — $ 42.000
                    </span>
                </div>
            </div>
        </div>

        <!-- 2. CONTACTO Y WHATSAPP -->
        <div class="card">
            <h2 style="font-size: 15px; font-weight: 700; color: var(--color-text-main); margin: 0 0 4px 0;">
                2. Contacto y Recepción de Pedidos
            </h2>
            <p style="font-size: 13px; color: var(--color-text-muted); margin: 0 0 20px 0;">
                Número telefónico al que se dirigirán los pedidos por WhatsApp y estado de apertura en tiempo real.
            </p>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;" class="config-grid-2">
                <div class="form-group">
                    <label for="whatsapp" class="form-label">WhatsApp Receptor de Pedidos * (con indicativo país, ej: 573001234567)</label>
                    <input type="tel" id="whatsapp" name="whatsapp" class="form-control" value="<?= e($tenant['whatsapp']) ?>" placeholder="573001234567" required style="font-weight: 600; font-variant-numeric: tabular-nums;">
                    <span style="font-size: 12px; color: var(--color-text-muted); display: block; margin-top: 4px;">
                        A este número el cliente enviará su pedido formateado al tocar Confirmar Pedido.
                    </span>
                </div>

                <div class="form-group">
                    <label class="form-label">Estado de Recepción del Restaurante</label>
                    <div style="padding: 6px 0;">
                        <label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">
                            <input type="checkbox" name="recibiendo_pedidos" value="1" <?= !empty($tenant['recibiendo_pedidos']) ? 'checked' : '' ?> style="width: 18px; height: 18px; accent-color: var(--color-brand);">
                            <span style="font-weight: 600; font-size: 14px; color: var(--color-text-main);">Abierto para recibir pedidos</span>
                        </label>
                        <span style="font-size: 12px; color: var(--color-text-muted); display: block; margin-top: 4px;">
                            Si se desmarca, el menú seguirá visible pero no permitirá enviar pedidos.
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. MODALIDADES DE PEDIDO -->
        <div class="card">
            <h2 style="font-size: 15px; font-weight: 700; color: var(--color-text-main); margin: 0 0 4px 0;">
                3. Modalidades de Pedido Aceptadas
            </h2>
            <p style="font-size: 13px; color: var(--color-text-muted); margin: 0 0 20px 0;">
                Habilita los canales de atención que ofreces a tus comensales:
            </p>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 16px;">
                <div style="border: 1px solid var(--color-border-subtle); border-radius: var(--radius-md); padding: 16px; background: var(--color-surface);">
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-weight: 600; font-size: 14px; color: var(--color-text-main);">
                        <input type="checkbox" name="acepta_mesa" value="1" <?= !empty($tenant['acepta_mesa']) ? 'checked' : '' ?> style="width: 16px; height: 16px; accent-color: var(--color-brand);">
                        <span>Consumo en Mesa (QR)</span>
                    </label>
                    <p style="color: var(--color-text-muted); font-size: 12.5px; margin: 8px 0 0 0; line-height: 1.4;">
                        Para clientes sentados en tu local que escanean el código QR en su mesa.
                    </p>
                </div>

                <div style="border: 1px solid var(--color-border-subtle); border-radius: var(--radius-md); padding: 16px; background: var(--color-surface);">
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-weight: 600; font-size: 14px; color: var(--color-text-main);">
                        <input type="checkbox" name="acepta_domicilio" value="1" <?= !empty($tenant['acepta_domicilio']) ? 'checked' : '' ?> style="width: 16px; height: 16px; accent-color: var(--color-brand);">
                        <span>Pedidos a Domicilio</span>
                    </label>
                    <div style="margin-top: 12px;">
                        <label class="form-label" style="font-size: 12px;">Costo de envío (COP):</label>
                        <input type="number" name="costo_envio" class="form-control" value="<?= (int)($tenant['costo_envio'] ?? 0) ?>" min="0" step="100" style="font-variant-numeric: tabular-nums;">
                    </div>
                    <div style="margin-top: 8px;">
                        <label class="form-label" style="font-size: 12px;">Pedido mínimo (COP):</label>
                        <input type="number" name="pedido_minimo" class="form-control" value="<?= (int)($tenant['pedido_minimo'] ?? 0) ?>" min="0" step="100" style="font-variant-numeric: tabular-nums;">
                    </div>
                </div>

                <div style="border: 1px solid var(--color-border-subtle); border-radius: var(--radius-md); padding: 16px; background: var(--color-surface);">
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-weight: 600; font-size: 14px; color: var(--color-text-main);">
                        <input type="checkbox" name="acepta_recoger" value="1" <?= !empty($tenant['acepta_recoger']) ? 'checked' : '' ?> style="width: 16px; height: 16px; accent-color: var(--color-brand);">
                        <span>Para Llevar / Recoger</span>
                    </label>
                    <p style="color: var(--color-text-muted); font-size: 12.5px; margin: 8px 0 0 0; line-height: 1.4;">
                        El cliente solicita su pedido para pasar a recogerlo en tu sede comercial.
                    </p>
                </div>
            </div>
        </div>

        <!-- 4. MEDIOS DE PAGO INFORMATIVOS -->
        <div class="card">
            <h2 style="font-size: 15px; font-weight: 700; color: var(--color-text-main); margin: 0 0 4px 0;">
                4. Medios de Pago Informados al Cliente
            </h2>
            <p style="font-size: 13px; color: var(--color-text-muted); margin: 0 0 20px 0;">
                Mesaly no procesa dinero directamente. Estos medios se detallan en el mensaje de pedido para conveniencia del comensal.
            </p>

            <div style="display: flex; gap: 20px; flex-wrap: wrap; margin-bottom: 16px;">
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 13.5px; font-weight: 500; color: var(--color-text-main);">
                    <input type="checkbox" name="metodos_pago[]" value="efectivo" <?= in_array('efectivo', $metodos, true) ? 'checked' : '' ?> style="width: 16px; height: 16px; accent-color: var(--color-brand);">
                    <span>Efectivo</span>
                </label>
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 13.5px; font-weight: 500; color: var(--color-text-main);">
                    <input type="checkbox" name="metodos_pago[]" value="transferencia" <?= in_array('transferencia', $metodos, true) ? 'checked' : '' ?> style="width: 16px; height: 16px; accent-color: var(--color-brand);">
                    <span>Transferencia (Nequi, Daviplata, Bancolombia)</span>
                </label>
                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 13.5px; font-weight: 500; color: var(--color-text-main);">
                    <input type="checkbox" name="metodos_pago[]" value="datafono" <?= in_array('datafono', $metodos, true) ? 'checked' : '' ?> style="width: 16px; height: 16px; accent-color: var(--color-brand);">
                    <span>Datáfono / Tarjeta</span>
                </label>
            </div>

            <div class="form-group">
                <label for="info_pago_texto" class="form-label">Datos e Instrucciones de Pago:</label>
                <textarea id="info_pago_texto" name="info_pago_texto" class="form-control" rows="2" placeholder="Ej: Nequi o Daviplata al 300 123 4567. Enviar comprobante una vez confirmado."><?= e($tenant['info_pago_texto'] ?? '') ?></textarea>
            </div>
        </div>

        <!-- 5. HORARIOS DE ATENCIÓN -->
        <div class="card">
            <h2 style="font-size: 15px; font-weight: 700; color: var(--color-text-main); margin: 0 0 4px 0;">
                5. Horarios de Atención Semanal
            </h2>
            <p style="font-size: 13px; color: var(--color-text-muted); margin: 0 0 16px 0;">
                Días de apertura y franja horaria en que opera la cocina:
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

            <div style="display: flex; flex-direction: column; gap: 8px;">
                <?php foreach ($nombresDias as $numDia => $nombreDia): ?>
                    <?php $h = $horarios[$numDia] ?? ['abre' => '11:00', 'cierra' => '22:00', 'activo' => 0]; ?>
                    <div style="display: flex; align-items: center; gap: 16px; padding: 8px 14px; background: var(--color-bg-subtle); border-radius: var(--radius-sm); border: 1px solid var(--color-border-subtle); flex-wrap: wrap;">
                        <div style="width: 130px;">
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-weight: 600; font-size: 13.5px; color: var(--color-text-main);">
                                <input type="checkbox" name="dias[<?= $numDia ?>][activo]" value="1" <?= !empty($h['activo']) ? 'checked' : '' ?> style="width: 16px; height: 16px; accent-color: var(--color-brand);">
                                <?= $nombreDia ?>
                            </label>
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span style="font-size: 12.5px; color: var(--color-text-muted);">Abre:</span>
                            <input type="time" name="dias[<?= $numDia ?>][abre]" value="<?= e($h['abre']) ?>" class="form-control" style="width: 120px; padding: 4px 8px; font-variant-numeric: tabular-nums;">
                        </div>
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span style="font-size: 12.5px; color: var(--color-text-muted);">Cierra:</span>
                            <input type="time" name="dias[<?= $numDia ?>][cierra]" value="<?= e($h['cierra']) ?>" class="form-control" style="width: 120px; padding: 4px 8px; font-variant-numeric: tabular-nums;">
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- ACCIONES DE GUARDADO -->
        <div style="display: flex; justify-content: flex-end; gap: 12px; margin-bottom: 32px;">
            <button type="submit" class="btn btn-primary" style="padding: 10px 28px;">
                Guardar Configuración
            </button>
        </div>

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
