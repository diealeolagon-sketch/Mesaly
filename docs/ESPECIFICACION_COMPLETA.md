# Mesaly — Especificación técnica y de producto

SaaS multitenant de menús digitales interactivos con pedidos por WhatsApp, para restaurantes, cafeterías y negocios gastronómicos locales de Colombia.

> **Este documento es la fuente de verdad del proyecto.** Si algo no está claro o hay contradicciones, se pregunta antes de asumir. Nombre de la plataforma: **Mesaly** (identidad de marca completa en la sección 15; confirmar disponibilidad legal antes de registrarla).

---

## 1. Visión y alcance

### 1.1 Qué es
Cada negocio crea su catálogo digital con una URL propia (`https://dominio.com/nombre-restaurante`). Los clientes ven el menú desde el celular, arman un carrito y confirman. El pedido:
1. Se **guarda en la base de datos** con un consecutivo (`#PED-0142`).
2. Abre **WhatsApp** del negocio con un mensaje ya estructurado (`https://wa.me/<numero>?text=<mensaje>`).

El negocio paga una **suscripción mensual o anual** al dueño de la plataforma.

### 1.2 Reglas de producto que NO se negocian
- **Cero pasarelas de pago.** La plataforma no procesa ni cobra dinero, ni de los clientes finales ni de los restaurantes.
- Los "métodos de pago aceptados" del restaurante son **solo informativos** (se muestran al cliente y se incluyen en el mensaje de WhatsApp).
- El cobro de la suscripción es **manual**: el restaurante paga por fuera (Nequi, transferencia, efectivo, etc.) y el Super Admin **registra el pago** en su panel.
- El cliente final **no se registra** nunca.
- **Sin planes y sin límites de uso:** productos, categorías, mesas y pedidos ilimitados para todos los negocios. El precio se pacta por negocio y lo define el Super Admin (ver 5.5).
- Todo debe funcionar bien en **celular con datos limitados** (Colombia).

### 1.3 Fuera de alcance de la versión 1 (no construir todavía)
Cupones, combos, zonas de domicilio con costo por barrio, multi-sucursal, usuarios de equipo (cajero/mesero), impresión de comandas, integración con WhatsApp Business API, notificaciones automáticas por correo/WhatsApp, pagos en línea.
> Dejar la arquitectura preparada para agregarlos (por ejemplo, la tabla de usuarios ya tiene campo `rol`), pero no implementarlos.

---

## 2. Stack y restricciones técnicas

| Aspecto | Decisión |
|---|---|
| Lenguaje | **PHP 8.2+** (compatible con hosting compartido y VPS) |
| Arquitectura | **MVC propio, sin framework pesado** (front controller + router + controladores + modelos + vistas) |
| Base de datos | **MySQL 8 / MariaDB 10.6+**, motor InnoDB, `utf8mb4` |
| Acceso a datos | **PDO con consultas preparadas** (nunca concatenar SQL) |
| Servidor | Apache con `mod_rewrite` (`.htaccess`) o Nginx. Document root apuntando a `/public` |
| Dependencias | **Composer**, mínimas. QR: `chillerlan/php-qrcode` o `endroid/qr-code`. PDF: `dompdf/dompdf`. Variables de entorno: `vlucas/phpdotenv` |
| Frontend | HTML + CSS propio con variables CSS + JavaScript vanilla. Gráficos: **Chart.js** servido localmente. Sin Node ni build obligatorio |
| Zona horaria | `America/Bogota` en PHP y MySQL |
| Moneda | COP, **sin decimales**, guardado como entero (`INT UNSIGNED`). Formato visual `$ 12.500` |
| Idioma | Interfaz en **español (Colombia)** |
| Despliegue | Debe poder subirse a hosting compartido con cPanel, o a un VPS, sin herramientas especiales |

**Requisito de portabilidad:** todo lo sensible va en `.env` (fuera de `/public`). Debe existir un `.env.example` y una guía de despliegue en el README.

---

## 3. Estructura de carpetas

```
mesaly/
├── public/                    ← ÚNICA carpeta expuesta (document root)
│   ├── index.php              ← front controller
│   ├── .htaccess
│   ├── assets/ (css, js, img, vendor/chartjs)
│   └── uploads/               ← imágenes subidas (con .htaccess que bloquea ejecución PHP)
├── app/
│   ├── Core/                  ← Router, Request, Response, Controller base, Model base, View, Session, Csrf, Auth, Validator, Env, Logger
│   ├── Controllers/
│   │   ├── Public/            ← MenuController, PedidoController
│   │   ├── Admin/             ← DashboardController, CatalogoController, ProductoController, MesaController, PedidoAdminController, ConfigController, PerfilController
│   │   └── Super/             ← NegocioController, PagoController, PlanController, MetricasController
│   ├── Models/
│   ├── Services/              ← SuscripcionService, PedidoService, WhatsAppService, QrService, ImagenService, MetricasService, AuditoriaService
│   ├── Middleware/            ← AuthMiddleware, RolMiddleware, TenantMiddleware, SuscripcionMiddleware, CsrfMiddleware, RateLimitMiddleware
│   └── Views/                 ← layouts/, public/, admin/, super/, errors/
├── config/                    ← app.php, routes.php, slugs_reservados.php
├── database/                  ← schema.sql, seeds.sql, migrations/
├── bin/                       ← crear_superadmin.php, (opcional) cron_diario.php
├── storage/                   ← logs/, cache/ (fuera de public, no escribible por web)
├── docs/                      ← este documento
├── vendor/
├── .env.example
├── composer.json
└── README.md
```

---

## 4. Roles y permisos

### 4.1 Roles
1. **Super Admin** (dueño de la plataforma): `restaurante_id = NULL`.
2. **Admin de negocio** (dueño/encargado del restaurante): ligado a un único `restaurante_id`.
3. **Cliente final**: sin cuenta, sin sesión de usuario.

### 4.2 Matriz de permisos

