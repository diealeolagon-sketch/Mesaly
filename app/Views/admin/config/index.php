<div class="content-header">
    <div>
        <h1 class="page-title">Configuración del Restaurante</h1>
        <p class="page-subtitle">
            Personaliza la identidad visual, métodos de entrega, WhatsApp y horarios de atención.
        </p>
    </div>
    <div class="content-actions">
        <?php if (!empty($tenant['slug'])): ?>
        <a href="<?= e(app_url('/' . $tenant['slug'])) ?>" target="_blank" class="btn btn-secondary btn-sm">
            <?= icon('eye', 14) ?>
            <span>Ver Menú</span>
        </a>
        <?php endif; ?>
    </div>
</div>

<form method="POST" action="<?= e(app_url('/admin/configuracion')) ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div style="display: flex; flex-direction: column; gap: 20px;">

        <!-- ═══ 1. MARCA Y APARIENCIA ═══ -->
        <div class="config-section">
            <div class="config-section-header">
                <div class="config-section-icon" style="background: #FFF1E6; color: var(--color-brand);">
                    <?= icon('palette', 20) ?>
                </div>
                <div>
                    <h2 class="config-section-title">Marca y Apariencia Visual</h2>
                    <p class="config-section-desc">Colores y logotipos que ven tus clientes en la carta digital</p>
                </div>
            </div>
            <div class="config-section-body">

                <div class="config-grid-2">
                    <div class="form-group">
                        <label for="nombre" class="form-label">Nombre Comercial del Restaurante *</label>
                        <input type="text" id="nombre" name="nombre" class="form-control" value="<?= e($tenant['nombre']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="fuente" class="form-label">Tipografía del Menú</label>
                        <select id="fuente" name="fuente" class="form-control">
                            <option value="Inter"     <?= ($tenant['fuente'] ?? '') === 'Inter'     ? 'selected' : '' ?>>Inter (Limpia y moderna)</option>
                            <option value="Poppins"   <?= ($tenant['fuente'] ?? '') === 'Poppins'   ? 'selected' : '' ?>>Poppins (Geométrica)</option>
                            <option value="Outfit"    <?= ($tenant['fuente'] ?? '') === 'Outfit'    ? 'selected' : '' ?>>Outfit (Contemporánea)</option>
                            <option value="Playfair"  <?= ($tenant['fuente'] ?? '') === 'Playfair'  ? 'selected' : '' ?>>Playfair (Elegante)</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="descripcion" class="form-label">Descripción Corta o Eslogan</label>
                    <textarea id="descripcion" name="descripcion" class="form-control" rows="2" placeholder="Ej: Carnes al carbón, cortes premium y hamburguesas artesanales."><?= e($tenant['descripcion'] ?? '') ?></textarea>
                </div>

                <!-- Logo & Banner -->
                <div class="config-grid-2" style="margin-top: 4px;">
                    <div>
                        <label class="form-label">Logo del Restaurante</label>
                        <input type="file" id="logo_input" name="logo" accept="image/jpeg,image/png,image/webp" style="display: none;">
                        <div class="dropzone" id="dz_logo" data-input="logo_input" tabindex="0" role="button" aria-label="Subir logo del restaurante">
                            <div id="preview_wrapper_logo" style="width: 100%;">
                                <?php $hasLogo = !empty($tenant['logo_path']); ?>
                                <img src="<?= $hasLogo ? e(app_url($tenant['logo_path'])) : '' ?>" 
                                     id="preview_logo" 
                                     class="dropzone-preview" 
                                     alt="Logo del restaurante" 
                                     style="max-height: 80px; object-fit: contain; <?= $hasLogo ? 'display: block;' : 'display: none;' ?>">
                                
                                <div class="dropzone-placeholder" id="placeholder_logo" style="<?= $hasLogo ? 'display: none;' : '' ?>">
                                    <div class="dz-icon"><?= icon('upload', 20) ?></div>
                                    <strong class="dz-label">Subir Logo</strong>
                                    <span class="dz-hint">JPG, PNG o WebP · máx 8 MB</span>
                                    <span class="btn btn-secondary btn-sm" style="margin-top: 8px; pointer-events: none;">
                                        Buscar archivo
                                    </span>
                                </div>

                                <div class="dropzone-change-btn" id="change_btn_logo" style="margin-top: 10px; <?= $hasLogo ? '' : 'display: none;' ?>">
                                    <span class="btn btn-secondary btn-sm" style="pointer-events: none;">
                                        <?= icon('upload', 14) ?>
                                        <span>Cambiar logo</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Banner de Portada</label>
                        <input type="file" id="banner_input" name="banner" accept="image/jpeg,image/png,image/webp" style="display: none;">
                        <div class="dropzone" id="dz_banner" data-input="banner_input" tabindex="0" role="button" aria-label="Subir banner de portada">
                            <div id="preview_wrapper_banner" style="width: 100%;">
                                <?php $hasBanner = !empty($tenant['banner_path']); ?>
                                <img src="<?= $hasBanner ? e(app_url($tenant['banner_path'])) : '' ?>" 
                                     id="preview_banner" 
                                     class="dropzone-preview" 
                                     alt="Banner de portada" 
                                     style="max-height: 80px; object-fit: cover; <?= $hasBanner ? 'display: block;' : 'display: none;' ?>">
                                
                                <div class="dropzone-placeholder" id="placeholder_banner" style="<?= $hasBanner ? 'display: none;' : '' ?>">
                                    <div class="dz-icon"><?= icon('upload', 20) ?></div>
                                    <strong class="dz-label">Subir Banner</strong>
                                    <span class="dz-hint">JPG, PNG o WebP · máx 8 MB</span>
                                    <span class="btn btn-secondary btn-sm" style="margin-top: 8px; pointer-events: none;">
                                        Buscar archivo
                                    </span>
                                </div>

                                <div class="dropzone-change-btn" id="change_btn_banner" style="margin-top: 10px; <?= $hasBanner ? '' : 'display: none;' ?>">
                                    <span class="btn btn-secondary btn-sm" style="pointer-events: none;">
                                        <?= icon('upload', 14) ?>
                                        <span>Cambiar portada</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Paletas predefinidas -->
                <div class="palette-presets">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                        <span style="font-size: 13px; font-weight: 600; color: var(--color-text-main);">Paletas Rápidas</span>
                        <span style="font-size: 12px; color: var(--color-text-muted);">Haz clic para previsualizar</span>
                    </div>
                    <div class="palette-presets-list">
                        <button type="button" class="preset-btn btn-preset" data-p="#E85D04" data-s="#FFB703" data-f="#FFF7EA" data-t="#1E1B18">
                            <span class="preset-swatch" style="background: #E85D04;"></span>Sazón
                        </button>
                        <button type="button" class="preset-btn btn-preset" data-p="#2A9D5C" data-s="#FFB703" data-f="#F6FBF4" data-t="#14281D">
                            <span class="preset-swatch" style="background: #2A9D5C;"></span>Fresca
                        </button>
                        <button type="button" class="preset-btn btn-preset" data-p="#C1121F" data-s="#1E1B18" data-f="#FFFFFF" data-t="#1E1B18">
                            <span class="preset-swatch" style="background: #C1121F;"></span>Parrilla
                        </button>
                        <button type="button" class="preset-btn btn-preset" data-p="#6F4E37" data-s="#D4A373" data-f="#FAF3E8" data-t="#2B1B12">
                            <span class="preset-swatch" style="background: #6F4E37;"></span>Café
                        </button>
                        <button type="button" class="preset-btn btn-preset" data-p="#E5527A" data-s="#FFC8DD" data-f="#FFF5F8" data-t="#3A1F2B">
                            <span class="preset-swatch" style="background: #E5527A;"></span>Dulce
                        </button>
                        <button type="button" class="preset-btn btn-preset" data-p="#0077B6" data-s="#90E0EF" data-f="#F4FBFE" data-t="#0B2A3A">
                            <span class="preset-swatch" style="background: #0077B6;"></span>Mar
                        </button>
                        <button type="button" class="preset-btn btn-preset" data-p="#FFB703" data-s="#E85D04" data-f="#16130F" data-t="#FFF7EA">
                            <span class="preset-swatch" style="background: #16130F; border: 1px solid #aaa;"></span>Noche
                        </button>
                    </div>
                </div>

                <!-- Selectores de color -->
                <div class="config-grid-4" style="margin-top: 16px;">
                    <div class="form-group">
                        <label for="color_primario" class="form-label">Color Primario *</label>
                        <div class="color-input-wrap">
                            <input type="color" id="picker_primario" value="<?= e($tenant['color_primario'] ?? '#E85D04') ?>" class="color-picker" data-sync="color_primario">
                            <input type="text" id="color_primario" name="color_primario" class="form-control color-text-input" value="<?= e($tenant['color_primario'] ?? '#E85D04') ?>" pattern="^#[0-9a-fA-F]{6}$" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="color_secundario" class="form-label">Color Secundario *</label>
                        <div class="color-input-wrap">
                            <input type="color" id="picker_secundario" value="<?= e($tenant['color_secundario'] ?? '#FFB703') ?>" class="color-picker" data-sync="color_secundario">
                            <input type="text" id="color_secundario" name="color_secundario" class="form-control color-text-input" value="<?= e($tenant['color_secundario'] ?? '#FFB703') ?>" pattern="^#[0-9a-fA-F]{6}$" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="color_fondo" class="form-label">Fondo del Menú *</label>
                        <div class="color-input-wrap">
                            <input type="color" id="picker_fondo" value="<?= e($tenant['color_fondo'] ?? '#FFF7EA') ?>" class="color-picker" data-sync="color_fondo">
                            <input type="text" id="color_fondo" name="color_fondo" class="form-control color-text-input" value="<?= e($tenant['color_fondo'] ?? '#FFF7EA') ?>" pattern="^#[0-9a-fA-F]{6}$" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="color_texto" class="form-label">Color de Texto *</label>
                        <div class="color-input-wrap">
                            <input type="color" id="picker_texto" value="<?= e($tenant['color_texto'] ?? '#1E1B18') ?>" class="color-picker" data-sync="color_texto">
                            <input type="text" id="color_texto" name="color_texto" class="form-control color-text-input" value="<?= e($tenant['color_texto'] ?? '#1E1B18') ?>" pattern="^#[0-9a-fA-F]{6}$" required>
                        </div>
                    </div>
                </div>

                <!-- Vista previa -->
                <div class="color-preview-box" id="preview_box" style="background: <?= e($tenant['color_fondo'] ?? '#FFF7EA') ?>;">
                    <span class="color-preview-label">Vista previa — así verán el botón tus clientes:</span>
                    <div style="display: flex; gap: 16px; align-items: center; flex-wrap: wrap; margin-top: 10px;">
                        <button type="button" id="preview_btn" class="btn" style="background: <?= e($tenant['color_primario'] ?? '#E85D04') ?>; color: #fff; padding: 10px 24px; font-weight: 700; font-size: 14px;">
                            Confirmar Pedido — $42.000
                        </button>
                        <span id="preview_text" style="color: <?= e($tenant['color_texto'] ?? '#1E1B18') ?>; font-weight: 600; font-size: 14px;">
                            Churrasco al Carbón 350g
                        </span>
                    </div>
                </div>

            </div>
        </div>

        <!-- ═══ 2. CONTACTO ═══ -->
        <div class="config-section">
            <div class="config-section-header">
                <div class="config-section-icon" style="background: #E6F9EF; color: #059669;">
                    <?= icon('phone', 20) ?>
                </div>
                <div>
                    <h2 class="config-section-title">Contacto y Recepción de Pedidos</h2>
                    <p class="config-section-desc">Número de WhatsApp y estado de apertura en tiempo real</p>
                </div>
            </div>
            <div class="config-section-body">
                <div class="config-grid-2">
                    <div class="form-group">
                        <label for="whatsapp" class="form-label">WhatsApp Receptor de Pedidos *</label>
                        <div class="input-prefix-wrap">
                            <span class="input-prefix">+</span>
                            <input type="tel" id="whatsapp" name="whatsapp" class="form-control" value="<?= e($tenant['whatsapp']) ?>" placeholder="573001234567" required style="font-weight: 600; font-variant-numeric: tabular-nums; border-radius: 0 var(--radius-sm) var(--radius-sm) 0;">
                        </div>
                        <span style="font-size: 12px; color: var(--color-text-muted); margin-top: 4px; display: block;">
                            Con indicativo de país. Los pedidos llegan a este número.
                        </span>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Estado de Apertura</label>
                        <label class="toggle-option-card">
                            <input type="checkbox" name="recibiendo_pedidos" value="1" <?= !empty($tenant['recibiendo_pedidos']) ? 'checked' : '' ?> style="width: 18px; height: 18px; accent-color: var(--color-brand);">
                            <div>
                                <span class="toggle-option-title">Abierto para recibir pedidos</span>
                                <span class="toggle-option-desc">Si se desmarca, el menú se ve pero no acepta pedidos.</span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- ═══ 3. MODALIDADES ═══ -->
        <div class="config-section">
            <div class="config-section-header">
                <div class="config-section-icon" style="background: #EDF3FF; color: #3B82F6;">
                    <?= icon('truck', 20) ?>
                </div>
                <div>
                    <h2 class="config-section-title">Modalidades de Pedido Aceptadas</h2>
                    <p class="config-section-desc">Canales de atención que ofreces a tus comensales</p>
                </div>
            </div>
            <div class="config-section-body">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 14px;">
                    <label class="modality-card">
                        <input type="checkbox" name="acepta_mesa" value="1" <?= !empty($tenant['acepta_mesa']) ? 'checked' : '' ?>>
                        <div class="modality-card-body">
                            <div class="modality-card-icon"><?= icon('menu', 20) ?></div>
                            <div>
                                <strong class="modality-card-title">Mesa (QR)</strong>
                                <p class="modality-card-desc">El cliente escanea el QR en su mesa y pide desde ahí.</p>
                            </div>
                        </div>
                    </label>

                    <label class="modality-card">
                        <input type="checkbox" name="acepta_domicilio" value="1" <?= !empty($tenant['acepta_domicilio']) ? 'checked' : '' ?>>
                        <div class="modality-card-body">
                            <div class="modality-card-icon"><?= icon('truck', 20) ?></div>
                            <div>
                                <strong class="modality-card-title">Domicilio</strong>
                                <div style="margin-top: 8px; display: flex; gap: 10px;">
                                    <div class="form-group" style="margin: 0; flex: 1;">
                                        <label class="form-label" style="font-size: 11px;">Envío (COP)</label>
                                        <input type="number" name="costo_envio" class="form-control" value="<?= (int)($tenant['costo_envio'] ?? 0) ?>" min="0" step="100" style="font-variant-numeric: tabular-nums; padding: 6px 10px;">
                                    </div>
                                    <div class="form-group" style="margin: 0; flex: 1;">
                                        <label class="form-label" style="font-size: 11px;">Mínimo (COP)</label>
                                        <input type="number" name="pedido_minimo" class="form-control" value="<?= (int)($tenant['pedido_minimo'] ?? 0) ?>" min="0" step="100" style="font-variant-numeric: tabular-nums; padding: 6px 10px;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </label>

                    <label class="modality-card">
                        <input type="checkbox" name="acepta_recoger" value="1" <?= !empty($tenant['acepta_recoger']) ? 'checked' : '' ?>>
                        <div class="modality-card-body">
                            <div class="modality-card-icon"><?= icon('store', 20) ?></div>
                            <div>
                                <strong class="modality-card-title">Para Llevar</strong>
                                <p class="modality-card-desc">El cliente pide para recoger en tu local.</p>
                            </div>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <!-- ═══ 4. MEDIOS DE PAGO ═══ -->
        <div class="config-section">
            <div class="config-section-header">
                <div class="config-section-icon" style="background: #F3ECFF; color: #7C3AED;">
                    <?= icon('star', 20) ?>
                </div>
                <div>
                    <h2 class="config-section-title">Medios de Pago Informados</h2>
                    <p class="config-section-desc">Mesaly no procesa dinero. Estos datos van en el mensaje de pedido.</p>
                </div>
            </div>
            <div class="config-section-body">
                <div style="display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 16px;">
                    <?php foreach (['efectivo' => 'Efectivo', 'transferencia' => 'Nequi / Daviplata / Bancolombia', 'datafono' => 'Datáfono / Tarjeta'] as $val => $label): ?>
                    <label class="payment-chip">
                        <input type="checkbox" name="metodos_pago[]" value="<?= $val ?>" <?= in_array($val, $metodos, true) ? 'checked' : '' ?>>
                        <span><?= $label ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>
                <div class="form-group">
                    <label for="info_pago_texto" class="form-label">Datos e Instrucciones de Pago</label>
                    <textarea id="info_pago_texto" name="info_pago_texto" class="form-control" rows="2" placeholder="Ej: Nequi o Daviplata al 300 123 4567. Enviar comprobante una vez confirmado."><?= e($tenant['info_pago_texto'] ?? '') ?></textarea>
                </div>
            </div>
        </div>

        <!-- ═══ 5. HORARIOS ═══ -->
        <div class="config-section">
            <div class="config-section-header">
                <div class="config-section-icon" style="background: #FFF8E6; color: #D97706;">
                    <?= icon('clock', 20) ?>
                </div>
                <div>
                    <h2 class="config-section-title">Horarios de Atención Semanal</h2>
                    <p class="config-section-desc">Días de apertura y franja horaria en que opera la cocina</p>
                </div>
            </div>
            <div class="config-section-body">
                <?php
                $nombresDias = [0=>'Domingo',1=>'Lunes',2=>'Martes',3=>'Miércoles',4=>'Jueves',5=>'Viernes',6=>'Sábado'];
                ?>
                <div style="display: flex; flex-direction: column; gap: 6px;">
                    <?php foreach ($nombresDias as $numDia => $nombreDia): ?>
                        <?php $h = $horarios[$numDia] ?? ['abre' => '11:00', 'cierra' => '22:00', 'activo' => 0]; ?>
                        <div class="horario-row">
                            <label class="horario-day">
                                <input type="checkbox" name="dias[<?= $numDia ?>][activo]" value="1" <?= !empty($h['activo']) ? 'checked' : '' ?> style="width: 15px; height: 15px; accent-color: var(--color-brand);">
                                <span><?= $nombreDia ?></span>
                            </label>
                            <label class="horario-time-wrap">
                                <span class="horario-label">Abre</span>
                                <input type="time" name="dias[<?= $numDia ?>][abre]" value="<?= e($h['abre']) ?>" class="form-control horario-input">
                            </label>
                            <label class="horario-time-wrap">
                                <span class="horario-label">Cierra</span>
                                <input type="time" name="dias[<?= $numDia ?>][cierra]" value="<?= e($h['cierra']) ?>" class="form-control horario-input">
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- ═══ Guardar ═══ -->
        <div style="display: flex; justify-content: flex-end; gap: 12px; margin-bottom: 32px;">
            <button type="submit" class="btn btn-primary" style="padding: 11px 32px; font-size: 14px; font-weight: 700;">
                <?= icon('check', 16) ?>
                <span>Guardar Configuración</span>
            </button>
        </div>

    </div>
