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

    <div class="stack gap-5">

        <!-- ═══ 1. MARCA Y APARIENCIA ═══ -->
        <div class="config-section">
            <div class="config-section-header">
                <div class="config-section-icon config-icon-palette">
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
                        <label for="fuente" class="form-label flex items-center justify-between">
                            <span>Tipografía del Menú Digital</span>
                            <span class="text-xs text-muted" id="fontCategoryBadge">Selección de marca</span>
                        </label>
                        <select id="fuente" name="fuente" class="form-control select-font-picker">
                            <optgroup label="Modernas &amp; Tecnológicas (Alta Legibilidad)">
                                <option value="Plus Jakarta Sans" data-category="Moderna" data-font="'Plus Jakarta Sans', sans-serif" <?= ($tenant['fuente'] ?? '') === 'Plus Jakarta Sans' ? 'selected' : '' ?>>Plus Jakarta Sans — Insignia, geométrica y ultra nítida</option>
                                <option value="Inter"             data-category="Moderna" data-font="'Inter', sans-serif"             <?= ($tenant['fuente'] ?? '') === 'Inter'             ? 'selected' : '' ?>>Inter — Estándar web, minimalista y neutral</option>
                                <option value="Outfit"            data-category="Moderna" data-font="'Outfit', sans-serif"            <?= ($tenant['fuente'] ?? '') === 'Outfit'            ? 'selected' : '' ?>>Outfit — Vanguardista y fresca</option>
                                <option value="DM Sans"           data-category="Moderna" data-font="'DM Sans', sans-serif"           <?= ($tenant['fuente'] ?? '') === 'DM Sans'           ? 'selected' : '' ?>>DM Sans — Humanista y contemporánea</option>
                            </optgroup>
                            <optgroup label="Urbanas &amp; Energéticas (Parrillas, Bares y Burgers)">
                                <option value="Oswald"            data-category="Urbana"  data-font="'Oswald', sans-serif"            <?= ($tenant['fuente'] ?? '') === 'Oswald'            ? 'selected' : '' ?>>Oswald — Condensada, imponente y rústica</option>
                                <option value="Montserrat"        data-category="Urbana"  data-font="'Montserrat', sans-serif"        <?= ($tenant['fuente'] ?? '') === 'Montserrat'        ? 'selected' : '' ?>>Montserrat — Sólida, urbana y con carácter</option>
                                <option value="Poppins"           data-category="Urbana"  data-font="'Poppins', sans-serif"           <?= ($tenant['fuente'] ?? '') === 'Poppins'           ? 'selected' : '' ?>>Poppins — Geométrica, juvenil y amigable</option>
                            </optgroup>
                            <optgroup label="Cálidas &amp; Artesanales (Cafeterías, Brunch y Panaderías)">
                                <option value="Fraunces"          data-category="Cálida"  data-font="'Fraunces', serif"               <?= ($tenant['fuente'] ?? '') === 'Fraunces'          ? 'selected' : '' ?>>Fraunces — Gourmet, cálida y de corte vintage</option>
                                <option value="Lora"              data-category="Cálida"  data-font="'Lora', serif"                   <?= ($tenant['fuente'] ?? '') === 'Lora'              ? 'selected' : '' ?>>Lora — Bistró, acogedora y orgánica</option>
                            </optgroup>
                            <optgroup label="Elegantes &amp; Clásicas (Fine Dining, Vinos y Autor)">
                                <option value="Playfair Display"  data-category="Elegante" data-font="'Playfair Display', serif"      <?= in_array(($tenant['fuente'] ?? ''), ['Playfair', 'Playfair Display'], true) ? 'selected' : '' ?>>Playfair Display — Clásica, editorial y distinguida</option>
                                <option value="Cormorant Garamond" data-category="Elegante" data-font="'Cormorant Garamond', serif"    <?= ($tenant['fuente'] ?? '') === 'Cormorant Garamond' ? 'selected' : '' ?>>Cormorant Garamond — Alta gastronomía y lujo</option>
                            </optgroup>
                            <optgroup label="Nativa del Dispositivo">
                                <option value="System"            data-category="Sistema" data-font="-apple-system, BlinkMacSystemFont, sans-serif" <?= ($tenant['fuente'] ?? '') === 'System' ? 'selected' : '' ?>>Sistema — Nativa de iOS / Android</option>
                            </optgroup>
                        </select>
                        <div class="font-live-preview-box" id="fontLivePreview" style="margin-top: 0.65rem; padding: 0.75rem 1rem; border: 1px solid #E2E8F0; border-radius: 0.5rem; background: #F8FAFC;">
                            <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.75rem; color: #64748B; margin-bottom: 0.35rem;">
                                <span>Muestra en vivo del menú:</span>
                                <strong id="previewFontLabel" style="color: var(--primary, #E85D04);">Plus Jakarta Sans</strong>
                            </div>
                            <div id="previewSampleText" style="font-size: 1.05rem; font-weight: 700; color: #0F172A; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                <?= e($tenant['nombre'] ?? 'Mesaly') ?> — Smash Burger Doble Carne &amp; Queso
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="descripcion" class="form-label">Descripción Corta o Eslogan</label>
                    <textarea id="descripcion" name="descripcion" class="form-control" rows="2" placeholder="Ej: Carnes al carbón, cortes premium y hamburguesas artesanales."><?= e($tenant['descripcion'] ?? '') ?></textarea>
                </div>

                <!-- Logo & Banner -->
                <div class="config-grid-2 mt-1">
                    <div>
                        <label class="form-label">Logo del Restaurante</label>
                        <input type="file" id="logo_input" name="logo" accept="image/jpeg,image/png,image/webp">
                        <?php $hasLogo = !empty($tenant['logo_path']); ?>
                        <div class="dropzone <?= $hasLogo ? 'has-preview' : '' ?>" id="dz_logo" data-input="logo_input" tabindex="0" role="button" aria-label="Subir logo del restaurante">
                            <div id="preview_wrapper_logo" class="w-full">
                                <img src="<?= $hasLogo ? e(app_url($tenant['logo_path'])) : '' ?>" 
                                     id="preview_logo" 
                                     class="dropzone-preview dropzone-preview-logo" 
                                     alt="Logo del restaurante">
                                
                                <div class="dropzone-placeholder" id="placeholder_logo">
                                    <div class="dz-icon"><?= icon('upload', 20) ?></div>
                                    <strong class="dz-label">Subir Logo</strong>
                                    <span class="dz-hint">JPG, PNG o WebP · máx 8 MB</span>
                                    <span class="btn btn-secondary btn-sm mt-2 no-events">
                                        Buscar archivo
                                    </span>
                                </div>

                                <div class="dropzone-change-btn" id="change_btn_logo">
                                    <span class="btn btn-secondary btn-sm no-events">
                                        <?= icon('upload', 14) ?>
                                        <span>Cambiar logo</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Banner de Portada</label>
                        <input type="file" id="banner_input" name="banner" accept="image/jpeg,image/png,image/webp">
                        <?php $hasBanner = !empty($tenant['banner_path']); ?>
                        <div class="dropzone <?= $hasBanner ? 'has-preview' : '' ?>" id="dz_banner" data-input="banner_input" tabindex="0" role="button" aria-label="Subir banner de portada">
                            <div id="preview_wrapper_banner" class="w-full">
                                <img src="<?= $hasBanner ? e(app_url($tenant['banner_path'])) : '' ?>" 
                                     id="preview_banner" 
                                     class="dropzone-preview dropzone-preview-banner" 
                                     alt="Banner de portada">
                                
                                <div class="dropzone-placeholder" id="placeholder_banner">
                                    <div class="dz-icon"><?= icon('upload', 20) ?></div>
                                    <strong class="dz-label">Subir Banner</strong>
                                    <span class="dz-hint">JPG, PNG o WebP · máx 8 MB</span>
                                    <span class="btn btn-secondary btn-sm mt-2 no-events">
                                        Buscar archivo
                                    </span>
                                </div>

                                <div class="dropzone-change-btn" id="change_btn_banner">
                                    <span class="btn btn-secondary btn-sm no-events">
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
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm fw-600 text-main">Paletas Rápidas</span>
                        <span class="text-xs text-muted">Haz clic para previsualizar</span>
                    </div>
                    <div class="palette-presets-list">
                        <button type="button" class="preset-btn btn-preset" data-p="#E85D04" data-s="#FFB703" data-f="#FFF7EA" data-t="#1E1B18">
                            <span class="preset-swatch preset-swatch-sazon"></span>Sazón
                        </button>
                        <button type="button" class="preset-btn btn-preset" data-p="#2A9D5C" data-s="#FFB703" data-f="#F6FBF4" data-t="#14281D">
                            <span class="preset-swatch preset-swatch-fresca"></span>Fresca
                        </button>
                        <button type="button" class="preset-btn btn-preset" data-p="#C1121F" data-s="#1E1B18" data-f="#FFFFFF" data-t="#1E1B18">
                            <span class="preset-swatch preset-swatch-parrilla"></span>Parrilla
                        </button>
                        <button type="button" class="preset-btn btn-preset" data-p="#6F4E37" data-s="#D4A373" data-f="#FAF3E8" data-t="#2B1B12">
                            <span class="preset-swatch preset-swatch-cafe"></span>Café
                        </button>
                        <button type="button" class="preset-btn btn-preset" data-p="#E5527A" data-s="#FFC8DD" data-f="#FFF5F8" data-t="#3A1F2B">
                            <span class="preset-swatch preset-swatch-dulce"></span>Dulce
                        </button>
                        <button type="button" class="preset-btn btn-preset" data-p="#0077B6" data-s="#90E0EF" data-f="#F4FBFE" data-t="#0B2A3A">
                            <span class="preset-swatch preset-swatch-mar"></span>Mar
                        </button>
                        <button type="button" class="preset-btn btn-preset" data-p="#FFB703" data-s="#E85D04" data-f="#16130F" data-t="#FFF7EA">
                            <span class="preset-swatch preset-swatch-noche"></span>Noche
                        </button>
                    </div>
                </div>

                <!-- Selectores de color -->
                <div class="config-grid-4 mt-4">
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
                <div class="color-preview-box" id="preview_box">
                    <span class="color-preview-label">Vista previa — así verán el botón tus clientes:</span>
                    <div class="flex items-center gap-4 flex-wrap mt-2">
                        <button type="button" id="preview_btn" class="btn btn-preview-sample">
                            Confirmar Pedido — $42.000
                        </button>
                        <span id="preview_text" class="preview-text-sample">
                            Churrasco al Carbón 350g
                        </span>
                    </div>
                </div>

            </div>
        </div>

        <!-- ═══ 2. CONTACTO ═══ -->
        <div class="config-section">
            <div class="config-section-header">
                <div class="config-section-icon config-icon-phone">
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
                            <input type="tel" id="whatsapp" name="whatsapp" class="form-control fw-600 tabular" value="<?= e($tenant['whatsapp']) ?>" placeholder="573001234567" required>
                        </div>
                        <span class="text-xs text-muted mt-1 block">
                            Con indicativo de país. Los pedidos llegan a este número.
                        </span>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Estado de Apertura</label>
                        <label class="toggle-option-card">
                            <input type="checkbox" name="recibiendo_pedidos" value="1" <?= !empty($tenant['recibiendo_pedidos']) ? 'checked' : '' ?> class="form-checkbox">
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
                <div class="config-section-icon config-icon-truck">
                    <?= icon('truck', 20) ?>
                </div>
                <div>
                    <h2 class="config-section-title">Modalidades de Pedido Aceptadas</h2>
                    <p class="config-section-desc">Canales de atención que ofreces a tus comensales</p>
                </div>
            </div>
            <div class="config-section-body">
                <div class="modalities-grid modalities-grid-2">
                    <label class="modality-card">
                        <input type="checkbox" name="acepta_mesa" value="1" <?= !empty($tenant['acepta_mesa']) ? 'checked' : '' ?>>
                        <div class="modality-card-body">
                            <div class="modality-card-icon"><?= icon('menu', 20) ?></div>
                            <div>
                                <strong class="modality-card-title">Mesa (QR)</strong>
                                <p class="modality-card-desc">El comensal escanea el QR en su mesa y comanda directamente a cocina.</p>
                            </div>
                        </div>
                    </label>

                    <label class="modality-card">
                        <input type="checkbox" name="acepta_domicilio" value="1" id="checkbox_acepta_domicilio" <?= !empty($tenant['acepta_domicilio']) ? 'checked' : '' ?>>
                        <div class="modality-card-body">
                            <div class="modality-card-icon"><?= icon('truck', 20) ?></div>
                            <div>
                                <strong class="modality-card-title">Domicilio</strong>
                                <p class="modality-card-desc">Entrega a la dirección del cliente con cálculo automático de flete.</p>
                            </div>
                        </div>
                    </label>
                </div>

                <!-- ══ Tarifas y Condiciones de Domicilio ══ -->
                <div class="delivery-rates-card mt-5">
                    <div class="delivery-rates-badge-header">
                        <div class="delivery-rates-badge-icon">
                            <?= icon('motorcycle', 22) ?>
                        </div>
                        <div>
                            <h3 class="delivery-rates-title">Tarifas y Condiciones de Domicilio</h3>
                            <p class="delivery-rates-desc">Especifica cuánto cobras por el envío y el monto mínimo de consumo para despachar a domicilio.</p>
                        </div>
                    </div>

                    <div class="config-grid-2 mt-4">
                        <div class="form-group mb-0">
                            <label for="costo_envio" class="form-label font-bold text-main">
                                Costo de Domicilio / Envío (COP)
                            </label>
                            <div class="input-currency-wrapper">
                                <span class="input-currency-symbol">$</span>
                                <input type="number" 
                                       id="costo_envio" 
                                       name="costo_envio" 
                                       class="form-control input-currency tabular font-bold text-lg" 
                                       value="<?= (int)($tenant['costo_envio'] ?? 0) ?>" 
                                       min="0" 
                                       step="500" 
                                       placeholder="Ej: 5000">
                                <span class="input-currency-code">COP</span>
                            </div>
                            <p class="form-hint">
                                Tarifa de transporte que se añadirá automáticamente a la cuenta cuando el comensal seleccione <strong>Domicilio</strong>.
                            </p>
                        </div>

                        <div class="form-group mb-0">
                            <label for="pedido_minimo" class="form-label font-bold text-main">
                                Pedido Mínimo para Domicilio (COP)
                            </label>
                            <div class="input-currency-wrapper">
                                <span class="input-currency-symbol">$</span>
                                <input type="number" 
                                       id="pedido_minimo" 
                                       name="pedido_minimo" 
                                       class="form-control input-currency tabular font-bold text-lg" 
                                       value="<?= (int)($tenant['pedido_minimo'] ?? 0) ?>" 
                                       min="0" 
                                       step="500" 
                                       placeholder="0">
                                <span class="input-currency-code">COP</span>
                            </div>
                            <p class="form-hint">
                                Monto mínimo en productos requerido para habilitar el despacho a domicilio (deja en <strong>0</strong> si no requieres mínimo).
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ═══ 4. HORARIOS ═══ -->
        <div class="config-section">
            <div class="config-section-header">
                <div class="config-section-icon config-icon-clock">
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
                <div class="stack gap-1">
                    <?php foreach ($nombresDias as $numDia => $nombreDia): ?>
                        <?php $h = $horarios[$numDia] ?? ['abre' => '11:00', 'cierra' => '22:00', 'activo' => 0]; ?>
                        <div class="horario-row">
                            <label class="horario-day">
                                <input type="checkbox" name="dias[<?= $numDia ?>][activo]" value="1" <?= !empty($h['activo']) ? 'checked' : '' ?> class="form-checkbox-sm">
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

        <!-- ═══ 6. SOPORTE OFICIAL MESALY ═══ -->
        <div class="config-section config-support-card mb-6">
            <div class="config-section-header items-center justify-between flex-wrap gap-4">
                <div class="flex items-center gap-3">
                    <div class="config-section-icon config-icon-whatsapp">
                        <?= icon('whatsapp', 22) ?>
                    </div>
                    <div>
                        <h2 class="config-section-title">Línea Oficial de Soporte Mesaly</h2>
                        <p class="config-section-desc">Atención técnica prioritaria y acompañamiento en Colombia: <strong>+57 322 899 1339</strong></p>
                    </div>
                </div>
                <div>
                    <a href="<?= e(support_whatsapp_url('Hola soporte Mesaly, me comunico desde el restaurante ' . ($tenant['nombre'] ?? ''))) ?>" 
                       target="_blank" 
                       rel="noopener" 
                       class="btn btn-whatsapp config-support-btn">
                        <?= icon('whatsapp', 18) ?>
                        <span class="fw-700">Chatear con Soporte por WhatsApp</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- ═══ Guardar ═══ -->
        <div class="flex justify-end gap-3 mb-6">
            <button type="submit" class="btn btn-primary btn-submit-action">
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
    if (/^#[0-9a-fA-F]{6}$/.test(p) && btn) {
        btn.style.setProperty('--preview-bg', p);
        btn.style.setProperty('--preview-color', getLuminance(p) > 0.55 ? '#1E1B18' : '#FFFFFF');
    }
    if (/^#[0-9a-fA-F]{6}$/.test(f) && box) {
        box.style.setProperty('--preview-box-bg', f);
    }
    if (/^#[0-9a-fA-F]{6}$/.test(t) && txt) {
        txt.style.setProperty('--preview-text-color', t);
    }
}
actualizarPreview();