| Acción | Super Admin | Admin negocio | Cliente |
|---|:-:|:-:|:-:|
| Crear / editar / suspender negocios | ✅ | ❌ | ❌ |
| Crear usuario admin de un negocio / resetear su contraseña | ✅ | ❌ | ❌ |
| Definir el precio y ciclo acordado de cada negocio | ✅ | ❌ | ❌ |
| Registrar pagos, extender fecha, suspender, "recordar pago" | ✅ | ❌ | ❌ |
| Ver métricas globales (negocios, pedidos, MRR) | ✅ | ❌ | ❌ |
| "Ver como" un negocio (soporte, queda en auditoría) | ✅ | ❌ | ❌ |
| Ver su fecha de vencimiento y estado de suscripción | — | ✅ (solo lectura) | ❌ |
| Marca: nombre, logo, banner, descripción, colores, fuente | ✅ | ✅ | ❌ |
| Categorías (crear, editar, ordenar, activar/desactivar, eliminar) | ✅ | ✅ | ❌ |
| Productos, variantes, modificadores, switch "Agotado" | ✅ | ✅ | ❌ |
| Horarios, abrir/cerrar recepción de pedidos | ✅ | ✅ | ❌ |
| Modalidades (mesa / domicilio / recoger), costo de envío, pedido mínimo | ✅ | ✅ | ❌ |
| Métodos de pago que muestra, número de WhatsApp | ✅ | ✅ | ❌ |
| Mesas y generación/descarga de QR | ✅ | ✅ | ❌ |
| Ver pedidos y cambiar su estado | ✅ | ✅ | ❌ |
| **Eliminar pedidos** | ❌ | ❌ | ❌ (solo cancelar/archivar) |
| Cambiar slug | ✅ | ⚠️ solo con advertencia clara (rompe QR impresos) | ❌ |
| Cantidad de productos, categorías y mesas | Sin límite | Sin límite | — |
| Ver datos de otro negocio | ✅ | ❌ **nunca** | ❌ |
| Cambiar su contraseña / datos de contacto | ✅ | ✅ | ❌ |
| Ver menú y enviar pedido | — | — | ✅ |

**Regla de oro (aislamiento multitenant):** toda consulta de un Admin de negocio lleva obligatoriamente `WHERE restaurante_id = :id_de_su_sesion`. El `restaurante_id` **nunca** se toma de un parámetro enviado por el navegador; siempre de la sesión. Los modelos deben forzar este filtro de forma centralizada (por ejemplo, un método base `scopedByTenant()`), no depender de que cada controlador lo recuerde.

---

## 5. Módulo Super Admin

### 5.1 Pantalla principal: "Vencimientos"
Lista de todos los negocios **ordenada por fecha de vencimiento ascendente**, con:
- Semáforo de color (ver 7.2), nombre, precio acordado, ciclo, fecha de vencimiento, días restantes (negativos si vencido), fecha y monto del último pago.
- Filtros rápidos: **Todos · Vencen esta semana · En gracia · Bloqueados · En prueba · Cancelados**.
- Búsqueda por nombre o slug.
- Acciones por fila: **Registrar pago · Recordar pago · Extender días · Suspender/Reactivar · Ver como · Editar**.

### 5.2 Gestión de negocios
- Crear negocio: nombre, slug (validado y único), número de WhatsApp, **precio acordado** (COP por periodo), ciclo, fecha de inicio o prueba, notas internas. Al crear, se genera el usuario Admin con **contraseña temporal** que se muestra una sola vez y obliga a cambiarla en el primer ingreso.
- Editar datos, cambiar precio y ciclo, resetear contraseña del admin, suspender manualmente, cancelar.

### 5.3 Registro de pagos (manual)
Formulario: negocio, **fecha de pago**, **monto** (se prellena con el precio acordado del negocio y es editable), **método** (Nequi, Daviplata, transferencia, efectivo, otro), **cantidad de periodos** (por defecto 1), **nota/referencia**. Reglas en 7.3. Historial de pagos por negocio, no editable (solo anulable con registro de auditoría).

### 5.4 Botón "Recordar pago"
Abre un enlace `wa.me` hacia el WhatsApp del dueño del negocio con un mensaje prellenado, por ejemplo:
> *Hola, te recuerdo que tu suscripción de Mesaly vence el 15 de noviembre. Cuando hagas el pago me avisas para registrarlo. ¡Gracias!*

El mensaje cambia según el estado (por vencer / en gracia / bloqueado). Al pulsarlo se guarda `ultimo_recordatorio_at` y se muestra "Último recordatorio: hace 2 días".

### 5.5 Precio por negocio (sin planes y sin límites)
**No existen planes ni límites de uso.** Todos los negocios tienen las mismas funciones y **ninguna cantidad está topada**: productos, categorías, mesas y pedidos son ilimitados. El Super Admin define **cuánto cobra a cada negocio** según su caso, directamente en la ficha del negocio:
- `precio`: monto acordado en COP por periodo (mensual o anual, según el `ciclo`).
- `notas_internas`: texto libre solo visible para el Super Admin (por ejemplo, "descuento por ser el primer cliente", "paga por Nequi").
- `mostrar_marca`: indica si se muestra el pie "Menú digital por Mesaly" (por defecto sí; lo decide el Super Admin por negocio).
El precio puede cambiarse en cualquier momento y aplica a los pagos futuros; los pagos ya registrados conservan su monto.

### 5.6 Métricas globales
Total de negocios por estado, pedidos totales en la red (con filtro de fechas), **MRR** = suma del `precio` acordado de cada negocio activo, llevado a base mensual (los anuales se dividen entre 12; las pruebas, suspendidos y cancelados no cuentan), pagos registrados del mes.

### 5.7 "Ver como"
El Super Admin puede entrar al panel de un negocio para dar soporte. Debe mostrarse un banner permanente "Estás viendo como: X — Salir", y **cada entrada queda en la tabla de auditoría**.

---

## 6. Módulo Admin del negocio

