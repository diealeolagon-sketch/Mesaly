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
        catalogMap.set(parseInt(p.id, 10), p);
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
        initCategoryObserver();
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
                    <span>📝 Instrucciones o notas para la cocina (Opcional)</span>
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

        // Abre el Drawer de Checkout inmediatamente para revisar y pedir
        setTimeout(() => {
            openCartDrawer();
        }, 150);
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

        if (totalItems > 0) {
            floatingCartBar.style.display = 'block';
            cartBadgeCount.textContent = totalItems;
            cartBtnTotal.textContent = formatCop(subtotal);
        } else {
            floatingCartBar.style.display = 'none';
        }
    }

    // 8. Drawer del Carrito & Checkout
    function openCartDrawer() {
        renderCartDrawerItems();
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
        cartItemsCountText.textContent = `${totalItems} ${totalItems === 1 ? 'ítem' : 'ítems'}`;

        if (cart.length === 0) {
            cartItemsContainer.innerHTML = `
                <div class="cart-empty-message">
                    <span class="empty-cart-icon">🛒</span>
                    <p>Tu pedido está vacío. Elige tus platos favoritos de la carta para comenzar.</p>
                </div>
            `;
            btnSubmitOrder.disabled = true;
            return;
        }

        btnSubmitOrder.disabled = false;
        let html = '';

        cart.forEach((item, index) => {
            html += `
                <div class="cart-item-row" data-index="${index}">
                    <div class="cart-item-details">
                        <div class="cart-item-title">${escapeHtml(item.nombre_producto)}</div>
                        <div class="cart-item-unit-price">${formatCop(item.precio_unitario)} c/u</div>
                        
                        ${item.opciones && item.opciones.length > 0 ? `
                            <div class="cart-item-modifiers">
                                ${item.opciones.map(opc => `
                                    <div class="cart-modifier-badge">
                                        <span>• ${escapeHtml(opc.nombre_grupo)}: <strong>${escapeHtml(opc.nombre_opcion)}</strong></span>
                                        ${opc.precio_extra > 0 ? `<small class="extra-cost">(+${formatCop(opc.precio_extra)})</small>` : ''}
                                    </div>
                                `).join('')}
                            </div>
                        ` : ''}

                        ${item.nota ? `
                            <div class="cart-item-note">
                                💬 "${escapeHtml(item.nota)}"
                            </div>
                        ` : ''}
                    </div>

                    <div class="cart-item-actions">
                        <div class="cart-item-total">${formatCop(item.total_linea)}</div>
                        <div class="cart-item-stepper">
                            <button type="button" class="btn-cart-qty" data-action="decrease" data-index="${index}">−</button>
                            <span class="cart-qty-value">${item.cantidad}</span>
                            <button type="button" class="btn-cart-qty" data-action="increase" data-index="${index}">+</button>
                        </div>
                    </div>
                </div>
            `;
        });

        cartItemsContainer.innerHTML = html;

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
        if (modalidad === 'mesa') {
            fieldMesa.style.display = 'block';
            fieldTelefono.style.display = 'none';
            fieldsDomicilio.style.display = 'none';
            fieldsRecoger.style.display = 'none';
            rowCostoEnvio.style.display = 'none';
        } else if (modalidad === 'domicilio') {
            fieldMesa.style.display = 'none';
            fieldTelefono.style.display = 'block';
            fieldsDomicilio.style.display = 'block';
            fieldsRecoger.style.display = 'none';
            if (restaurant.costo_envio > 0) {
                rowCostoEnvio.style.display = 'flex';
            }
        } else if (modalidad === 'recoger') {
            fieldMesa.style.display = 'none';
            fieldTelefono.style.display = 'block';
            fieldsDomicilio.style.display = 'none';
            fieldsRecoger.style.display = 'block';
            rowCostoEnvio.style.display = 'none';
        }
    }

    function updateCartTotals() {
        const subtotal = cart.reduce((sum, it) => sum + it.total_linea, 0);
        const modalidad = document.querySelector('input[name="modalidad"]:checked')?.value || 'mesa';

        let costoEnvio = 0;
        if (modalidad === 'domicilio' && restaurant.costo_envio > 0) {
            costoEnvio = restaurant.costo_envio;
            rowCostoEnvio.style.display = 'flex';
            checkoutCostoEnvio.textContent = formatCop(costoEnvio);
        } else {
            rowCostoEnvio.style.display = 'none';
        }

        const grandTotal = subtotal + costoEnvio;
        checkoutSubtotal.textContent = formatCop(subtotal);
        checkoutGrandTotal.textContent = formatCop(grandTotal);

        const minAlert = document.getElementById('deliveryMinAlert');
        if (minAlert && modalidad === 'domicilio') {
            if (restaurant.pedido_minimo > 0 && subtotal < restaurant.pedido_minimo) {
                minAlert.classList.add('warning');
            } else {
                minAlert.classList.remove('warning');
            }
        }
    }

    // 9. Envío del Pedido a WhatsApp
    async function handleCheckoutSubmit(e) {
        e.preventDefault();
        checkoutErrorMsg.style.display = 'none';

        if (cart.length === 0) {
            showCheckoutError('Tu pedido está vacío.');
            return;
        }

        const formData = new FormData(checkoutForm);
        const modalidad = formData.get('modalidad');
        const clienteNombre = formData.get('cliente_nombre')?.trim();
        const clienteTelefono = formData.get('cliente_telefono')?.trim();
        const subtotal = cart.reduce((sum, it) => sum + it.total_linea, 0);

        if (!clienteNombre) {
            showCheckoutError('Por favor escribe tu nombre completo.');
            return;
        }

        if (modalidad === 'mesa') {
            const mesaNum = parseInt(formData.get('mesa_numero'), 10);
            if (!mesaNum || mesaNum <= 0) {
                showCheckoutError('Por favor indica el número de tu mesa.');
                return;
            }
        } else if (modalidad === 'domicilio') {
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
        } else if (modalidad === 'recoger') {
            if (!clienteTelefono || clienteTelefono.length < 7) {
                showCheckoutError('Por favor ingresa un número de teléfono o WhatsApp válido.');
                return;
            }
        }

        const payload = {
            modalidad: modalidad,
            cliente_nombre: clienteNombre,
            cliente_telefono: clienteTelefono,
            mesa_numero: formData.get('mesa_numero'),
            direccion: formData.get('direccion'),
            barrio: formData.get('barrio'),
            notas_entrega: formData.get('notas_entrega'),
            hora_recogida: formData.get('hora_recogida'),
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
        submitOrderBtnText.textContent = 'Enviando a WhatsApp...';

        try {
            const res = await fetch(restaurant.endpoint_pedido, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify(payload),
            });

            const data = await res.json();

            if (!res.ok || !data.ok) {
                showCheckoutError(data.error || 'Ocurrió un error al procesar tu pedido.');
                btnSubmitOrder.disabled = false;
                submitOrderBtnText.textContent = 'Enviar Pedido por WhatsApp';
                return;
            }

            // Éxito: Limpiar carrito
            cart = [];
            saveCart();
            updateCartUI();
            closeCartDrawer();

            // Mostrar modal de éxito
            successOrderCode.textContent = data.codigo;
            successWaLink.href = data.whatsapp_url;
            successModalBackdrop.style.display = 'flex';
            void successModalBackdrop.offsetWidth;
            successModalBackdrop.classList.add('is-active');

            setTimeout(() => {
                window.location.href = data.whatsapp_url;
            }, 600);

        } catch (err) {
            console.error(err);
            showCheckoutError('No se pudo conectar con el servidor. Revisa tu conexión a internet.');
        } finally {
            btnSubmitOrder.disabled = false;
            submitOrderBtnText.textContent = 'Enviar Pedido por WhatsApp';
        }
    }

    function showCheckoutError(msg) {
        checkoutErrorMsg.textContent = msg;
        checkoutErrorMsg.style.display = 'block';
        checkoutErrorMsg.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    // 10. Sticky Observer de Categorías
    function initCategoryObserver() {
        const pills = document.querySelectorAll('.category-nav-pill');
        const sections = document.querySelectorAll('.menu-section');

        if (!sections.length || !pills.length) return;

        pills.forEach(pill => {
            pill.addEventListener('click', function (e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                const targetEl = document.querySelector(targetId);
                if (targetEl) {
                    const navHeight = document.getElementById('categoryNav')?.offsetHeight || 60;
                    const elPos = targetEl.getBoundingClientRect().top + window.pageYOffset - navHeight - 10;
                    window.scrollTo({ top: elPos, behavior: 'smooth' });

                    pills.forEach(p => p.classList.remove('active'));
                    this.classList.add('active');
                }
            });
        });

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const secId = entry.target.id;
                    pills.forEach(pill => {
                        if (pill.getAttribute('href') === `#${secId}`) {
                            pill.classList.add('active');
                            pill.scrollIntoView({ behavior: 'smooth', inline: 'nearest', block: 'nearest' });
                        } else {
                            pill.classList.remove('active');
                        }
                    });
                }
            });
        }, {
            rootMargin: '-80px 0px -70% 0px',
            threshold: 0,
        });

        sections.forEach(sec => observer.observe(sec));
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
