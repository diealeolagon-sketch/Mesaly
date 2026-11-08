# Auditoría 01 — Estado actual de Mesaly

Fecha de la auditoría: 8 de noviembre de 2026
Base revisada: `Mesaly.zip` (último commit: `f27f03c`, fases 0, 1A y 1B más varias rondas de rediseño de interfaz).

> **Alcance y límites.** Esta auditoría es **de código**: leí controladores, vistas, CSS, JS, esquema, rutas, logs e historial de git. **No pude ejecutar la aplicación** (no hay PHP en el entorno de auditoría), así que los hallazgos que dependen del navegador (CSP, renderizado, fuentes) deben **confirmarse abriendo DevTools → Console** en el navegador. Cada hallazgo indica cómo comprobarlo. Las pruebas de PHPUnit tampoco se ejecutaron.

---

## 1. Resumen

| # | Hallazgo | Gravedad | Qué ve el usuario |
|---|---|---|---|
| 1 | La CSP combina `'unsafe-inline'` con `nonce`: los navegadores **ignoran** `'unsafe-inline'` cuando hay nonce | **Crítica** | Cambios del admin que "no cargan": colores y tipografía del restaurante no se aplican al menú, vistas previas y botones del formulario no responden |
| 2 | Las fuentes de Google están **bloqueadas por la propia CSP** y la carpeta `assets/fonts/` está vacía | **Alta** | Tipografía "muy básica": siempre cae a la fuente del sistema, elijas lo que elijas |
| 3 | La **Fase 4 (suscripciones y pagos) no está construida**: solo existen las tablas | **Alta** | No hay botón "Registrar pago", ni semáforo, ni recordar pago, ni extender, ni suspender |
| 4 | El menú público ya **bloquea por vencimiento** con 3 días "quemados" y no hay forma de renovar desde la interfaz | **Alta (riesgo)** | Un negocio con fecha vencida queda bloqueado y solo se arregla con SQL |
| 5 | **61 emojis** en interfaz, JavaScript y mensaje de WhatsApp | Media | Se ve poco profesional |
| 6 | `POST /{slug}/pedido` y `/{slug}/mesa/limpiar` **sin protección CSRF** | Media | (invisible; incumple la sección 9.5) |
| 7 | La mesa llega por `/{slug}/mesa/{n}` (no `?mesa=N`) y **no se valida contra una tabla de mesas**; el número de mesa del pedido lo manda el cliente | Media | Se puede pedir "Mesa 999" |
| 8 | Archivos CSS muertos, versionado de caché manual y `user-scalable=no` | Baja | Cambios de CSS que no se ven por caché; no se puede hacer zoom |
| 9 | **Cambio de requisito:** el código actual impone planes y límites (productos, categorías, mesas) que ya no se quieren | **Alta** | El admin del negocio ve "Límite del plan" y no puede crear más productos o categorías |

---

## 2. Hallazgos detallados

### Hallazgo 1 — CSP que invalida los estilos y scripts en línea (CRÍTICO)

**Evidencia**
- `app/Core/App.php:83` envía:
  `style-src 'self' 'unsafe-inline' 'nonce-{$nonce}'; script-src 'self' 'unsafe-inline' 'nonce-{$nonce}'`
- Según la especificación CSP nivel 3, **cuando una directiva incluye un nonce o un hash, el navegador ignora `'unsafe-inline'`**. Es decir, la regla efectiva es "solo lo que lleve el nonce correcto".
- `app/Views/layouts/menu.php:33` abre un `<style>` **sin atributo `nonce`**. Ahí se definen `--menu-primary`, `--menu-secondary`, `--menu-bg`, `--menu-text` y `--menu-font`. Si el navegador lo bloquea, **los colores y la fuente que el restaurante guardó nunca llegan al menú**: el CSS cae a los valores por defecto (`var(--menu-primary, #E85D04)` aparece 30 veces en `mesaly.css`).
- **32 manejadores en línea** (`onchange=`, `oninput=`, `onclick=`) que quedarían inertes: 17 en `admin/config/index.php` (vista previa de logo y banner, selector de color, presets de paleta), 7 en `modificadores/index.php`, 5 en `catalogo/index.php`, 1 en `producto_form.php`, 1 en `disponibilidad/index.php` y 1 en `super/negocios/index.php`.
- **Unos 340 atributos `style="..."`** repartidos por casi todas las vistas (el helper `icon()` incluso agrega uno a cada ícono, `app/Core/helpers.php:77`).
- El historial lo confirma: los commits `6de51c1` ("permitir unsafe-inline en style-src") y `7138b43` ("resolver carga de estilos… nonce CSP") intentaron arreglar esto parcheando, pero la causa de fondo sigue.