### 6.1 Identidad y marca
- Nombre comercial, slug (ver reglas en 9.1), descripción corta, logo, banner.
- **Colores personalizables:** primario (botones, íconos, acentos), secundario, fondo y texto. Selector de color con **vista previa en vivo** del menú.
- Fuente elegida de una lista corta y segura (por ejemplo 6 opciones; las fuentes se sirven desde el propio servidor, no desde CDN externo).
- **Validación de contraste:** el texto sobre botones se calcula automáticamente (blanco o negro según luminancia del color primario) para que nunca sea ilegible.
- **Paletas predefinidas (presets):** además del selector libre, el admin puede elegir una paleta lista con un clic (ver 15.5) y luego ajustarla.
- Los colores se aplican con **variables CSS** inyectadas en el `<head>` del menú público (valores validados con regex `^#[0-9a-fA-F]{6}$`).

### 6.2 Contacto y operación
- WhatsApp receptor con código de país (se normaliza a solo dígitos, ej. `573001234567`).
- Moneda COP sin decimales.
- **Horarios** por día de la semana (uno o dos tramos por día).
- **Switch "Recibir pedidos"** (abrir/cerrar en tiempo real) que tiene prioridad sobre el horario. Si está cerrado, el cliente puede ver el menú pero el botón de confirmar queda deshabilitado con mensaje claro.

### 6.3 Modalidades de pedido
Tres interruptores independientes: **Consumo en mesa**, **Domicilio** (costo fijo de envío y pedido mínimo opcionales), **Para recoger**. Al menos una debe estar activa.

### 6.4 Métodos de pago aceptados (informativos)
Selección múltiple: Efectivo, Transferencia / billeteras digitales, Datáfono contra entrega. Opcional: texto con datos de transferencia (ej. "Nequi 300 123 4567") que se muestra al cliente. **No se procesa ningún pago.**

### 6.5 Catálogo
- **Categorías:** nombre, orden (arrastrar y soltar), activa/inactiva. Una categoría inactiva se oculta con todos sus productos.
- **Producto:** nombre, descripción corta, precio base, imagen, categoría, orden, **destacado** (opcional), **switch Disponible/Agotado**.
  - Producto **agotado**: se muestra deshabilitado con etiqueta "Agotado", no se puede agregar al carrito. No se elimina de la BD.
- **Grupos de modificadores** (por producto, reutilizables dentro del negocio):
  - **Selección única obligatoria** (ej. "Término de la carne": Bien cocido / Tres cuartos), opciones con o sin costo.
  - **Selección múltiple opcional** con costo extra (ej. Tocineta +$3.000, Queso +$2.000), con mínimo y máximo configurables.
- Eliminar un producto que ya tiene pedidos históricos → **borrado lógico** (`deleted_at`) para no romper el historial.
- **Edición rápida de agotados:** una pantalla simple con la lista de productos y un switch por cada uno, pensada para usarse desde el celular en plena operación.

### 6.6 Mesas y QR
- El admin ingresa el número de mesas (sin límite). Puede darles nombre opcional ("Terraza 1").
- El sistema genera:
  - **QR general:** `https://dominio.com/slug` (redes sociales, tarjetas, mostrador).
  - **Un QR por mesa:** `https://dominio.com/slug?mesa=N`.
- **Exportar PDF** listo para imprimir con diseño de tarjeta (logo del negocio, nombre, "Mesa N", QR, colores de la marca, varias tarjetas por hoja). También descarga individual en PNG/SVG.
- Reducir el número de mesas **no borra** las mesas con pedidos; se desactivan.

### 6.7 Pedidos
- Tabla paginada con fecha, `#PED-XXXX`, cliente, ítems, total, modalidad (mesa o dirección), estado, y **botón "Abrir WhatsApp"** con el cliente (si dejó teléfono).
- Estados: **Generado → En preparación → Listo → Entregado**, y **Cancelado**. Cambio manual por el admin. Los pedidos cancelados no cuentan en métricas.
- Detalle del pedido con todos los datos y opciones elegidas.
- Filtros por fecha, modalidad y estado.

### 6.8 Dashboard de métricas
- Tarjetas del mes: **facturación estimada**, **total de pedidos**, **ticket promedio**.
- Gráfico de distribución por modalidad (Mesa / Domicilio / Recoger) en porcentaje.
- **Top 5** de productos más pedidos del periodo.
- Selector de periodo (hoy, 7 días, este mes, mes anterior, rango).
- Gráfico de pedidos por día.
- Se excluyen pedidos cancelados.

### 6.9 Banner de suscripción
Si el estado es "Por vencer" o "En gracia", se muestra un banner con el color del semáforo en **todas** las pantallas del admin. El admin ve su fecha de vencimiento y estado pero no puede modificarlos.

---

## 7. Motor de suscripción (manual con semáforo)

### 7.1 Campos clave en `restaurantes`
`precio` (INT, COP por periodo), `ciclo` (mensual/anual), `notas_internas`, `mostrar_marca`, `fecha_vencimiento` (DATE), `dia_corte`, `en_prueba` + `fecha_fin_prueba`, `estado_manual` (NULL | `suspendido` | `cancelado`), `ultimo_recordatorio_at`.
Configurables globalmente (tabla `configuracion`): **`dias_alerta` = 5**, **`dias_gracia` = 3**.

### 7.2 Estado calculado (NO se guarda, se calcula al vuelo)
El estado se **calcula comparando `fecha_vencimiento` con la fecha de hoy** (zona `America/Bogota`) cada vez que se necesita. Así el bloqueo es automático sin depender de un cron.

