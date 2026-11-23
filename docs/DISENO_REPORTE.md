# Reporte de Reforma de Estilos y Cumplimiento CSP Estricto — Mesaly

Este documento registra la eliminación completa de atributos `style="..."`, eliminación de manejadores de eventos en línea, adopción de `tokens.css` y estandarización del sistema de diseño (Taste & Impeccable) en Mesaly.

## Reglas Obligatorias Aplicadas
1. **0 atributos `style="..."`** en HTML, SVG o cadenas PHP/JS.
2. **0 manejadores de eventos en línea** (`onclick`, `onchange`, etc.).
3. **100% de bloques `<style>` y `<script>` con `nonce`**.
4. **Escala de espaciado base 4px** (`--space-1` a `--space-8`) y escala tipográfica fija (12, 13, 14, 16, 20, 24, 32 px) importada desde `public/assets/css/tokens.css`.
5. **Colores y radios derivados de tokens CSS**, nunca valores sueltos hardcodeados.
6. **Excepción documentada:** `app/Views/admin/mesas/pdf_tarjetas.php`, plantilla renderizada en el backend por Dompdf para generación de PDF binario (no se ejecuta en navegador cliente, no sujeta a directivas CSP HTTP).

---

## Registro de Archivos Modificados

| Archivo | `style=""` Antes | `style=""` Después | Clases / Tokens Creados o Utilizados | Estado |
| :--- | :---: | :---: | :--- | :---: |
| `app/Views/layouts/admin.php` | 4 | 0 | `impersonation-bar`, `btn-impersonation-return`, `tokens.css` | Completado |
| `app/Views/layouts/super.php` | 2 | 0 | `flex`, `items-center`, `gap-3`, `text-sm`, `fw-600`, `tokens.css` | Completado |
| `app/Views/layouts/auth.php` | 0 | 0 | `tokens.css` cargado como base | Completado |
| `app/Views/layouts/menu.php` | 0 | 0 | `tokens.css` cargado como base | Completado |
| `app/Views/layouts/main.php` | 1 | 0 | `container-main-shell`, `tokens.css` | Completado |
| `app/Views/auth/login.php` | 0 | 0 | `auth-form`, `auth-toggle-pwd`, `auth-footer-text`, `auth-footer-link` | Completado |
| `app/Views/layouts/auth.php` | 0 | 0 | Rediseño split moderno con imagen de fondo fluida abstracta (`auth-bg.jpg`), `auth-hero-sparkle`, `auth-hero-headline`, `auth-mobile-hero`, `auth-desktop-brand`, `auth.js` | Completado |
| `app/Views/auth/cambiar_password.php` | 0 | 0 | `auth-toggle-pwd` para show/hide password, armonizado con login | Completado |
| `app/Views/errors/403.php` | 2 | 0 | `text-danger`, `mx-auto` | Completado |
| `app/Views/errors/404.php` | 1 | 0 | `mx-auto` | Completado |
| `app/Views/errors/500.php` | 2 | 0 | `text-subtle`, `mx-auto` | Completado |
| `app/Views/public/menu.php` | 5 | 0 | `flex`, `items-center`, `gap-2`, `.floating-cart-bar.is-visible` | Completado |
| `app/Views/admin/suscripcion_vencida.php` | 16 | 0 | `card-suscripcion-vencida`, `suscripcion-icon-bubble`, `suscripcion-details-box`, `suscripcion-detail-row` | Completado |
| `app/Views/admin/disponibilidad/index.php` | 16 | 0 | `icon-bubble-lg`, `card-header-bar`, `text-md`, `mb-5`, `text-success`, `text-danger`, `avail-row--off` | Completado |
| `app/Views/admin/catalogo/index.php` | 33 | 0 | `card-header-bar`, `modal-dialog-md`, `btn-badge-toggle`, `input-w-sm`, `form-checkbox`, `empty-state-box`, `col-foto` | Completado |
| `app/Views/admin/modificadores/index.php` | 36 | 0 | `modifier-icon-box`, `modifier-options-header`, `modifier-empty-options`, `bullet-dot`, `btn-sm-icon`, `price-input-wrapper`, `multi-limits-grid` | Completado |
| `app/Views/admin/catalogo/producto_form.php` | 50 | 0 | `form-layout-container`, `btn-back-link`, `card-p-6`, `card-p-7`, `toggle-group-border`, `card-header-bordered`, `price-input-container`, `input-price-field`, `modifier-check-card`, `form-actions-bar` | Completado |
| `app/Views/admin/config/index.php` | 52 | 0 | `config-icon-*`, `preset-swatch-*`, `dropzone-preview-logo`, `dropzone-preview-banner`, `color-preview-box`, `btn-preview-sample`, `preview-text-sample`, `modalities-grid` | Completado |
| `app/Views/admin/dashboard/index.php` | 69 | 0 | `dot-indicator`, `badge-counter`, `kpi-card--warning`, `charts-grid`, `chart-container-wrap`, `rank-badge`, `module-card-danger` | Completado |
| `app/Views/admin/mesas/index.php` | 84 | 0 | `qr-general-card`, `qr-general-url-text`, `salon-summary-box`, `quick-batch-label`, `btn-quick-batch`, `mesas-grid`, `mesa-card`, `mesa-badge-active`, `mesa-badge-inactive`, `mesa-qr-wrap` | Completado |
| `app/Views/admin/pedidos/index.php` | 151 | 0 | `table-header-domicilio`, `table-header-salon`, `badge-orange-subtle`, `badge-blue-subtle`, `btn-wa-notify-inline`, `table-row-code`, `table-row-total`, `order-item-*`, `estado-sel--*`, `estado-badge--*`, `pagination-bar` | Completado |
| `app/Views/super/negocios/index.php` | 20 | 0 | `card-header-bar`, `empty-state-box`, `icon-bubble-lg`, `table-container`, `action-btns` | Completado |
| `app/Views/super/dashboard.php` | 22 | 0 | `kpi-grid`, `card-header-bar`, `empty-state-box`, `text-brand`, `text-warning`, `text-success` | Completado |
| `app/Views/super/negocios/credenciales.php` | 23 | 0 | `credentials-container`, `card-credentials`, `bubble-check-success`, `alert-temp-pass`, `credentials-box`, `slug-pill`, `email-display-box`, `temp-pass-box`, `btn-wa-send` | Completado |
| `app/Views/super/negocios/crear.php` | 37 | 0 | `container-form-md`, `section-icon-box`, `section-icon-brand`, `section-icon-success`, `section-icon-blue`, `input-price-green` | Completado |
| `app/Views/super/pagos/index.php` | 45 | 0 | `kpi-card-modern`, `select-filter-compact`, `badge-note-subtle`, `table-row-total`, `price-input-wrapper` | Completado |
| `app/Views/super/negocios/editar.php` | 52 | 0 | `container-form-md`, `card-admin-danger`, `btn-save-super`, `section-icon-box` | Completado |
| `app/Views/super/metricas/index.php` | 68 | 0 | `kpi-val-28`, `stat-box-super`, `stat-box-title`, `stat-val-super`, `chart-doughnut-wrap` | Completado |
| `app/Views/super/vencimientos/index.php` | 82 | 0 | `btn-register-negocio`, `btn-cobrar-wa`, `btn-submit-pago`, `input-price-super`, `modal-dialog-modern` | Completado |