**Cómo confirmarlo:** abrir el menú y el formulario de configuración, presionar F12 → Console. Deberían aparecer mensajes como *"Refused to apply inline style because it violates the following Content Security Policy directive"* y *"Refused to execute inline event handler"*.

**Corrección requerida**
1. Dejar una CSP **estricta y coherente**:
   ```
   default-src 'self';
   script-src 'self' 'nonce-{nonce}';
   style-src  'self' 'nonce-{nonce}';
   img-src 'self' data:;
   font-src 'self';
   connect-src 'self';
   object-src 'none'; base-uri 'self'; form-action 'self'; frame-ancestors 'none'
   ```
   Sin `'unsafe-inline'` en ninguna directiva.
2. **Todo `<style>` y `<script>` ejecutable lleva `nonce="<?= e($cspNonce) ?>"`**, incluido el bloque de variables de marca de `layouts/menu.php`.
3. **Eliminar los 32 manejadores en línea**: pasar a archivos JS externos (`admin.js`) con `addEventListener` y atributos `data-*` (por ejemplo `data-action="aplicar-preset" data-primario="#E85D04"`).
4. **Eliminar los ~340 `style="..."`**: moverlos a clases CSS. Cuando un valor sea realmente dinámico (un ancho, un color de un dato), pasarlo por `data-*` y asignarlo con JavaScript (`el.style.setProperty(...)`), que la CSP sí permite.
5. El helper `icon()` no debe emitir `style=`; usar una clase `.icon`.
6. Quitar `https:` de `img-src` salvo que haya una razón real.

**Criterio de aceptación:** al recorrer todas las pantallas, la consola del navegador no muestra **ninguna** violación de CSP, y la vista previa de colores, los presets, la vista previa de logo/banner y los switches funcionan.

---

### Hallazgo 2 — Tipografía: no carga ninguna fuente (ALTA)

**Evidencia**
- `layouts/menu.php:23-25` carga Inter, Outfit y Poppins desde `fonts.googleapis.com`, pero la CSP no permite ese origen en `style-src`, así que **la hoja de estilos de Google queda bloqueada**.
- `public/assets/fonts/` está **vacía**; la especificación (9.8 y 15.3) pide fuentes `.woff2` alojadas en el propio servidor.
- La lista blanca de `ConfigController.php` incluye `Roboto` y `System`, pero el `<select>` solo ofrece cuatro opciones; Playfair se puede elegir pero **nunca se carga**.
- Una sola familia sirve para títulos y texto; `font-weight: 800` aparece 30 veces en las reglas del menú (todo pesa lo mismo, sin jerarquía); hay tamaños con medios píxeles (13.5, 12.5, 15.5 px) sin una escala.
- `brand.css` define `--font-heading` (Poppins) y `--font-body` (Inter), pero **ningún layout carga `brand.css`**.

