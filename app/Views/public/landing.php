<?php
/**
 * Vista de la Landing Page Oficial de Mesaly (Ultra-Pro Edition)
 * Variables disponibles:
 * - $demoSlug (string)
 * - $demoNombre (string)
 * - $isLoggedIn (bool)
 * - $dashboardUrl (string)
 * - $cspNonce (string)
 */
?>

<!-- ==============================================================================
     BARRA DE NAVEGACIÓN
     ============================================================================== -->
<header class="landing-navbar">
    <div class="landing-container">
        <div class="landing-nav-inner">
            <a href="<?= e(app_url('/')) ?>" class="landing-logo" aria-label="Mesaly Software Gastronómico">
                <img src="<?= e(asset_url('/assets/img/logo-horizontal.svg')) ?>" alt="Mesaly">
            </a>

            <nav>
                <ul class="landing-nav-menu">
                    <li><a href="#como-funciona" class="landing-nav-link">Cómo Funciona</a></li>
                    <li><a href="#guia-pedidos" class="landing-nav-link">Guía de Pedido</a></li>
                    <li><a href="#restaurantes" class="landing-nav-link">Conceptos</a></li>
                    <li><a href="#soluciones" class="landing-nav-link">Soluciones</a></li>
                    <li><a href="#calculadora" class="landing-nav-link">Calculadora</a></li>
                    <li><a href="#tour" class="landing-nav-link">El Sistema</a></li>
                    <li><a href="#precios" class="landing-nav-link">Planes</a></li>
                    <li><a href="#faq" class="landing-nav-link">FAQ</a></li>
                </ul>
            </nav>

            <div class="landing-nav-actions">
                <?php if ($isLoggedIn): ?>
                    <a href="<?= e($dashboardUrl) ?>" class="btn-landing-ghost">Ir al Panel</a>
                <?php else: ?>
                    <a href="<?= e(app_url('/login')) ?>" class="btn-landing-ghost">Iniciar Sesión</a>
                <?php endif; ?>
                <a href="<?= e(app_url('/' . $demoSlug)) ?>" target="_blank" rel="noopener" class="btn-landing-primary">
                    <span>Ver Menú Demo</span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="7" y1="17" x2="17" y2="7"></line>
                        <polyline points="7 7 17 7 17 17"></polyline>
                    </svg>
                </a>
            </div>

            <!-- Botón Hamburguesa en Móvil -->
            <button type="button" class="landing-hamburger" id="hamburgerBtn" aria-label="Abrir menú de navegación" aria-expanded="false">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </div>

    <!-- Menú Desplegable Móvil -->
    <div class="landing-mobile-drawer" id="mobileDrawer">
        <ul class="mobile-drawer-links">
            <li><a href="#como-funciona" class="mobile-drawer-link">Cómo Funciona</a></li>
            <li><a href="#guia-pedidos" class="mobile-drawer-link">Guía de Pedido</a></li>
            <li><a href="#restaurantes" class="mobile-drawer-link">Conceptos Gastronómicos</a></li>
            <li><a href="#soluciones" class="mobile-drawer-link">Soluciones para Restaurante</a></li>
            <li><a href="#calculadora" class="mobile-drawer-link">Calculadora de Ahorro</a></li>
            <li><a href="#tour" class="mobile-drawer-link">Tour por la Plataforma</a></li>
            <li><a href="#precios" class="mobile-drawer-link">Tarifas y Planes</a></li>
            <li><a href="#faq" class="mobile-drawer-link">Preguntas Frecuentes</a></li>
        </ul>
        <div class="mobile-drawer-actions">
            <a href="<?= e(app_url('/' . $demoSlug)) ?>" target="_blank" rel="noopener" class="btn-landing-primary" style="width: 100%;">
                <span>Probar Menú Demo</span>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="7" y1="17" x2="17" y2="7"></line>
                    <polyline points="7 7 17 7 17 17"></polyline>
                </svg>
            </a>
            <?php if ($isLoggedIn): ?>
                <a href="<?= e($dashboardUrl) ?>" class="btn-landing-outline" style="width: 100%;">Acceder al Panel</a>
            <?php else: ?>
                <a href="<?= e(app_url('/login')) ?>" class="btn-landing-outline" style="width: 100%;">Acceso Clientes</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<main>
    <!-- ==============================================================================
         HERO SECTION
         ============================================================================== -->
    <section class="landing-hero">
        <div class="hero-glow-bg"></div>
        <div class="landing-container">
            <div class="hero-grid">
                <!-- Columna Izquierda: Mensaje Central y Acciones -->
                <div class="hero-content reveal-left">
                    <div class="hero-badge-pill">
                        <span class="hero-badge-dot"></span>
                        Tecnología Gastronómica de Nueva Generación
                    </div>

                    <h1 class="hero-title">
                        El software que transforma tu restaurante: <span class="hero-title-highlight">Menú Digital, Pedidos a WhatsApp y en Mesa con QR</span>
                    </h1>

                    <p class="hero-desc">
                        Elimina comisiones abusivas por venta. Brinda a tus comensales un menú digital interactivo optimizado 100% para celulares con pedidos por código QR y WhatsApp, mientras tu equipo coordina comandas, mesas y reportes desde un potente panel operativo diseñado para computadores (PC).
                    </p>

                    <div class="hero-cta-group">
                        <a href="<?= e(app_url('/' . $demoSlug)) ?>" target="_blank" rel="noopener" class="btn-landing-primary">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="5 3 19 12 5 21 5 3"></polygon>
                            </svg>
                            <span>Probar Menú Demo en Vivo</span>
                        </a>

                        <a href="<?= e(app_url('/login')) ?>" class="btn-landing-outline">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                            <span>Acceso Restaurantes</span>
                        </a>
                    </div>

                    <div class="hero-trust-row">
                        <div class="trust-item">
                            <span class="trust-item-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </span>
                            <span>0% Comisiones por Venta</span>
                        </div>
                        <div class="trust-item">
                            <span class="trust-item-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect>
                                    <line x1="12" y1="18" x2="12.01" y2="18"></line>
                                </svg>
                            </span>
                            <span>Menú Móvil para Clientes</span>
                        </div>
                        <div class="trust-item">
                            <span class="trust-item-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                                    <line x1="8" y1="21" x2="16" y2="21"></line>
                                    <line x1="12" y1="17" x2="12" y2="21"></line>
                                </svg>
                            </span>
                            <span>Panel Operativo para PC</span>
                        </div>
                    </div>
                </div>

                <!-- Columna Derecha: Mockup del Teléfono con Badges -->
                <div class="hero-mockup-wrapper reveal-right">
                    <!-- Marco de Smartphone -->
                    <div class="phone-mockup-frame">
                        <div class="phone-speaker-notch"></div>
                        <img src="<?= e(asset_url('/assets/img/landing/menu-mobile.png')) ?>?v=<?= time() ?>" alt="Menú Digital Mesaly en Móvil" class="phone-screen-img" loading="eager">
                    </div>

                    <!-- Personaje 3D Chef Humano Móvil con fondo 100% transparente y entrada animada desde la izquierda -->
                    <img src="<?= e(asset_url('/assets/img/landing/chef-mobile-transparent.png')) ?>" 
                         alt="Chef Digital Mesaly con Móvil" 
                         class="hero-mascot-transparent mascot-slide-left is-revealed" 
                         loading="eager">

                    <!-- Badge Flotante 1: Mesa 04 Confirmada -->
                    <div class="hero-floating-card hero-floating-card-1">
                        <div class="floating-icon-wrap is-mesa">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 3h18v18H3z"></path>
                                <path d="M9 9h6v6H9z"></path>
                            </svg>
                        </div>
                        <div class="floating-card-content">
                            <strong>Mesa 04 — Pedido Confirmado</strong>
                            <span>1x Hamburguesa Trufada, 1x Bebida</span>
                        </div>
                    </div>

                    <!-- Badge Flotante 2: WhatsApp Despacho -->
                    <div class="hero-floating-card hero-floating-card-2">
                        <div class="floating-icon-wrap is-wa">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
                            </svg>
                        </div>
                        <div class="floating-card-content">
                            <strong>Domicilio Directo a WhatsApp</strong>
                            <span>Sin intermediarios ni cobros extras</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ==============================================================================
         LIVE ACTIVITY TICKER (MARQUEE INFINITO EN TIEMPO REAL)
         ============================================================================== -->
    <div class="landing-ticker-wrap" aria-hidden="true">
        <div class="ticker-label-badge">
            <span class="ticker-dot"></span>
            <span>EN VIVO</span>
        </div>
        <div class="ticker-track">
            <div class="ticker-item"><span class="ticker-tag is-mesa">Mesa 04</span> Pedido enviado: Hamburguesa Trufada + Papas Rústicas</div>
            <div class="ticker-item"><span class="ticker-tag is-wa">WhatsApp</span> Domicilio despachado sin comisión ($54.000 COP)</div>
            <div class="ticker-item"><span class="ticker-tag is-kds">Cocina KDS</span> Comanda atendida en 0.7 segundos</div>
            <div class="ticker-item"><span class="ticker-tag is-mesa">Mesa 09</span> 2x Cervezas Artesanales ordenadas desde el QR</div>
            <div class="ticker-item"><span class="ticker-tag is-wa">WhatsApp</span> Domicilio recibido con ubicación GPS exacta</div>
            <!-- Duplicado para ciclo continuo -->
            <div class="ticker-item"><span class="ticker-tag is-mesa">Mesa 04</span> Pedido enviado: Hamburguesa Trufada + Papas Rústicas</div>
            <div class="ticker-item"><span class="ticker-tag is-wa">WhatsApp</span> Domicilio despachado sin comisión ($54.000 COP)</div>
            <div class="ticker-item"><span class="ticker-tag is-kds">Cocina KDS</span> Comanda atendida en 0.7 segundos</div>
            <div class="ticker-item"><span class="ticker-tag is-mesa">Mesa 09</span> 2x Cervezas Artesanales ordenadas desde el QR</div>
            <div class="ticker-item"><span class="ticker-tag is-wa">WhatsApp</span> Domicilio recibido con ubicación GPS exacta</div>
        </div>
    </div>

    <!-- ==============================================================================
         BARRA DE MÉTRICAS RÁPIDAS
         ============================================================================== -->
    <section class="landing-metrics-bar">
        <div class="landing-container">
            <div class="metrics-grid reveal-scale">
                <div class="metric-item">
                    <div class="metric-num">0%</div>
                    <div class="metric-label">Comisiones por ventas o pedidos</div>
                </div>
                <div class="metric-item">
                    <div class="metric-num">&lt; 3s</div>
                    <div class="metric-label">Velocidad de carga en cualquier celular</div>
                </div>
                <div class="metric-item">
                    <div class="metric-num">+35%</div>
                    <div class="metric-label">Incremento en ticket promedio</div>
                </div>
                <div class="metric-item">
                    <div class="metric-num">100%</div>
                    <div class="metric-label">Control total de tu marca y clientes</div>
                </div>
            </div>
        </div>
    </section>

    <!-- ==============================================================================
         CÓMO FUNCIONA EN 3 PASOS
         ============================================================================== -->
    <section id="como-funciona" class="landing-section">
        <div class="landing-container">
            <div class="text-center">
                <span class="section-badge-pill">Fácil y Rápido</span>
                <h2 class="section-title">¿Cómo funciona Mesaly en tu restaurante?</h2>
                <p class="section-desc mx-auto">
                    Dos experiencias creadas a la medida: tus comensales ordenan con total agilidad desde cualquier teléfono móvil, y tu equipo gestiona comandas, mesas y caja desde un computador (PC) o pantalla de escritorio para máxima rapidez operativa.
                </p>
            </div>

            <div class="steps-grid">
                <!-- Paso 1: Mesera Humana 3D con QR que entra desde la izquierda -->
                <div class="step-card reveal delay-100">
                    <img src="<?= e(asset_url('/assets/img/landing/waitress-qr-transparent.png')) ?>" 
                         alt="Mesera con Código QR Mesaly" 
                         class="step-mascot-inline mascot-slide-left" 
                         loading="lazy">
                    <div class="step-num-badge">01</div>
                    <h3>Escanean o Abren el Enlace</h3>
                    <p>
                        El comensal en salón escanea el código QR de su mesa con la cámara de su celular, o el cliente de domicilio abre tu enlace directo desde Instagram o WhatsApp.
                    </p>
                </div>

                <!-- Paso 2: Cliente Humana 3D con Móvil y Hamburguesa que entra desde la izquierda -->
                <div class="step-card reveal delay-200">
                    <img src="<?= e(asset_url('/assets/img/landing/happy-diner-transparent.png')) ?>" 
                         alt="Cliente Explorando Menú y Hamburguesa en Celular" 
                         class="step-mascot-inline mascot-slide-left" 
                         loading="lazy">
                    <div class="step-num-badge">02</div>
                    <h3>Exploran y Personalizan</h3>
                    <p>
                        Ven fotos apetitosas en alta definición, eligen término de carne, bebidas o adiciones especiales, arman su carrito y confirman el pedido en segundos.
                    </p>
                </div>

                <!-- Paso 3: Cocinero Humano 3D con Comanda que entra desde la derecha -->
                <div class="step-card reveal delay-300">
                    <img src="<?= e(asset_url('/assets/img/landing/chef-kitchen-transparent.png')) ?>" 
                         alt="Cocinero Mesaly en Pantalla de Cocina KDS" 
                         class="step-mascot-inline mascot-slide-right" 
                         loading="lazy">
                    <div class="step-num-badge">03</div>
                    <h3>Cocina y Caja en PC</h3>
                    <p>
                        La orden suena al instante en tu computador de cocina o caja con el número de mesa exacto, o llega directo a tu WhatsApp con los datos de entrega.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- ==============================================================================
         COMPARATIVA: APPS TRADICIONALES VS MESALY
         ============================================================================== -->
    <section id="ventajas" class="landing-section bg-soft">
        <div class="landing-container">
            <div class="text-center">
                <span class="section-badge-pill">Diferencia Clave</span>
                <h2 class="section-title">Por qué los restaurantes eligen Mesaly</h2>
                <p class="section-desc mx-auto">
                    Dejar tus ventas en manos de intermediarios o cartas físicas desgastadas frena tu rentabilidad. Compara el modelo tradicional con la autonomía de Mesaly.
                </p>
            </div>

            <div class="compare-grid">
                <!-- Tarjeta: Modelo Tradicional / Apps de Terceros -->
                <div class="compare-card card-traditional reveal-left">
                    <span class="compare-tag tag-bad">Modelo Tradicional / Plataformas</span>
                    <h3 class="compare-title">Apps de Domicilio y Cartas de Papel</h3>
                    <ul class="compare-list">
                        <li class="compare-item is-bad">
                            <span class="compare-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="15" y1="9" x2="9" y2="15"></line>
                                    <line x1="9" y1="9" x2="15" y2="15"></line>
                                </svg>
                            </span>
                            <span>Comisiones agresivas del 20% al 30% sobre cada plato vendido.</span>
                        </li>
                        <li class="compare-item is-bad">
                            <span class="compare-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="15" y1="9" x2="9" y2="15"></line>
                                    <line x1="9" y1="9" x2="15" y2="15"></line>
                                </svg>
                            </span>
                            <span>Los clientes pertenecen a la aplicación, nunca obtienes sus datos ni teléfono.</span>
                        </li>
                        <li class="compare-item is-bad">
                            <span class="compare-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="15" y1="9" x2="9" y2="15"></line>
                                    <line x1="9" y1="9" x2="15" y2="15"></line>
                                </svg>
                            </span>
                            <span>Cartas físicas de papel costosas de reimprimir que se desgastan y quedan desactualizadas.</span>
                        </li>
                        <li class="compare-item is-bad">
                            <span class="compare-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="15" y1="9" x2="9" y2="15"></line>
                                    <line x1="9" y1="9" x2="15" y2="15"></line>
                                </svg>
                            </span>
                            <span>Retrasos en la toma de pedidos en sala por falta de meseros en horas pico.</span>
                        </li>
                    </ul>
                </div>

                <!-- Tarjeta: Con Mesaly -->
                <div class="compare-card card-mesaly reveal-right">
                    <!-- Personaje 3D Chef Ahorro con Alcancía Dorada y Pulgar Arriba -->
                    <img src="<?= e(asset_url('/assets/img/landing/savings-chef-transparent.png')) ?>" 
                         alt="Chef Ahorro 0% Comisiones Mesaly" 
                         class="compare-mascot-transparent mascot-slide-right" 
                         loading="lazy">
                    <span class="compare-tag tag-good">Con Mesaly</span>
                    <h3 class="compare-title">Tu Propio Ecosistema Digital Gastronómico</h3>
                    <ul class="compare-list">
                        <li class="compare-item is-good">
                            <span class="compare-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                </svg>
                            </span>
                            <span>0% comisiones sobre pedidos. Tu ganancia y tus ingresos son 100% tuyos.</span>
                        </li>
                        <li class="compare-item is-good">
                            <span class="compare-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                </svg>
                            </span>
                            <span>Pedidos directos a tu WhatsApp con teléfono y datos de tus clientes para fidelización.</span>
                        </li>
                        <li class="compare-item is-good">
                            <span class="compare-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                </svg>
                            </span>
                            <span>Menú digital interactivo en alta definición, actualizable al segundo desde cualquier móvil o PC.</span>
                        </li>
                        <li class="compare-item is-good">
                            <span class="compare-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                </svg>
                            </span>
                            <span>Códigos QR únicos por mesa: el comensal pide desde su silla y la comanda va directo a cocina.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- ==============================================================================
         CALCULADORA INTERACTIVA DE AHORRO
         ============================================================================== -->
    <section id="calculadora" class="landing-section">
        <div class="landing-container">
            <div class="calculator-box reveal-scale">
                <div class="calc-left">
                    <span class="section-badge-pill" style="color: #C2410C;">Simulador Financiero</span>
                    <h3>Calcula cuánto dinero estás perdiendo en comisiones</h3>
                    <p>
                        Ajusta tus ventas mensuales estimadas y descubre la cantidad exacta de dinero que retienes en tu restaurante mes a mes al operar con Mesaly.
                    </p>

                    <!-- Slider 1: Ventas Mensuales -->
                    <div class="slider-group">
                        <div class="slider-header">
                            <span>Ventas mensuales en domicilios:</span>
                            <span class="slider-val-tag" id="displayVentas">$ 15.000.000 COP</span>
                        </div>
                        <input type="range" id="inputVentas" class="calc-slider" min="3000000" max="60000000" step="1000000" value="15000000" aria-label="Ventas mensuales">
                    </div>

                    <!-- Slider 2: Porcentaje de Comisión de Apps -->
                    <div class="slider-group">
                        <div class="slider-header">
                            <span>Comisión promedio que cobran las apps:</span>
                            <span class="slider-val-tag" id="displayComision">22%</span>
                        </div>
                        <input type="range" id="inputComision" class="calc-slider" min="15" max="30" step="1" value="22" aria-label="Porcentaje de comisión">
                    </div>
                </div>

                <div class="calc-right">
                    <div class="calc-right-card">
                        <!-- Personaje Humano 3D Chef Ahorro con Alcancía Dorada -->
                        <img src="<?= e(asset_url('/assets/img/landing/savings-chef-transparent.png')) ?>" 
                             alt="Chef Ahorro Mesaly" 
                             class="calc-mascot-transparent mascot-slide-right" 
                             loading="lazy">
                        <div class="calc-result-label">Ahorro Anual Estimado con Mesaly</div>
                        <div class="calc-result-num" id="calcAhorroAnual">$ 39.600.000 COP</div>
                        <div class="calc-result-sub">
                            Dinero real que vuelve al bolsillo de tu negocio en vez de pagarse en comisiones a plataformas externas.
                        </div>
                        <a href="<?= e(app_url('/' . $demoSlug)) ?>" target="_blank" rel="noopener" class="btn-landing-primary" style="width: 100%;">
                            <span>Probar Menú Demo Ahora</span>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <polyline points="12 5 19 12 12 19"></polyline>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ==============================================================================
         LOS 4 PILARES DE MESALY
         ============================================================================== -->
    <section id="soluciones" class="landing-section bg-soft">
        <div class="landing-container">
            <div class="text-center">
                <span class="section-badge-pill">Funcionalidades Clave</span>
                <h2 class="section-title">Diseñado exactamente para la rutina de tu restaurante</h2>
                <p class="section-desc mx-auto">
                    Una plataforma integral creada para simplificar cada etapa del servicio: desde que el cliente descubre tu carta hasta que el plato sale de cocina.
                </p>
            </div>

            <div class="features-grid">
                <!-- Pilar 1: Menú Digital Móvil con Comensal con Smartphone -->
                <div class="feature-card reveal delay-100">
                    <img src="<?= e(asset_url('/assets/img/landing/customer-phone-transparent.png')) ?>" 
                         alt="Cliente Explorando Carta Digital Móvil" 
                         class="feature-mascot-inline mascot-slide-left" 
                         loading="lazy">
                    <div class="feature-icon-bubble">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect>
                            <line x1="12" y1="18" x2="12.01" y2="18"></line>
                        </svg>
                    </div>
                    <span class="section-badge-pill" style="font-size: 11px; margin-bottom: 8px;">Para Celulares de Clientes</span>
                    <h4>Menú Digital Móvil en Alta Definición</h4>
                    <p>
                        Diseñado especialmente para la pantalla de cualquier smartphone. Tus comensales navegan fotos apetitosas en HD, eligen términos y adiciones con carga ultrarrápida y sin descargar apps.
                    </p>
                </div>

                <!-- Pilar 2: Pedidos en Mesa con Mesera con QR -->
                <div class="feature-card reveal delay-200">
                    <img src="<?= e(asset_url('/assets/img/landing/waitress-qr-transparent.png')) ?>" 
                         alt="Mesera con QR en Mesa Mesaly" 
                         class="feature-mascot-inline mascot-slide-right" 
                         loading="lazy">
                    <div class="feature-icon-bubble">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 3h7v7H3z"></path>
                            <path d="M14 3h7v7h-7z"></path>
                            <path d="M14 14h7v7h-7z"></path>
                            <path d="M3 14h7v7H3z"></path>
                        </svg>
                    </div>
                    <h4>Pedidos en Mesa con Códigos QR</h4>
                    <p>
                        Genera códigos QR únicos por mesa. El comensal escanea el código con su teléfono, arma su orden y la comanda ingresa inmediatamente al sistema con el número de mesa exacto.
                    </p>
                </div>

                <!-- Pilar 3: Repartidor Humano en Scooter que entra desde la derecha -->
                <div class="feature-card reveal delay-300">
                    <img src="<?= e(asset_url('/assets/img/landing/delivery-order-transparent.png')) ?>" 
                         alt="Repartidor Domicilio WhatsApp Mesaly" 
                         class="feature-mascot-inline mascot-slide-right" 
                         loading="lazy">
                    <div class="feature-icon-bubble">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
                        </svg>
                    </div>
                    <h4>Despacho a WhatsApp sin Comisiones</h4>
                    <p>
                        Los pedidos a domicilio se envían automáticamente a tu línea de WhatsApp con un mensaje ordenado: detalle de productos, adiciones, dirección exacta de entrega y total a cobrar.
                    </p>
                </div>

                <!-- Pilar 4: Gestión KDS en PC con Gerente con Pantalla PC -->
                <div class="feature-card reveal delay-400">
                    <img src="<?= e(asset_url('/assets/img/landing/manager-pc-transparent.png')) ?>" 
                         alt="Gerente Gestionando Pedidos y Cocina en PC" 
                         class="feature-mascot-inline mascot-slide-left" 
                         loading="lazy">
                    <div class="feature-icon-bubble">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                            <line x1="8" y1="21" x2="16" y2="21"></line>
                            <line x1="12" y1="17" x2="12" y2="21"></line>
                        </svg>
                    </div>
                    <span class="section-badge-pill" style="font-size: 11px; margin-bottom: 8px;">Para Computadores (PC) y Cocina</span>
                    <h4>Gestión de Pedidos en Vivo KDS para PC</h4>
                    <p>
                        Diseñado específicamente para computadores de escritorio, laptops y pantallas de cocina/caja. Vista panorámica de comandas, avisos sonoros al instante y control de estados sin saturar el flujo de trabajo.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- ==============================================================================
         SHOWCASE DE CONCEPTOS GASTRONÓMICOS (Pizzería, Café, Hamburguesería)
         ============================================================================== -->
    <section id="restaurantes" class="landing-section">
        <div class="landing-container">
            <div class="text-center">
                <span class="section-badge-pill">Adaptado a tu Gastronomía</span>
                <h2 class="section-title">Diseñado para el ADN único de tu negocio</h2>
                <p class="section-desc mx-auto">
                    Ya sea una pizzería con horno de leña, una cafetería de especialidad o una hamburguesería urbana, Mesaly se adapta a tu carta con fotografía gourmet, modificadores de platos y atención rápida.
                </p>
            </div>

            <div class="concepts-grid">
                <!-- Concepto 1: Pizzería -->
                <div class="concept-card reveal delay-100">
                    <div class="concept-media-wrap">
                        <span class="concept-badge-tag">Pizzerías & Trattorias</span>
                        <img src="<?= e(asset_url('/assets/img/landing/showcase-pizzeria.png')) ?>?v=<?= time() ?>" alt="Menú Digital de Pizzería Di Roma" class="concept-screen-img" loading="lazy">
                        <img src="<?= e(asset_url('/assets/img/landing/mascot-pizza-transparent.png')) ?>" alt="Pizzaiolo 3D Mesaly" class="concept-mascot-img mascot-fade-bottom" loading="lazy">
                    </div>
                    <div class="concept-body">
                        <h3 class="concept-title">Pizzería Di Roma</h3>
                        <p class="concept-desc">Especialistas en pizzas al horno de leña, pastas artesanales y maridaje italiano.</p>
                        <div class="concept-chips-list">
                            <span class="concept-chip"><?= icon('cutlery', 12) ?> Mesas y QR en Salón</span>
                            <span class="concept-chip"><?= icon('check', 12) ?> Ingredientes Extra & Bordes</span>
                            <span class="concept-chip"><?= icon('motorcycle', 12) ?> Pedidos por WhatsApp</span>
                        </div>
                        <div class="concept-actions">
                            <a href="<?= e(app_url('/pizzeria-di-roma/mesa/1')) ?>" target="_blank" rel="noopener" class="btn-concept-demo">
                                <span>Ver Menú de Pizzería</span>
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="7" y1="17" x2="17" y2="7"></line>
                                    <polyline points="7 7 17 7 17 17"></polyline>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Concepto 2: Cafetería -->
                <div class="concept-card reveal delay-200">
                    <div class="concept-media-wrap">
                        <span class="concept-badge-tag">Cafeterías & Reposterías</span>
                        <img src="<?= e(asset_url('/assets/img/landing/showcase-cafe.png')) ?>" alt="Menú Digital de Café Macondo" class="concept-screen-img" loading="lazy">
                        <img src="<?= e(asset_url('/assets/img/landing/mascot-cafe-transparent.png')) ?>" alt="Barista 3D Mesaly" class="concept-mascot-img mascot-fade-bottom" loading="lazy">
                    </div>
                    <div class="concept-body">
                        <h3 class="concept-title">Café Macondo</h3>
                        <p class="concept-desc">Café de especialidad colombiano, tostadas de masa madre y repostería artesanal.</p>
                        <div class="concept-chips-list">
                            <span class="concept-chip"><?= icon('cutlery', 12) ?> Pedido Ágil en Mesa & Barra</span>
                            <span class="concept-chip"><?= icon('check', 12) ?> Tipos de Leche & Endulzantes</span>
                            <span class="concept-chip"><?= icon('clock', 12) ?> Horarios Matutinos & Tarde</span>
                        </div>
                        <div class="concept-actions">
                            <a href="<?= e(app_url('/cafe-macondo/mesa/1')) ?>" target="_blank" rel="noopener" class="btn-concept-demo">
                                <span>Ver Menú de Cafetería</span>
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="7" y1="17" x2="17" y2="7"></line>
                                    <polyline points="7 7 17 7 17 17"></polyline>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Concepto 3: Hamburguesería & Grill -->
                <div class="concept-card reveal delay-300">
                    <div class="concept-media-wrap">
                        <span class="concept-badge-tag">Hamburgueserías & Bares</span>
                        <img src="<?= e(asset_url('/assets/img/landing/showcase-burger.png')) ?>?v=<?= time() ?>" alt="Menú Digital de Fuego Urbano" class="concept-screen-img" loading="lazy">
                        <img src="<?= e(asset_url('/assets/img/landing/mascot-burger-transparent.png')) ?>" alt="Parrillero 3D Mesaly" class="concept-mascot-img mascot-fade-bottom" loading="lazy">
                    </div>
                    <div class="concept-body">
                        <h3 class="concept-title">Fuego Urbano — Burger & Grill</h3>
                        <p class="concept-desc">Smash burgers jugosas, cortes Angus a la brasa, papas trufadas y bebidas frías.</p>
                        <div class="concept-chips-list">
                            <span class="concept-chip"><?= icon('check', 12) ?> Términos de Carne & Salsas</span>
                            <span class="concept-chip"><?= icon('motorcycle', 12) ?> Domicilios 0% Comisiones</span>
                            <span class="concept-chip"><?= icon('cutlery', 12) ?> Comandas Directas a Cocina</span>
                        </div>
                        <div class="concept-actions">
                            <a href="<?= e(app_url('/fuego-urbano')) ?>" target="_blank" rel="noopener" class="btn-concept-demo">
                                <span>Ver Menú de Hamburguesería</span>
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="7" y1="17" x2="17" y2="7"></line>
                                    <polyline points="7 7 17 7 17 17"></polyline>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ==============================================================================
         GUÍA DE EXPERIENCIA DE PEDIDO: EN MESA VS A DOMICILIO
         ============================================================================== -->
    <section id="guia-pedidos" class="landing-section bg-soft">
        <div class="landing-container">
            <div class="text-center">
                <span class="section-badge-pill">Experiencia Simple y Sin Fricciones</span>
                <h2 class="section-title">Así de fácil y natural pide tu cliente</h2>
                <p class="section-desc mx-auto">
                    Cero aplicaciones que descargar y cero registros complicados. Tu cliente pide en segundos, ya sea comiendo en tu salón o pidiendo desde su casa.
                </p>
            </div>

            <div class="order-guide-grid">
                <!-- Modalidad 1: Pedido en Mesa (Salón) -->
                <div class="order-guide-card table-mode reveal-left">
                    <div class="guide-header">
                        <div class="guide-header-text">
                            <span class="guide-badge table"><?= icon('cutlery', 12) ?> Modo Salón / Mesa</span>
                            <h3 class="guide-title">Experiencia en Salón</h3>
                            <p class="guide-subtitle">Atención rápida en mesa sin depender de la carta física ni esperar al mesero.</p>
                        </div>
                        <img src="<?= e(asset_url('/assets/img/landing/mascot-diner-qr-transparent.png')) ?>" alt="Cliente Escaneando Código QR en Mesa" class="guide-mascot-img mascot-fade-bottom" loading="lazy">
                    </div>

                    <ul class="guide-steps-list">
                        <li class="guide-step-item table">
                            <span class="guide-step-num">1</span>
                            <div class="guide-step-content">
                                <h5>Escanea el QR en su Mesa</h5>
                                <p>Apunta con la cámara de su celular al código QR pegado en la mesa. El menú abre de inmediato con el número de mesa precargado.</p>
                            </div>
                        </li>
                        <li class="guide-step-item table">
                            <span class="guide-step-num">2</span>
                            <div class="guide-step-content">
                                <h5>Elige y Personaliza sus Platos</h5>
                                <p>Explora fotos reales en alta definición, selecciona salsas, términos de cocción y agrega comentarios para la cocina.</p>
                            </div>
                        </li>
                        <li class="guide-step-item table">
                            <span class="guide-step-num">3</span>
                            <div class="guide-step-content">
                                <h5>Comanda Directa a Cocina</h5>
                                <p>La orden entra al instante al sistema KDS de tu cocina identificada como "Mesa #1", agilizando el servicio y evitando platos equivocados.</p>
                            </div>
                        </li>
                    </ul>

                    <a href="<?= e(app_url('/cafe-macondo/mesa/1')) ?>" target="_blank" rel="noopener" class="btn-concept-demo">
                        <span>Probar Experiencia en Mesa (Demo)</span>
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="7" y1="17" x2="17" y2="7"></line>
                            <polyline points="7 7 17 7 17 17"></polyline>
                        </svg>
                    </a>
                </div>

                <!-- Modalidad 2: Pedido a Domicilio (WhatsApp) -->
                <div class="order-guide-card delivery-mode reveal-right">
                    <div class="guide-header">
                        <div class="guide-header-text">
                            <span class="guide-badge delivery"><?= icon('motorcycle', 12) ?> Canal Directo 0% Comisiones</span>
                            <h3 class="guide-title">Experiencia a Domicilio</h3>
                            <p class="guide-subtitle">Venta directa sin comisiones abusivas de apps intermediarias.</p>
                        </div>
                        <img src="<?= e(asset_url('/assets/img/landing/delivery-order-transparent.png')) ?>" alt="Repartidor de Domicilio Mesaly" class="guide-mascot-img mascot-fade-bottom" loading="lazy">
                    </div>

                    <ul class="guide-steps-list">
                        <li class="guide-step-item delivery">
                            <span class="guide-step-num">1</span>
                            <div class="guide-step-content">
                                <h5>Acceso desde tus Redes o WhatsApp</h5>
                                <p>El cliente entra desde el enlace en tu biografía de Instagram, TikTok, perfil de Google Maps o estado de WhatsApp.</p>
                            </div>
                        </li>
                        <li class="guide-step-item delivery">
                            <span class="guide-step-num">2</span>
                            <div class="guide-step-content">
                                <h5>Arma su Pedido con Total Claro</h5>
                                <p>Elige productos, añade su dirección de entrega y forma de pago. El subtotal y domicilio se calculan sin errores.</p>
                            </div>
                        </li>
                        <li class="guide-step-item delivery">
                            <span class="guide-step-num">3</span>
                            <div class="guide-step-content">
                                <h5>Llega Listo a tu WhatsApp Corporativo</h5>
                                <p>Recibes un mensaje estructurado y listo para confirmar con el cliente y despachar con tu propio repartidor.</p>
                            </div>
                        </li>
                    </ul>

                    <a href="<?= e(app_url('/fuego-urbano')) ?>" target="_blank" rel="noopener" class="btn-concept-demo">
                        <span>Probar Experiencia de Domicilio (Demo)</span>
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="7" y1="17" x2="17" y2="7"></line>
                            <polyline points="7 7 17 7 17 17"></polyline>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ==============================================================================
         TOUR INTERACTIVO CON BROWSER WINDOW MOCKUP
         ============================================================================== -->
    <section id="tour" class="landing-section">
        <div class="landing-container">
            <div class="text-center">
                <span class="section-badge-pill">Explora la Interfaz</span>
                <h2 class="section-title">Potente, limpio y fácil de dominar en minutos</h2>
                <p class="section-desc mx-auto">
                    Conoce las pantallas del software: el menú interactivo para clientes y el centro de control operativo creado para computadores (PC) y pantallas de servicio.
                </p>
            </div>

            <!-- Botones de Pestañas Interactivas -->
            <div class="product-tour-tabs" role="tablist">
                <button type="button" class="tour-tab-btn active" data-tour-target="tour-menu" data-url="https://mesaly.app/pizzeria-di-roma" role="tab" aria-selected="true">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                        <line x1="8" y1="21" x2="16" y2="21"></line>
                        <line x1="12" y1="17" x2="12" y2="21"></line>
                    </svg>
                    <span>1. Menú Digital Restaurante (Pizzería Di Roma)</span>
                </button>
                <button type="button" class="tour-tab-btn" data-tour-target="tour-pedidos" data-url="https://mesaly.app/admin/pedidos" role="tab" aria-selected="false">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                        <line x1="8" y1="21" x2="16" y2="21"></line>
                        <line x1="12" y1="17" x2="12" y2="21"></line>
                    </svg>
                    <span>2. Pedidos en Vivo KDS (PC Cocina)</span>
                </button>
                <button type="button" class="tour-tab-btn" data-tour-target="tour-mesas" data-url="https://mesaly.app/admin/mesas" role="tab" aria-selected="false">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 3h7v7H3z"></path>
                        <path d="M14 3h7v7h-7z"></path>
                        <path d="M14 14h7v7h-7z"></path>
                        <path d="M3 14h7v7H3z"></path>
                    </svg>
                    <span>3. Mesas y Códigos QR (PC Admin)</span>
                </button>
                <button type="button" class="tour-tab-btn" data-tour-target="tour-reportes" data-url="https://mesaly.app/admin/reportes" role="tab" aria-selected="false">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="20" x2="18" y2="10"></line>
                        <line x1="12" y1="20" x2="12" y2="4"></line>
                        <line x1="6" y1="20" x2="6" y2="14"></line>
                    </svg>
                    <span>4. Reportes y Cierre (PC Admin)</span>
                </button>
            </div>

            <!-- Marco de Ventana de Navegador (Browser Mockup Frame) -->
            <div class="browser-window-frame reveal-scale">
                    <div class="browser-header-bar">
                    <div class="browser-dots">
                        <span class="b-dot red"></span>
                        <span class="b-dot yellow"></span>
                        <span class="b-dot green"></span>
                    </div>

                    <div class="browser-url-pill">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                        <span id="browserUrlText">https://mesaly.app/pizzeria-di-roma</span>
                    </div>

                    <div class="browser-badge-status">
                        <span style="width: 6px; height: 6px; border-radius: 50%; background: #10B981;"></span>
                        <span>Activo</span>
                    </div>
                </div>

                <!-- Panel de Visualización de Capturas Reales -->
                <div class="tour-screenshot-box">
                    <!-- Vista 1: Menú Digital HD Pizzería Di Roma (Escritorio Pro) -->
                    <div class="tour-content-view active" id="tour-menu" role="tabpanel">
                        <img src="<?= e(asset_url('/assets/img/landing/tour-pizzeria-desktop.png')) ?>?v=<?= time() ?>" alt="Menú Digital Pizzería Di Roma" class="tour-screenshot-img" loading="lazy">
                    </div>

                    <!-- Vista 2: Pedidos en Vivo KDS -->
                    <div class="tour-content-view" id="tour-pedidos" role="tabpanel">
                        <img src="<?= e(asset_url('/assets/img/landing/admin-pedidos.png')) ?>?v=<?= time() ?>" alt="Gestión de Pedidos en Vivo KDS" class="tour-screenshot-img" loading="lazy">
                    </div>

                    <!-- Vista 3: Gestión de Mesas y Códigos QR -->
                    <div class="tour-content-view" id="tour-mesas" role="tabpanel">
                        <img src="<?= e(asset_url('/assets/img/landing/admin-mesas.png')) ?>?v=<?= time() ?>" alt="Control de Mesas y Códigos QR" class="tour-screenshot-img" loading="lazy">
                    </div>

                    <!-- Vista 4: Reportes y Cierre de Ventas -->
                    <div class="tour-content-view" id="tour-reportes" role="tabpanel">
                        <img src="<?= e(asset_url('/assets/img/landing/admin-reportes.png')) ?>?v=<?= time() ?>" alt="Métricas y Reportes de Ventas" class="tour-screenshot-img" loading="lazy">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ==============================================================================
         SECCIÓN DE PRECIOS TRANSPARENTES
         ============================================================================== -->
    <section id="precios" class="landing-section bg-soft">
        <div class="landing-container">
            <div class="text-center">
                <span class="section-badge-pill">Tarifas Claras y Sin Sorpresas</span>
                <h2 class="section-title">Elige el plan ideal según el tamaño de tu restaurante</h2>
                <p class="section-desc mx-auto">
                    Sin cobros ocultos por comensal ni comisiones por porcentaje. 0% comisiones sobre tus ventas con facturación electrónica y POS incluidos.
                </p>

                <!-- Selector de Ciclo de Facturación (Mensual / Anual) -->
                <div class="pricing-toggle-wrap">
                    <button type="button" class="pricing-toggle-btn active" id="btnPlanMensual" data-cycle="mensual">
                        <span>Pago Mensual</span>
                    </button>
                    <button type="button" class="pricing-toggle-btn" id="btnPlanAnual" data-cycle="anual">
                        <span>Pago Anual</span>
                        <span class="pricing-save-badge">Ahorra 2 meses</span>
                    </button>
                </div>
            </div>

            <div class="pricing-grid">
                <!-- Plan 1: Básico / Express -->
                <div class="pricing-card reveal-left">
                    <h3 class="pricing-plan-title">Plan Básico</h3>
                    <p class="pricing-plan-sub">Cafeterías, dark kitchens y locales pequeños de 1 a 6 mesas.</p>

                    <div class="pricing-price-box">
                        <div class="pricing-val-row">
                            <span class="pricing-currency">$</span>
                            <span class="pricing-amount price-val" data-monthly="79.000" data-annual="65.800">79.000</span>
                            <span class="pricing-cop-tag">COP</span>
                        </div>
                        <div class="pricing-period cycle-label">/ mes</div>
                        <div class="pricing-sub-billed billed-label">0% comisiones por ventas</div>
                    </div>

                    <ul class="pricing-features-list">
                        <li class="pricing-feature-item">
                            <span class="pricing-feature-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </span>
                            <span><strong>Hasta 6 mesas</strong> con código QR</span>
                        </li>
                        <li class="pricing-feature-item">
                            <span class="pricing-feature-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </span>
                            <span>Menú digital interactivo en alta definición</span>
                        </li>
                        <li class="pricing-feature-item">
                            <span class="pricing-feature-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </span>
                            <span>Pedidos directos a tu WhatsApp</span>
                        </li>
                        <li class="pricing-feature-item">
                            <span class="pricing-feature-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </span>
                            <span>Hasta <strong>600 facturas electrónicas</strong> o POS / mes</span>
                        </li>
                        <li class="pricing-feature-item">
                            <span class="pricing-feature-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </span>
                            <span>Soporte técnico estándar</span>
                        </li>
                    </ul>

                    <a href="https://wa.me/573228991339?text=Hola,%20deseo%20activar%20el%20Plan%20B%C3%A1sico%20de%20Mesaly" target="_blank" rel="noopener" class="btn-landing-outline" style="width: 100%;">
                        <span>Elegir Plan Básico</span>
                    </a>
                </div>

                <!-- Plan 2: Pro / Tradicional (Destacado) -->
                <div class="pricing-card is-popular reveal-scale">
                    <div class="pricing-badge-popular">Más Popular</div>
                    <h3 class="pricing-plan-title">Plan Pro</h3>
                    <p class="pricing-plan-sub">Restaurantes medianos, pizzerías y gastrobare de 8 a 25 mesas.</p>

                    <div class="pricing-price-box">
                        <div class="pricing-val-row">
                            <span class="pricing-currency">$</span>
                            <span class="pricing-amount price-val" data-monthly="149.000" data-annual="124.000">149.000</span>
                            <span class="pricing-cop-tag">COP</span>
                        </div>
                        <div class="pricing-period cycle-label">/ mes</div>
                        <div class="pricing-sub-billed billed-label">0% comisiones por ventas</div>
                    </div>

                    <ul class="pricing-features-list">
                        <li class="pricing-feature-item">
                            <span class="pricing-feature-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </span>
                            <span><strong>Mesas y códigos QR ilimitados</strong></span>
                        </li>
                        <li class="pricing-feature-item">
                            <span class="pricing-feature-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </span>
                            <span>Pantalla de comandas KDS en vivo en PC con sonido</span>
                        </li>
                        <li class="pricing-feature-item">
                            <span class="pricing-feature-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </span>
                            <span>Control de caja y reportes de ventas diarios</span>
                        </li>
                        <li class="pricing-feature-item">
                            <span class="pricing-feature-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </span>
                            <span>Hasta <strong>1.500 facturas electrónicas</strong> o POS / mes</span>
                        </li>
                        <li class="pricing-feature-item">
                            <span class="pricing-feature-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </span>
                            <span>Soporte prioritario y capacitación</span>
                        </li>
                    </ul>

                    <a href="https://wa.me/573228991339?text=Hola,%20deseo%20activar%20el%20Plan%20Pro%20de%20Mesaly" target="_blank" rel="noopener" class="btn-landing-primary" style="width: 100%;">
                        <span>Elegir Plan Pro</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </a>
                </div>

                <!-- Plan 3: Cadena / Gran Salón -->
                <div class="pricing-card reveal-right">
                    <h3 class="pricing-plan-title">Plan Gran Salón</h3>
                    <p class="pricing-plan-sub">Restaurantes de más de 25 mesas, terrazas, salones VIP o múltiples sedes.</p>

                    <div class="pricing-price-box">
                        <div class="pricing-val-row">
                            <span class="pricing-currency">$</span>
                            <span class="pricing-amount price-val" data-monthly="199.000" data-annual="165.800">199.000</span>
                            <span class="pricing-cop-tag">COP</span>
                        </div>
                        <div class="pricing-period cycle-label">/ mes por sede</div>
                        <div class="pricing-sub-billed billed-label">0% comisiones por ventas</div>
                    </div>

                    <ul class="pricing-features-list">
                        <li class="pricing-feature-item">
                            <span class="pricing-feature-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </span>
                            <span><strong>Mesas y salones ilimitados</strong></span>
                        </li>
                        <li class="pricing-feature-item">
                            <span class="pricing-feature-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </span>
                            <span>Multi-pantallas KDS (Cocina, Barra, Postres)</span>
                        </li>
                        <li class="pricing-feature-item">
                            <span class="pricing-feature-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </span>
                            <span>Personalización total de marca y tipografías</span>
                        </li>
                        <li class="pricing-feature-item">
                            <span class="pricing-feature-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </span>
                            <span>Hasta <strong>3.500 facturas electrónicas</strong> o POS / mes</span>
                        </li>
                        <li class="pricing-feature-item">
                            <span class="pricing-feature-icon">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                            </span>
                            <span>Asesor dedicado y soporte prioritario 24/7</span>
                        </li>
                    </ul>

                    <a href="https://wa.me/573228991339?text=Hola,%20deseo%20activar%20el%20Plan%20Gran%20Sal%C3%B3n%20de%20Mesaly" target="_blank" rel="noopener" class="btn-landing-outline" style="width: 100%;">
                        <span>Elegir Plan Gran Salón</span>
                    </a>
                </div>
            </div>
        </div>
    </section>
        </div>
    </section>

    <!-- ==============================================================================
         PREGUNTAS FRECUENTES (FAQ)
         ============================================================================== -->
    <section id="faq" class="landing-section">
        <div class="landing-container">
            <div class="text-center">
                <span class="section-badge-pill">Resolvemos tus Dudas</span>
                <h2 class="section-title">Preguntas Frecuentes</h2>
                <p class="section-desc mx-auto">
                    Todo lo que necesitas saber antes de implementar Mesaly en tu restaurante.
                </p>
            </div>

            <div class="faq-wrap">
                <!-- FAQ 1 -->
                <div class="faq-item">
                    <div class="faq-question">
                        <span>¿Mis clientes necesitan descargar una aplicación?</span>
                        <svg class="faq-chevron" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </div>
                    <div class="faq-answer">
                        No. Los clientes simplemente escanean el código QR en la mesa con la cámara de su teléfono o abren el enlace en WhatsApp. El menú carga al instante en el navegador web sin descargas ni registros obligatorios.
                    </div>
                </div>

                <!-- FAQ 2 -->
                <div class="faq-item">
                    <div class="faq-question">
                        <span>¿Cómo reciben los pedidos mis cocineros o meseros?</span>
                        <svg class="faq-chevron" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </div>
                    <div class="faq-answer">
                        Dispones de la pantalla de Pedidos en Vivo (KDS) diseñada para computadores de escritorio (PC o laptops) y pantallas táctiles de caja/cocina. Emite una alerta sonora en tiempo real cada vez que entra una orden y permite a tu equipo organizar los despachos con vista panorámica.
                    </div>
                </div>

                <!-- FAQ 3 -->
                <div class="faq-item">
                    <div class="faq-question">
                        <span>¿Realmente no cobran comisión por ventas?</span>
                        <svg class="faq-chevron" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </div>
                    <div class="faq-answer">
                        Exactamente. A diferencia de las plataformas que cobran entre el 20% y 30% por cada plato, en Mesaly pagas una suscripción fija. Todo lo que vendas a través de tu menú digital o pedidos por WhatsApp es 100% tuyo.
                    </div>
                </div>

                <!-- FAQ 4 -->
                <div class="faq-item">
                    <div class="faq-question">
                        <span>¿En qué dispositivos se usa Mesaly? ¿Puedo administrarlo desde un celular?</span>
                        <svg class="faq-chevron" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </div>
                    <div class="faq-answer">
                        Mesaly separa estratégicamente cada experiencia para garantizar el mejor desempeño:<br><br>
                        <strong>1. Para tus Clientes (Comensales y Domicilios):</strong> El Menú Digital está 100% optimizado para smartphones y teléfonos celulares de cualquier marca. Abre al segundo mediante el código QR de la mesa o por WhatsApp, sin necesidad de descargar aplicaciones.<br><br>
                        <strong>2. Para tu Restaurante (Cocina, Caja y Administración):</strong> El Panel de Control, la pantalla de pedidos en vivo (KDS), el control de mesas y los reportes están diseñados específicamente para computadores (PC, Mac, laptops o pantallas táctiles de escritorio). Aunque funciona en cualquier navegador, la experiencia administrativa profesional se opera en computador para brindarte amplitud de pantalla, velocidad y máxima comodidad en horas pico.
                    </div>
                </div>

                <!-- FAQ 5 -->
                <div class="faq-item">
                    <div class="faq-question">
                        <span>¿Puedo personalizar el menú con mi logo y colores de marca?</span>
                        <svg class="faq-chevron" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </div>
                    <div class="faq-answer">
                        Sí. Puedes subir tu propio logotipo, imagen de portada, seleccionar la tipografía oficial que mejor combine con tu marca y configurar los horarios de apertura y cierre de tu restaurante.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ==============================================================================
         FINAL CTA BANNER
         ============================================================================== -->
    <section class="landing-container">
        <div class="landing-final-cta reveal-scale">
            <div class="final-cta-glow"></div>

            <div class="final-cta-content">
                <h2>Lleva la experiencia de tu restaurante al siguiente nivel</h2>
                <p>
                    Acelera la rotación de tus mesas, ahorra miles de dólares en comisiones y fideliza a tus clientes con su propia carta digital interactiva.
                </p>
                <div class="final-cta-buttons">
                    <a href="<?= e(app_url('/' . $demoSlug)) ?>" target="_blank" rel="noopener" class="btn-landing-white">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="5 3 19 12 5 21 5 3"></polygon>
                        </svg>
                        <span>Probar Menú Demo en Vivo</span>
                    </a>
                    <a href="https://wa.me/573228991339?text=Hola,%20quiero%20conocer%20m%C3%A1s%20detalles%20de%20Mesaly%20para%20mi%20restaurante" target="_blank" rel="noopener" class="btn-landing-white-outline">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
                        </svg>
                        <span>Hablar con un Asesor</span>
                    </a>
                </div>
            </div>

            <!-- Hostess de Bienvenida 3D que entra desde la derecha -->
            <div class="final-cta-mascot-wrap">
                <img src="<?= e(asset_url('/assets/img/landing/hostess-welcome-transparent.png')) ?>" 
                     alt="Bienvenido a Mesaly" 
                     class="final-cta-mascot-transparent mascot-slide-right" 
                     loading="lazy">
            </div>
        </div>
    </section>