---

## Mejoras Operativas y de Experiencia de Usuario (Fases 1 a 5)

| Componente | Archivos | Mejoras Realizadas |
| :--- | :--- | :--- |
| **Fase 1: Despacho Inmediato en Pedidos** | `app/Views/admin/pedidos/index.php`, `app/Controllers/Admin/PedidoAdminController.php` | Salida inmediata de filas con animación CSS suave (`.order-row--exit`), actualización instantánea de contadores numéricos en pestañas y filtros sin recargar. |
| **Fase 2: Notificaciones en Vivo con Sonido** | `app/Views/layouts/admin.php`, `public/assets/js/admin.js`, `public/assets/css/admin.css` | Campana de notificaciones con punto rojo pulsante (`#btnAdminBell`, `#adminBellDot`), sintetizador Web Audio API autónomo (chime D5→A5, 0 dependencias externas/CDN), polling reactivo cada 15s (`/admin/pedidos/poll-nuevos`) y toasts flotantes. |
| **Fase 3: Header y Categorías en Móvil** | `app/Views/public/menu.php`, `public/assets/css/mesaly.css`, `public/assets/js/menu-app.js` | Descongestión del header móvil, micro-pills con `var(--menu-primary)`, tipografía moderna (`Outfit`/`Inter`), degradado lateral derecho y banner guía interactivo *"Desliza para ver más categorías →"* que se desvanece al deslizar o filtrar. |
| **Fase 4: Menú PC Full-Width** | `public/assets/css/mesaly.css` | Expansión de `.menu-container` a `1440px` en escritorio, cuadrícula de 4 columnas de productos y banner panorámico de 280px. |
| **Fase 5: Modal de Confirmación y WhatsApp** | `app/Views/public/menu.php`, `public/assets/js/menu-app.js`, `public/assets/css/mesaly.css` | Modal popup de confirmación con código de pedido `#PAR-XXXX`, vaciado automático del carrito, botón de reintento de apertura a WhatsApp y apertura externa en segundo plano (`window.open`) para mantener la sesión del comensal abierta. |

---

## Verificación de Criterios de Aceptación

Resultados de la suite de validación automatizada:

1. `grep -rn 'style="' app/Views app/Core public/assets/js`: **0 ocurrencias** (salvo la excepción del generador PDF Dompdf en `pdf_tarjetas.php`).
2. `grep -rnE 'on(click|change|input|submit)=' app/Views`: **0 ocurrencias**.
3. `grep -rnE 'setAttribute\(.style' public/assets/js`: **0 ocurrencias**.
4. Bloques `<style>` y `<script>` sin `nonce` en `app/Views`: **0 ocurrencias** (todos protegidos por `nonce="<?= e($cspNonce) ?>"`).
5. Violaciones CSP en consola del navegador (Puppeteer Headless): **0 violaciones** (`Refused to apply inline style`: 0, `Refused to execute inline`: 0).
6. Suite de Pruebas Unitarias PHPUnit: **56 de 56 pruebas aprobadas (100%)**, 460 aserciones, 0 errores, 0 fallos.
