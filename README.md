# Mesaly

Plataforma SaaS integral de gestión operativa y menú digital interactivo para restaurantes, cafés, bares y negocios gastronómicos.

Diseñada con un enfoque **mobile-first** para comensales y un panel de administración en tiempo real para cocinas, salones y administradores.

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
- Ranking de los productos y categorías más vendidos.
- Desglose por canales de venta (Mesa vs Domicilio) y métodos de pago (Efectivo, Transferencia, Tarjeta).

### 5. SuperAdmin y Multitenant
- Gestión centralizada de múltiples restaurantes y marcas.
- Control de planes de suscripción, fechas de corte y estados de cuenta.
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
DB_PASSWORD=
```

### 3. Base de Datos
Importa la estructura inicial y los datos base en tu gestor MySQL:
```bash
# 1. Esquema de tablas
mysql -u root mesaly < database/schema.sql

# 2. Datos semilla iniciales (restaurantes de muestra, usuarios y catálogo)
mysql -u root mesaly < database/seeds.sql
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
│   └── Views/             # Vistas PHP (layouts, admin, comensal, landing)
├── config/                # Configuraciones de la app y rutas
├── database/              # Esquema SQL, seeds y migraciones
├── docs/                  # Documentación técnica y especificaciones
├── public/                # DocumentRoot (index.php, CSS, JS, fuentes, imágenes)
│   ├── assets/            # Estilos, scripts y assets de marca
│   └── uploads/           # Imágenes cargadas por los comercios
├── storage/               # Logs y archivos de caché temporal
├── .env.example           # Plantilla de configuración de entorno
├── .gitignore             # Reglas de exclusión de Git
└── README.md              # Documentación principal
```

---

## Licencia

Este proyecto está bajo licencia privada. Todos los derechos reservados.