```
si estado_manual = 'cancelado'                       → CANCELADO  (bloqueado)
si estado_manual = 'suspendido'                      → SUSPENDIDO (bloqueado)
días = fecha_vencimiento - hoy
si días > dias_alerta                                → AL_DIA        (verde)
si 0 < días ≤ dias_alerta                            → POR_VENCER    (amarillo)
si días ≤ 0 y días ≥ -dias_gracia                    → EN_GRACIA     (rojo, TODO funciona)
si días < -dias_gracia                               → BLOQUEADO     (rojo oscuro)
```
Si `en_prueba` = 1, se usa `fecha_fin_prueba` en lugar de `fecha_vencimiento` y se etiqueta "En prueba" (misma lógica de semáforo).

Esta lógica vive en **un solo lugar** (`SuscripcionService::estadoDe($restaurante)`) y todo el sistema la consume.

### 7.3 Qué pasa en cada estado

| Estado | Admin del negocio | Menú público |
|---|---|---|
| AL_DIA | Normal | Normal |
| POR_VENCER | Normal + banner amarillo con días restantes | Normal |
| EN_GRACIA | Normal + banner fuerte "Tu suscripción venció, tienes X días" | **Normal** |
| BLOQUEADO / SUSPENDIDO / CANCELADO | Solo ve **pantalla de reactivación** (fecha de vencimiento, instrucciones de pago con el WhatsApp del Super Admin). No puede usar ninguna otra ruta | Mensaje sobrio: "Este menú se encuentra temporalmente fuera de servicio". **Sin carrito y sin pedidos** |

- El bloqueo se aplica en un **middleware** (`SuscripcionMiddleware`) y **también en el endpoint de crear pedido** (un cliente con la página abierta no puede enviar un pedido a un negocio bloqueado).
- Los datos del negocio **nunca se borran** por falta de pago.

### 7.4 Registrar un pago: cálculo de la nueva fecha
- Si el negocio estaba AL_DIA, POR_VENCER o EN_GRACIA: `nueva_fecha = fecha_vencimiento_anterior + periodos × ciclo`. Así su día de corte no se corre aunque pague con retraso.
- Si estaba BLOQUEADO (pasó la gracia) o es el primer pago tras la prueba: `nueva_fecha = fecha_de_pago + periodos × ciclo`.
- Al sumar meses, usar `dia_corte` y ajustar al último día del mes cuando no exista (ej. corte 31 → 28/29 en febrero).
- Registrar el pago limpia `estado_manual = suspendido` solo si el Super Admin lo indica explícitamente.
- Todo en una **transacción** + registro en auditoría.

### 7.5 Controles manuales del Super Admin
- **Extender X días:** suma días a `fecha_vencimiento` sin registrar pago (queda en auditoría con motivo).
- **Suspender / Reactivar:** cambia `estado_manual`.

### 7.6 Cron (opcional, no obligatorio)
`bin/cron_diario.php` puede, si en el futuro se desea, generar una lista diaria de negocios por vencer. **El sistema no debe depender de él para bloquear.**

---

## 8. Experiencia del cliente final

### 8.1 Rutas inteligentes
- `GET /{slug}` → menú del negocio.
- `GET /{slug}?mesa=N` → mismo menú, y se **fija la mesa en la sesión** (cookie de sesión con `SameSite=Lax`).
- Validación: la mesa `N` debe existir y estar activa **para ese negocio**; si no, se ignora el parámetro y se trata como visita general.
- La mesa guardada en sesión es **por negocio** (un cliente que escanea en un restaurante y luego abre otro no arrastra la mesa).
- Visita desde Instagram u otro enlace **sin** `?mesa` = modo general (sin mesa).

### 8.2 Navegación
- Encabezado con logo, nombre y estado (Abierto/Cerrado). Si hay mesa: **insignia fija "Ubicación: Mesa N"**.
- Pestañas horizontales de categorías (scroll lateral, se resaltan al hacer scroll).
- **Buscador en tiempo real** por nombre, descripción e ingredientes.
- Tarjetas de producto con imagen (lazy loading), precio, etiqueta "Agotado" si aplica.
- Al tocar un producto: modal/hoja inferior con modificadores, cantidad y **nota especial** ("Sin cebolla").

### 8.3 Carrito flotante
Barra fija inferior con cantidad de ítems y subtotal. Detalle editable: cambiar cantidades, eliminar, editar nota. El carrito vive en `localStorage` **por slug** (el cliente no pierde su carrito si recarga). El carrito en el navegador solo es una comodidad: **los precios se recalculan siempre en el servidor** (ver 9.3).

### 8.4 Checkout
- **Caso A — con mesa:** se omite tipo de entrega y dirección. Solo pide **nombre** y **método de pago previsto**.
- **Caso B — sin mesa:** el cliente elige entre las modalidades activas:
  - **Domicilio:** nombre, teléfono, dirección completa, barrio, notas para el repartidor. Aplica costo de envío y valida pedido mínimo.
  - **Para recoger:** nombre y hora estimada de recogida.
- Validación en cliente (UX) **y en servidor** (seguridad).

### 8.5 Disparo del pedido
1. El cliente pulsa **"Confirmar pedido"** → `POST /{slug}/pedido` (fetch asíncrono con token CSRF).
2. El servidor valida todo, **recalcula precios**, guarda el pedido en una transacción con estado `generado` y consecutivo por negocio, y devuelve el `wa_url`.
3. El navegador redirige a `https://wa.me/<numero>?text=<mensaje_codificado>`.
4. Se muestra pantalla de confirmación con el número de pedido (por si WhatsApp no abre).

El mensaje de WhatsApp lo **construye el servidor** (`WhatsAppService`), con `rawurlencode`, y se ve así:

```
🧾 *Pedido #PED-0142*
📍 *Mesa 5*            (o: 🛵 Domicilio / 🥡 Para recoger)
👤 Cliente: Juan Pérez

*Detalle*
2 x Hamburguesa clásica — $ 36.000
   • Término: Tres cuartos
   • + Tocineta extra
   📝 Sin cebolla
1 x Limonada de coco — $ 9.000

*Subtotal:* $ 45.000
*Domicilio:* $ 4.000
*TOTAL:* $ 49.000
💳 Pago previsto: Efectivo
```
Para domicilio incluye teléfono, dirección, barrio y notas. Para recoger, la hora estimada. Debe mantenerse corto para no pasar los límites de longitud de URL.