</form>

<script nonce="<?= e($cspNonce) ?>">
function syncColor(targetId, val) {
    const el = document.getElementById(targetId);
    if (el) el.value = val;
    actualizarPreview();
}
function aplicarPreset(p, s, f, t) {
    document.getElementById('color_primario').value   = p; document.getElementById('picker_primario').value   = p;
    document.getElementById('color_secundario').value = s; document.getElementById('picker_secundario').value = s;
    document.getElementById('color_fondo').value      = f; document.getElementById('picker_fondo').value      = f;
    document.getElementById('color_texto').value      = t; document.getElementById('picker_texto').value      = t;
    actualizarPreview();
}

document.querySelectorAll('.btn-preset').forEach(btn => {
    btn.addEventListener('click', function() {
        aplicarPreset(this.dataset.p, this.dataset.s, this.dataset.f, this.dataset.t);
    });
});

document.querySelectorAll('.color-picker').forEach(picker => {
    picker.addEventListener('input', function() {
        syncColor(this.dataset.sync, this.value);
    });
});

document.querySelectorAll('.color-text-input').forEach(inp => {
    inp.addEventListener('input', function() {
        const pickerId = 'picker_' + this.id.replace('color_', '');
        const picker = document.getElementById(pickerId);
        if (picker && /^#[0-9a-fA-F]{6}$/i.test(this.value)) {
            picker.value = this.value;
        }
        actualizarPreview();
    });
});
function getLuminance(hex) {
    hex = hex.replace('#',''); if (hex.length===3) hex = hex[0]+hex[0]+hex[1]+hex[1]+hex[2]+hex[2];
    return (0.299*parseInt(hex.substr(0,2),16)/255 + 0.587*parseInt(hex.substr(2,2),16)/255 + 0.114*parseInt(hex.substr(4,2),16)/255);
}
function actualizarPreview() {
    const p = document.getElementById('color_primario').value;
    const f = document.getElementById('color_fondo').value;
    const t = document.getElementById('color_texto').value;
    const btn = document.getElementById('preview_btn');
    const txt = document.getElementById('preview_text');
    const box = document.getElementById('preview_box');
    if (/^#[0-9a-fA-F]{6}$/.test(p)) { btn.style.backgroundColor = p; btn.style.color = getLuminance(p) > 0.55 ? '#1E1B18' : '#FFFFFF'; }
    if (/^#[0-9a-fA-F]{6}$/.test(f)) { box.style.backgroundColor = f; }
    if (/^#[0-9a-fA-F]{6}$/.test(t)) { txt.style.color = t; }
}
actualizarPreview();
</script>
