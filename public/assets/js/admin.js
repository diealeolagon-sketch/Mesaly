/**
 * MESALY — Panel de Administración
 * Controladores interactivos globales para admin y superadmin
 * Totalmente compatible con CSP estricto (cero manejadores inline)
 */
(function () {
    'use strict';

    // 1. Confirmación de formularios destructivos (data-confirm="...")
    document.addEventListener('submit', function (e) {
        const form = e.target;
        if (form && form.hasAttribute('data-confirm')) {
            const msg = form.getAttribute('data-confirm');
            if (msg && !window.confirm(msg)) {
                e.preventDefault();
            }
        }
    });

    // 2. Sistema unificado y accesible de Dropzones de Imágenes (.dropzone[data-input])
    function setupDropzones() {
        document.querySelectorAll('.dropzone[data-input]').forEach(function (dropzone) {
            const inputId = dropzone.getAttribute('data-input');
            const fileInput = document.getElementById(inputId);
            if (!fileInput) return;

            const previewImg = dropzone.querySelector('.dropzone-preview');
            const placeholder = dropzone.querySelector('.dropzone-placeholder');
            const changeBtn = dropzone.querySelector('.dropzone-change-btn');

            function handleFile(file) {
                if (!file) return;

                const maxSize = 8 * 1024 * 1024; // 8 MB
                if (file.size > maxSize) {
                    alert('La imagen seleccionada supera el límite máximo permitido de 8 MB.');
                    fileInput.value = '';
                    return;
                }

                const validTypes = ['image/jpeg', 'image/png', 'image/webp'];
                if (!validTypes.includes(file.type)) {
                    alert('Formato de imagen no soportado. Por favor sube un archivo JPG, PNG o WebP.');
                    fileInput.value = '';
                    return;
                }

                if (previewImg) {
                    try {
                        previewImg.src = URL.createObjectURL(file);
                    } catch (e) {
                        console.error('Error al generar previsualización:', e);
                    }
                }

                dropzone.classList.add('has-preview');
            }

            // Clic en cualquier área del dropzone activa el selector nativo de archivos
            dropzone.addEventListener('click', function () {
                fileInput.click();
            });

            // Accesibilidad de teclado (Enter o Espacio)
            dropzone.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    fileInput.click();
                }
            });

            // Cambio en el input de archivo
            fileInput.addEventListener('change', function () {
                if (this.files && this.files[0]) {
                    handleFile(this.files[0]);
                }
            });

            // Arrastrar y soltar nativo (Drag & Drop)
            ['dragenter', 'dragover'].forEach(function (eventName) {
                dropzone.addEventListener(eventName, function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    dropzone.classList.add('is-dragover');
                });
            });

            ['dragleave', 'drop'].forEach(function (eventName) {
                dropzone.addEventListener(eventName, function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    dropzone.classList.remove('is-dragover');
                });
            });

            dropzone.addEventListener('drop', function (e) {
                const dt = e.dataTransfer;
                if (dt && dt.files && dt.files.length > 0) {
                    try {
                        fileInput.files = dt.files;
                    } catch (err) {
                        // Fallback si el navegador no permite asignar DataTransfer a files
                    }
                    handleFile(dt.files[0]);
                }
            });
        });
    }

    // 3. Previsualización de imágenes segura para inputs con data-preview-img
    function setupImagePreviews() {
        document.querySelectorAll('input[type="file"][data-preview-img]').forEach(function (input) {
            input.addEventListener('change', function () {
                if (!this.files || !this.files[0]) return;
                const file = this.files[0];
                const maxSize = parseInt(this.getAttribute('data-max-size') || '8388608', 10);
                if (file.size > maxSize) {
                    alert('La imagen seleccionada supera el límite permitido. Por favor elige una imagen de menor peso.');
                    this.value = '';
                    return;
                }
                const targetId = this.getAttribute('data-preview-img');
                const placeholderId = this.getAttribute('data-preview-placeholder');
                const targetImg = document.getElementById(targetId);
                const placeholder = placeholderId ? document.getElementById(placeholderId) : null;

                if (targetImg) {
                    targetImg.src = URL.createObjectURL(file);
                    targetImg.style.display = 'block';
                }
                if (placeholder) {
                    placeholder.style.display = 'none';
                }
            });
        });
    }

    // 4. Sistema de menús desplegables de acciones (.dropdown-wrap)
    function setupDropdowns() {
        document.addEventListener('click', function (e) {
            const trigger = e.target.closest('.dropdown-trigger');
            const openWrap = trigger ? trigger.closest('.dropdown-wrap') : null;

            // Cerrar cualquier otro dropdown abierto
            document.querySelectorAll('.dropdown-wrap.is-open').forEach(function (wrap) {
                if (wrap !== openWrap) {
                    wrap.classList.remove('is-open');
                }
            });

            // Si se hizo clic en un trigger, alternar apertura
            if (openWrap) {
                e.preventDefault();
                e.stopPropagation();
                openWrap.classList.toggle('is-open');
            }
        });

        // Cerrar con Escape
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.dropdown-wrap.is-open').forEach(function (wrap) {
                    wrap.classList.remove('is-open');
                });
            }
        });
    }

    // 5. Generación automática y amigable de slugs (data-slug-from="...")
    function setupSlugifiers() {
        document.querySelectorAll('input[data-slug-from]').forEach(function (slugInput) {
            const sourceId = slugInput.getAttribute('data-slug-from');
            const sourceEl = document.getElementById(sourceId);
            if (!sourceEl) return;

            let isUserModified = slugInput.value.trim().length > 0;

            slugInput.addEventListener('input', function () {
                isUserModified = this.value.trim().length > 0;
            });

            sourceEl.addEventListener('input', function () {
                if (isUserModified) return;
                const text = this.value || '';
                const slug = text
                    .toString()
                    .toLowerCase()
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '') // Quitar tildes
                    .replace(/[^a-z0-9\s-]/g, '')    // Quitar caracteres especiales
                    .trim()
                    .replace(/\s+/g, '-')            // Espacios a guiones
                    .replace(/-+/g, '-');            // Evitar guiones múltiples
                slugInput.value = slug;
            });
        });
    }

    // 6. Sistema Global de Toasts de Notificación
    window.mostrarAdminToast = function (mensaje) {
        let container = document.getElementById('adminToastContainer');
        if (!container) {
            container = document.createElement('div');
            container.id = 'adminToastContainer';
            container.className = 'admin-toast-container';
            document.body.appendChild(container);
        }

        const toast = document.createElement('div');
        toast.className = 'admin-toast';
        toast.innerHTML = '<span class="admin-toast-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg></span><span>' + mensaje + '</span>';
        container.appendChild(toast);

        setTimeout(function () {
            toast.classList.add('is-fading');
            setTimeout(function () {
                toast.remove();
            }, 220);
        }, 3200);
    };

    // 7. Notificaciones en Vivo con Sonido (Web Audio API) y Campanita
    function setupAdminNotifications() {
        const bellBtn = document.getElementById('btnAdminBell');
        const bellDot = document.getElementById('adminBellDot');

        let sharedAudioCtx = null;

        function getAudioContext() {
            if (!sharedAudioCtx) {
                const AudioCtx = window.AudioContext || window.webkitAudioContext;
                if (AudioCtx) {
                    sharedAudioCtx = new AudioCtx();
                }
            }
            if (sharedAudioCtx && sharedAudioCtx.state === 'suspended') {
                sharedAudioCtx.resume().catch(() => {});
            }
            return sharedAudioCtx;
        }

        // Desbloquear AudioContext en la primera interacción del usuario con la página
        function unlockAudio() {
            const ctx = getAudioContext();
            if (ctx && ctx.state === 'suspended') {
                ctx.resume().catch(() => {});
            }
        }
        document.addEventListener('click', unlockAudio, { passive: true });
        document.addEventListener('keydown', unlockAudio, { passive: true });
        document.addEventListener('touchstart', unlockAudio, { passive: true });

        // Función para reproducir el sintetizador chime agradable
        function playChime() {
            if (localStorage.getItem('mesaly_admin_sound_muted') === 'true') return;
            try {
                const ctx = getAudioContext();
                if (!ctx) return;

                const runSynth = () => {
                    const now = ctx.currentTime;
                    // Nota 1: D5 (587.33 Hz)
                    const osc1 = ctx.createOscillator();
                    const gain1 = ctx.createGain();
                    osc1.type = 'sine';
                    osc1.frequency.setValueAtTime(587.33, now);
                    gain1.gain.setValueAtTime(0.20, now);
                    gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.28);
                    osc1.connect(gain1);
                    gain1.connect(ctx.destination);
                    osc1.start(now);
                    osc1.stop(now + 0.28);

                    // Nota 2: A5 (880 Hz) - efecto campana ding-dong
                    const osc2 = ctx.createOscillator();
                    const gain2 = ctx.createGain();
                    osc2.type = 'sine';
                    osc2.frequency.setValueAtTime(880, now + 0.14);
                    gain2.gain.setValueAtTime(0.28, now + 0.14);
                    gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.60);
                    osc2.connect(gain2);
                    gain2.connect(ctx.destination);
                    osc2.start(now + 0.14);
                    osc2.stop(now + 0.60);
                };

                if (ctx.state === 'suspended') {
                    ctx.resume().then(runSynth).catch(() => {});
                } else {
                    runSynth();
                }
            } catch (e) {
                console.warn('Audio playback error:', e);
            }
        }

        // Estado inicial de silenciado
        if (bellBtn) {
            const isMuted = localStorage.getItem('mesaly_admin_sound_muted') === 'true';
            bellBtn.classList.toggle('is-muted', isMuted);
            bellBtn.title = isMuted 
                ? 'Sonido de pedidos desactivado (Clic para activar)' 
                : 'Sonido de pedidos activo (Clic para silenciar o probar)';

            bellBtn.addEventListener('click', function () {
                const currentMuted = localStorage.getItem('mesaly_admin_sound_muted') === 'true';
                const nextMuted = !currentMuted;
                localStorage.setItem('mesaly_admin_sound_muted', nextMuted ? 'true' : 'false');
                bellBtn.classList.toggle('is-muted', nextMuted);
                bellBtn.title = nextMuted 
                    ? 'Sonido de pedidos desactivado (Clic para activar)' 
                    : 'Sonido de pedidos activo (Clic para silenciar o probar)';

                // Al hacer clic en la campana se limpia el punto rojo de alerta
                if (bellDot) {
                    bellDot.classList.add('is-hidden');
                }

                if (!nextMuted) {
                    playChime();
                    window.mostrarAdminToast('🔔 Sonido de pedidos activado');
                } else {
                    window.mostrarAdminToast('🔕 Sonido de pedidos silenciado');
                }
            });
        }

        // Sincronización en vivo del plano de mesas en el salón (Dashboard)
        function actualizarPlanoMesas(mesas, metricasHoy) {
            if (!Array.isArray(mesas)) return;

            mesas.forEach(function (m) {
                const card = document.getElementById('table_node_' + m.id) ||
                             document.querySelector('[data-mesa-node-id="' + m.id + '"]');
                if (!card) return;

                const wasOccupied = card.classList.contains('is-occupied');
                const isOccupied = !!m.ocupada;

                if (wasOccupied !== isOccupied) {
                    if (isOccupied) {
                        card.classList.remove('is-free');
                        card.classList.add('is-occupied', 'table-node-pulse');
                        setTimeout(function () {
                            card.classList.remove('table-node-pulse');
                        }, 1200);
                    } else {
                        card.classList.remove('is-occupied');
                        card.classList.add('is-free');
                    }
                }

                // Actualizar insignia Libre / Ocupada
                const badge = card.querySelector('.table-node-badge');
                if (badge) {
                    badge.className = 'table-node-badge ' + (isOccupied ? 'occupied' : 'free');
                    badge.textContent = isOccupied ? 'Ocupada' : 'Libre';
                }

                // Actualizar bloque de contenido
                const content = card.querySelector('.table-node-content');
                if (content) {
                    if (isOccupied) {
                        let estadoTexto = 'En cocina';
                        if (m.estado_pedido === 'entregado') {
                            estadoTexto = 'Comensales en mesa';
                        } else if (m.estado_pedido === 'listo') {
                            estadoTexto = 'Listo para servir';
                        }

                        const totalStr = m.total_formateado || '$ 0';
                        const min = typeof m.tiempo_min === 'number' ? m.tiempo_min : 0;

                        content.innerHTML =
                            '<div class="table-node-total">' + totalStr + '</div>' +
                            '<div class="table-node-meta">' +
                                '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>' +
                                '<span>Hace ' + min + ' min</span>' +
                                '<span>&bull;</span>' +
                                '<span class="fw-600 text-main">' + estadoTexto + '</span>' +
                            '</div>';
                    } else {
                        const nombreStr = m.nombre ? m.nombre : 'Lista para ordenar';
                        content.innerHTML =
                            '<div class="table-node-meta text-muted">' +
                                '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>' +
                                '<span>' + nombreStr + '</span>' +
                            '</div>';
                    }
                }

                // Actualizar botones de acción (Comanda + Liberar vs Abrir QR)
                const actions = card.querySelector('.table-node-actions');
                if (actions) {
                    if (isOccupied && m.url_comanda) {
                        actions.innerHTML =
                            '<a href="' + m.url_comanda + '" class="table-node-link">' +
                                '<span>Comanda</span> &rarr;' +
                            '</a>' +
                            '<button type="button" class="btn-liberar-mesa-node btn-ajax-liberar" data-liberar-url="' + (m.url_liberar || '') + '" title="Liberar mesa (marcar como disponible y sin clientes)">' +
                                '<svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>' +
                                '<span>Liberar</span>' +
                            '</button>';
                    } else {
                        actions.innerHTML =
                            '<a href="' + (m.url_qr || '#') + '" target="_blank" rel="noopener" class="table-node-link text-muted">' +
                                '<span>Abrir QR</span> ' +
                                '<svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>' +
                            '</a>';
                    }
                }
            });

            // Actualizar la insignia resumen del salón ("X ocupadas • Y libres")
            const summaryBadge = document.getElementById('floorPlanSummaryBadge');
            if (summaryBadge && metricasHoy) {
                const ocupadas = parseInt(metricasHoy.mesas_ocupadas || 0, 10);
                const total = parseInt(metricasHoy.total_mesas || 0, 10);
                const libres = Math.max(0, total - ocupadas);
                summaryBadge.classList.toggle('has-occupied', ocupadas > 0);
                summaryBadge.innerHTML =
                    '<span class="fp-ocupadas">' + ocupadas + ' ocupadas</span>' +
                    '<span>&bull;</span>' +
                    '<span class="fp-libres">' + libres + ' libres</span>';
            }
        }

        // Sincronización en vivo de la cola de cocina y despacho
        function actualizarColaCocina(comandas) {
            const container = document.getElementById('liveKitchenBody');
            if (!container || !Array.isArray(comandas)) return;

            if (comandas.length === 0) {
                container.innerHTML =
                    '<div class="live-empty-kitchen">' +
                        '<div class="icon-bubble-lg mx-auto mb-3">' +
                            '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>' +
                        '</div>' +
                        '<h3 class="text-sm fw-700 text-main m-0 mb-1">¡Cocina y barra al día!</h3>' +
                        '<p class="text-xs text-muted m-0">No hay comandas pendientes por preparar o despachar en este momento.</p>' +
                    '</div>';
                return;
            }

            let html = '';
            comandas.forEach(function (cmd) {
                let origen = '';
                if (cmd.modalidad === 'mesa') {
                    origen = 'Mesa ' + (cmd.mesa_nombre || 'S/N');
                } else if (cmd.modalidad === 'domicilio') {
                    origen = 'Domicilio &bull; ' + (cmd.cliente_nombre || 'Cliente');
                } else {
                    origen = 'Para Llevar';
                }
                origen += ' &bull; ' + (cmd.total_items || 1) + ' platos';

                html +=
                    '<a href="' + cmd.url_comanda + '" class="live-ticket-item text-decoration-none">' +
                        '<div class="live-ticket-left">' +
                            '<span class="live-ticket-code">' +
                                '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>' +
                                '<span>#' + (cmd.consecutivo || cmd.codigo) + '</span>' +
                            '</span>' +
                            '<span class="live-ticket-origin">' + origen + '</span>' +
                        '</div>' +
                        '<div class="live-ticket-right">' +
                            '<span class="live-ticket-status ' + (cmd.estado || 'recibido') + '">' +
                                (cmd.estado_formateado || 'Recibido') +
                            '</span>' +
                            '<span class="live-ticket-time">' + (cmd.tiempo_texto || 'Justo ahora') + '</span>' +
                        '</div>' +
                    '</a>';
            });
            container.innerHTML = html;
        }

        // Intercepta clics en botones de liberar mesa para hacerlo instantáneo vía AJAX
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-ajax-liberar');
            if (!btn) return;
            e.preventDefault();

            const url = btn.getAttribute('data-liberar-url');
            if (!url) return;

            btn.disabled = true;
            btn.style.opacity = '0.5';

            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            const token = csrfMeta ? csrfMeta.getAttribute('content') : '';

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-Token': token
                }
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                btn.disabled = false;
                btn.style.opacity = '1';
                if (data && data.success) {
                    window.mostrarAdminToast('Mesa liberada exitosamente');
                    checkNuevosPedidos();
                } else {
                    window.mostrarAdminToast('No se pudo liberar la mesa');
                }
            })
            .catch(function () {
                btn.disabled = false;
                btn.style.opacity = '1';
            });
        });

        // Polling en segundo plano en tiempo real (cada 3.5 segundos)
        let lastMaxId = 0;
        let isInitialPoll = true;

        function checkNuevosPedidos() {
            if (!bellBtn) return;

            const pollUrl = bellBtn.getAttribute('data-poll-url') || (
                window.location.pathname.includes('/public')
                    ? window.location.origin + '/Mesaly/public/admin/pedidos/poll-nuevos'
                    : '/admin/pedidos/poll-nuevos'
            );

            fetch(pollUrl + '?since_id=' + lastMaxId, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (!data || !data.success) return;

                // Sincronizar siempre todos los contadores y KPIs de la pantalla
                if (data.conteos) {
                    document.querySelectorAll('[data-count-key]').forEach(function (el) {
                        const key = el.getAttribute('data-count-key');
                        if (typeof data.conteos[key] !== 'undefined') {
                            el.textContent = data.conteos[key];
                        }
                    });
                    if (typeof window.actualizarConteosPedidos === 'function') {
                        window.actualizarConteosPedidos(data.conteos);
                    }
                }

                // Sincronizar métricas del dashboard si están presentes
                if (data.metricas_hoy) {
                    document.querySelectorAll('[data-metric-hoy]').forEach(function (el) {
                        const key = el.getAttribute('data-metric-hoy');
                        if (typeof data.metricas_hoy[key] !== 'undefined') {
                            el.textContent = data.metricas_hoy[key];
                        }
                    });
                }
                if (data.metricas_mes) {
                    document.querySelectorAll('[data-metric-key]').forEach(function (el) {
                        const key = el.getAttribute('data-metric-key');
                        if (typeof data.metricas_mes[key] !== 'undefined') {
                            el.textContent = data.metricas_mes[key];
                        }
                    });
                }

                // Sincronizar plano de mesas del salón en vivo sin recargar página
                if (Array.isArray(data.mesas)) {
                    actualizarPlanoMesas(data.mesas, data.metricas_hoy);
                }

                // Sincronizar cola de cocina y despacho en vivo sin recargar página
                if (Array.isArray(data.comandas_activas)) {
                    actualizarColaCocina(data.comandas_activas);
                }

                if (isInitialPoll) {
                    lastMaxId = data.max_id || 0;
                    isInitialPoll = false;
                    return;
                }

                if (data.nuevos_count > 0) {
                    lastMaxId = data.max_id || lastMaxId;

                    // 1. Mostrar punto rojo en la campana
                    if (bellDot) {
                        bellDot.classList.remove('is-hidden');
                    }

                    // 2. Animar campana con vibración
                    bellBtn.classList.remove('bell-ring-active');
                    void bellBtn.offsetWidth;
                    bellBtn.classList.add('bell-ring-active');

                    // 3. Tocar sonido chime
                    playChime();

                    // 4. Notificación flotante Toast con detalle del pedido
                    const p = (data.nuevos_pedidos && data.nuevos_pedidos[0]) ? data.nuevos_pedidos[0] : null;
                    let detalle = '';
                    if (p) {
                        if (p.modalidad === 'mesa') {
                            detalle = '🍽️ ¡Nueva comanda en Mesa ' + (p.mesa_numero || p.cliente_nombre) + '! #' + (p.consecutivo || p.codigo);
                        } else {
                            detalle = '🛵 ¡Nuevo pedido a Domicilio (' + p.cliente_nombre + ')! #' + (p.consecutivo || p.codigo);
                        }
                    } else {
                        detalle = '🔔 ' + data.nuevos_count + ' nuevo(s) pedido(s) recibido(s)!';
                    }
                    window.mostrarAdminToast(detalle);

                    // 5. Notificación nativa del navegador si tiene permisos
                    if (window.Notification && Notification.permission === 'granted') {
                        try {
                            new Notification('Mesaly — Nueva Comanda', {
                                body: detalle.replace(/[^\w\s#—áéíóúÁÉÍÓÚñÑ]/g, '').trim(),
                                icon: '/Mesaly/public/assets/img/logo-icon.svg'
                            });
                        } catch (e) {}
                    }

                    // 6. Si estamos en la pantalla de pedidos (/admin/pedidos), recargar vía background fetch sin parpadeo
                    if (window.location.pathname.includes('/admin/pedidos')) {
                        const nuevosIds = (data.nuevos_pedidos || []).map(function (it) { return it.id; });
                        if (typeof window.recargarPedidosEnVivo === 'function') {
                            window.recargarPedidosEnVivo(nuevosIds);
                        }
                    }
                }
            })
            .catch(function () {
                // Silencioso en caso de pérdida momentánea de conexión
            });
        }

        // Iniciar polling (cada 3.5 segundos)
        if (bellBtn) {
            checkNuevosPedidos();
            setInterval(checkNuevosPedidos, 3500);
        }
    }

    // 8. Menú lateral (Sidebar) interactivo y responsive
    function setupAdminSidebar() {
        const toggleBtn = document.getElementById('adminSidebarToggle');
        const closeBtn = document.getElementById('adminSidebarClose');
        const backdrop = document.getElementById('adminSidebarBackdrop');
        const sidebar = document.getElementById('adminSidebar');

        if (!sidebar) return;

        function openSidebar() {
            sidebar.classList.add('is-open');
            if (backdrop) backdrop.classList.add('is-visible');
            document.body.classList.add('sidebar-open');
        }

        function closeSidebar() {
            sidebar.classList.remove('is-open');
            if (backdrop) backdrop.classList.remove('is-visible');
            document.body.classList.remove('sidebar-open');
        }

        if (toggleBtn) {
            toggleBtn.addEventListener('click', openSidebar);
        }
        if (closeBtn) {
            closeBtn.addEventListener('click', closeSidebar);
        }
        if (backdrop) {
            backdrop.addEventListener('click', closeSidebar);
        }
    }

    // 9. Inicializar al cargar el DOM
    function init() {
        setupDropzones();
        setupImagePreviews();
        setupDropdowns();
        setupSlugifiers();
        setupAdminNotifications();
        setupAdminSidebar();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
