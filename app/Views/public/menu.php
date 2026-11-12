<?php
/**
 * Vista del Menú Digital Público (Comensales)
 *
 * @var array $restaurante
 * @var int|null $mesaNumero
 * @var array $estadoHorario
 * @var array $metodosPago
 * @var array $categorias
 * @var array $productosPorCategoria
 * @var array $destacados
 */

$tieneBanner = !empty($restaurante['banner_path']);
$tieneLogo = !empty($restaurante['logo_path']);

// Determinar modalidad activa por defecto:
// Si escaneó QR o tiene mesa en sesión, siempre 'mesa'.
// Si no, si el restaurante acepta mesa -> 'mesa'.
// Si solo acepta domicilio -> 'domicilio'.
// Si solo acepta recoger -> 'recoger'.
$modalidadDefault = 'mesa';
if ($mesaNumero !== null) {
    $modalidadDefault = 'mesa';
} elseif (!empty($restaurante['acepta_mesa'])) {
    $modalidadDefault = 'mesa';
} elseif (!empty($restaurante['acepta_domicilio'])) {
    $modalidadDefault = 'domicilio';
} elseif (!empty($restaurante['acepta_recoger'])) {
    $modalidadDefault = 'recoger';
}
?>

<div class="menu-container" id="menuApp">

    <!-- 1. HERO HEADER DEL RESTAURANTE -->
    <header class="menu-hero <?= $tieneBanner ? 'has-banner' : 'no-banner' ?>">
        <div class="menu-hero-banner <?= $tieneBanner ? '' : 'default-gradient' ?>">
            <?php if ($tieneBanner): ?>
                <img src="<?= e(app_url($restaurante['banner_path'])) ?>" alt="Portada de <?= e($restaurante['nombre']) ?>" class="menu-hero-banner-img" loading="eager">
            <?php endif; ?>
        </div>

        <div class="menu-hero-content">
            <div class="menu-brand-header">
                <?php if ($tieneLogo): ?>
                    <img src="<?= e(app_url($restaurante['logo_path'])) ?>" alt="<?= e($restaurante['nombre']) ?>" class="menu-logo">
                <?php else: ?>
                    <div class="menu-logo-placeholder">
                        <?= e(strtoupper(substr($restaurante['nombre'], 0, 2))) ?>
                    </div>
                <?php endif; ?>

                <div class="menu-brand-info">
                    <div class="menu-brand-top-row">
                        <h1 class="menu-brand-title"><?= e($restaurante['nombre']) ?></h1>
                        <button type="button" class="menu-header-cart-btn" id="btnHeaderCart" title="Ver mi pedido">
                            <span class="header-cart-icon"><?= icon('cart', 18) ?></span>
                            <span class="header-cart-label">Mi Pedido</span>
                            <span class="header-cart-badge" id="headerCartBadge">0</span>
                        </button>
                    </div>
                    
                    <div class="menu-status-row">
                        <?php if (!empty($restaurante['recibiendo_pedidos'])): ?>
                            <?php if ($estadoHorario['abierto']): ?>
                                <span class="badge-status open">
                                    <span class="status-dot"></span> <?= e($estadoHorario['mensaje']) ?>
                                </span>
                            <?php else: ?>
                                <span class="badge-status closed">
                                    <span class="status-dot"></span> <?= e($estadoHorario['mensaje']) ?>
                                </span>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="badge-status paused">
                                <span class="status-dot"></span> No recibe pedidos ahora
                            </span>
                        <?php endif; ?>

                        <!-- Modalidades activas -->
                        <div class="modalities-chips">
                            <?php if (!empty($restaurante['acepta_mesa'])): ?>
                                <button type="button" class="chip-modality btn-chip-modality <?= ($modalidadDefault === 'mesa') ? 'active' : '' ?>" data-modality="mesa" title="Seleccionar Atención en Mesa">
                                    <?= icon('cutlery', 13) ?>
                                    <span>Mesa</span>
                                </button>
                            <?php endif; ?>
                            <?php if (!empty($restaurante['acepta_domicilio'])): ?>
                                <button type="button" class="chip-modality btn-chip-modality <?= ($modalidadDefault === 'domicilio') ? 'active' : '' ?>" data-modality="domicilio" title="Seleccionar Servicio a Domicilio">
                                    <?= icon('motorcycle', 13) ?>
                                    <span>Domicilio</span>
                                </button>
                            <?php endif; ?>
                            <?php if (!empty($restaurante['acepta_recoger'])): ?>
                                <button type="button" class="chip-modality btn-chip-modality <?= ($modalidadDefault === 'recoger') ? 'active' : '' ?>" data-modality="recoger" title="Seleccionar Para Llevar">
                                    <?= icon('shopping-bag', 13) ?>
                                    <span>Recoger</span>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (!empty($restaurante['descripcion'])): ?>
                <p class="menu-brand-desc"><?= e($restaurante['descripcion']) ?></p>
            <?php endif; ?>

            <!-- Detección de Mesa Activa -->
            <?php if ($mesaNumero !== null): ?>
                <div class="menu-table-banner">
                    <div class="table-info">
                        <span class="table-icon"><?= icon('cutlery', 20) ?></span>
                        <div>
                            <strong>Estás ordenando para la Mesa #<?= e((string)$mesaNumero) ?></strong>
                            <small>Tus pedidos se asociarán directamente a esta mesa</small>
                        </div>
                    </div>
                    <form action="<?= e(app_url('/' . $restaurante['slug'] . '/mesa/limpiar')) ?>" method="POST">
                        <button type="submit" class="btn-unlink-table" title="Desvincular mesa">No estoy en mesa</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </header>

    <!-- 2. NAVEGACIÓN STICKY DE CATEGORÍAS (FILTRADO DINÁMICO) -->
    <nav class="menu-category-nav" id="categoryNav">
        <div class="category-nav-scroll">
            <button type="button" class="category-nav-pill active" data-cat="all">
                <?= icon('cutlery', 14) ?> Todos los Platos
            </button>

            <?php if (!empty($destacados)): ?>
                <button type="button" class="category-nav-pill" data-cat="cat-destacados">
                    <?= icon('sparkles', 13) ?> Populares (<?= count($destacados) ?>)
                </button>
            <?php endif; ?>

            <?php foreach ($categorias as $cat): ?>
                <?php $cant = count($productosPorCategoria[$cat['id']] ?? []); ?>
                <button type="button" class="category-nav-pill" data-cat="cat-<?= $cat['id'] ?>">
                    <?= e($cat['nombre']) ?> (<?= $cant ?>)
                </button>
            <?php endforeach; ?>
        </div>
    </nav>

    <!-- 3. CATÁLOGO DE PRODUCTOS -->
    <main class="menu-catalog-main">

        <!-- Destacados / Populares -->
        <?php if (!empty($destacados)): ?>
            <section class="menu-section" id="cat-destacados">
                <div class="menu-section-header">
                    <h2 class="menu-section-title"><?= icon('sparkles', 16) ?> Platos Populares</h2>
                    <span class="menu-section-count"><?= count($destacados) ?> recomendados</span>
                </div>

                <div class="menu-products-grid">
                    <?php foreach ($destacados as $prod): ?>
                        <?= renderProductCard($prod) ?>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <!-- Categorías -->
        <?php foreach ($categorias as $cat): ?>
            <?php $prods = $productosPorCategoria[$cat['id']] ?? []; ?>
            <?php if (!empty($prods)): ?>
                <section class="menu-section" id="cat-<?= $cat['id'] ?>">
                    <div class="menu-section-header">
                        <h2 class="menu-section-title"><?= e($cat['nombre']) ?></h2>
                        <span class="menu-section-count"><?= count($prods) ?> <?= count($prods) === 1 ? 'plato' : 'platos' ?></span>
                    </div>

                    <div class="menu-products-grid">
                        <?php foreach ($prods as $prod): ?>
                            <?= renderProductCard($prod) ?>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>
        <?php endforeach; ?>

        <?php if (empty($destacados) && empty($categorias)): ?>
            <div class="menu-empty-state">
                <div class="empty-icon"><?= icon('cutlery', 40) ?></div>
                <h3>La carta se está preparando</h3>
                <p>El restaurante está actualizando sus platos y precios. ¡Vuelve pronto!</p>
            </div>
        <?php endif; ?>

    </main>

    <!-- FOOTER MESALY -->
    <footer class="menu-public-footer">
        <a href="https://mesaly.co" target="_blank" rel="noopener" class="powered-by">
            <span>Potenciado por</span>
            <strong>Mesaly</strong>
        </a>
    </footer>

    <!-- 4. BOTÓN FLOTANTE DEL CARRITO -->
    <div class="floating-cart-bar" id="floatingCartBar" style="display: none;">
        <button type="button" class="btn-floating-cart" id="btnOpenCart">
            <div class="cart-btn-left">
                <span class="cart-badge-count" id="cartBadgeCount">0</span>
                <span class="cart-btn-label">Ver Pedido</span>
            </div>
            <div class="cart-btn-right">
                <span class="cart-btn-total" id="cartBtnTotal">$ 0</span>
                <span class="cart-btn-arrow">&rarr;</span>
            </div>
        </button>
    </div>

    <!-- 5. MODAL DE PLATO & MODIFICADORES (BOTTOM SHEET) -->
    <div class="modal-backdrop" id="productModalBackdrop" style="display: none;">
        <div class="product-modal-sheet" id="productModalSheet">
            <button type="button" class="modal-close-btn" id="btnCloseProductModal" aria-label="Cerrar">&times;</button>

            <div class="product-modal-scroll" id="productModalContent">
                <!-- Se llena dinámicamente con JavaScript -->
            </div>

            <!-- Footer fijo del modal con Stepper y CTA -->
            <div class="product-modal-footer">
                <div class="quantity-stepper">
                    <button type="button" class="stepper-btn" id="btnQtyMinus" aria-label="Restar">−</button>
                    <span class="stepper-val" id="stepperValue">1</span>
                    <button type="button" class="stepper-btn" id="btnQtyPlus" aria-label="Sumar">+</button>
                </div>
                <button type="button" class="btn-add-to-cart" id="btnAddToCart">
                    <span id="addToCartText">Agregar al Pedido</span>
                    <span class="btn-add-price" id="addToCartPrice">$ 0</span>
                </button>
            </div>
        </div>
    </div>

    <!-- 6. DRAWER / MODAL DEL CARRITO Y CHECKOUT -->
    <div class="modal-backdrop" id="cartDrawerBackdrop" style="display: none;">
        <div class="cart-drawer-sheet" id="cartDrawerSheet">
            <div class="cart-drawer-header">
                <div>
                    <h2 class="cart-drawer-title">Tu Pedido</h2>
                    <span class="cart-drawer-subtitle" id="cartItemsCountText">0 ítems</span>
                </div>
                <button type="button" class="modal-close-btn" id="btnCloseCartDrawer" aria-label="Cerrar">&times;</button>
            </div>

            <div class="cart-drawer-body">
                <!-- Lista de ítems en el carrito -->
                <div class="cart-items-list" id="cartItemsContainer">
                    <!-- Dinámico -->
                </div>

                <!-- Formulario de Checkout -->
                <form id="checkoutForm" class="checkout-form">
                    <!-- Honeypot anti-bots -->
                    <div style="display: none !important; position: absolute; left: -9999px;">
                        <input type="text" name="empresa" value="" tabindex="-1" autocomplete="off">
                        <input type="hidden" name="website" value="">
                    </div>

                    <h3 class="checkout-section-title">¿Cómo deseas recibir tu pedido?</h3>

                    <!-- Selector de Modalidad -->
                    <div class="modality-selector">
                        <?php if (!empty($restaurante['acepta_mesa'])): ?>
                            <label class="modality-option <?= ($modalidadDefault === 'mesa') ? 'selected' : '' ?>">
                                <input type="radio" name="modalidad" value="mesa" <?= ($modalidadDefault === 'mesa') ? 'checked' : '' ?>>
                                <span class="modality-card">
                                    <span class="icon"><?= icon('cutlery', 18) ?></span>
                                    <span class="name">En Mesa</span>
                                </span>
                            </label>
                        <?php endif; ?>

                        <?php if (!empty($restaurante['acepta_domicilio'])): ?>
                            <label class="modality-option <?= ($modalidadDefault === 'domicilio') ? 'selected' : '' ?>">
                                <input type="radio" name="modalidad" value="domicilio" <?= ($modalidadDefault === 'domicilio') ? 'checked' : '' ?>>
                                <span class="modality-card">
                                    <span class="icon"><?= icon('motorcycle', 18) ?></span>
                                    <span class="name">Domicilio</span>
                                </span>
                            </label>
                        <?php endif; ?>

                        <?php if (!empty($restaurante['acepta_recoger'])): ?>
                            <label class="modality-option <?= ($modalidadDefault === 'recoger') ? 'selected' : '' ?>">
                                <input type="radio" name="modalidad" value="recoger" <?= ($modalidadDefault === 'recoger') ? 'checked' : '' ?>>
                                <span class="modality-card">
                                    <span class="icon"><?= icon('shopping-bag', 18) ?></span>
                                    <span class="name">Para Llevar</span>
                                </span>
                            </label>
                        <?php endif; ?>
                    </div>

                    <!-- Campos dinámicos según modalidad -->
                    <div class="modality-fields" id="modalityFields">

                        <!-- 1. CASO MESA: Mesa Confirmada por QR o Sesión -->
                        <div id="fieldMesaConfirmada" class="field-group" style="display: <?= ($modalidadDefault === 'mesa' && $mesaNumero !== null) ? 'block' : 'none' ?>;">
                            <div class="table-confirmed-badge" style="background: rgba(232, 93, 4, 0.08); border: 1.5px solid var(--color-brand); border-radius: 10px; padding: 12px 16px; display: flex; align-items: center; justify-content: space-between; gap: 12px;">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div style="color: var(--color-brand);"><?= icon('cutlery', 20) ?></div>
                                    <div>
                                        <strong style="color: var(--color-brand); font-size: 14.5px; display: block;">Mesa #<?= (int)($mesaNumero ?? 0) ?> Asignada</strong>
                                        <span style="font-size: 12px; color: var(--color-text-muted);">Tus platos se prepararán y servirán directamente en esta mesa</span>
                                    </div>
                                </div>
                                <span class="badge" style="background: var(--color-brand); color: #fff; font-size: 11px; font-weight: 700; padding: 3px 8px; border-radius: 999px;">QR Activo</span>
                            </div>
                        </div>

                        <!-- 2. CASO MESA: Ingresar Número de Mesa Manualmente (si no escaneó QR específico) -->
                        <div class="field-group" id="fieldMesa" style="display: <?= ($modalidadDefault === 'mesa' && $mesaNumero === null) ? 'block' : 'none' ?>;">
                            <label for="mesaNumeroInput">Número de Mesa *</label>
                            <input type="number" id="mesaNumeroInput" name="mesa_numero" class="menu-input" placeholder="Ej: 4" value="<?= $mesaNumero ?? '' ?>" min="1" max="999">
                            <span class="field-hint">Indica el número que ves en la tarjeta o sticker de tu mesa.</span>
                        </div>

                        <!-- 3. Campo Nombre del Cliente -->
                        <div class="field-group" id="fieldNombre">
                            <label for="clienteNombreInput" id="labelClienteNombre">
                                <?= ($modalidadDefault === 'mesa') ? 'Tu Nombre (Opcional en mesa)' : 'Tu Nombre Completo *' ?>
                            </label>
                            <input type="text" id="clienteNombreInput" name="cliente_nombre" class="menu-input" 
                                   placeholder="<?= ($modalidadDefault === 'mesa') ? '¿Cómo te llamas? (Opcional)' : '¿A nombre de quién?' ?>" 
                                   maxlength="100" <?= ($modalidadDefault !== 'mesa') ? 'required' : '' ?>>
                        </div>

                        <!-- 4. Campo Teléfono (Oculto en Mesa, Visible en Domicilio y Recoger) -->
                        <div class="field-group" id="fieldTelefono" style="display: <?= ($modalidadDefault !== 'mesa') ? 'block' : 'none' ?>;">
                            <label for="clienteTelefonoInput">Teléfono / WhatsApp *</label>
                            <input type="tel" id="clienteTelefonoInput" name="cliente_telefono" class="menu-input" placeholder="Ej: 300 123 4567" maxlength="20">
                            <span class="field-hint">Para avisarte cuando tu pedido esté listo o en camino.</span>
                        </div>

                        <!-- 5. Campos Exclusivos de Domicilio (Dirección, Barrio, etc.) -->
                        <div id="fieldsDomicilio" style="display: <?= ($modalidadDefault === 'domicilio') ? 'block' : 'none' ?>;">
                            <div class="field-group">
                                <label for="direccionInput">Dirección de Entrega *</label>
                                <input type="text" id="direccionInput" name="direccion" class="menu-input" placeholder="Ej: Calle 10 # 43-20, Apto 501" maxlength="200">
                            </div>

                            <div class="field-group">
                                <label for="barrioInput">Barrio / Sector</label>
                                <input type="text" id="barrioInput" name="barrio" class="menu-input" placeholder="Ej: El Poblado, Laureles..." maxlength="100">
                            </div>

                            <div class="field-group">
                                <label for="notasEntregaDomicilioInput">Indicaciones de Entrega (Opcional)</label>
                                <input type="text" id="notasEntregaDomicilioInput" name="notas_entrega_domicilio" class="menu-input" placeholder="Ej: Timbre 501, dejar en portería..." maxlength="250">
                            </div>

                            <?php if (!empty($restaurante['pedido_minimo'])): ?>
                                <div class="delivery-min-alert" id="deliveryMinAlert">
                                    <div style="display: flex; align-items: center; gap: 6px;">
                                        <?= icon('alert', 15) ?>
                                        <span>Pedido mínimo para domicilio: <strong><?= format_cop((int)$restaurante['pedido_minimo']) ?></strong></span>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- 6. Campos Exclusivos de Para Llevar (Recoger) -->
                        <div id="fieldsRecoger" style="display: <?= ($modalidadDefault === 'recoger') ? 'block' : 'none' ?>;">
                            <div class="field-group">
                                <label for="horaRecogidaInput">Hora Estimada de Recogida</label>
                                <input type="text" id="horaRecogidaInput" name="hora_recogida" class="menu-input" placeholder="Ej: En 20 minutos / 1:30 PM" maxlength="50">
                            </div>
                        </div>

                        <!-- 7. Notas para Cocina (para En Mesa y Para Llevar) -->
                        <div class="field-group" id="fieldNotasCocina" style="display: <?= ($modalidadDefault !== 'domicilio') ? 'block' : 'none' ?>;">
                            <label for="notasCocinaInput">Notas para la cocina o atención (Opcional)</label>
                            <input type="text" id="notasCocinaInput" name="notas_cocina" class="menu-input" placeholder="Ej: Sin cebolla, salsas aparte, servilletas extra..." maxlength="250">
                        </div>

                    </div>

                    <!-- Métodos de Pago -->
                    <?php if (!empty($metodosPago)): ?>
                        <h3 class="checkout-section-title">Método de Pago Informado</h3>
                        <div class="payment-methods-grid">
                            <?php
                            $metodosInfo = [
                                'efectivo' => ['icon' => icon('cash', 18), 'nombre' => 'Efectivo'],
                                'transferencia' => ['icon' => icon('smartphone', 18), 'nombre' => 'Transferencia / Nequi / Daviplata'],
                                'datafono' => ['icon' => icon('credit-card', 18), 'nombre' => 'Datáfono (Contra entrega)'],
                            ];
                            ?>
                            <?php foreach ($metodosPago as $idx => $m): ?>
                                <?php $info = $metodosInfo[$m] ?? ['icon' => icon('credit-card', 18), 'nombre' => ucfirst($m)]; ?>
                                <label class="payment-method-option <?= $idx === 0 ? 'selected' : '' ?>">
                                    <input type="radio" name="metodo_pago" value="<?= e($m) ?>" <?= $idx === 0 ? 'checked' : '' ?>>
                                    <span class="payment-card">
                                        <span class="icon"><?= $info['icon'] ?></span>
                                        <span class="name"><?= $info['nombre'] ?></span>
                                    </span>
                                </label>
                            <?php endforeach; ?>
                        </div>

                        <?php if (!empty($restaurante['info_pago_texto'])): ?>
                            <div class="payment-instructions-box" id="paymentInstructions">
                                <span class="instructions-icon"><?= icon('alert', 16) ?></span>
                                <div><?= nl2br(e($restaurante['info_pago_texto'])) ?></div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <!-- Desglose de Totales -->
                    <div class="checkout-totals-box">
                        <div class="total-row">
                            <span>Subtotal</span>
                            <span id="checkoutSubtotal">$ 0</span>
                        </div>
                        <div class="total-row" id="rowCostoEnvio" style="display: none;">
                            <span>Costo de Envío</span>
                            <span id="checkoutCostoEnvio"><?= format_cop((int)($restaurante['costo_envio'] ?? 0)) ?></span>
                        </div>
                        <div class="total-row grand-total">
                            <span>Total a Pagar</span>
                            <span id="checkoutGrandTotal">$ 0</span>
                        </div>
                    </div>

                    <!-- Mensajes de Error de Validación -->
                    <div class="checkout-error-msg" id="checkoutErrorMsg" style="display: none;"></div>

                    <!-- Botón Enviar a WhatsApp -->
                    <button type="submit" class="btn-submit-order" id="btnSubmitOrder">
                        <span class="btn-wa-icon"><?= icon('whatsapp', 18) ?></span>
                        <span id="submitOrderBtnText">Enviar Pedido por WhatsApp</span>
                    </button>

                    <button type="button" class="btn-continue-shopping" id="btnContinueShopping">
                        &larr; Seguir agregando platos
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- 7. MODAL DE CONFIRMACIÓN / ÉXITO -->
    <div class="modal-backdrop" id="successModalBackdrop" style="display: none;">
        <div class="success-modal-sheet">
            <div class="success-icon-bubble"><?= icon('check', 28) ?></div>
            <h2 class="success-title">¡Pedido Generado!</h2>
            <p class="success-code">Código: <strong id="successOrderCode">PAR-0000</strong></p>
            <p class="success-desc">
                Tu pedido ha sido preparado para enviarse a WhatsApp. Si no se abrió automáticamente, presiona el botón a continuación:
            </p>
            <a href="#" id="successWaLink" class="btn-open-wa" target="_blank" rel="noopener">
                Abrir WhatsApp Ahora &rarr;
            </a>
            <button type="button" class="btn-close-success" id="btnCloseSuccessModal">Cerrar y volver al Menú</button>
        </div>
    </div>

