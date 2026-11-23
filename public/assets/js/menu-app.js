/**
 * MESALY — Menú Digital Interactivo
 * Motor Mobile-First del Menú, Carrito y Pedidos a WhatsApp
 */

(function () {
    'use strict';

    // 1. Datos iniciales
    const rawRestData = document.getElementById('mesalyRestaurantData')?.textContent || '{}';
    const rawCatData = document.getElementById('mesalyCatalogData')?.textContent || '[]';

    let restaurant = {};
    let catalog = [];

    try {
        restaurant = JSON.parse(rawRestData);
        catalog = JSON.parse(rawCatData);
    } catch (err) {
        console.error('Error parseando datos de Mesaly:', err);
    }

    const catalogMap = new Map();
    catalog.forEach(p => {
        p.id = parseInt(p.id, 10);
        p.precio = parseInt(p.precio, 10);
        p.disponible = !!parseInt(p.disponible, 10);
        if (p.grupos && Array.isArray(p.grupos)) {
            p.grupos.forEach(g => {
                g.id = parseInt(g.id, 10);
                g.obligatorio = parseInt(g.obligatorio, 10);
                g.min_selecciones = parseInt(g.min_selecciones || 0, 10);
                g.max_selecciones = parseInt(g.max_selecciones || 0, 10);
                if (g.opciones && Array.isArray(g.opciones)) {
                    g.opciones.forEach(o => {
                        o.id = parseInt(o.id, 10);
                        o.precio_extra = parseInt(o.precio_extra || 0, 10);
                    });
                }
            });
        }
        catalogMap.set(p.id, p);
    });

    // Helper: Resuelve URL de imágenes absolutas y relativas
    function getImageUrl(path) {
        if (!path) return '';
        if (path.startsWith('http://') || path.startsWith('https://')) return path;
        const base = (restaurant.base_url || '').replace(/\/$/, '');
        return base + (path.startsWith('/') ? path : '/' + path);
    }

    // Clave de almacenamiento local para persistir el carrito durante la sesión
    const CART_STORAGE_KEY = `mesaly_cart_${restaurant.id || 0}`;

    // 2. Estado del Carrito
    let cart = [];
    try {
        const saved = sessionStorage.getItem(CART_STORAGE_KEY);
        if (saved) {
            cart = JSON.parse(saved);
        }
    } catch (e) {
        cart = [];
    }

    // Estado del modal de producto
    let activeProduct = null;
    let activeQty = 1;
    let selectedOptionIds = new Set();

    // 3. Elementos del DOM
    const floatingCartBar = document.getElementById('floatingCartBar');
    const cartBadgeCount = document.getElementById('cartBadgeCount');
    const cartBtnTotal = document.getElementById('cartBtnTotal');
    const btnOpenCart = document.getElementById('btnOpenCart');
    const btnHeaderCart = document.getElementById('btnHeaderCart');
    const headerCartBadge = document.getElementById('headerCartBadge');

    // Product Modal
    const productModalBackdrop = document.getElementById('productModalBackdrop');
    const productModalContent = document.getElementById('productModalContent');
    const btnCloseProductModal = document.getElementById('btnCloseProductModal');
    const btnQtyMinus = document.getElementById('btnQtyMinus');
    const btnQtyPlus = document.getElementById('btnQtyPlus');
    const stepperValue = document.getElementById('stepperValue');
    const btnAddToCart = document.getElementById('btnAddToCart');
    const addToCartPrice = document.getElementById('addToCartPrice');

    // Cart Drawer
    const cartDrawerBackdrop = document.getElementById('cartDrawerBackdrop');
    const btnCloseCartDrawer = document.getElementById('btnCloseCartDrawer');
    const btnContinueShopping = document.getElementById('btnContinueShopping');
    const cartItemsContainer = document.getElementById('cartItemsContainer');
    const cartItemsCountText = document.getElementById('cartItemsCountText');
    const checkoutForm = document.getElementById('checkoutForm');
    const checkoutSubtotal = document.getElementById('checkoutSubtotal');
    const rowCostoEnvio = document.getElementById('rowCostoEnvio');
    const checkoutCostoEnvio = document.getElementById('checkoutCostoEnvio');
    const checkoutGrandTotal = document.getElementById('checkoutGrandTotal');
    const checkoutErrorMsg = document.getElementById('checkoutErrorMsg');
    const btnSubmitOrder = document.getElementById('btnSubmitOrder');
    const submitOrderBtnText = document.getElementById('submitOrderBtnText');

    // Success Modal
    const successModalBackdrop = document.getElementById('successModalBackdrop');
    const successOrderCode = document.getElementById('successOrderCode');
    const successWaLink = document.getElementById('successWaLink');
    const btnCloseSuccessModal = document.getElementById('btnCloseSuccessModal');

    // Modalidad Fields
    const fieldMesa = document.getElementById('fieldMesa');
    const fieldTelefono = document.getElementById('fieldTelefono');
    const fieldsDomicilio = document.getElementById('fieldsDomicilio');
    const fieldsRecoger = document.getElementById('fieldsRecoger');

    // Helper: Formato COP
    function formatCop(amount) {
        return '$ ' + Math.round(amount).toLocaleString('es-CO');
    }

    // 4. Inicialización
    function init() {
        bindEvents();
        updateCartUI();
        initCategoryFiltering();

        const initialMod = restaurant.mesa_numero ? 'mesa' : 'domicilio';
        updateModalityFields(initialMod);
    }

    // 5. Enlace de Eventos
    function bindEvents() {
        // Delegación de eventos para abrir modal al hacer clic en tarjeta o botón
        document.querySelector('.menu-catalog-main')?.addEventListener('click', function (e) {
            const card = e.target.closest('.menu-product-card');
            if (!card || card.classList.contains('is-out-of-stock')) return;

            e.preventDefault();
            const prodId = parseInt(card.getAttribute('data-id'), 10);
            if (prodId > 0) {
                openProductModal(prodId);
            }
        });

        // Cerrar modal de producto
        btnCloseProductModal?.addEventListener('click', closeProductModal);
        productModalBackdrop?.addEventListener('click', function (e) {
            if (e.target === productModalBackdrop) {
                closeProductModal();
            }
        });

        // Stepper en modal
        btnQtyMinus?.addEventListener('click', () => {
            if (activeQty > 1) {
                activeQty--;
                stepperValue.textContent = activeQty;
                updateModalTotalPrice();
            }
        });

        btnQtyPlus?.addEventListener('click', () => {
            if (activeQty < 10) {
                activeQty++;
                stepperValue.textContent = activeQty;
                updateModalTotalPrice();
            }
        });

        // Botón Agregar al Carrito (Abre checkout directamente)
        btnAddToCart?.addEventListener('click', handleAddToCart);

        // Abrir / Cerrar Drawer del Carrito
        btnOpenCart?.addEventListener('click', openCartDrawer);
        btnHeaderCart?.addEventListener('click', openCartDrawer);
        btnCloseCartDrawer?.addEventListener('click', closeCartDrawer);
        btnContinueShopping?.addEventListener('click', closeCartDrawer);
        cartDrawerBackdrop?.addEventListener('click', function (e) {
            if (e.target === cartDrawerBackdrop) {
                closeCartDrawer();
            }
        });

        // Cambios de modalidad en el checkout
        document.querySelectorAll('input[name="modalidad"]').forEach(radio => {
            radio.addEventListener('change', function () {
                document.querySelectorAll('.modality-option').forEach(opt => opt.classList.remove('selected'));
                this.closest('.modality-option')?.classList.add('selected');
                updateModalityFields(this.value);
                updateCartTotals();
            });
        });

        // Chips interactivos de modalidad en cabecera (solo si no tiene mesa asignada)
        if (!restaurant.mesa_numero) {
            document.querySelectorAll('.btn-chip-modality').forEach(chip => {
                chip.addEventListener('click', function () {
                    const mod = this.getAttribute('data-modality');
                    const radio = document.querySelector(`input[name="modalidad"][value="${mod}"]`);
                    if (radio) {
                        radio.checked = true;
                        document.querySelectorAll('.modality-option').forEach(opt => opt.classList.remove('selected'));
                        radio.closest('.modality-option')?.classList.add('selected');
                        updateModalityFields(mod);
                        updateCartTotals();
                    }
                });
            });
        }

        // Cambios de método de pago
        document.querySelectorAll('input[name="metodo_pago"]').forEach(radio => {
            radio.addEventListener('change', function () {
                document.querySelectorAll('.payment-method-option').forEach(opt => opt.classList.remove('selected'));
                this.closest('.payment-method-option')?.classList.add('selected');
            });
        });

        // Envío de Pedido a WhatsApp
        checkoutForm?.addEventListener('submit', handleCheckoutSubmit);

        // Cerrar Modal Éxito
        btnCloseSuccessModal?.addEventListener('click', () => {
            successModalBackdrop.classList.remove('is-active');
            successModalBackdrop.style.display = 'none';
            document.body.style.overflow = '';
        });
    }

    // 6. Modal de Producto & Modificadores
    function openProductModal(prodId) {
        const prod = catalogMap.get(prodId);
        if (!prod || !prod.disponible) return;

        activeProduct = prod;
        activeQty = 1;
        selectedOptionIds.clear();
        stepperValue.textContent = '1';

        // Preseleccionar primera opción en grupos obligatorios de selección única
        if (prod.grupos && prod.grupos.length > 0) {
            prod.grupos.forEach(grp => {
                if (grp.obligatorio && grp.tipo === 'unica' && grp.opciones && grp.opciones.length > 0) {
                    selectedOptionIds.add(grp.opciones[0].id);
                }
            });
        }

        renderModalContent(prod);
        updateModalTotalPrice();

        productModalBackdrop.style.display = 'flex';
        // Forzar reflow para animación CSS
        void productModalBackdrop.offsetWidth;
        productModalBackdrop.classList.add('is-active');
        document.body.style.overflow = 'hidden';
    }

    function closeProductModal() {
        productModalBackdrop.classList.remove('is-active');
        setTimeout(() => {
            if (!productModalBackdrop.classList.contains('is-active')) {
                productModalBackdrop.style.display = 'none';
                document.body.style.overflow = '';
            }
        }, 220);
        activeProduct = null;
    }

    function renderModalContent(prod) {
        let html = '';

        const imgUrl = getImageUrl(prod.imagen_path);

        if (imgUrl) {
            html += `
                <div class="product-modal-image">
                    <img src="${imgUrl}" alt="${escapeHtml(prod.nombre)}" loading="lazy">
                    <div class="product-modal-image-gradient"></div>
                </div>
            `;
        }

        html += `
            <div class="product-modal-header-info">
                <div class="modal-title-row">
                    <h2 class="product-modal-title">${escapeHtml(prod.nombre)}</h2>
                    <span class="product-modal-price-badge">${formatCop(prod.precio)}</span>
                </div>
                ${prod.descripcion ? `<p class="product-modal-desc">${escapeHtml(prod.descripcion)}</p>` : ''}
            </div>
        `;

        // Renderizar grupos de modificadores (Términos, Toppings, etc.)
        if (prod.grupos && prod.grupos.length > 0) {
            html += `<div class="modifier-groups-container">`;

            prod.grupos.forEach(grp => {
                const isSingle = grp.tipo === 'unica';
                const isRequired = grp.obligatorio === 1;

                let badgeText = isRequired ? 'Obligatorio' : 'Opcional';
                if (isSingle) {
                    badgeText += ' • Elige 1 opción';
                } else if (grp.max_selecciones > 0) {
                    badgeText += ` • Máximo ${grp.max_selecciones}`;
                }

                html += `
                    <div class="modal-modifier-group" data-group-id="${grp.id}" data-type="${grp.tipo}" data-required="${grp.obligatorio}" data-min="${grp.min_selecciones}" data-max="${grp.max_selecciones}">
                        <div class="group-header">
                            <div>
                                <h3 class="group-title">${escapeHtml(grp.nombre)}</h3>
                                <span class="group-instruction">${isSingle ? 'Selecciona una opción' : 'Puedes elegir varios adicionales'}</span>
                            </div>
                            <span class="group-badge ${isRequired ? 'required' : 'optional'}">${badgeText}</span>
                        </div>
                        <div class="options-list">
                `;

                if (grp.opciones && grp.opciones.length > 0) {
                    grp.opciones.forEach(opc => {
                        const isChecked = selectedOptionIds.has(opc.id);
                        const extraPriceText = opc.precio_extra > 0 ? `+${formatCop(opc.precio_extra)}` : 'Incluido';

                        html += `
                            <label class="modifier-option-card ${isChecked ? 'selected' : ''}">
                                <div class="option-card-left">
                                    <input type="${isSingle ? 'radio' : 'checkbox'}" 
                                           name="group_${grp.id}" 
                                           value="${opc.id}" 
                                           data-group-id="${grp.id}"
                                           data-price="${opc.precio_extra}"
                                           ${isChecked ? 'checked' : ''}
                                           class="option-input">
                                    <span class="custom-indicator ${isSingle ? 'radio-dot' : 'check-box'}"></span>
                                    <span class="option-name">${escapeHtml(opc.nombre)}</span>
                                </div>
                                <span class="option-extra-badge ${opc.precio_extra > 0 ? 'has-extra' : ''}">${extraPriceText}</span>
                            </label>
                        `;
                    });
                }

                html += `
                        </div>
                    </div>
                `;
            });

            html += `</div>`;
        }

        // Campo de notas especiales para la cocina
        html += `
            <div class="modal-notes-section">
                <label for="modalItemNotes" class="modal-notes-label">
                    <span>Instrucciones o notas para la cocina (Opcional)</span>
                </label>
                <textarea id="modalItemNotes" class="modal-notes-textarea" placeholder="Ej: Sin cebolla, salsas aparte, término bien sellado..." rows="2" maxlength="300"></textarea>
            </div>
        `;

        productModalContent.innerHTML = html;

        // Enlazar eventos de selección de opciones
        productModalContent.querySelectorAll('.option-input').forEach(input => {
            input.addEventListener('change', handleOptionChange);
        });
    }

    function handleOptionChange(e) {
        const input = e.target;
        const grpEl = input.closest('.modal-modifier-group');
        const grpType = grpEl.getAttribute('data-type');
        const max = parseInt(grpEl.getAttribute('data-max'), 10) || 0;
        const optId = parseInt(input.value, 10);

        if (grpType === 'unica') {
            // Desmarcar las demás opciones de este grupo
            grpEl.querySelectorAll('.option-input').forEach(other => {
                const otherId = parseInt(other.value, 10);
                if (other !== input) {
                    selectedOptionIds.delete(otherId);
                    other.closest('.modifier-option-card')?.classList.remove('selected');
                }
            });

            if (input.checked) {
                selectedOptionIds.add(optId);
                input.closest('.modifier-option-card')?.classList.add('selected');
            } else {
                selectedOptionIds.delete(optId);
                input.closest('.modifier-option-card')?.classList.remove('selected');
            }
        } else {
            // Múltiple selección
            if (input.checked) {
                if (max > 0) {
                    const checkedInGroup = grpEl.querySelectorAll('.option-input:checked').length;
                    if (checkedInGroup > max) {
                        input.checked = false;
                        alert(`Solo puedes elegir hasta ${max} opciones en "${grpEl.querySelector('.group-title')?.textContent}".`);
                        return;
                    }
                }
                selectedOptionIds.add(optId);
                input.closest('.modifier-option-card')?.classList.add('selected');
            } else {
                selectedOptionIds.delete(optId);
                input.closest('.modifier-option-card')?.classList.remove('selected');
            }
        }

        updateModalTotalPrice();
    }

    function updateModalTotalPrice() {
        if (!activeProduct) return;

        let unitTotal = activeProduct.precio;

        if (activeProduct.grupos) {
            activeProduct.grupos.forEach(grp => {
                if (grp.opciones) {
                    grp.opciones.forEach(opc => {
                        if (selectedOptionIds.has(opc.id)) {
                            unitTotal += (opc.precio_extra || 0);
                        }
                    });
                }
            });
        }

        const grandTotal = unitTotal * activeQty;
        addToCartPrice.textContent = formatCop(grandTotal);
    }

    function handleAddToCart() {
        if (!activeProduct) return;

        // Validar grupos obligatorios
        if (activeProduct.grupos) {
            for (const grp of activeProduct.grupos) {
                if (grp.obligatorio) {
                    let count = 0;
                    if (grp.opciones) {
                        grp.opciones.forEach(opc => {
                            if (selectedOptionIds.has(opc.id)) count++;
                        });
                    }
                    const min = grp.min_selecciones || 1;
                    if (count < min) {
                        alert(`Por favor elige tu opción en "${grp.nombre}".`);
                        return;
                    }
                }
            }
        }

        // Recopilar datos detallados de las opciones elegidas
        const opcionesDetalladas = [];
        let extrasUnitarios = 0;

        if (activeProduct.grupos) {
            activeProduct.grupos.forEach(grp => {
                if (grp.opciones) {
                    grp.opciones.forEach(opc => {
                        if (selectedOptionIds.has(opc.id)) {
                            opcionesDetalladas.push({
                                id: opc.id,
                                nombre_grupo: grp.nombre,
                                nombre_opcion: opc.nombre,
                                precio_extra: opc.precio_extra || 0,
                            });
                            extrasUnitarios += (opc.precio_extra || 0);
                        }
                    });
                }
            });
        }

        const notas = document.getElementById('modalItemNotes')?.value.trim() || '';
        const precioUnitario = activeProduct.precio + extrasUnitarios;

        // Crear ítem del carrito
        const itemKey = `${activeProduct.id}_${Array.from(selectedOptionIds).sort().join('-')}_${notas}`;

        const existingIndex = cart.findIndex(it => it.item_key === itemKey);
        if (existingIndex > -1) {
            cart[existingIndex].cantidad = Math.min(10, cart[existingIndex].cantidad + activeQty);
            cart[existingIndex].total_linea = cart[existingIndex].cantidad * cart[existingIndex].precio_unitario;
        } else {
            cart.push({
                item_key: itemKey,
                producto_id: activeProduct.id,
                nombre_producto: activeProduct.nombre,
                precio_base: activeProduct.precio,
                precio_unitario: precioUnitario,
                cantidad: activeQty,
                nota: notas,
                opciones: opcionesDetalladas,
                opciones_ids: Array.from(selectedOptionIds),
                total_linea: precioUnitario * activeQty,
            });
        }

        saveCart();
        closeProductModal();
        updateCartUI();

        // Feedback visual con micro-animación fluida sin interrumpir la navegación del cliente
        mostrarAnimacionItemAgregado(activeProduct.nombre);
    }

    function saveCart() {
        try {
            sessionStorage.setItem(CART_STORAGE_KEY, JSON.stringify(cart));
        } catch (e) {
            console.error('Error guardando carrito:', e);
        }
    }

    // 7. Actualización de la Interfaz del Carrito
    function updateCartUI() {
        const totalItems = cart.reduce((sum, it) => sum + it.cantidad, 0);
        const subtotal = cart.reduce((sum, it) => sum + it.total_linea, 0);

        if (headerCartBadge) {
            headerCartBadge.textContent = totalItems;
        }

        if (totalItems > 0) {
            floatingCartBar.style.display = 'block';
            cartBadgeCount.textContent = totalItems;
            cartBtnTotal.textContent = formatCop(subtotal);
        } else {
            floatingCartBar.style.display = 'none';
        }
    }

    let toastTimeout = null;

    function mostrarAnimacionItemAgregado(nombreProducto) {
        // 1. Animación Bump en el botón flotante del carrito
        if (floatingCartBar) {
            floatingCartBar.classList.remove('cart-bar-bump');
            void floatingCartBar.offsetWidth; // Forzar reflujo
            floatingCartBar.classList.add('cart-bar-bump');
        }

        // 2. Animación Pop en los contadores numéricos
        if (cartBadgeCount) {
            cartBadgeCount.classList.remove('cart-badge-pop');
            void cartBadgeCount.offsetWidth;
            cartBadgeCount.classList.add('cart-badge-pop');
        }
        if (headerCartBadge) {
            headerCartBadge.classList.remove('cart-badge-pop');
            void headerCartBadge.offsetWidth;
            headerCartBadge.classList.add('cart-badge-pop');
        }

        // 3. Notificación tipo Toast no invasiva
        mostrarToastAgregado(nombreProducto);
    }

    function mostrarToastAgregado(nombre) {
        let toast = document.getElementById('toastAddedNotification');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'toastAddedNotification';
            toast.className = 'toast-added-notification';
            toast.innerHTML = `
                <span class="toast-added-icon">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                </span>
                <span class="toast-added-text"></span>
                <button type="button" class="toast-btn-ver" id="btnToastVerPedido">Ver pedido &rarr;</button>
            `;
            document.body.appendChild(toast);
            toast.querySelector('#btnToastVerPedido')?.addEventListener('click', openCartDrawer);
        }

        const textEl = toast.querySelector('.toast-added-text');
        if (textEl) {
            textEl.textContent = `¡${nombre} agregado!`;
        }

        toast.classList.add('is-visible');

        if (toastTimeout) clearTimeout(toastTimeout);
        toastTimeout = setTimeout(() => {
            toast.classList.remove('is-visible');
        }, 2200);
    }

    // 8. Drawer del Carrito & Checkout
    function openCartDrawer() {
        renderCartDrawerItems();

        const activeMod = restaurant.mesa_numero ? 'mesa' : 'domicilio';
        updateModalityFields(activeMod);
        updateCartTotals();

        cartDrawerBackdrop.style.display = 'flex';
        void cartDrawerBackdrop.offsetWidth;
        cartDrawerBackdrop.classList.add('is-active');
        document.body.style.overflow = 'hidden';
    }

    function closeCartDrawer() {
        cartDrawerBackdrop.classList.remove('is-active');
        setTimeout(() => {
            if (!cartDrawerBackdrop.classList.contains('is-active')) {
                cartDrawerBackdrop.style.display = 'none';
                document.body.style.overflow = '';
            }
        }, 220);
    }

    function renderCartDrawerItems() {
        const totalItems = cart.reduce((sum, it) => sum + it.cantidad, 0);
        if (cartItemsCountText) {
            cartItemsCountText.textContent = `${totalItems} ${totalItems === 1 ? 'plato' : 'platos'}`;
        }

        if (cart.length === 0) {
            cartItemsContainer.innerHTML = `
                <div class="cart-empty-message">
                    <span class="empty-cart-icon"><svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="icon-svg"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg></span>
                    <p>Tu pedido está vacío. Elige tus platos favoritos de la carta para comenzar.</p>
                </div>
            `;
            btnSubmitOrder.disabled = true;
            return;
        }

        btnSubmitOrder.disabled = false;
        let html = '';

        cart.forEach((item, index) => {
            const rawImg = item.imagen_path || (catalogMap.get(item.producto_id)?.imagen_path) || '';
            const imgUrl = rawImg
                ? (rawImg.startsWith('http') ? rawImg : (restaurant.base_url.replace(/\/+$/, '') + '/' + rawImg.replace(/^\/+/, '')))
                : '';

            const thumbHtml = imgUrl
                ? `<img src="${escapeHtml(imgUrl)}" alt="${escapeHtml(item.nombre_producto)}" class="cart-item-thumb" loading="lazy">`
                : `<div class="cart-item-thumb-fallback"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg></div>`;

            html += `
                <div class="cart-item-row" data-index="${index}">
                    <!-- Fila 1: Miniatura + Nombre y Precio c/u + Eliminar -->
                    <div class="cart-item-top">
                        <div class="cart-item-thumb-box">
                            ${thumbHtml}
                        </div>
                        <div class="cart-item-main-info">
                            <h4 class="cart-item-title">${escapeHtml(item.nombre_producto)}</h4>
                            <div class="cart-item-unit-price">${formatCop(item.precio_unitario)} c/u</div>
                        </div>
                        <button type="button" class="btn-cart-remove" data-index="${index}" title="Eliminar plato del pedido" aria-label="Eliminar plato del pedido">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                        </button>
                    </div>

                    <!-- Fila 2: Adiciones a Ancho Completo (Sin comprimir) -->
                    ${item.opciones && item.opciones.length > 0 ? `
                        <div class="cart-item-modifiers-full">
                            ${item.opciones.map(opc => `
                                <div class="cart-modifier-chip">
                                    <div class="chip-text-wrap">
                                        <span class="chip-group-label">${escapeHtml(opc.nombre_grupo)}:</span>
                                        <strong class="chip-opt-value">${escapeHtml(opc.nombre_opcion)}</strong>
                                    </div>
                                    ${opc.precio_extra > 0 ? `<span class="chip-extra-price">+${formatCop(opc.precio_extra).replace(/\s+/g, '&nbsp;')}</span>` : ''}
                                </div>
                            `).join('')}
                        </div>
                    ` : ''}

                    ${item.nota ? `
                        <div class="cart-item-note-full">
                            <span class="cart-note-label">Nota:</span> "${escapeHtml(item.nota)}"
                        </div>
                    ` : ''}

                    <!-- Fila 3: Cantidad y Subtotal del plato -->
                    <div class="cart-item-bottom">
                        <div class="cart-item-stepper">
                            <button type="button" class="btn-cart-qty" data-action="decrease" data-index="${index}" aria-label="Disminuir cantidad">−</button>
                            <span class="cart-qty-value">${item.cantidad}</span>
                            <button type="button" class="btn-cart-qty" data-action="increase" data-index="${index}" aria-label="Aumentar cantidad">+</button>
                        </div>
                        <div class="cart-item-line-total">
                            <span class="line-total-amount">${formatCop(item.total_linea)}</span>
                        </div>
                    </div>
                </div>
            `;
        });

        cartItemsContainer.innerHTML = html;

        // Enlazar botones de eliminación individual en el drawer
        cartItemsContainer.querySelectorAll('.btn-cart-remove').forEach(btn => {
            btn.addEventListener('click', function () {
                const idx = parseInt(this.getAttribute('data-index'), 10);
                if (!isNaN(idx) && cart[idx]) {
                    cart.splice(idx, 1);
                    saveCart();
                    renderCartDrawerItems();
                    updateCartTotals();
                    updateCartUI();
                }
            });
        });

        // Enlazar botones de cantidad en el drawer
        cartItemsContainer.querySelectorAll('.btn-cart-qty').forEach(btn => {
            btn.addEventListener('click', function () {
                const idx = parseInt(this.getAttribute('data-index'), 10);
                const action = this.getAttribute('data-action');

                if (action === 'increase') {
                    if (cart[idx].cantidad < 10) {
                        cart[idx].cantidad++;
                        cart[idx].total_linea = cart[idx].cantidad * cart[idx].precio_unitario;
                    }
                } else if (action === 'decrease') {
                    cart[idx].cantidad--;
                    if (cart[idx].cantidad <= 0) {
                        cart.splice(idx, 1);
                    } else {
                        cart[idx].total_linea = cart[idx].cantidad * cart[idx].precio_unitario;
                    }
                }

                saveCart();
                renderCartDrawerItems();
                updateCartTotals();
                updateCartUI();
            });
        });
    }

    function updateModalityFields(modalidad) {
        modalidad = restaurant.mesa_numero ? 'mesa' : 'domicilio';

        const fieldTelefono = document.getElementById('fieldTelefono');
        const fieldsDomicilio = document.getElementById('fieldsDomicilio');
        const fieldNotasCocina = document.getElementById('fieldNotasCocina');
        const submitOrderBtnText = document.getElementById('submitOrderBtnText');

        if (modalidad === 'mesa') {
            if (fieldTelefono) fieldTelefono.style.display = 'none';
            if (fieldsDomicilio) fieldsDomicilio.style.display = 'none';
            if (fieldNotasCocina) fieldNotasCocina.style.display = 'block';
            if (rowCostoEnvio) rowCostoEnvio.style.display = 'none';

            if (submitOrderBtnText) {
                submitOrderBtnText.textContent = 'Enviar Pedido a Cocina';
            }
        } else {
            // Domicilio
            if (fieldTelefono) fieldTelefono.style.display = 'block';
            if (fieldsDomicilio) fieldsDomicilio.style.display = 'block';
            if (fieldNotasCocina) fieldNotasCocina.style.display = 'none';
            if (rowCostoEnvio && restaurant.costo_envio > 0) {
                rowCostoEnvio.style.display = 'flex';
            }

            if (submitOrderBtnText) {
                submitOrderBtnText.textContent = 'Enviar Pedido por WhatsApp';
            }
        }
    }

    function updateCartTotals() {
        const subtotal = cart.reduce((sum, it) => sum + it.total_linea, 0);
        const modalidad = restaurant.mesa_numero ? 'mesa' : 'domicilio';

        let costoEnvio = 0;
        if (modalidad === 'domicilio' && restaurant.costo_envio > 0) {
            costoEnvio = restaurant.costo_envio;
            if (rowCostoEnvio) rowCostoEnvio.classList.add('is-visible');
            if (checkoutCostoEnvio) checkoutCostoEnvio.textContent = formatCop(costoEnvio);
        } else {
            if (rowCostoEnvio) rowCostoEnvio.classList.remove('is-visible');
        }

        const grandTotal = subtotal + costoEnvio;
        if (checkoutSubtotal) checkoutSubtotal.textContent = formatCop(subtotal);
        if (checkoutGrandTotal) checkoutGrandTotal.textContent = formatCop(grandTotal);

        const minAlert = document.getElementById('deliveryMinAlert');
        if (minAlert && modalidad === 'domicilio') {
            if (restaurant.pedido_minimo > 0 && subtotal < restaurant.pedido_minimo) {
                minAlert.classList.add('warning');
            } else {
                minAlert.classList.remove('warning');
            }
        }
    }

    // 9. Envío del Pedido a WhatsApp o Cocina
    async function handleCheckoutSubmit(e) {
        e.preventDefault();
        checkoutErrorMsg.classList.remove('is-visible');
        checkoutErrorMsg.textContent = '';

        if (cart.length === 0) {
            showCheckoutError('Tu pedido está vacío.');
            return;
        }

        const formData = new FormData(checkoutForm);
        const modalidad = restaurant.mesa_numero ? 'mesa' : (document.getElementById('inputModalidad')?.value || 'domicilio');
        let clienteNombre = formData.get('cliente_nombre')?.trim();
        const clienteTelefono = formData.get('cliente_telefono')?.trim();
        const subtotal = cart.reduce((sum, it) => sum + it.total_linea, 0);

        let mesaNum = restaurant.mesa_numero ? parseInt(restaurant.mesa_numero, 10) : null;

        if (modalidad === 'mesa') {
            const val = formData.get('mesa_numero');
            mesaNum = val ? parseInt(val, 10) : null;
        }

        if (modalidad === 'mesa') {
            if (!mesaNum || mesaNum <= 0) {
                showCheckoutError('Por favor indica en qué mesa te encuentras.');
                return;
            }
            if (!clienteNombre) {
                clienteNombre = 'Mesa ' + mesaNum;
            }
        } else {
            // Domicilio
            if (!clienteNombre) {
                showCheckoutError('Por favor escribe tu nombre completo.');
                return;
            }
            if (!clienteTelefono || clienteTelefono.length < 7) {
                showCheckoutError('Por favor ingresa un número de teléfono o WhatsApp válido.');
                return;
            }
            const dir = formData.get('direccion')?.trim();
            if (!dir) {
                showCheckoutError('Por favor escribe tu dirección de entrega.');
                return;
            }
            if (restaurant.pedido_minimo > 0 && subtotal < restaurant.pedido_minimo) {
                showCheckoutError(`El pedido mínimo para domicilio es de ${formatCop(restaurant.pedido_minimo)}.`);
                return;
            }
        }

        // Determinar notas según modalidad
        let notasEntrega = '';
        if (modalidad === 'domicilio') {
            notasEntrega = formData.get('notas_entrega_domicilio') || '';
        } else {
            notasEntrega = formData.get('notas_cocina') || '';
        }

        const payload = {
            modalidad: modalidad,
            cliente_nombre: clienteNombre,
            cliente_telefono: (modalidad === 'mesa') ? '' : clienteTelefono,
            mesa_numero: (modalidad === 'mesa') ? mesaNum : null,
            direccion: (modalidad === 'domicilio') ? formData.get('direccion') : '',
            barrio: (modalidad === 'domicilio') ? formData.get('barrio') : '',
            notas_entrega: notasEntrega,
            hora_recogida: (modalidad === 'recoger') ? formData.get('hora_recogida') : '',
            metodo_pago: formData.get('metodo_pago') || 'efectivo',
            empresa: formData.get('empresa') || '',
            items: cart.map(it => ({
                producto_id: it.producto_id,
                cantidad: it.cantidad,
                nota: it.nota,
                opciones_ids: it.opciones_ids,
            })),
        };

        btnSubmitOrder.disabled = true;
        const submitOrderBtnText = document.getElementById('submitOrderBtnText');
        if (submitOrderBtnText) {
            submitOrderBtnText.textContent = (modalidad === 'mesa') ? 'Enviando a Cocina...' : 'Enviando a WhatsApp...';
        }

        try {
            const res = await fetch(restaurant.endpoint_pedido, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': restaurant.csrf_token || '',
                },
                body: JSON.stringify(payload),
            });

            const data = await res.json();

            if (!res.ok || !data.ok) {
                showCheckoutError(data.error || 'Ocurrió un error al procesar tu pedido.');
                btnSubmitOrder.disabled = false;
                if (submitOrderBtnText) {
                    submitOrderBtnText.textContent = (modalidad === 'mesa') ? 'Enviar Pedido a Cocina' : 'Enviar Pedido por WhatsApp';
                }
                return;
            }

            // Éxito: Limpiar carrito
            cart = [];
            saveCart();
            updateCartUI();
            closeCartDrawer();

            const isMesa = (data.modalidad === 'mesa' || modalidad === 'mesa');

            // Configurar elementos del Modal de Éxito
            const successTitle = document.getElementById('successTitle');
            const successCodeLabel = document.getElementById('successCodeLabel');
            const successDesc = document.getElementById('successDesc');
            const successWaBtnText = document.getElementById('successWaBtnText');
            const successOrderCode = document.getElementById('successOrderCode');
            const successWaLink = document.getElementById('successWaLink');

            if (successOrderCode) {
                successOrderCode.textContent = '#' + (data.codigo || '0000');
            }
            if (successWaLink) {
                successWaLink.href = data.whatsapp_url;
            }

            if (isMesa) {
                // MODALIDAD EN MESA:
                // NO redirigir automáticamente a WhatsApp.
                // Mostrar confirmación de cocina y botón opcional de WhatsApp.
                const mesaTexto = data.mesa_numero ? `Mesa #${data.mesa_numero}` : 'tu mesa';
                if (successTitle) successTitle.textContent = '¡Comanda Enviada a Cocina!';
                if (successCodeLabel) successCodeLabel.textContent = `Atención en ${mesaTexto}`;
                if (successDesc) {
                    successDesc.innerHTML = `Tu pedido <strong>#${data.codigo}</strong> ya está registrado y el equipo de cocina ha comenzado su preparación directa para <strong>${mesaTexto}</strong>. No es necesario salir de aquí; te llevaremos tus platos tan pronto estén listos.`;
                }
                if (successWaBtnText) {
                    successWaBtnText.textContent = 'Enviar copia a WhatsApp (Opcional)';
                }
                if (successWaLink) {
                    successWaLink.className = 'btn-open-wa btn-wa-secondary';
                }
            } else {
                // MODALIDAD DOMICILIO:
                // Indicar preparación profesional y redirigir a WhatsApp para confirmar
                if (successTitle) successTitle.textContent = '¡Pedido Registrado con Éxito!';
                if (successCodeLabel) successCodeLabel.textContent = 'Número de Pedido';
                if (successDesc) {
                    successDesc.innerHTML = `Tu pedido <strong>#${data.codigo}</strong> ha sido preparado de manera profesional por el equipo. A continuación te redirigimos a WhatsApp para confirmar tus datos de entrega y despacho.`;
                }
                if (successWaBtnText) {
                    successWaBtnText.textContent = 'Confirmar Pedido en WhatsApp Ahora →';
                }
                if (successWaLink) {
                    successWaLink.className = 'btn-open-wa btn-wa-primary';
                }

                // Redirección para confirmar entrega a domicilio
                setTimeout(() => {
                    window.location.href = data.whatsapp_url;
                }, 1400);
            }

            // Mostrar modal de éxito
            successModalBackdrop.style.display = 'flex';
            void successModalBackdrop.offsetWidth;
            successModalBackdrop.classList.add('is-active');

        } catch (err) {
            console.error(err);
            showCheckoutError('No se pudo conectar con el servidor. Revisa tu conexión a internet.');
        } finally {
            btnSubmitOrder.disabled = false;
            if (submitOrderBtnText) {
                submitOrderBtnText.textContent = (modalidad === 'mesa') ? 'Enviar Pedido a Cocina' : 'Enviar Pedido por WhatsApp';
            }
        }
    }

    function showCheckoutError(msg) {
        checkoutErrorMsg.textContent = msg;
        checkoutErrorMsg.classList.add('is-visible');
        checkoutErrorMsg.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    // 10. Filtrado Dinámico de Categorías y Secciones
    function initCategoryFiltering() {
        const pills = document.querySelectorAll('.category-nav-pill');
        const sections = document.querySelectorAll('.menu-section');

        if (!sections.length || !pills.length) return;

        pills.forEach(pill => {
            pill.addEventListener('click', function (e) {
                e.preventDefault();
                const targetCat = this.getAttribute('data-cat') || 'all';

                // Marcar la píldora activa
                pills.forEach(p => p.classList.remove('active'));
                this.classList.add('active');

                // Asegurar que la píldora sea visible en el scroll horizontal
                this.scrollIntoView({ behavior: 'smooth', inline: 'center', block: 'nearest' });

                // Filtrar las secciones
                if (targetCat === 'all') {
                    sections.forEach(sec => {
                        sec.style.display = '';
                    });
                } else {
                    sections.forEach(sec => {
                        if (sec.id === targetCat) {
                            sec.style.display = '';
                        } else {
                            sec.style.display = 'none';
                        }
                    });
                }

                // Desplazar suavemente a la carta
                const nav = document.getElementById('categoryNav');
                if (nav) {
                    const navTop = nav.getBoundingClientRect().top + window.pageYOffset;
                    window.scrollTo({ top: navTop, behavior: 'smooth' });
                }
            });
        });
    }

    function escapeHtml(text) {
        if (!text) return '';
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return text.toString().replace(/[&<>"']/g, m => map[m]);
    }

    // Arrancar aplicación
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