---

## 9. Seguridad (requisitos obligatorios)

### 9.1 Slugs
Solo `a-z`, `0-9` y guion, 3–40 caracteres, único. Lista de **slugs reservados** (`admin`, `superadmin`, `login`, `logout`, `api`, `assets`, `uploads`, `public`, `static`, `app`, `qr`, `pedido`, `soporte`, `www`, etc.) en `config/slugs_reservados.php`.

### 9.2 Autenticación y sesiones
- Contraseñas con `password_hash()` (**Argon2id** o bcrypt cost ≥ 12) y `password_verify()`; `password_needs_rehash()`.
- Política mínima: 10 caracteres.
- `session_regenerate_id(true)` al iniciar sesión y al cambiar de rol.
- Cookies de sesión: `HttpOnly`, `Secure` (en HTTPS), `SameSite=Lax`, timeout por inactividad (ej. 2 h).
- **No existe registro público.** El primer Super Admin se crea por consola: `php bin/crear_superadmin.php`. Los admins de negocio los crea el Super Admin.
- **Rate limiting de login:** máximo 5 intentos fallidos por IP + usuario en 15 minutos (tabla `intentos_login`), con bloqueo temporal y mensaje genérico (no revelar si el usuario existe).
- Cambio de contraseña obligatorio en el primer ingreso con contraseña temporal.

### 9.3 Integridad de datos del pedido (CRÍTICO)
**Nunca confiar en precios, totales ni nombres enviados por el navegador.** El cliente envía solo: `producto_id`, `cantidad`, `ids de opciones elegidas` y `nota`. El servidor:
- Verifica que cada producto pertenezca al negocio, esté activo y disponible.
- Verifica que las opciones pertenezcan a ese producto y cumplan mínimos/máximos.
- **Recalcula** precio unitario, extras, subtotal, envío y total desde la base de datos.
- Guarda en `pedido_items` una **copia congelada** (nombre y precio al momento), para que cambios futuros del menú no alteren el historial.

### 9.4 Inyección y XSS
- **100% consultas preparadas** (PDO con `ATTR_EMULATE_PREPARES = false`).
- Escapar toda salida en las vistas con un helper `e()` (`htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`). Nada de `echo` directo de datos de usuario.
- Los textos libres (nombre, notas, dirección) se tratan como texto plano; se limita su longitud y se eliminan caracteres de control.

### 9.5 CSRF
Token por sesión en **todos** los formularios y peticiones `POST/PUT/DELETE` (incluido el de crear pedido y el de cambio de estado). Comparación con `hash_equals`. Operaciones destructivas **nunca por GET**.

### 9.6 Control de acceso
- Middleware por ruta: autenticación → rol → tenant → suscripción.
- **Anti-IDOR:** cada acceso por ID (producto, pedido, mesa) verifica que pertenezca al `restaurante_id` de la sesión. Si no, responder 404 (no 403, para no revelar existencia).
- Rutas `/superadmin/*` solo con rol `super_admin`.

### 9.7 Subida de imágenes
- Validar con `finfo`/`getimagesize` (no confiar en extensión ni `Content-Type`). Solo JPG, PNG, WebP. Tamaño máximo (ej. 3 MB).
- **Re-procesar con GD/Imagick** (redimensionar y convertir a WebP), lo que elimina cualquier carga maliciosa. Generar versión miniatura.
- Nombre de archivo aleatorio generado por el servidor (nunca el original).
- `public/uploads/.htaccess` que **deshabilita ejecución de PHP** (`php_flag engine off` / `RemoveHandler`) y solo sirve imágenes.
- Subcarpeta por negocio.

### 9.8 Cabeceras y configuración
- Forzar **HTTPS** (redirección + HSTS).
- Cabeceras: `Content-Security-Policy` (restrictiva, sin scripts inline cuando sea posible), `X-Content-Type-Options: nosniff`, `X-Frame-Options: DENY`, `Referrer-Policy: strict-origin-when-cross-origin`, `Permissions-Policy`.
- `display_errors = Off` en producción; errores a `storage/logs/`. Páginas de error amigables (404, 403, 500, 503).
- `.env` y carpetas `app/`, `config/`, `storage/`, `database/`, `vendor/` **fuera del document root** (o bloqueadas por `.htaccess` si el hosting no permite cambiar la raíz).

### 9.9 Anti-abuso en el endpoint público de pedidos
- Rate limit por IP (ej. 10 pedidos / 10 min por negocio).
- Campo honeypot oculto en el formulario.
- Límite de ítems por pedido (ej. 50) y de cantidad por ítem (ej. 20).

### 9.10 Auditoría
Tabla `auditoria`: quién, qué, sobre qué entidad, IP, fecha, valores antes/después en JSON. Se registra: inicio de sesión del Super Admin, registro/anulación de pagos, extender, suspender/reactivar, "ver como", resets de contraseña, cambios de plan, cambios de slug.

### 9.11 Backups
Documentar en el README cómo hacer respaldo de la BD (`mysqldump`) y de `public/uploads/`.

---

## 10. Modelo de datos (MySQL)

Convenciones: `id BIGINT UNSIGNED AUTO_INCREMENT PK`, `created_at`, `updated_at`, claves foráneas con índices, motor InnoDB, `utf8mb4_unicode_ci`.

