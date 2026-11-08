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
?>

<div class="menu-container" id="menuApp">

    <!-- 1. HERO HEADER DEL RESTAURANTE -->
    <header class="menu-hero <?= $tieneBanner ? 'has-banner' : 'no-banner' ?>">
        <?php if ($tieneBanner): ?>
            <div class="menu-hero-banner" style="background-image: url('<?= e(app_url($restaurante['banner_path'])) ?>');"></div>
        <?php else: ?>
            <div class="menu-hero-banner default-gradient"></div>
        <?php endif; ?>

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
                    <h1 class="menu-brand-title"><?= e($restaurante['nombre']) ?></h1>
                    
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
                                <span class="chip-modality" title="Atención en Mesa">🍽️ Mesa</span>
                            <?php endif; ?>
                            <?php if (!empty($restaurante['acepta_domicilio'])): ?>
                                <span class="chip-modality" title="Servicio a Domicilio">🛵 Domicilio</span>
                            <?php endif; ?>
                            <?php if (!empty($restaurante['acepta_recoger'])): ?>
                                <span class="chip-modality" title="Para Llevar">🛍️ Recoger</span>
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
                        <span class="table-icon">🍽️</span>
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

    <!-- 2. NAVEGACIÓN STICKY DE CATEGORÍAS -->
    <nav class="menu-category-nav" id="categoryNav">
        <div class="category-nav-scroll">
            <?php if (!empty($destacados)): ?>
                <a href="#cat-destacados" class="category-nav-pill active" data-cat="cat-destacados">
                    ⭐ Populares
                </a>
            <?php endif; ?>

            <?php foreach ($categorias as $idx => $cat): ?>
                <a href="#cat-<?= $cat['id'] ?>" class="category-nav-pill <?= (empty($destacados) && $idx === 0) ? 'active' : '' ?>" data-cat="cat-<?= $cat['id'] ?>">
                    <?= e($cat['nombre']) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </nav>

    <!-- 3. CATÁLOGO DE PRODUCTOS -->
    <main class="menu-catalog-main">

        <!-- Destacados / Populares -->
        <?php if (!empty($destacados)): ?>
            <section class="menu-section" id="cat-destacados">
                <div class="menu-section-header">
                    <h2 class="menu-section-title">⭐ Platos Populares</h2>
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
                <div class="empty-icon">🍽️</div>
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
                <span class="cart-btn-arrow">➔</span>
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
                    <input type="text" name="empresa" value="" style="display:none !important;" tabindex="-1" autocomplete="off">
                    <input type="hidden" name="website" value="" style="display:none !important;">

                    <h3 class="checkout-section-title">¿Cómo deseas recibir tu pedido?</h3>

                    <!-- Selector de Modalidad -->
                    <div class="modality-selector">
                        <?php if (!empty($restaurante['acepta_mesa'])): ?>
                            <label class="modality-option <?= ($mesaNumero !== null) ? 'selected' : '' ?>">
                                <input type="radio" name="modalidad" value="mesa" <?= ($mesaNumero !== null) ? 'checked' : '' ?>>
                                <span class="modality-card">
                                    <span class="icon">🍽️</span>
                                    <span class="name">En Mesa</span>
                                </span>
                            </label>
                        <?php endif; ?>

                        <?php if (!empty($restaurante['acepta_domicilio'])): ?>
                            <label class="modality-option <?= ($mesaNumero === null && !empty($restaurante['acepta_domicilio'])) ? 'selected' : '' ?>">
                                <input type="radio" name="modalidad" value="domicilio" <?= ($mesaNumero === null && !empty($restaurante['acepta_domicilio'])) ? 'checked' : '' ?>>
                                <span class="modality-card">
                                    <span class="icon">🛵</span>
                                    <span class="name">Domicilio</span>
                                </span>
                            </label>
                        <?php endif; ?>

                        <?php if (!empty($restaurante['acepta_recoger'])): ?>
                            <label class="modality-option <?= ($mesaNumero === null && empty($restaurante['acepta_domicilio']) && !empty($restaurante['acepta_recoger'])) ? 'selected' : '' ?>">
                                <input type="radio" name="modalidad" value="recoger" <?= ($mesaNumero === null && empty($restaurante['acepta_domicilio']) && !empty($restaurante['acepta_recoger'])) ? 'checked' : '' ?>>
                                <span class="modality-card">
                                    <span class="icon">🛍️</span>
                                    <span class="name">Para Llevar</span>
                                </span>
                            </label>
                        <?php endif; ?>
                    </div>

                    <!-- Campos dinámicos según modalidad -->
                    <div class="modality-fields" id="modalityFields">
                        <!-- Campo Mesa -->
                        <div class="field-group" id="fieldMesa" style="display: <?= ($mesaNumero !== null) ? 'block' : 'none' ?>;">
                            <label for="mesaNumeroInput">Número de Mesa *</label>
                            <input type="number" id="mesaNumeroInput" name="mesa_numero" class="menu-input" placeholder="Ej: 4" value="<?= $mesaNumero ?? '' ?>" min="1" max="999">
                            <span class="field-hint">Número asignado a tu mesa en el restaurante.</span>
                        </div>

                        <!-- Campos Cliente (Nombre y Teléfono) -->
                        <div class="field-group">
                            <label for="clienteNombreInput">Tu Nombre *</label>
                            <input type="text" id="clienteNombreInput" name="cliente_nombre" class="menu-input" placeholder="¿A nombre de quién?" required maxlength="100">
                        </div>

                        <div class="field-group" id="fieldTelefono" style="display: <?= ($mesaNumero !== null) ? 'none' : 'block' ?>;">
                            <label for="clienteTelefonoInput">Teléfono / WhatsApp *</label>
                            <input type="tel" id="clienteTelefonoInput" name="cliente_telefono" class="menu-input" placeholder="Ej: 300 123 4567" maxlength="20">
                            <span class="field-hint">Para confirmar tu entrega o coordinar la recogida.</span>
                        </div>

                        <!-- Campos exclusivos de Domicilio -->
                        <div id="fieldsDomicilio" style="display: <?= ($mesaNumero === null && !empty($restaurante['acepta_domicilio'])) ? 'block' : 'none' ?>;">
                            <div class="field-group">
                                <label for="direccionInput">Dirección de Entrega *</label>
                                <input type="text" id="direccionInput" name="direccion" class="menu-input" placeholder="Ej: Calle 10 # 43-20, Apto 501" maxlength="200">
                            </div>

                            <div class="field-group">
                                <label for="barrioInput">Barrio / Sector</label>
                                <input type="text" id="barrioInput" name="barrio" class="menu-input" placeholder="Ej: El Poblado, Laureles..." maxlength="100">
                            </div>

                            <div class="field-group">
                                <label for="notasEntregaInput">Notas de Entrega (Opcional)</label>
                                <input type="text" id="notasEntregaInput" name="notas_entrega" class="menu-input" placeholder="Ej: Timbre 501, dejar en portería..." maxlength="250">
                            </div>

                            <?php if (!empty($restaurante['pedido_minimo'])): ?>
                                <div class="delivery-min-alert" id="deliveryMinAlert">
                                    ℹ️ Pedido mínimo para domicilio: <strong><?= format_cop((int)$restaurante['pedido_minimo']) ?></strong>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Campos exclusivos de Para Llevar -->
                        <div id="fieldsRecoger" style="display: none;">
                            <div class="field-group">
                                <label for="horaRecogidaInput">Hora Estimada de Recogida</label>
                                <input type="text" id="horaRecogidaInput" name="hora_recogida" class="menu-input" placeholder="Ej: En 30 minutos / 1:30 PM" maxlength="50">
                            </div>
                        </div>
                    </div>

                    <!-- Métodos de Pago -->
                    <?php if (!empty($metodosPago)): ?>
                        <h3 class="checkout-section-title">Método de Pago Informado</h3>
                        <div class="payment-methods-grid">
                            <?php
                            $metodosInfo = [
                                'efectivo' => ['icon' => '💵', 'nombre' => 'Efectivo'],
                                'transferencia' => ['icon' => '📱', 'nombre' => 'Transferencia / Nequi / Daviplata'],
                                'datafono' => ['icon' => '💳', 'nombre' => 'Datáfono (Contra entrega)'],
                            ];
                            ?>
                            <?php foreach ($metodosPago as $idx => $m): ?>
                                <?php $info = $metodosInfo[$m] ?? ['icon' => '💳', 'nombre' => ucfirst($m)]; ?>
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
                                <span class="instructions-icon">ℹ️</span>
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
                        <span class="btn-wa-icon">🟢</span>
                        <span id="submitOrderBtnText">Enviar Pedido por WhatsApp</span>
                    </button>

                    <button type="button" class="btn-continue-shopping" id="btnContinueShopping">
                        ← Seguir agregando platos
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- 7. MODAL DE CONFIRMACIÓN / ÉXITO -->
    <div class="modal-backdrop" id="successModalBackdrop" style="display: none;">
        <div class="success-modal-sheet">
            <div class="success-icon-bubble">🎉</div>
            <h2 class="success-title">¡Pedido Generado!</h2>
            <p class="success-code">Código: <strong id="successOrderCode">PAR-0000</strong></p>
            <p class="success-desc">
                Tu pedido ha sido preparado para enviarse a WhatsApp. Si no se abrió automáticamente, presiona el botón a continuación:
            </p>
            <a href="#" id="successWaLink" class="btn-open-wa" target="_blank" rel="noopener">
                Abrir WhatsApp Ahora ➔
            </a>
            <button type="button" class="btn-close-success" id="btnCloseSuccessModal">Cerrar y volver al Menú</button>
        </div>
    </div>

</div>

<!-- Datos del Restaurante en JSON para el motor de la app -->
<script id="mesalyRestaurantData" type="application/json">
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
], JSON_UNESCAPED_UNICODE) ?>
</script>

<!-- Datos de los Productos en JSON -->
<script id="mesalyCatalogData" type="application/json">
<?= json_encode($todosLosProductos, JSON_UNESCAPED_UNICODE) ?>
</script>

<!-- Script del Menú Interactivo de Mesaly -->
<script src="<?= e(app_url('/assets/js/menu-app.js')) ?>?v=7"></script>

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
                    <span>🍽️</span>
                </div>
            <?php endif; ?>

            <div class="product-card-badges">
                <?php if (!$disponible): ?>
                    <span class="badge-out">Agotado</span>
                <?php else: ?>
                    <?php if (!empty($prod['destacado'])): ?>
                        <span class="badge-featured">⭐ Recomendado</span>
                    <?php endif; ?>
                    <?php if ($tieneModificadores): ?>
                        <span class="badge-customizable">🥩 Término / Opciones</span>
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