**Corrección requerida**
1. **Alojar las fuentes localmente** (`.woff2`, licencia OFL, subconjunto latino + latino extendido, solo los pesos necesarios) en `public/assets/fonts/` con `@font-face` y `font-display: swap`. Precargar (`<link rel="preload">`) únicamente la fuente principal del menú.
2. **Cambiar la selección de "una fuente" por pares tipográficos** (títulos + texto), que es lo que da un aspecto profesional:

   | Opción | Títulos | Texto | Carácter |
   |---|---|---|---|
   | Moderna | Outfit | Inter | Limpia, tecnológica |
   | Cálida | Fraunces | Inter | Acogedora, gastronómica |
   | Elegante | Playfair Display | Inter | Restaurante de mantel |
   | Urbana | Oswald | Inter | Parrilla, hamburguesas |
   | Sistema | (fuente del dispositivo) | (fuente del dispositivo) | Sin descarga |

   Migrar los valores existentes en la columna `fuente` (Inter, Poppins, Outfit → Moderna; Playfair → Elegante).
3. **Escala tipográfica del menú** con variables: 12 / 14 / 16 / 18 / 22 / 28 px. Texto base de **16 px**, `line-height` 1.5, solo pesos 400, 500, 600 y 700. Nombre de producto 17 px/600; precio con `font-variant-numeric: tabular-nums`; títulos de sección 22–24 px con `letter-spacing: -0.01em`.
4. Cargar **brand.css** en los layouts de plataforma (admin, super, auth) o eliminarlo; el panel debe usar Poppins/Inter propios de Mesaly.

**Criterio de aceptación:** sin conexión a Internet y con la CSP estricta, el menú muestra el par elegido; cambiar el par en configuración cambia el menú al recargar.

---

### Hallazgo 3 — No existe el flujo de pagos y suscripciones (ALTA)

**Evidencia:** no hay `SuscripcionService`, `PagoController`, ni vistas o rutas de pagos (`config/routes.php` solo tiene crear/editar negocio y reset de contraseña). La tabla `pagos_suscripcion` y los campos `fecha_vencimiento`, `dia_corte`, `estado_manual` y `ultimo_recordatorio_at` existen en `schema.sql` pero ningún código los usa. El Super Admin solo **muestra** la fecha de vencimiento (`super/negocios/index.php:82`, `super/dashboard.php:111`).

Esto no es un error de implementación sino **trabajo pendiente de la Fase 4**, que quedó atrás mientras se hicieron varias rondas de rediseño visual. Se propone adelantarla (ver sección 4).

**Alcance mínimo requerido** (sección 5 y 7 de `ESPECIFICACION_COMPLETA.md`):
- `SuscripcionService::estadoDe()` con estados calculados al vuelo y colores de 15.2; días de alerta y gracia **leídos de `configuracion`** (hoy están en `.env`).
- Pantalla "Vencimientos" ordenada por fecha, con semáforo, días restantes, último pago y filtros.
- Botón **Registrar pago** (fecha, monto, método, periodos, nota) con el cálculo de nueva fecha de 7.4, en transacción y con auditoría.
- Botón **Recordar pago** (enlace `wa.me` con mensaje según el estado y guardado de `ultimo_recordatorio_at`).
- Botones **Extender días** y **Suspender / Reactivar**.
- Historial de pagos por negocio.
- `SuscripcionMiddleware` en `/admin/*` (pantalla de reactivación) y bloqueo del menú público y de `POST /{slug}/pedido`.
- Banner de semáforo en el admin del negocio.
- Pruebas PHPUnit de todas las transiciones y del cálculo de fechas (incluye 31 de enero → 28 de febrero).

### Hallazgo 4 — Bloqueo por vencimiento duplicado y sin salida (ALTA, riesgo)

`MenuController::show` (líneas 37–44) bloquea el menú público si pasan **3 días** del vencimiento, con el número escrito a mano, sin considerar `estado_manual`, plan de gracia configurable ni `fecha_fin_prueba`. Además el panel del negocio **no se bloquea**, y no existe forma de registrar un pago. Resultado: cualquier negocio vencido queda con el menú caído y la única salida es editar la base de datos.

**Corrección:** eliminar esa lógica del controlador y usar solo `SuscripcionService` (un único lugar). Hasta que exista el botón de pago, **no activar fechas de vencimiento reales** en negocios de clientes.

---

### Hallazgo 5 — Emojis (MEDIA)

**61 apariciones.** Dónde están:

| Archivo | Cantidad | Ejemplos |
|---|---|---|
| `app/Views/public/menu.php` | 23 | carrito, chips de modalidad, estrellas, métodos de pago, botón de WhatsApp, pantalla de éxito |
| `app/Services/WhatsAppService.php` | 21 | mensaje del pedido |
| `app/Views/layouts/auth.php` | 7 | pantalla de login |
| `public/assets/js/menu-app.js` | 3 | notas y carrito vacío |
| `admin.php`, `credenciales.php`, `editar.php`, `producto_form.php`, `mesaly.css` | 1 a 2 c/u | ✓ ✕ ⚠ ⭐ |

**Corrección requerida**
1. Reemplazar **todos** los emojis de la interfaz por **íconos SVG de trazo** del mismo estilo del helper `icon()` (trazo 1.75, esquinas redondeadas). Íconos necesarios: cubiertos (mesa), moto (domicilio), bolsa (recoger), carrito, estrella, billete, celular, tarjeta, check, alerta, burbuja de chat, flecha, candado, rayo, etiqueta, búsqueda, más, menos, cerrar.
2. **Mensaje de WhatsApp sin emojis**, en texto limpio con negritas de WhatsApp y separadores. Los emojis se ven distinto en cada celular y en Android antiguos pueden aparecer como cuadros. Formato propuesto:
   ```
   *PEDIDO #PED-0142*
   Mesa 5 · Cliente: Juan Pérez
   ------------------------------
   2 x Hamburguesa clásica — $ 36.000
      Término: Tres cuartos
      Extra: Tocineta
      Nota: Sin cebolla
   1 x Limonada de coco — $ 9.000
   ------------------------------
   Subtotal: $ 45.000
   Domicilio: $ 4.000
   *TOTAL: $ 49.000*
   Pago previsto: Efectivo
   ```
   Actualizar `HorarioAndWhatsAppServiceTest.php` y la sección 8.5 de la especificación.
3. En la pantalla de login (`auth.php`) quitar las "tarjetas" con emojis (🔥🥩) y usar una composición con el logo, el eslogan y capturas o formas de marca.
4. Los avisos ✓ ✕ del admin pasan a íconos SVG dentro de una clase `.alert`.

**Criterio de aceptación:** una búsqueda de emojis en `app/`, `public/assets/js` y `public/assets/css` devuelve **cero** resultados (se puede automatizar como prueba).

---

### Hallazgo 6 — Falta CSRF en endpoints públicos (MEDIA)

`config/routes.php` registra `POST /{slug}/pedido` y `POST /{slug}/mesa/limpiar` **sin `CsrfMiddleware`**. La sección 9.5 exige token en todas las peticiones de escritura. El rate limit (5 pedidos en 10 minutos por IP) y el honeypot existen y están bien, pero el token falta.

**Corrección:** emitir un token de sesión en el menú público (dentro del JSON `mesalyRestaurantData`), enviarlo en el encabezado `X-CSRF-Token` desde `menu-app.js` y validarlo en ambas rutas.

### Hallazgo 7 — Mesas (MEDIA)

- Se usa `/{slug}/mesa/{numero}` en vez del `?mesa=N` de la especificación (secciones 4 y 8.1). Los QR que se impriman deben coincidir con lo definido.
- `MenuController::mesa` guarda **cualquier número** en sesión sin comprobar que la mesa exista y esté activa (la tabla `mesas` existe pero no se usa todavía).
- En `crearPedido` (líneas 232–237) `mesa_numero` se toma de la **entrada del cliente**; se puede enviar cualquier valor.
- La expiración de 4 horas **sí** está implementada. Bien.

**Corrección (se resuelve en la Fase 2):** soportar `?mesa=N` (puede mantenerse la ruta actual como alias), validar contra la tabla `mesas` por restaurante, y en `crearPedido` ignorar `mesa_numero` del cliente y usar solo la mesa de la sesión validada.

### Hallazgo 8 — Orden y mantenimiento (BAJA)