| Tabla | Campos principales |
|---|---|
| `configuracion` | `clave` (PK), `valor` (ej. `dias_alerta`, `dias_gracia`, `whatsapp_soporte`, `nombre_plataforma`) |
| `restaurantes` | `nombre`, `slug` (UNIQUE), `descripcion`, `logo_path`, `banner_path`, `color_primario`, `color_secundario`, `color_fondo`, `color_texto`, `fuente`, `whatsapp`, `moneda` (COP), `recibiendo_pedidos` (bool), `acepta_mesa`, `acepta_domicilio`, `acepta_recoger`, `costo_envio`, `pedido_minimo`, `info_pago_texto`, `precio`, `notas_internas`, `mostrar_marca`, `ciclo`, `fecha_vencimiento`, `dia_corte`, `en_prueba`, `fecha_fin_prueba`, `estado_manual`, `ultimo_recordatorio_at`, `siguiente_consecutivo` |
| `usuarios` | `restaurante_id` (NULL = super admin), `nombre`, `email` (UNIQUE), `password_hash`, `rol` (`super_admin`/`admin_negocio`), `debe_cambiar_password`, `activo`, `ultimo_login_at` |
| `horarios` | `restaurante_id`, `dia_semana` (0–6), `abre`, `cierra`, `tramo` (1/2), `activo` |
| `metodos_pago_negocio` | `restaurante_id`, `metodo` (`efectivo`/`transferencia`/`datafono`) |
| `categorias` | `restaurante_id`, `nombre`, `orden`, `activa`, `deleted_at` |
| `productos` | `restaurante_id`, `categoria_id`, `nombre`, `descripcion`, `precio`, `imagen_path`, `disponible`, `destacado`, `orden`, `deleted_at` |
| `grupos_modificadores` | `restaurante_id`, `nombre`, `tipo` (`unica`/`multiple`), `obligatorio`, `min_selecciones`, `max_selecciones` |
| `opciones_modificadores` | `grupo_id`, `nombre`, `precio_extra`, `orden`, `activa` |
| `producto_grupos` | `producto_id`, `grupo_id`, `orden` (relación N:M) |
| `mesas` | `restaurante_id`, `numero`, `nombre`, `activa`; UNIQUE (`restaurante_id`, `numero`) |
| `pedidos` | `restaurante_id`, `consecutivo` (UNIQUE con `restaurante_id`), `codigo` (`PED-0142`), `modalidad` (`mesa`/`domicilio`/`recoger`), `mesa_id`, `cliente_nombre`, `cliente_telefono`, `direccion`, `barrio`, `notas_entrega`, `hora_recogida`, `metodo_pago`, `subtotal`, `costo_envio`, `total`, `estado`, `ip`, `created_at` |
| `pedido_items` | `pedido_id`, `producto_id`, `nombre_producto`, `precio_unitario`, `cantidad`, `nota`, `total_linea` |
| `pedido_item_opciones` | `pedido_item_id`, `nombre_grupo`, `nombre_opcion`, `precio_extra` |
| `pagos_suscripcion` | `restaurante_id`, `fecha_pago`, `monto`, `metodo`, `periodos`, `fecha_vencimiento_anterior`, `fecha_vencimiento_nueva`, `nota`, `registrado_por`, `anulado`, `anulado_motivo` |
| `intentos_login` | `ip`, `email`, `exitoso`, `created_at` |
| `auditoria` | `usuario_id`, `restaurante_id`, `accion`, `entidad`, `entidad_id`, `antes` (JSON), `despues` (JSON), `ip`, `created_at` |

**Consecutivo del pedido:** se asigna dentro de una transacción con `SELECT siguiente_consecutivo ... FOR UPDATE` sobre el negocio, para que nunca haya duplicados con pedidos simultáneos. Formato visible: `PED-` + número con 4 dígitos mínimo.

---

## 11. Mapa de rutas

**Públicas**
```
GET   /{slug}                    Menú
POST  /{slug}/pedido             Crear pedido (JSON, CSRF) → {codigo, wa_url}
GET   /{slug}/manifest.json      Manifest PWA dinámico por negocio
```
**Autenticación**
```
GET/POST /login                  (un solo login; redirige según rol)
POST     /logout
GET/POST /cambiar-password
```
**Admin del negocio** (prefijo `/admin`)
```
/admin                           Dashboard + métricas
/admin/pedidos                   Listado, detalle, cambio de estado
/admin/catalogo                  Categorías y productos
/admin/modificadores             Grupos y opciones
/admin/disponibilidad            Pantalla rápida de agotados
/admin/mesas                     Mesas + QR + exportar PDF
/admin/configuracion             Marca, colores, contacto, horarios, modalidades, pagos
/admin/perfil                    Cuenta y contraseña
/admin/suscripcion               Estado de la suscripción (solo lectura) / pantalla de reactivación
```
**Super Admin** (prefijo `/superadmin`)
```
/superadmin                      Vencimientos (pantalla principal)
/superadmin/negocios             CRUD de negocios
/superadmin/negocios/{id}/pagos  Historial y registro de pagos
/superadmin/metricas             Métricas globales y MRR
/superadmin/configuracion        Días de alerta/gracia, WhatsApp de soporte
/superadmin/auditoria            Registro de acciones
```

---

## 12. Requisitos no funcionales
- **Rendimiento:** menú público con carga inicial < 2 s en 4G. Imágenes WebP con `loading="lazy"`, CSS/JS minimizado, caché HTTP de assets, un solo archivo CSS y uno JS en la vista pública.
- **PWA ligera:** manifest dinámico (nombre y color del negocio) y service worker básico para cachear el menú; instalable en pantalla de inicio.
- **Responsive mobile-first.** El admin también debe ser usable en celular (el dueño lo usará desde ahí).
- **Accesibilidad básica:** contraste adecuado, etiquetas en formularios, objetivos táctiles ≥ 44 px.
- **Mantenibilidad:** código documentado, PSR-12, PSR-4 para autoload, sin lógica de negocio en las vistas.
- **Pruebas:** al menos pruebas unitarias (PHPUnit) de `SuscripcionService` (todos los estados y cálculo de fechas, incluyendo fin de mes), `PedidoService` (recálculo de precios, validaciones) y `WhatsAppService`.

---

## 13. Fases de construcción

Cada fase termina con algo **funcionando y probado** antes de pasar a la siguiente.