</div>

<!-- Datos del Restaurante en JSON para el motor de la app -->
<script nonce="<?= e($cspNonce) ?>" id="mesalyRestaurantData" type="application/json">
<?= json_encode([
    'id' => (int)$restaurante['id'],
    'slug' => $restaurante['slug'],
    'nombre' => $restaurante['nombre'],
    'whatsapp' => $restaurante['whatsapp'],
    'costo_envio' => (int)($restaurante['costo_envio'] ?? 0),
    'pedido_minimo' => (int)($restaurante['pedido_minimo'] ?? 0),
    'acepta_mesa' => !empty($restaurante['acepta_mesa']),
    'acepta_domicilio' => !empty($restaurante['acepta_domicilio']),
    'acepta_recoger' => !empty($restaurante['acepta_recoger']),
    'mesa_numero' => $mesaNumero,
    'base_url' => app_url(''),
    'endpoint_pedido' => app_url('/' . $restaurante['slug'] . '/pedido'),
    'csrf_token' => csrf_token(),
], JSON_UNESCAPED_UNICODE) ?>
</script>

<!-- Datos de los Productos en JSON -->
<script nonce="<?= e($cspNonce) ?>" id="mesalyCatalogData" type="application/json">
<?= json_encode($todosLosProductos, JSON_UNESCAPED_UNICODE) ?>
</script>

