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
                        previewImg.style.display = 'block';
                    } catch (e) {
                        console.error('Error al generar previsualización:', e);
                    }
                }

                if (placeholder) {
                    placeholder.style.display = 'none';
                }

                if (changeBtn) {
                    changeBtn.style.display = 'block';
                }
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

    // 6. Inicializar al cargar el DOM
    function init() {
        setupDropzones();
        setupImagePreviews();
        setupDropdowns();
        setupSlugifiers();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