// Feedback visual al activar o desactivar Domicilios
const chkDomicilio = document.getElementById('checkbox_acepta_domicilio');
const deliveryRatesCard = document.querySelector('.delivery-rates-card');
function updateDeliveryCardState() {
    if (!deliveryRatesCard || !chkDomicilio) return;
    if (chkDomicilio.checked) {
        deliveryRatesCard.classList.remove('is-disabled');
    } else {
        deliveryRatesCard.classList.add('is-disabled');
    }
}
if (chkDomicilio) {
    chkDomicilio.addEventListener('change', updateDeliveryCardState);
    updateDeliveryCardState();
}

// Selector Dinámico de Tipografía con Previsualización
const selectFuente = document.getElementById('fuente');
const previewSampleText = document.getElementById('previewSampleText');
const previewFontLabel = document.getElementById('previewFontLabel');
const fontCategoryBadge = document.getElementById('fontCategoryBadge');

function updateFontPreview() {
    if (!selectFuente || !previewSampleText) return;
    const opt = selectFuente.options[selectFuente.selectedIndex];
    if (!opt) return;
    const fontVal = opt.dataset.font || opt.value;
    const cat = opt.dataset.category || '';
    previewSampleText.style.fontFamily = fontVal;
    if (previewFontLabel) previewFontLabel.textContent = opt.value;
    if (fontCategoryBadge && cat) fontCategoryBadge.textContent = 'Estilo: ' + cat;
    
    // También aplicamos la fuente a la muestra del preview de colores
    const txtPreview = document.getElementById('preview_text');
    if (txtPreview) txtPreview.style.fontFamily = fontVal;
}
if (selectFuente) {
    selectFuente.addEventListener('change', updateFontPreview);
    updateFontPreview();
}
</script>