</main>

<!-- ==============================================================================
     FOOTER
     ============================================================================== -->
<footer class="landing-footer">
    <div class="landing-container">
        <div class="footer-top">
            <div class="footer-logo">
                <img src="<?= e(asset_url('/assets/img/logo-horizontal-white.svg')) ?>" alt="Mesaly">
            </div>

            <ul class="footer-links">
                <li><a href="#como-funciona" class="footer-link">Cómo Funciona</a></li>
                <li><a href="#ventajas" class="footer-link">Ventajas</a></li>
                <li><a href="#soluciones" class="footer-link">Soluciones</a></li>
                <li><a href="#calculadora" class="footer-link">Calculadora</a></li>
                <li><a href="#tour" class="footer-link">El Sistema</a></li>
                <li><a href="#precios" class="footer-link">Planes</a></li>
                <li><a href="#faq" class="footer-link">Preguntas</a></li>
                <li><a href="<?= e(app_url('/login')) ?>" class="footer-link">Acceso Clientes</a></li>
            </ul>
        </div>

        <div class="footer-bottom">
            <div>
                &copy; <?= date('Y') ?> Mesaly. Todos los derechos reservados. Tecnología gastronómica integral.
            </div>
            <div>
                Hecho para potenciar restaurantes independientes y cadenas gastronómicas.
            </div>
        </div>
    </div>