**Fase 0 — Cimientos:** estructura de carpetas, Composer, `.env`, router, base MVC, sesión, CSRF, login, roles, middleware, layout base, `schema.sql`, `bin/crear_superadmin.php`, páginas de error.
*Criterio de aceptación:* se crea el Super Admin por consola, se inicia sesión y se protege `/superadmin`.

**Fase 1 — Núcleo vendible:** Super Admin crea negocios y admins; admin configura marca/colores/contacto/horarios/modalidades; catálogo completo (categorías, productos, modificadores, agotado); menú público; carrito; checkout (casos A y B); guardado de pedido y redirección a WhatsApp; subida segura de imágenes.
*Criterio:* un restaurante real puede recibir un pedido de punta a punta.

**Fase 2 — Mesas y QR:** generación de mesas, QR general y por mesa, exportación a PDF, lógica `?mesa=`.
*Criterio:* imprimir las tarjetas, escanear una y ver "Mesa N" en el checkout.

**Fase 3 — Pedidos y métricas:** historial, estados, dashboard, gráficos, top 5.

**Fase 4 — Suscripciones:** precio por negocio (sin planes ni límites), semáforo, pagos manuales, extender/suspender, "recordar pago", bloqueo automático con gracia, pantalla de reactivación, MRR, auditoría y "ver como".

**Fase 5 — Pulido y despliegue:** PWA, optimización, pruebas, endurecimiento de seguridad (revisión de la sección 9 punto por punto), README y guía de despliegue en hosting compartido y VPS.

---

## 14. Criterios de "terminado" del proyecto
- [ ] Todo lo de la sección 9 implementado y revisado.
- [ ] Ninguna consulta SQL concatenada; ninguna salida sin escapar.
- [ ] Un admin de negocio no puede ver ni modificar datos de otro (probado explícitamente con IDs ajenos).
- [ ] Un pedido con precios manipulados desde el navegador se guarda con los precios correctos del servidor.
- [ ] Un negocio bloqueado no puede recibir pedidos ni usar el panel, y vuelve a la normalidad al registrar el pago.
- [ ] Los QR impresos funcionan y fijan la mesa correctamente.
- [ ] Instalación desde cero siguiendo solo el README.

---

## 15. Identidad de marca de la plataforma

> Esta sección define la marca **de la plataforma** (la de quien vende el software). Los menús públicos de cada restaurante usan los colores del restaurante (ver 6.1), no estos.

### 15.1 Nombre y eslogan
- **Nombre: Mesaly.** Tres sílabas, se escribe y se pronuncia igual en español, incluye "mesa" (el corazón del producto) y la terminación "-ly" le da aire de software moderno, al estilo de nombres cortos y fáciles de recordar como Shopify.
- **Alternativas si Mesaly no está disponible:** *Cartea* (de "carta", el menú) y *Pidea* (de "pídelo").
- **Eslogan principal:** *Tu menú en cada mesa.* Alternativa: *Tu menú digital que recibe pedidos en tu WhatsApp.*
- **Antes de comprometerse con el nombre (obligatorio, lo hace el dueño del proyecto):**
  1. Buscar el nombre en Google y en Instagram/TikTok/Facebook para ver si hay otro negocio igual en Colombia o en el sector.
  2. Verificar dominio (`mesaly.co` o `mesaly.com`) y reservarlo.
  3. Consultar la base de marcas de la Superintendencia de Industria y Comercio (SIC) y el registro mercantil de la Cámara de Comercio.
  4. Si todo está libre, registrar la marca (más adelante, cuando haya ingresos).
- El código no debe tener el nombre "quemado": va en `.env` / tabla `configuracion` (`nombre_plataforma`) para poder cambiarlo si hiciera falta.

### 15.2 Paleta de colores

| Nombre | Hex | Uso |
|---|---|---|
| **Naranja Sazón** | `#E85D04` | Color principal: botones, logo, enlaces destacados |
| **Amarillo Mantequilla** | `#FFB703` | Acento: notificaciones, etiquetas, resaltados |
| **Verde Albahaca** | `#2A9D5C` | Secundario: éxito, "Abierto", confirmaciones |
| **Tinta** | `#1E1B18` | Texto principal y títulos |
| **Crema** | `#FFF7EA` | Fondo general (cálido, no blanco puro) |
| **Blanco** | `#FFFFFF` | Tarjetas y superficies |

**Por qué estos colores:** los tonos cálidos (naranja y amarillo) se asocian con apetito y energía, y el verde transmite frescura y confianza, además de no chocar con el verde de WhatsApp. El naranja es el color protagonista para que la marca se reconozca de un vistazo.

**Colores de estado (semáforo de suscripción)**, separados de la marca para que el naranja de la marca no se confunda con una alerta:

| Estado | Color |
|---|---|
| AL_DIA | Verde `#2A9D5C` |
| POR_VENCER | Amarillo `#FFB703` |
| EN_GRACIA | Rojo `#D62828` |
| BLOQUEADO / SUSPENDIDO | Rojo oscuro `#7F1D1D` |
| EN PRUEBA | Azul `#2563EB` |
| CANCELADO | Gris `#6B7280` |

**Contraste:** el blanco sobre `#E85D04` tiene un contraste cercano a 3,5:1. Sirve para botones y texto grande en negrita (16 px o más), **no** para texto pequeño; el texto pequeño sobre naranja usa Tinta. El cálculo automático de contraste de la sección 6.1 aplica también a la marca.

### 15.3 Tipografía
- **Títulos y logotipo:** Poppins (700/800).
- **Texto de lectura:** Inter.
- Ambas **alojadas en el propio servidor** (archivos `.woff2`), sin CDN externo, como pide la sección 9.8. Con tipografías de respaldo del sistema.

### 15.4 Logo
**Concepto:** un ícono de app (cuadrado redondeado naranja) con una **burbuja de chat** blanca, que representa el pedido por WhatsApp. Dentro de la burbuja hay un **plato** visto desde arriba (un círculo con borde). En la esquina superior derecha, un **punto amarillo** como notificación de "nuevo pedido". Junto al ícono va la palabra **mesaly** en minúsculas, en Poppins ExtraBold: "mesa" en Tinta y "ly" en Naranja Sazón.

