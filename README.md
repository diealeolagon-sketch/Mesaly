# Mesaly

Plataforma SaaS integral de gestión operativa y menú digital interactivo para restaurantes, cafés, bares y negocios gastronómicos.

Diseñada con un enfoque **mobile-first** para comensales y un panel de administración en tiempo real para cocinas, salones y administradores.

---

## Registro de Cambios y Mejoras Recientes

A continuación se detallan las mejoras, correcciones y nuevas capacidades implementadas en el sistema:

### 1. Aislamiento Multi-Tenant Estricto y Corrección en Reportes
- **Aislamiento Total de Productos:** Se corrigió la consulta y filtrado de pedidos en reportes para garantizar que ningún producto de un restaurante aparezca en el resumen o estadísticas de otro (cero contaminación cruzada entre tenants).
- **Consistencia en Métricas Top 5:** Los rankings de productos más vendidos ahora reflejan de manera fidedigna los platos propios del establecimiento consultado.

### 2. Reconstrucción y Semillado de Locales Gastronómicos
Se reconstruyeron desde cero tres restaurantes de muestra con catálogos 100% auténticos, grupos de modificadores, mesas y órdenes históricas:
- **Café Macondo (`/cafe-macondo`):** Especialidad en café de origen, repostería artesanal, desayunos y sándwiches gourmet (10 platos).
- **Pizzería Di Roma (`/pizzeria-di-roma`):** Pizzería artesanal al horno de piedra con pastas, calzones y postres italianos (10 platos).
- **Sabor Criollo Colombiano (`/sabor-criollo`):** Cocina típica tradicional colombiana con Bandeja Paisa, Ajiaco Santafereño, Cazuela de Mariscos, Sancocho y entradas autóctonas (12 platos).
- **Fotografía Gastronómica Real:** Cada restaurante cuenta con logotipo corporativo, banner panorámico e imágenes individuales en alta resolución para cada uno de sus 32 productos en `public/uploads/`.

### 3. Experiencia de Pedido sin Fricción para Comensales
- **Checkout Ágil:** Se eliminó la exigencia de registrar métodos de pago al comensal en la toma de comanda móvil. Los pedidos se envían de forma fluida a la cocina y a WhatsApp sin bloqueos financieros innecesarios para el cliente final.

### 4. Flujo de Cobro Seguro con Wompi y Renovación por Mes Calendario
- **Enlace Oficial Wompi en Producción:** Se integró el link de pagos oficial del comercio (`https://checkout.wompi.co/l/VPOS_8rydsK`) en el botón *"Pagar Suscripción (Wompi)"* del panel del restaurante, permitiendo pagos con tarjeta, Nequi, Daviplata y PSE.
- **Flujo Guiado en Dos Pasos:**
  1. *Paso 1:* El restaurante hace clic en *"Ir a Pagar en Wompi (Link Oficial)"* y completa el pago en la pasarela oficial en una nueva pestaña.
  2. *Paso 2:* Presiona *"Ya Pagué • Enviar Comprobante por WhatsApp"*, abriendo un chat directo con el soporte de Mesaly con un mensaje prellenado con el nombre del restaurante y la tarifa.
- **Control de Acreditación Manual (Super Admin):** Para garantizar máxima seguridad financiera y evitar renovaciones sin pago verificado, el Super Administrador valida el ingreso en su cuenta bancaria/Wompi y ejecuta la renovación desde su panel con el botón *"Renovar (+1 Mes)"*.
- **Cálculo por Mes Calendario Real:** La renovación no es un plazo rígido de 30 días, sino que se computa sumando **1 mes calendario exacto** (`+1 month`), respetando la duración específica de cada mes del año (28, 29, 30 o 31 días) y manteniendo fijo el día de corte del suscriptor.

### 5. Módulo de Logs y Auditoría del Sistema (Super Admin)
- Se desarrolló la pantalla `/superadmin/logs` accesible para el Super Administrador, la cual incluye:
  - Tarjetas de métricas (KPIs): Total de eventos registrados, accesos exitosos, intentos bloqueados e incidentes críticos.
  - Buscador en tiempo real y filtros dinámicos por severidad (info, warning, danger).
  - Tabla de eventos de auditoría y tabla detallada de intentos de inicio de sesión con visualización de parámetros JSON formateados.

### 6. Rediseño y Corrección de "Actualizar Contraseña" (`/cambiar-password`)
- **Resolución de Error 500:** Se solucionó la excepción por método no definido (`Auth::isAdmin()`) añadiendo la compatibilidad en `App\Core\Auth` y ajustando la referencia a `Auth::isAdminNegocio()`.
- **Identidad Visual Oficial de Mesaly:** Se integró la vista con el layout de login (`layouts/auth.php`), incorporando el fondo naranja con ondas de marca, el destello característico, el logotipo corporativo y campos con iconos interactivos de visualización de contraseña.
- **Personaje 3D y Escala en PC:** Se reemplazó el personaje previo por el Chef 3D con teléfono inteligente (`chef-mobile-transparent.png`) y se amplió el contenedor en escritorio (`max-width: 440px`, `max-height: 520px`) para otorgarle presencia y balance visual en el panel lateral.

### 7. Credenciales y Acceso Administrativo
- **Super Admin:** `admin@mesaly.co` / `SuperAdmin123`
- Acceso al panel de control global: `http://localhost/Mesaly/public/superadmin`
- Actualización de contraseña: `http://localhost/Mesaly/public/cambiar-password`

---

## Características Principales