</footer>

<!-- ==============================================================================
     BARRA FLOTANTE MÓVIL (STICKY EN PANTALLAS PEQUEÑAS)
     ============================================================================== -->
<div class="landing-mobile-bar" aria-label="Acciones rápidas móviles">
    <a href="<?= e(app_url('/' . $demoSlug)) ?>" target="_blank" rel="noopener" class="mobile-bar-btn-primary">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polygon points="5 3 19 12 5 21 5 3"></polygon>
        </svg>
        <span>Ver Menú Demo</span>
    </a>
    <a href="https://wa.me/573228991339?text=Hola,%20deseo%20m%C3%A1s%20informaci%C3%B3n%20sobre%20Mesaly" target="_blank" rel="noopener" class="mobile-bar-btn-ghost">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
        </svg>
        <span>WhatsApp</span>
    </a>
</div>

<!-- ==============================================================================
     SCRIPTS INTERACTIVOS (CSP NONCE PROTECTED)
     ============================================================================== -->
<script nonce="<?= e($cspNonce) ?>">
document.addEventListener('DOMContentLoaded', function () {
    // -------------------------------------------------------------------------
    // 1. Menú Móvil Hamburger y Drawer
    // -------------------------------------------------------------------------
    var hamburgerBtn = document.getElementById('hamburgerBtn');
    var mobileDrawer = document.getElementById('mobileDrawer');

    if (hamburgerBtn && mobileDrawer) {
        hamburgerBtn.addEventListener('click', function () {
            var isOpen = mobileDrawer.classList.contains('open');
            if (isOpen) {
                mobileDrawer.classList.remove('open');
                hamburgerBtn.classList.remove('open');
                hamburgerBtn.setAttribute('aria-expanded', 'false');
            } else {
                mobileDrawer.classList.add('open');
                hamburgerBtn.classList.add('open');
                hamburgerBtn.setAttribute('aria-expanded', 'true');
            }
        });

        // Cerrar menú al hacer clic en un enlace interno
        var drawerLinks = mobileDrawer.querySelectorAll('.mobile-drawer-link');
        drawerLinks.forEach(function (link) {
            link.addEventListener('click', function () {
                mobileDrawer.classList.remove('open');
                hamburgerBtn.classList.remove('open');
                hamburgerBtn.setAttribute('aria-expanded', 'false');
            });
        });
    }

    // -------------------------------------------------------------------------
    // 2. Calculadora Interactiva de Ahorro
    // -------------------------------------------------------------------------
    var inputVentas = document.getElementById('inputVentas');
    var inputComision = document.getElementById('inputComision');
    var displayVentas = document.getElementById('displayVentas');
    var displayComision = document.getElementById('displayComision');
    var calcAhorroAnual = document.getElementById('calcAhorroAnual');

    function formatearCOP(valor) {
        return '$ ' + Math.round(valor).toLocaleString('es-CO') + ' COP';
    }

    function recalcularAhorro() {
        if (!inputVentas || !inputComision) return;
        var ventasMes = parseFloat(inputVentas.value) || 0;
        var comisionPct = parseFloat(inputComision.value) || 0;

        if (displayVentas) {
            displayVentas.textContent = formatearCOP(ventasMes);
        }
        if (displayComision) {
            displayComision.textContent = comisionPct + '%';
        }

        var comisionMensual = ventasMes * (comisionPct / 100);
        var ahorroAnual = comisionMensual * 12;

        if (calcAhorroAnual) {
            calcAhorroAnual.textContent = formatearCOP(ahorroAnual);
        }
    }

    if (inputVentas && inputComision) {
        inputVentas.addEventListener('input', recalcularAhorro);
        inputComision.addEventListener('input', recalcularAhorro);
        recalcularAhorro();
    }

    // -------------------------------------------------------------------------
    // 3. Tour del Producto: Pestañas Interactivas + URL Dinámica
    // -------------------------------------------------------------------------
    var tourTabs = document.querySelectorAll('.tour-tab-btn');
    var tourPanels = document.querySelectorAll('.tour-content-view');
    var browserUrlText = document.getElementById('browserUrlText');

    tourTabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            var targetId = tab.getAttribute('data-tour-target');
            var mockUrl = tab.getAttribute('data-url');

            tourTabs.forEach(function (t) {
                t.classList.remove('active');
                t.setAttribute('aria-selected', 'false');
            });
            tourPanels.forEach(function (p) {
                p.classList.remove('active');
            });

            tab.classList.add('active');
            tab.setAttribute('aria-selected', 'true');

            var activePanel = document.getElementById(targetId);
            if (activePanel) {
                activePanel.classList.add('active');
            }

            if (browserUrlText && mockUrl) {
                browserUrlText.textContent = mockUrl;
            }
        });
    });

    // -------------------------------------------------------------------------
    // 4. FAQ Accordion Toggle
    // -------------------------------------------------------------------------
    var faqQuestions = document.querySelectorAll('.faq-question');
    faqQuestions.forEach(function (q) {
        q.addEventListener('click', function () {
            var item = q.closest('.faq-item');
            if (!item) return;

            var wasActive = item.classList.contains('active');

            document.querySelectorAll('.faq-item').forEach(function (other) {
                other.classList.remove('active');
            });

            if (!wasActive) {
                item.classList.add('active');
            }
        });
    });

    // -------------------------------------------------------------------------
    // Selector de Ciclo de Facturación (Mensual / Anual)
    // -------------------------------------------------------------------------
    var btnPlanMensual = document.getElementById('btnPlanMensual');
    var btnPlanAnual = document.getElementById('btnPlanAnual');
    var priceElements = document.querySelectorAll('.price-val');
    var cycleLabels = document.querySelectorAll('.cycle-label');
    var billedLabels = document.querySelectorAll('.billed-label');

    function setPricingCycle(isAnnual) {
        if (btnPlanMensual && btnPlanAnual) {
            if (isAnnual) {
                btnPlanAnual.classList.add('active');
                btnPlanMensual.classList.remove('active');
            } else {
                btnPlanMensual.classList.add('active');
                btnPlanAnual.classList.remove('active');
            }
        }

        priceElements.forEach(function (el) {
            el.textContent = isAnnual ? el.getAttribute('data-annual') : el.getAttribute('data-monthly');
        });

        cycleLabels.forEach(function (el) {
            el.textContent = isAnnual ? '/ mes en plan anual' : '/ mes';
        });

        billedLabels.forEach(function (el) {
            el.textContent = isAnnual ? 'Facturado anualmente (Ahorras 2 meses)' : '0% comisiones por ventas';
        });
    }

    if (btnPlanMensual && btnPlanAnual) {
        btnPlanMensual.addEventListener('click', function () { setPricingCycle(false); });
        btnPlanAnual.addEventListener('click', function () { setPricingCycle(true); });
    }

    // -------------------------------------------------------------------------
    // 5. Intersection Observer Scroll Reveal (Animaciones de Entrada)
    // -------------------------------------------------------------------------
    var revealElements = document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-scale, .mascot-slide-left, .mascot-slide-right');
    if ('IntersectionObserver' in window) {
        var revealObserver = new IntersectionObserver(function (entries, observer) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-revealed');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            rootMargin: '0px 0px -40px 0px',
            threshold: 0.1
        });

        revealElements.forEach(function (el) {
            revealObserver.observe(el);
        });
    } else {
        revealElements.forEach(function (el) {
            el.classList.add('is-revealed');
        });
    }
});
</script>