- `mesaly.css` tiene **3.697 líneas** que mezclan panel, login y menú. `admin.css` (872 líneas) y `brand.css` (176) **no se cargan en ninguna vista**: son código muerto.
- Versionado de caché manual y desigual: panel `?v=5`, menú `?v=7`, JS `?v=7`. Si se cambia un CSS sin subir el número, el navegador muestra la versión anterior. Usar `filemtime()` automático.
- Meta viewport del menú: `maximum-scale=1.0, user-scalable=no` impide el zoom (accesibilidad). Quitarlo.
- Dividir CSS en `brand.css` (variables), `admin.css`, `auth.css` y `menu.css` (como indicaba el plan aprobado).
- `layouts/menu.php` y `public/menu.php` (509 líneas) mezclan lógica, HTML y estilos en línea; separar en parciales (`partials/producto-card.php`, `partials/carrito.php`, `partials/checkout.php`).
- El `.env` y los logs venían dentro del zip (no están en git, pero no los compartas ni los subas a un hosting). El proyecto trae una carpeta `skills/` (cientos de archivos) que no debe subirse nunca a producción. En los logs aparece un `scratch_test_user.php`: confirmar que no esté en la raíz.
- `APP_DEBUG=true` es correcto en local; recordar `false` en producción.

---

### Hallazgo 9 — Planes y límites a eliminar (CAMBIO DE REQUISITO)

**Decisión:** no habrá planes ni topes de uso. Todos los negocios tienen todo ilimitado y el Super Admin fija el precio de cada negocio según su caso (sección 5.5 de la especificación).

**Dónde está el código a quitar o cambiar**
- `app/Controllers/Admin/CatalogoController.php` líneas 46–48, 85–86 y 218–219: leen `max_categorias` y `max_productos` del plan y **bloquean la creación** al llegar al tope (con 999 como respaldo). Eliminar esas comprobaciones por completo.
- `app/Views/admin/dashboard/index.php` líneas 35, 44 y 61: tarjetas con "Límite del plan: N" y el nombre del plan. Mostrar solo el conteo de categorías y productos, y reemplazar el nombre del plan por el estado de la suscripción.
- `app/Controllers/Admin/DashboardController.php`: quitar el uso de `Plan`.
- `app/Controllers/Super/NegocioController.php` (crear y editar, líneas 44–76, 122, 190–254): quitar `plan_id` y el listado de planes; recibir y validar `precio` (entero mayor o igual a 0, COP) y `notas_internas`.
- `app/Views/super/negocios/crear.php` y `editar.php`: reemplazar el selector de plan por un campo **Precio acordado (COP)** y un área de **Notas internas**.
- `app/Views/super/negocios/index.php` y `super/dashboard.php`: reemplazar la columna "Plan Contratado" por "Precio acordado".
- `app/Models/Plan.php` y `Restaurante::allWithPlan()` (con su `JOIN planes`): eliminar el modelo y simplificar la consulta.
- `config/slugs_reservados.php` línea 40: `planes` puede quedar como slug reservado, es inofensivo.

**Base de datos (migración, no solo editar `schema.sql`)**
1. `ALTER TABLE restaurantes ADD COLUMN precio INT UNSIGNED NOT NULL DEFAULT 0, ADD COLUMN notas_internas TEXT NULL, ADD COLUMN mostrar_marca TINYINT(1) NOT NULL DEFAULT 1;`
2. Copiar el precio actual de cada negocio desde su plan (`precio_mensual` o `precio_anual` según el `ciclo`) a `restaurantes.precio`.
3. Quitar la llave foránea `fk_restaurantes_plan` y la columna `plan_id`; después `DROP TABLE planes`.
4. Actualizar `schema.sql` y `seeds.sql` (el seed actual crea dos planes con topes de 50 y 150 productos) y guardar la migración en `database/migrations/`.