**Archivos entregados (borradores en SVG, para usar desde ya):**
- `docs/brand/logo-icon.svg`: solo el ícono. Se usa como favicon y como ícono de la PWA (exportar a PNG de 192 y 512 px).
- `docs/brand/logo-horizontal.svg`: ícono + palabra. Se usa en el panel, el login, la landing y las tarjetas de QR.

**Nota:** son un primer borrador funcional. El texto del horizontal depende de que Poppins esté instalada; para uso definitivo conviene convertir el texto a trazos en un editor (Figma, Inkscape) o pedirle a un diseñador un pulido.

**Reglas de uso:**
- Espacio libre alrededor del logo: como mínimo el diámetro del punto amarillo.
- Tamaño mínimo del ícono: 24 px.
- Versión de una tinta: blanco sobre naranja, o negro sobre blanco.
- No deformar, no cambiar los colores, no poner sobre fondos que reduzcan el contraste.

### 15.5 Dónde se aplica la marca de la plataforma
- Panel de Super Admin, pantalla de login, páginas de error, pantalla de reactivación y landing: paleta de la plataforma.
- **Menú público de cada negocio:** colores del negocio. Para un negocio nuevo, los colores por defecto son los de la plataforma.
- Pie opcional "Menú digital por Mesaly" en el menú público y en las tarjetas PDF de QR (activable/desactivable por negocio con el campo `mostrar_marca` de `restaurantes`, que decide el Super Admin). Es publicidad gratuita al estilo "Powered by".
- Variables CSS globales en `public/assets/css/brand.css` (`--brand-primary`, `--brand-accent`, `--brand-secondary`, `--brand-ink`, `--brand-cream`, y las de estado) para que el cambio de marca sea editar un solo archivo.

**Paletas predefinidas para los restaurantes (presets).** En la configuración de marca (6.1), además del selector libre, el admin puede elegir una paleta lista con un clic y luego ajustarla:

| Preset | Primario | Secundario | Fondo | Texto |
|---|---|---|---|---|
| Sazón (naranja) | `#E85D04` | `#FFB703` | `#FFF7EA` | `#1E1B18` |
| Fresca (verde) | `#2A9D5C` | `#FFB703` | `#F6FBF4` | `#14281D` |
| Parrilla (rojo) | `#C1121F` | `#1E1B18` | `#FFFFFF` | `#1E1B18` |
| Café (marrón) | `#6F4E37` | `#D4A373` | `#FAF3E8` | `#2B1B12` |
| Dulce (rosa) | `#E5527A` | `#FFC8DD` | `#FFF5F8` | `#3A1F2B` |
| Mar (azul) | `#0077B6` | `#90E0EF` | `#F4FBFE` | `#0B2A3A` |
| Noche (oscuro) | `#FFB703` | `#E85D04` | `#16130F` | `#FFF7EA` |

### 15.6 Landing pública de la plataforma (ruta `/`)
Página de presentación para conseguir clientes, **sin registro público** (consistente con la sección 9.2):
- Hero con eslogan, una imagen del menú en un celular y botón **"Quiero mi menú"** que abre WhatsApp del Super Admin (`whatsapp_soporte` en `configuracion`) con mensaje prellenado.
- Tres beneficios (menú con QR, pedidos directo a WhatsApp, métricas de ventas).
- Cómo funciona en tres pasos.
- Enlace a un **restaurante demo** real dentro de la plataforma.
- Precios: texto orientativo editable desde `configuracion` (el precio real se pacta con cada negocio; no hay planes cerrados).
- Preguntas frecuentes y pie con enlaces a Términos y Privacidad.
- Muy liviana (sin frameworks), con la paleta de la plataforma y optimizada para celular.
- `/` queda reservada para la landing y no puede ser un slug.

### 15.7 Empresa y aspectos legales (a confirmar con un contador o abogado)
Esta parte es orientativa y no reemplaza asesoría profesional.
- **Nombre comercial:** Mesaly (sujeto a la verificación de 15.1).
- **Cómo arrancar de forma realista:** seguir como persona natural con RUT, con la actividad de desarrollo de software (CIIU 6201, verificar con el contador) y matrícula mercantil en la Cámara de Comercio para el nombre comercial. Constituir una S.A.S. cuando los ingresos recurrentes lo justifiquen.
- **Registro de marca ante la SIC** más adelante, cuando haya clientes (software y servicios de software son las clases que aplican; confirmar con el abogado).
- **Protección de datos (Ley 1581 de 2012, Colombia):** la plataforma guarda nombres, teléfonos y direcciones de clientes finales. Requisitos para el sistema:
  - Páginas públicas `/terminos` y `/privacidad` (política de tratamiento de datos).
  - **Aviso de tratamiento de datos en el checkout**, antes de confirmar, indicando que el restaurante y la plataforma tratan sus datos para gestionar el pedido, con enlace a la política.
  - Conservar solo los datos necesarios y permitir al Super Admin eliminar o anonimizar los datos de un cliente final a solicitud.
- **Contrato/términos de servicio con cada restaurante**, aceptados al crear su cuenta (primer ingreso), que expliquen el cobro manual, el periodo de gracia y el bloqueo por vencimiento.
- La facturación electrónica de los cobros propios queda fuera del software; consultarla con el contador.

### 15.8 Impacto en las fases de construcción
- **Fase 0:** crear `brand.css`, cargar tipografías locales, incluir los logos SVG y generar favicon e íconos PNG de la PWA.
- **Fase 1:** presets de paletas en la configuración de marca del restaurante; aviso de datos en el checkout.
- **Fase 4:** colores de estado del semáforo de 15.2.
- **Fase 5:** landing `/`, páginas `/terminos` y `/privacidad`, pie "por Mesaly" opcional y aceptación de términos en el primer ingreso del admin.
