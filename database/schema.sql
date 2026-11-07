-- ==============================================================================
-- MESALY — Esquema de Base de Datos
-- Motor: MySQL 8+ / MariaDB 10.6+ (InnoDB, utf8mb4_unicode_ci)
-- ==============================================================================

SET NAMES utf8mb4;
SET time_zone = '-05:00';
SET foreign_key_checks = 0;

-- ------------------------------------------------------------------------------
-- 1. Tabla: configuracion
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `configuracion` (
    `clave` VARCHAR(64) NOT NULL,
    `valor` TEXT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`clave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 2. Tabla: planes
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `planes` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(100) NOT NULL,
    `precio_mensual` INT UNSIGNED NOT NULL,
    `precio_anual` INT UNSIGNED NOT NULL,
    `max_productos` INT UNSIGNED NOT NULL DEFAULT 50,
    `max_mesas` INT UNSIGNED NOT NULL DEFAULT 15,
    `max_categorias` INT UNSIGNED NOT NULL DEFAULT 10,
    `quitar_marca` TINYINT(1) NOT NULL DEFAULT 0,
    `activo` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 3. Tabla: restaurantes
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `restaurantes` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(150) NOT NULL,
    `slug` VARCHAR(50) NOT NULL UNIQUE,
    `descripcion` VARCHAR(500) NULL,
    `logo_path` VARCHAR(255) NULL,
    `banner_path` VARCHAR(255) NULL,
    `color_primario` VARCHAR(7) NOT NULL DEFAULT '#E85D04',
    `color_secundario` VARCHAR(7) NOT NULL DEFAULT '#FFB703',
    `color_fondo` VARCHAR(7) NOT NULL DEFAULT '#FFF7EA',
    `color_texto` VARCHAR(7) NOT NULL DEFAULT '#1E1B18',
    `fuente` VARCHAR(50) NOT NULL DEFAULT 'Inter',
    `whatsapp` VARCHAR(20) NOT NULL,
    `moneda` VARCHAR(10) NOT NULL DEFAULT 'COP',
    `recibiendo_pedidos` TINYINT(1) NOT NULL DEFAULT 1,
    `acepta_mesa` TINYINT(1) NOT NULL DEFAULT 1,
    `acepta_domicilio` TINYINT(1) NOT NULL DEFAULT 1,
    `acepta_recoger` TINYINT(1) NOT NULL DEFAULT 1,
    `costo_envio` INT UNSIGNED NOT NULL DEFAULT 0,
    `pedido_minimo` INT UNSIGNED NOT NULL DEFAULT 0,
    `info_pago_texto` TEXT NULL,
    `plan_id` BIGINT UNSIGNED NULL,
    `ciclo` ENUM('mensual','anual') NOT NULL DEFAULT 'mensual',
    `fecha_vencimiento` DATE NULL,
    `dia_corte` TINYINT UNSIGNED NULL,
    `en_prueba` TINYINT(1) NOT NULL DEFAULT 0,
    `fecha_fin_prueba` DATE NULL,
    `estado_manual` ENUM('suspendido','cancelado') NULL DEFAULT NULL,
    `ultimo_recordatorio_at` DATETIME NULL,
    `siguiente_consecutivo` INT UNSIGNED NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME NULL,
    KEY `idx_restaurantes_slug` (`slug`),
    KEY `idx_restaurantes_vencimiento` (`fecha_vencimiento`),
    CONSTRAINT `fk_restaurantes_plan` FOREIGN KEY (`plan_id`) REFERENCES `planes` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 4. Tabla: usuarios
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `usuarios` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `restaurante_id` BIGINT UNSIGNED NULL,
    `nombre` VARCHAR(150) NOT NULL,
    `email` VARCHAR(191) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `rol` ENUM('super_admin','admin_negocio') NOT NULL,
    `debe_cambiar_password` TINYINT(1) NOT NULL DEFAULT 0,
    `activo` TINYINT(1) NOT NULL DEFAULT 1,
    `ultimo_login_at` DATETIME NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY `idx_usuarios_restaurante` (`restaurante_id`),
    CONSTRAINT `fk_usuarios_restaurante` FOREIGN KEY (`restaurante_id`) REFERENCES `restaurantes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 5. Tabla: horarios
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `horarios` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `restaurante_id` BIGINT UNSIGNED NOT NULL,
    `dia_semana` TINYINT UNSIGNED NOT NULL COMMENT '0=Domingo, 1=Lunes, ..., 6=Sábado',
    `abre` TIME NOT NULL,
    `cierra` TIME NOT NULL,
    `tramo` TINYINT UNSIGNED NOT NULL DEFAULT 1,
    `activo` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY `idx_horarios_restaurante` (`restaurante_id`, `dia_semana`),
    CONSTRAINT `fk_horarios_restaurante` FOREIGN KEY (`restaurante_id`) REFERENCES `restaurantes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 6. Tabla: metodos_pago_negocio
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `metodos_pago_negocio` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `restaurante_id` BIGINT UNSIGNED NOT NULL,
    `metodo` ENUM('efectivo','transferencia','datafono') NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_restaurante_metodo` (`restaurante_id`, `metodo`),
    CONSTRAINT `fk_metodos_restaurante` FOREIGN KEY (`restaurante_id`) REFERENCES `restaurantes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 7. Tabla: categorias
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `categorias` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `restaurante_id` BIGINT UNSIGNED NOT NULL,
    `nombre` VARCHAR(100) NOT NULL,
    `orden` INT UNSIGNED NOT NULL DEFAULT 0,
    `activa` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME NULL,
    KEY `idx_categorias_restaurante` (`restaurante_id`, `orden`),
    CONSTRAINT `fk_categorias_restaurante` FOREIGN KEY (`restaurante_id`) REFERENCES `restaurantes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 8. Tabla: productos
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `productos` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `restaurante_id` BIGINT UNSIGNED NOT NULL,
    `categoria_id` BIGINT UNSIGNED NOT NULL,
    `nombre` VARCHAR(150) NOT NULL,
    `descripcion` VARCHAR(500) NULL,
    `precio` INT UNSIGNED NOT NULL,
    `imagen_path` VARCHAR(255) NULL,
    `disponible` TINYINT(1) NOT NULL DEFAULT 1,
    `destacado` TINYINT(1) NOT NULL DEFAULT 0,
    `orden` INT UNSIGNED NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME NULL,
    KEY `idx_productos_restaurante_cat` (`restaurante_id`, `categoria_id`, `orden`),
    CONSTRAINT `fk_productos_restaurante` FOREIGN KEY (`restaurante_id`) REFERENCES `restaurantes` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_productos_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 9. Tabla: grupos_modificadores
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `grupos_modificadores` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `restaurante_id` BIGINT UNSIGNED NOT NULL,
    `nombre` VARCHAR(100) NOT NULL,
    `tipo` ENUM('unica','multiple') NOT NULL DEFAULT 'unica',
    `obligatorio` TINYINT(1) NOT NULL DEFAULT 0,
    `min_selecciones` INT UNSIGNED NOT NULL DEFAULT 0,
    `max_selecciones` INT UNSIGNED NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME NULL,
    KEY `idx_grupos_restaurante` (`restaurante_id`),
    CONSTRAINT `fk_grupos_restaurante` FOREIGN KEY (`restaurante_id`) REFERENCES `restaurantes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 10. Tabla: opciones_modificadores
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `opciones_modificadores` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `grupo_id` BIGINT UNSIGNED NOT NULL,
    `nombre` VARCHAR(100) NOT NULL,
    `precio_extra` INT UNSIGNED NOT NULL DEFAULT 0,
    `orden` INT UNSIGNED NOT NULL DEFAULT 0,
    `activa` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME NULL,
    KEY `idx_opciones_grupo` (`grupo_id`, `orden`),
    CONSTRAINT `fk_opciones_grupo` FOREIGN KEY (`grupo_id`) REFERENCES `grupos_modificadores` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 11. Tabla: producto_grupos (N:M)
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `producto_grupos` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `producto_id` BIGINT UNSIGNED NOT NULL,
    `grupo_id` BIGINT UNSIGNED NOT NULL,
    `orden` INT UNSIGNED NOT NULL DEFAULT 0,
    UNIQUE KEY `uk_producto_grupo` (`producto_id`, `grupo_id`),
    CONSTRAINT `fk_pg_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_pg_grupo` FOREIGN KEY (`grupo_id`) REFERENCES `grupos_modificadores` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 12. Tabla: mesas
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `mesas` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `restaurante_id` BIGINT UNSIGNED NOT NULL,
    `numero` INT UNSIGNED NOT NULL,
    `nombre` VARCHAR(50) NULL,
    `activa` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME NULL,
    UNIQUE KEY `uk_restaurante_mesa` (`restaurante_id`, `numero`),
    CONSTRAINT `fk_mesas_restaurante` FOREIGN KEY (`restaurante_id`) REFERENCES `restaurantes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 13. Tabla: pedidos
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `pedidos` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `restaurante_id` BIGINT UNSIGNED NOT NULL,
    `consecutivo` INT UNSIGNED NOT NULL,
    `codigo` VARCHAR(30) NOT NULL,
    `modalidad` ENUM('mesa','domicilio','recoger') NOT NULL,
    `mesa_id` BIGINT UNSIGNED NULL,
    `cliente_nombre` VARCHAR(150) NOT NULL,
    `cliente_telefono` VARCHAR(30) NULL,
    `direccion` VARCHAR(255) NULL,
    `barrio` VARCHAR(100) NULL,
    `notas_entrega` VARCHAR(500) NULL,
    `hora_recogida` VARCHAR(50) NULL,
    `metodo_pago` VARCHAR(50) NOT NULL,
    `subtotal` INT UNSIGNED NOT NULL,
    `costo_envio` INT UNSIGNED NOT NULL DEFAULT 0,
    `total` INT UNSIGNED NOT NULL,
    `estado` ENUM('generado','en_preparacion','listo','entregado','cancelado') NOT NULL DEFAULT 'generado',
    `ip` VARCHAR(45) NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_restaurante_consecutivo` (`restaurante_id`, `consecutivo`),
    KEY `idx_pedidos_restaurante_estado` (`restaurante_id`, `estado`, `created_at`),
    CONSTRAINT `fk_pedidos_restaurante` FOREIGN KEY (`restaurante_id`) REFERENCES `restaurantes` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_pedidos_mesa` FOREIGN KEY (`mesa_id`) REFERENCES `mesas` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 14. Tabla: pedido_items
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `pedido_items` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `pedido_id` BIGINT UNSIGNED NOT NULL,
    `producto_id` BIGINT UNSIGNED NOT NULL,
    `nombre_producto` VARCHAR(150) NOT NULL,
    `precio_unitario` INT UNSIGNED NOT NULL,
    `cantidad` INT UNSIGNED NOT NULL,
    `nota` VARCHAR(300) NULL,
    `total_linea` INT UNSIGNED NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY `idx_items_pedido` (`pedido_id`),
    CONSTRAINT `fk_items_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 15. Tabla: pedido_item_opciones
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `pedido_item_opciones` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `pedido_item_id` BIGINT UNSIGNED NOT NULL,
    `nombre_grupo` VARCHAR(100) NOT NULL,
    `nombre_opcion` VARCHAR(100) NOT NULL,
    `precio_extra` INT UNSIGNED NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY `idx_item_opciones` (`pedido_item_id`),
    CONSTRAINT `fk_opciones_item` FOREIGN KEY (`pedido_item_id`) REFERENCES `pedido_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 16. Tabla: pagos_suscripcion
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `pagos_suscripcion` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `restaurante_id` BIGINT UNSIGNED NOT NULL,
    `fecha_pago` DATE NOT NULL,
    `monto` INT UNSIGNED NOT NULL,
    `metodo` VARCHAR(50) NOT NULL,
    `periodos` INT UNSIGNED NOT NULL DEFAULT 1,
    `fecha_vencimiento_anterior` DATE NULL,
    `fecha_vencimiento_nueva` DATE NOT NULL,
    `nota` VARCHAR(500) NULL,
    `registrado_por` BIGINT UNSIGNED NOT NULL,
    `anulado` TINYINT(1) NOT NULL DEFAULT 0,
    `anulado_motivo` VARCHAR(500) NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY `idx_pagos_restaurante` (`restaurante_id`),
    CONSTRAINT `fk_pagos_restaurante` FOREIGN KEY (`restaurante_id`) REFERENCES `restaurantes` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_pagos_usuario` FOREIGN KEY (`registrado_por`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 17. Tabla: intentos_login
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `intentos_login` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `ip` VARCHAR(45) NOT NULL,
    `email` VARCHAR(191) NOT NULL,
    `exitoso` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY `idx_intentos_ip_email` (`ip`, `email`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------------------------
-- 18. Tabla: auditoria
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `auditoria` (
    `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `usuario_id` BIGINT UNSIGNED NULL,
    `restaurante_id` BIGINT UNSIGNED NULL,
    `accion` VARCHAR(100) NOT NULL,
    `entidad` VARCHAR(100) NOT NULL,
    `entidad_id` BIGINT UNSIGNED NULL,
    `antes` JSON NULL,
    `despues` JSON NULL,
    `ip` VARCHAR(45) NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY `idx_auditoria_restaurante` (`restaurante_id`, `created_at`),
    KEY `idx_auditoria_usuario` (`usuario_id`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET foreign_key_checks = 1;