**Impacto en el Bloque B (suscripciones y pagos)**
- No hay CRUD de planes ni validación de topes.
- El formulario de **Registrar pago** prellena el monto con `restaurantes.precio` (editable).
- El **MRR** suma el `precio` de cada negocio activo llevado a base mensual (anual dividido entre 12).
- El campo `mostrar_marca` reemplaza al antiguo `quitar_marca` por plan.

**Criterio de aceptación:** se puede crear un negocio sin elegir ningún plan, un negocio puede tener 500 productos y 100 mesas sin ningún bloqueo, y la búsqueda de `max_productos`, `max_mesas`, `max_categorias`, `plan_id` y `Plan` en el código y el esquema no devuelve resultados.

---

## 3. Lo que está bien (no tocar)

- **Recálculo de precios en el servidor** (`MenuController::crearPedido`, líneas 283–387): consulta cada producto y opción del restaurante, valida disponibilidad y congela nombre y precio.
- **Honeypot y límite por IP** en la creación de pedidos.
- **Aislamiento multitenant** centralizado (`TenantContext` y `TenantModel`) con pruebas dedicadas.
- Consultas preparadas, validación hexadecimal de colores, procesamiento de imágenes a WebP.
- **Expiración de la mesa en sesión a 4 horas**.
- Seis archivos de pruebas con cobertura de seguridad, catálogo, pedido público y WhatsApp.
- Los errores de los logs (layout duplicado `layouts/layouts`, variable `$todosLosProductos`) ya fueron corregidos en commits posteriores.

---

## 4. Plan de corrección (orden recomendado)

Cada bloque termina con un commit, pruebas y revisión de seguridad. **Esperar confirmación entre bloques.**

### Bloque A — Base: CSP, fuentes e íconos (primero)
1. CSP estricta y coherente (hallazgo 1), con el criterio de "consola sin violaciones".
2. Eliminar manejadores y `style=""` en línea; pasar a clases y `data-*`.
3. Fuentes alojadas localmente, pares tipográficos y escala (hallazgo 2).
4. Sustituir emojis por íconos SVG y limpiar el mensaje de WhatsApp (hallazgo 5).
5. Hallazgo 8: CSS por archivo, `filemtime()`, zoom habilitado.
6. Prueba automática que falle si reaparecen emojis, `style="` o `on...="` en las vistas.

### Bloque B — Fase 4 mínima: suscripciones y pagos, sin planes ni límites (antes de las fases 2 y 3)
Todo el alcance del hallazgo 3, más los hallazgos 4 y 9 (eliminar planes y límites, precio por negocio). Se adelanta porque el menú **ya bloquea por vencimiento** y hoy no hay cómo renovar.

### Bloque C — Rediseño del menú público
Con la base arreglada, un pase de diseño del menú: jerarquía tipográfica, tarjetas de producto sin degradados excesivos, imagen 4:3, precio destacado, pestañas de categoría fijas, barra de carrito con área segura, estados de foco, objetivos táctiles de 44 px. Presentar capturas antes y después en el celular.

### Bloque D — Fase 2 (mesas y QR) y Fase 3 (pedidos y métricas)
Incluye los hallazgos 6 y 7. Es el trabajo que sigue en el plan original.

---

## 5. Cambios a la especificación (`ESPECIFICACION_COMPLETA.md`)
1. **6.1 y 15.3:** la tipografía se elige por **par tipográfico** (tabla del hallazgo 2) y se sirve localmente.
2. **8.5:** el mensaje de WhatsApp es **solo texto**, sin emojis (formato del hallazgo 5).
3. **9.8:** la CSP definida arriba y la regla "sin `style=` ni manejadores en línea".
4. **11 y 8.1:** confirmar `?mesa=N` como formato del QR.
5. **7.1:** días de alerta y gracia viven en `configuracion` (no en `.env`).
6. **Nueva regla de interfaz:** prohibidos los emojis en la interfaz; solo íconos SVG.
7. **Sin planes ni límites:** se eliminan la tabla `planes` y los topes; cada negocio tiene `precio`, `notas_internas` y `mostrar_marca` (ya reflejado en 1.2, 5.5, 7.1 y 10).