<!-- Script del Menú Interactivo de Mesaly -->
<script nonce="<?= e($cspNonce) ?>" src="<?= e(asset_url('/assets/js/menu-app.js')) ?>"></script>

<?php
/**
 * Función auxiliar para renderizar cada tarjeta de producto
 */
function renderProductCard(array $prod): string
{
    $disponible = !empty($prod['disponible']);
    $tieneImagen = !empty($prod['imagen_path']);
    $tieneModificadores = !empty($prod['grupos']);

    ob_start();
    ?>
    <div class="menu-product-card <?= !$disponible ? 'is-out-of-stock' : '' ?>" 
         data-id="<?= (int)$prod['id'] ?>">
        
        <div class="product-card-thumb">
            <?php if ($tieneImagen): ?>
                <img src="<?= e(app_url($prod['imagen_path'])) ?>" alt="<?= e($prod['nombre']) ?>" loading="lazy">
            <?php else: ?>
                <div class="product-card-thumb-fallback">
                    <span><?= icon('cutlery', 24) ?></span>
                </div>
            <?php endif; ?>

            <div class="product-card-badges">
                <?php if (!$disponible): ?>
                    <span class="badge-out">Agotado</span>
                <?php else: ?>
                    <?php if (!empty($prod['destacado'])): ?>
                        <span class="badge-featured"><?= icon('sparkles', 11) ?> Recomendado</span>
                    <?php endif; ?>
                    <?php if ($tieneModificadores): ?>
                        <span class="badge-customizable"><?= icon('tag', 11) ?> Opciones</span>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="product-card-content">
            <div class="product-card-header">
                <h3 class="product-title"><?= e($prod['nombre']) ?></h3>
            </div>

            <?php if (!empty($prod['descripcion'])): ?>
                <p class="product-description"><?= e($prod['descripcion']) ?></p>
            <?php endif; ?>

            <div class="product-card-footer">
                <div class="product-price-box">
                    <span class="product-price"><?= format_cop((int)$prod['precio']) ?></span>
                </div>

                <?php if ($disponible): ?>
                    <button type="button" class="btn-add-product" data-id="<?= (int)$prod['id'] ?>">
                        <span class="btn-plus-icon">+</span>
                        <span><?= $tieneModificadores ? 'Personalizar' : 'Pedir' ?></span>
                    </button>
                <?php else: ?>
                    <button type="button" class="btn-add-product disabled" disabled>
                        <span>Agotado</span>
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
?>