### 1. Menú Digital Interactivo y Comensales
- **Experiencia Fluida:** Interfaz optimizada para dispositivos móviles y computadoras, con carga ultra rápida y diseño moderno.
- **Categorías y Búsqueda:** Navegación por categorías con scroll horizontal interactivo y filtrado instantáneo.
- **Modificadores y Variantes:** Selección de términos de carne, tipos de queso, bebidas, salsas y adicionales obligatorios u opcionales con cálculo de precio en tiempo real.
- **Pedidos en Mesa (QR):** Escaneo de código QR en mesa que asocia la comanda automáticamente a la mesa ocupada.
- **Pedidos a Domicilio:** Carrito interactivo con cálculo de propina, recargo de domicilio y envío estructurado a WhatsApp para confirmación inmediata.

### 2. KDS (Kitchen Display System) y Comandas
- Tablero visual en tiempo real para visualización de comandas.
- Notificaciones de sonido en vivo para nuevos pedidos.
- Transición de estados de cocina: *Pendiente -> En Preparación -> Listo -> Entregado*.

### 3. Gestión Visual de Salón y Mesas
- Distribución de mesas con identificación por zonas y números.
- Monitoreo en vivo del estado de cada mesa (Libre, Ocupada, Por Cobrar).
- Generación y descarga individual de códigos QR listos para imprimir y colocar en mesas.

### 4. Reportes y Analítica Financiera
- Dashboard con métricas clave: Ventas totales, ticket promedio, número de pedidos y ocupación.
- Gráficos interactivos de ventas por fecha y horas de mayor consumo.
- Ranking de los productos y categorías más vendidos por restaurante.
- Desglose por canales de venta (Mesa vs Domicilio) y métodos de pago (Efectivo, Transferencia, Tarjeta).

### 5. SuperAdmin y Multitenant
- Gestión centralizada de múltiples restaurantes y marcas.
- Control de planes de suscripción, fechas de corte y estados de cuenta.
- Registro centralizado de logs y auditoría de eventos de seguridad.
- Restricción automática de acceso y catálogos ante vencimiento de plan.

---

## Stack Tecnológico

- **Backend:** PHP 8.1+ (Arquitectura MVC pura y limpia, PDO seguro con sentencias preparadas).
- **Base de Datos:** MySQL 8.0 / MariaDB (Soporte UTF8mb4, transacciones seguras e índices optimizados).
- **Frontend:** HTML5 semántico, CSS3 modular con diseño responsivo, JavaScript ES6+ reactivo.
- **Librerías:** Chart.js (analítica), Flatpickr (rangos de fechas), Google Fonts locales (Plus Jakarta Sans).
- **Servidor Web:** Apache con mod_rewrite habilitado (archivo `.htaccess` configurado para enrutamiento limpio) o Nginx.

---

## Requisitos del Sistema

- Servidor Web (Apache con `mod_rewrite` o Nginx).
- PHP 8.1 o superior con extensiones: `pdo`, `pdo_mysql`, `mbstring`, `json`, `openssl`.
- MySQL 8.0 o MariaDB 10.4+.
- Composer (opcional para dependencias de prueba o herramientas adicionales).

---

## Instalación y Configuración

### 1. Clonar el Repositorio
```bash
git clone https://github.com/diealeolagon-sketch/Mesaly.git
cd Mesaly
```

### 2. Configurar el Entorno
Copia el archivo de ejemplo de variables de entorno y ajusta tus credenciales:
```bash
cp .env.example .env
```
Edita `.env` con los datos de tu conexión local o de producción:
```env
APP_NAME=Mesaly
APP_ENV=local
APP_URL=http://localhost/Mesaly/public

DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mesaly
DB_USERNAME=root
DB_PASSWORD=root
```

### 3. Base de Datos
Importa la estructura y datos completos más recientes desde `database/mesaly_db_completa.sql`:
```bash
mysql -u root -p mesaly_db < database/mesaly_db_completa.sql
```

### 4. Permisos de Almacenamiento
Asegúrate de que la carpeta de almacenamiento y subida de archivos tenga permisos de escritura:
```bash
chmod -R 775 storage/ public/uploads/
```

### 5. Iniciar el Proyecto
Si usas XAMPP, accede directamente desde tu navegador en:
`http://localhost/Mesaly/public`

O utilizando el servidor integrado de PHP:
```bash
php -S localhost:8000 -t public
```
Y abre en tu navegador: `http://localhost:8000`

---

## Estructura del Proyecto

```text
Mesaly/
├── app/
│   ├── Controllers/       # Controladores (Admin, Public, Super, Api)
│   ├── Core/              # Enrutador, Base Controller, Base Model, Helpers
│   ├── Middleware/        # Filtros de autenticación y tenant
│   ├── Models/            # Modelos de datos con PDO
│   ├── Services/          # Lógica de negocio (Horarios, WhatsApp, etc.)
│   └── Views/             # Vistas PHP (layouts, admin, comensal, landing, super)
├── config/                # Configuraciones de la app y rutas
├── database/              # Esquema SQL, seeds y respaldo mesaly_db_completa.sql
├── docs/                  # Documentación técnica y especificaciones
├── public/                # DocumentRoot (index.php, CSS, JS, fuentes, imágenes)
│   ├── assets/            # Estilos, scripts y assets de marca
│   └── uploads/           # Imágenes gastronómicas organizadas por restaurante
├── storage/               # Logs y archivos de caché temporal
├── .env.example           # Plantilla de configuración de entorno
├── .gitignore             # Reglas de exclusión de Git
└── README.md              # Documentación principal y changelog
```

---

## Licencia

Este proyecto está bajo licencia privada. Todos los derechos reservados.
