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

    // 2. Previsualización de imágenes segura
    function setupImagePreviews() {
        document.querySelectorAll('input[type="file"][data-preview-img]').forEach(input => {
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

    // 3. Inicializar al cargar el DOM
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', setupImagePreviews);
    } else {
        setupImagePreviews();
    }
})();
