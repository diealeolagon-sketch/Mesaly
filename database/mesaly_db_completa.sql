-- Mesaly Complete Database Dump
-- Generated: 2026-11-24 21:37:48
-- Database: mesaly_db

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
SET NAMES utf8mb4;

-- --------------------------------------------------------
-- Table structure for `auditoria`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `auditoria`;
CREATE TABLE `auditoria` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `usuario_id` bigint unsigned DEFAULT NULL,
  `restaurante_id` bigint unsigned DEFAULT NULL,
  `accion` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entidad` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entidad_id` bigint unsigned DEFAULT NULL,
  `antes` json DEFAULT NULL,
  `despues` json DEFAULT NULL,
  `ip` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_auditoria_restaurante` (`restaurante_id`,`created_at`),
  KEY `idx_auditoria_usuario` (`usuario_id`,`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `auditoria` (6 rows)
LOCK TABLES `auditoria` WRITE;
/*!40000 ALTER TABLE `auditoria` DISABLE KEYS */;
INSERT INTO `auditoria` VALUES
(1, 1, NULL, 'inicializar_plataforma', 'sistema', NULL, NULL, '{\"estado\": \"operativo\", \"version\": \"2.0.0\", \"restaurantes_activos\": 3}', '127.0.0.1', '2026-11-22 20:37:44'),
(2, 1, 1, 'crear_negocio', 'restaurante', 1, NULL, '{\"slug\": \"cafe-macondo\", \"nombre\": \"Café Macondo\"}', '127.0.0.1', '2026-11-22 10:00:00'),
(3, 1, 2, 'crear_negocio', 'restaurante', 2, NULL, '{\"slug\": \"pizzeria-di-roma\", \"nombre\": \"Pizzería Di Roma\"}', '127.0.0.1', '2026-11-22 10:30:00'),
(4, 1, 3, 'crear_negocio', 'restaurante', 3, NULL, '{\"slug\": \"sabor-criollo\", \"nombre\": \"Sabor Criollo Colombiano\"}', '127.0.0.1', '2026-11-22 11:00:00'),
(5, 1, 1, 'pago_suscripcion_registrado', 'pago_suscripcion', 1, NULL, '{\"monto\": 49000, \"metodo\": \"Wompi\", \"referencia\": \"WOMPI-MESA-001\"}', '127.0.0.1', '2026-11-14 20:37:44'),
(6, 1, 2, 'reset_password_admin', 'usuarios', 3, NULL, '{\"email\": \"admin@pizzeriadiroma.co\"}', '::1', '2026-11-24 15:05:25');
/*!40000 ALTER TABLE `auditoria` ENABLE KEYS */;
UNLOCK TABLES;

-- --------------------------------------------------------
-- Table structure for `categorias`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `categorias`;
CREATE TABLE `categorias` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `restaurante_id` bigint unsigned NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `orden` int unsigned NOT NULL DEFAULT '0',
  `activa` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_categorias_restaurante` (`restaurante_id`,`orden`),
  CONSTRAINT `fk_categorias_restaurante` FOREIGN KEY (`restaurante_id`) REFERENCES `restaurantes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `categorias` (13 rows)
LOCK TABLES `categorias` WRITE;
/*!40000 ALTER TABLE `categorias` DISABLE KEYS */;
INSERT INTO `categorias` VALUES
(1, 1, 'Cafés de Especialidad', 1, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(2, 1, 'Brunch & Tostadas', 2, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(3, 1, 'Repostería & Panadería', 3, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(4, 1, 'Bebidas Frías', 4, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(5, 2, 'Pizzas Artesanales', 1, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(6, 2, 'Pastas al Horno & Tradición', 2, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(7, 2, 'Postres Italianos', 3, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(8, 2, 'Bebidas & Refrescos', 4, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(9, 3, 'Platos Fuertes Tradicionales', 1, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(10, 3, 'Sopas & Cazuelas de la Casa', 2, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(11, 3, 'Entradas, Picadas & Arepas', 3, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(12, 3, 'Jugos Naturales & Bebidas Típicas', 4, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(13, 3, 'Postres Tradicionales', 5, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL);
/*!40000 ALTER TABLE `categorias` ENABLE KEYS */;
UNLOCK TABLES;

-- --------------------------------------------------------
-- Table structure for `configuracion`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `configuracion`;
CREATE TABLE `configuracion` (
  `clave` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `valor` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`clave`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `configuracion` (4 rows)
LOCK TABLES `configuracion` WRITE;
/*!40000 ALTER TABLE `configuracion` DISABLE KEYS */;
INSERT INTO `configuracion` VALUES
('dias_alerta', 5, '2026-11-07 17:28:00', '2026-11-07 17:28:00'),
('dias_gracia', 3, '2026-11-07 17:28:00', '2026-11-07 17:28:00'),
('nombre_plataforma', 'Mesaly', '2026-11-07 17:28:00', '2026-11-07 17:28:00'),
('whatsapp_soporte', 573248670360, '2026-11-07 17:28:00', '2026-11-24 14:27:44');
/*!40000 ALTER TABLE `configuracion` ENABLE KEYS */;
UNLOCK TABLES;

-- --------------------------------------------------------
-- Table structure for `grupos_modificadores`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `grupos_modificadores`;
CREATE TABLE `grupos_modificadores` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `restaurante_id` bigint unsigned NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` enum('unica','multiple') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unica',
  `obligatorio` tinyint(1) NOT NULL DEFAULT '0',
  `min_selecciones` int unsigned NOT NULL DEFAULT '0',
  `max_selecciones` int unsigned NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_grupos_restaurante` (`restaurante_id`),
  CONSTRAINT `fk_grupos_restaurante` FOREIGN KEY (`restaurante_id`) REFERENCES `restaurantes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `grupos_modificadores` (9 rows)
LOCK TABLES `grupos_modificadores` WRITE;
/*!40000 ALTER TABLE `grupos_modificadores` DISABLE KEYS */;
INSERT INTO `grupos_modificadores` VALUES
(1, 1, 'Tipo de Leche', 'unica', 0, 0, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(2, 1, 'Adicionales para tu Café', 'multiple', 0, 0, 3, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(3, 1, 'Adiciones Brunch', 'multiple', 0, 0, 3, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(4, 2, 'Borde de la Pizza', 'unica', 0, 0, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(5, 2, 'Ingredientes Extra', 'multiple', 0, 0, 4, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(6, 2, 'Queso Parmesano Extra', 'unica', 0, 0, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(7, 3, 'Adiciones Criollas', 'multiple', 0, 0, 4, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(8, 3, 'Término de la Carne', 'unica', 1, 1, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(9, 3, 'Preparación del Jugo', 'unica', 1, 1, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL);
/*!40000 ALTER TABLE `grupos_modificadores` ENABLE KEYS */;
UNLOCK TABLES;

-- --------------------------------------------------------
-- Table structure for `horarios`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `horarios`;
CREATE TABLE `horarios` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `restaurante_id` bigint unsigned NOT NULL,
  `dia_semana` tinyint unsigned NOT NULL COMMENT '0=Domingo, 1=Lunes, ..., 6=Sábado',
  `abre` time NOT NULL,
  `cierra` time NOT NULL,
  `tramo` tinyint unsigned NOT NULL DEFAULT '1',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_horarios_restaurante` (`restaurante_id`,`dia_semana`),
  CONSTRAINT `fk_horarios_restaurante` FOREIGN KEY (`restaurante_id`) REFERENCES `restaurantes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `horarios` (21 rows)
LOCK TABLES `horarios` WRITE;
/*!40000 ALTER TABLE `horarios` DISABLE KEYS */;
INSERT INTO `horarios` VALUES
(1, 1, 0, '08:00:00', '22:00:00', 1, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44'),
(2, 1, 1, '08:00:00', '22:00:00', 1, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44'),
(3, 1, 2, '08:00:00', '22:00:00', 1, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44'),
(4, 1, 3, '08:00:00', '22:00:00', 1, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44'),
(5, 1, 4, '08:00:00', '22:00:00', 1, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44'),
(6, 1, 5, '08:00:00', '22:00:00', 1, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44'),
(7, 1, 6, '08:00:00', '22:00:00', 1, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44'),
(8, 2, 0, '08:00:00', '22:00:00', 1, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44'),
(9, 2, 1, '08:00:00', '22:00:00', 1, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44'),
(10, 2, 2, '08:00:00', '22:00:00', 1, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44'),
(11, 2, 3, '08:00:00', '22:00:00', 1, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44'),
(12, 2, 4, '08:00:00', '22:00:00', 1, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44'),
(13, 2, 5, '08:00:00', '22:00:00', 1, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44'),
(14, 2, 6, '08:00:00', '22:00:00', 1, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44'),
(15, 3, 0, '08:00:00', '22:00:00', 1, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44'),
(16, 3, 1, '08:00:00', '22:00:00', 1, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44'),
(17, 3, 2, '08:00:00', '22:00:00', 1, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44'),
(18, 3, 3, '08:00:00', '22:00:00', 1, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44'),
(19, 3, 4, '08:00:00', '22:00:00', 1, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44'),
(20, 3, 5, '08:00:00', '22:00:00', 1, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44'),
(21, 3, 6, '08:00:00', '22:00:00', 1, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44');
/*!40000 ALTER TABLE `horarios` ENABLE KEYS */;
UNLOCK TABLES;

-- --------------------------------------------------------
-- Table structure for `intentos_login`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `intentos_login`;
CREATE TABLE `intentos_login` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ip` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `exitoso` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_intentos_ip_email` (`ip`,`email`,`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `intentos_login` (9 rows)
LOCK TABLES `intentos_login` WRITE;
/*!40000 ALTER TABLE `intentos_login` DISABLE KEYS */;
INSERT INTO `intentos_login` VALUES
(1, '::1', 'admin@mesaly.co', 1, '2026-11-24 15:00:58'),
(2, '::1', 'admin@mesaly.co', 1, '2026-11-24 15:04:24'),
(3, '::1', 'admin@pizzeriadiroma.co', 1, '2026-11-24 15:06:06'),
(4, '::1', 'admin@mesaly.co', 1, '2026-11-24 15:10:13'),
(5, '::1', 'admin@mesaly.co', 1, '2026-11-24 15:22:08'),
(6, '::1', 'admin@mesaly.co', 1, '2026-11-24 15:31:33'),
(7, '::1', 'admin@mesaly.co', 1, '2026-11-24 15:36:04'),
(8, '::1', 'admin@mesaly.co', 1, '2026-11-24 15:36:55'),
(9, '::1', 'admin@mesaly.co', 1, '2026-11-24 15:37:35');
/*!40000 ALTER TABLE `intentos_login` ENABLE KEYS */;
UNLOCK TABLES;

-- --------------------------------------------------------
-- Table structure for `mesas`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `mesas`;
CREATE TABLE `mesas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `restaurante_id` bigint unsigned NOT NULL,
  `numero` int unsigned NOT NULL,
  `nombre` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activa` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_restaurante_mesa` (`restaurante_id`,`numero`),
  CONSTRAINT `fk_mesas_restaurante` FOREIGN KEY (`restaurante_id`) REFERENCES `restaurantes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `mesas` (18 rows)
LOCK TABLES `mesas` WRITE;
/*!40000 ALTER TABLE `mesas` DISABLE KEYS */;
INSERT INTO `mesas` VALUES
(1, 1, 1, 'Mesa 1', 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(2, 1, 2, 'Mesa 2', 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(3, 1, 3, 'Mesa 3', 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(4, 1, 4, 'Mesa 4', 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(5, 1, 5, 'Mesa 5', 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(6, 1, 6, 'Mesa 6', 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(7, 2, 1, 'Mesa 1', 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(8, 2, 2, 'Mesa 2', 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(9, 2, 3, 'Mesa 3', 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(10, 2, 4, 'Mesa 4', 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(11, 2, 5, 'Mesa 5', 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(12, 2, 6, 'Mesa 6', 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(13, 3, 1, 'Mesa 1', 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(14, 3, 2, 'Mesa 2', 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(15, 3, 3, 'Mesa 3', 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(16, 3, 4, 'Mesa 4', 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(17, 3, 5, 'Mesa 5', 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(18, 3, 6, 'Mesa 6', 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL);
/*!40000 ALTER TABLE `mesas` ENABLE KEYS */;
UNLOCK TABLES;

-- --------------------------------------------------------
-- Table structure for `metodos_pago_negocio`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `metodos_pago_negocio`;
CREATE TABLE `metodos_pago_negocio` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `restaurante_id` bigint unsigned NOT NULL,
  `metodo` enum('efectivo','transferencia','datafono') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_restaurante_metodo` (`restaurante_id`,`metodo`),
  CONSTRAINT `fk_metodos_restaurante` FOREIGN KEY (`restaurante_id`) REFERENCES `restaurantes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `opciones_modificadores`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `opciones_modificadores`;
CREATE TABLE `opciones_modificadores` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `grupo_id` bigint unsigned NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `precio_extra` int unsigned NOT NULL DEFAULT '0',
  `orden` int unsigned NOT NULL DEFAULT '0',
  `activa` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_opciones_grupo` (`grupo_id`,`orden`),
  CONSTRAINT `fk_opciones_grupo` FOREIGN KEY (`grupo_id`) REFERENCES `grupos_modificadores` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `opciones_modificadores` (33 rows)
LOCK TABLES `opciones_modificadores` WRITE;
/*!40000 ALTER TABLE `opciones_modificadores` DISABLE KEYS */;
INSERT INTO `opciones_modificadores` VALUES
(1, 1, 'Leche Entera', 0, 1, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(2, 1, 'Leche Deslactosada', 0, 2, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(3, 1, 'Leche de Almendras', 2500, 3, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(4, 1, 'Leche de Avena', 2500, 4, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(5, 2, 'Shot Extra de Espresso Origen', 3000, 1, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(6, 2, 'Sirope de Vainilla Francesa', 2000, 2, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(7, 2, 'Sirope de Caramelo Salado', 2000, 3, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(8, 2, 'Crema Chantilly Artesanal', 2000, 4, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(9, 3, 'Porción Tocineta Ahumada Crocante', 4500, 1, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(10, 3, 'Huevo Poché Adicional', 2500, 2, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(11, 3, 'Porción Aguacate Hass Fresco', 3500, 3, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(12, 3, 'Queso Parmesano Extra', 3000, 4, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(13, 4, 'Borde Tradicional Crujiente', 0, 1, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(14, 4, 'Borde Relleno de Queso Mozzarella', 8000, 2, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(15, 4, 'Borde Relleno de Bocadillo y Queso', 8000, 3, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(16, 5, 'Extra Pepperoni Rústico', 6000, 1, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(17, 5, 'Queso Burrata Fresco 125g', 12000, 2, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(18, 5, 'Champiñones Frescos Salteados', 4500, 3, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(19, 5, 'Tocineta Ahumada en Tiras', 5000, 4, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(20, 5, 'Jamón Serrano Reserva', 7000, 5, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(21, 6, 'Parmesano Tradicional Incluido', 0, 1, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(22, 6, 'Doble Porción Parmigiano Reggiano', 4000, 2, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(23, 7, 'Chicharrón Carnudo Crocante Extra', 9500, 1, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(24, 7, 'Porción de Aguacate Hass', 4000, 2, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(25, 7, 'Huevo Criollo Frito', 2500, 3, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(26, 7, 'Tajadas de Plátano Maduro con Quesito', 5000, 4, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(27, 7, 'Porción de Arroz Blanco del Campo', 3000, 5, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(28, 7, 'Porción Extra de Hogao Casero', 2500, 6, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(29, 8, 'Término Medio (Jugosa y tierna)', 0, 1, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(30, 8, 'Término Tres Cuartos (Punto ideal)', 0, 2, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(31, 8, 'Bien Asada (Dorada por fuera y dentro)', 0, 3, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(32, 9, 'En Agua Natural Refrescante', 0, 1, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL),
(33, 9, 'En Leche Entera Cremosa', 2000, 2, 1, '2026-11-24 14:37:44', '2026-11-24 14:37:44', NULL);
/*!40000 ALTER TABLE `opciones_modificadores` ENABLE KEYS */;
UNLOCK TABLES;

-- --------------------------------------------------------
-- Table structure for `pagos_suscripcion`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `pagos_suscripcion`;
CREATE TABLE `pagos_suscripcion` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `restaurante_id` bigint unsigned NOT NULL,
  `fecha_pago` date NOT NULL,
  `monto` int unsigned NOT NULL,
  `metodo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `periodos` int unsigned NOT NULL DEFAULT '1',
  `fecha_vencimiento_anterior` date DEFAULT NULL,
  `fecha_vencimiento_nueva` date NOT NULL,
  `referencia` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nota` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `registrado_por` bigint unsigned NOT NULL,
  `anulado` tinyint(1) NOT NULL DEFAULT '0',
  `anulado_motivo` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_pagos_restaurante` (`restaurante_id`),
  KEY `fk_pagos_usuario` (`registrado_por`),
  CONSTRAINT `fk_pagos_restaurante` FOREIGN KEY (`restaurante_id`) REFERENCES `restaurantes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pagos_usuario` FOREIGN KEY (`registrado_por`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `pagos_suscripcion` (3 rows)
LOCK TABLES `pagos_suscripcion` WRITE;
/*!40000 ALTER TABLE `pagos_suscripcion` DISABLE KEYS */;
INSERT INTO `pagos_suscripcion` VALUES
(1, 1, '2026-11-14', 49000, 'Wompi - Tarjeta Débito / PSE', 1, '2026-11-14', '2026-12-24', 'WOMPI-MESA-001', 'Renovación mensual automática vía Wompi', 1, 0, NULL, '2026-11-24 14:37:44', '2026-11-24 14:37:44'),
(2, 2, '2026-11-20', 49000, 'Wompi - Tarjeta de Crédito Visa', 1, '2026-11-20', '2026-12-20', 'WOMPI-MESA-002', 'Pago mensual plan Pro Wompi', 1, 0, NULL, '2026-11-24 14:37:44', '2026-11-24 14:37:44'),
(3, 3, '2026-11-22', 49000, 'Wompi - Nequi', 1, '2026-11-22', '2026-12-22', 'WOMPI-MESA-003', 'Activación de suscripción mensual Sabor Criollo', 1, 0, NULL, '2026-11-24 14:37:44', '2026-11-24 14:37:44');
/*!40000 ALTER TABLE `pagos_suscripcion` ENABLE KEYS */;
UNLOCK TABLES;

-- --------------------------------------------------------
-- Table structure for `pedido_item_opciones`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `pedido_item_opciones`;
CREATE TABLE `pedido_item_opciones` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pedido_item_id` bigint unsigned NOT NULL,
  `nombre_grupo` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre_opcion` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `precio_extra` int unsigned NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_item_opciones` (`pedido_item_id`),
  CONSTRAINT `fk_opciones_item` FOREIGN KEY (`pedido_item_id`) REFERENCES `pedido_items` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `pedido_item_opciones` (14 rows)
LOCK TABLES `pedido_item_opciones` WRITE;
/*!40000 ALTER TABLE `pedido_item_opciones` DISABLE KEYS */;
INSERT INTO `pedido_item_opciones` VALUES
(1, 1, 'Tipo de Leche', 'Leche de Almendras', 2500, '2026-11-23 10:15:00'),
(2, 2, 'Adiciones Brunch', 'Porción Tocineta Ahumada Crocante', 4500, '2026-11-23 10:15:00'),
(3, 7, 'Adicionales para tu Café', 'Shot Extra de Espresso Origen', 3000, '2026-11-24 18:37:44'),
(4, 11, 'Borde de la Pizza', 'Borde Relleno de Queso Mozzarella', 8000, '2026-11-23 19:45:00'),
(5, 11, 'Ingredientes Extra', 'Extra Pepperoni Rústico', 6000, '2026-11-23 19:45:00'),
(6, 15, 'Ingredientes Extra', 'Queso Burrata Fresco 125g', 12000, '2026-11-23 20:30:00'),
(7, 16, 'Queso Parmesano Extra', 'Doble Porción Parmigiano Reggiano', 4000, '2026-11-23 20:30:00'),
(8, 21, 'Adiciones Criollas', 'Chicharrón Carnudo Crocante Extra', 9500, '2026-11-23 12:45:00'),
(9, 21, 'Adiciones Criollas', 'Porción de Aguacate Hass', 4000, '2026-11-23 12:45:00'),
(10, 23, 'Preparación del Jugo', 'En Leche Entera Cremosa', 2000, '2026-11-23 12:45:00'),
(11, 25, 'Adiciones Criollas', 'Porción de Aguacate Hass', 4000, '2026-11-23 13:30:00'),
(12, 28, 'Adiciones Criollas', 'Tajadas de Plátano Maduro con Quesito', 5000, '2026-11-24 19:52:44'),
(13, 29, 'Término de la Carne', 'Término Tres Cuartos (Punto ideal)', 0, '2026-11-24 19:52:44'),
(14, 30, 'Preparación del Jugo', 'En Agua Natural Refrescante', 0, '2026-11-24 19:52:44');
/*!40000 ALTER TABLE `pedido_item_opciones` ENABLE KEYS */;
UNLOCK TABLES;

-- --------------------------------------------------------
-- Table structure for `pedido_items`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `pedido_items`;
CREATE TABLE `pedido_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pedido_id` bigint unsigned NOT NULL,
  `producto_id` bigint unsigned NOT NULL,
  `nombre_producto` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `precio_unitario` int unsigned NOT NULL,
  `cantidad` int unsigned NOT NULL,
  `nota` varchar(300) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_linea` int unsigned NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_items_pedido` (`pedido_id`),
  CONSTRAINT `fk_items_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `pedido_items` (32 rows)
LOCK TABLES `pedido_items` WRITE;
/*!40000 ALTER TABLE `pedido_items` DISABLE KEYS */;
INSERT INTO `pedido_items` VALUES
(1, 1, 2, 'Cappuccino Italiano', 11000, 2, NULL, 22000, '2026-11-23 10:15:00'),
(2, 1, 4, 'Tostada de Aguacate & Huevo Poché', 21000, 1, NULL, 21000, '2026-11-23 10:15:00'),
(3, 1, 7, 'Croissant de Almendras Horneado', 10000, 2, NULL, 20000, '2026-11-23 10:15:00'),
(4, 2, 1, 'Café Espresso Doble', 5500, 2, NULL, 11000, '2026-11-23 16:30:00'),
(5, 2, 8, 'Tarta de Zanahoria & Nuez con Frosting', 12000, 2, NULL, 24000, '2026-11-23 16:30:00'),
(6, 2, 5, 'Tostada Francesa Brioche con Frutos Rojos', 19000, 1, NULL, 19000, '2026-11-23 16:30:00'),
(7, 3, 3, 'Latte Vainilla Bourbon', 12500, 2, NULL, 25000, '2026-11-24 18:37:44'),
(8, 3, 6, 'Sandwich Gourmet Jamón Serrano & Brie', 22000, 1, NULL, 22000, '2026-11-24 18:37:44'),
(9, 4, 2, 'Cappuccino Italiano', 8500, 1, NULL, 8500, '2026-11-24 20:07:44'),
(10, 4, 7, 'Croissant de Almendras Horneado', 10000, 3, NULL, 30000, '2026-11-24 20:07:44'),
(11, 5, 12, 'Pizza Pepperoni Rústica Artesanal', 53000, 1, NULL, 53000, '2026-11-23 19:45:00'),
(12, 5, 11, 'Pizza Margarita Clásica Napolitana', 34000, 1, NULL, 34000, '2026-11-23 19:45:00'),
(13, 5, 17, 'Tiramisú Tradicional de Café & Mascarpone', 16000, 2, NULL, 32000, '2026-11-23 19:45:00'),
(14, 5, 20, 'Cerveza Moretti Italiana 330ml', 15000, 2, NULL, 30000, '2026-11-23 19:45:00'),
(15, 6, 13, 'Pizza Cuatro Quesos DOP Italiana', 54000, 1, NULL, 54000, '2026-11-23 20:30:00'),
(16, 6, 15, 'Lasagna Tradicional Boloñesa al Horno', 36000, 1, NULL, 36000, '2026-11-23 20:30:00'),
(17, 6, 19, 'Limonada de Coco Cremosita', 11000, 2, NULL, 22000, '2026-11-23 20:30:00'),
(18, 7, 14, 'Pizza Prosciutto & Rúcula Fresca', 46000, 1, NULL, 46000, '2026-11-24 19:37:44'),
(19, 7, 16, 'Fettuccine Alfredo con Pollo & Champiñones', 30000, 1, NULL, 30000, '2026-11-24 19:37:44'),
(20, 7, 18, 'Panna Cotta Artesanal con Frutos Rojos', 14000, 1, NULL, 14000, '2026-11-24 19:37:44'),
(21, 8, 21, 'Bandeja Paisa Tradicional Completa', 51500, 2, NULL, 103000, '2026-11-23 12:45:00'),
(22, 8, 27, 'Canasta de Empanaditas Vallunas (5 unidades)', 15000, 1, NULL, 15000, '2026-11-23 12:45:00'),
(23, 8, 29, 'Jugo Natural de Lulo Colombiano', 10500, 2, NULL, 21000, '2026-11-23 12:45:00'),
(24, 8, 32, 'Brevas en Almíbar con Arequipe & Queso', 10000, 2, NULL, 20000, '2026-11-23 12:45:00'),
(25, 9, 23, 'Ajiaco Santafereño con Pollo Desmechado', 36000, 2, NULL, 72000, '2026-11-23 13:30:00'),
(26, 9, 28, 'Arepa de Chócolo con Quesito Campesino', 12000, 2, NULL, 24000, '2026-11-23 13:30:00'),
(27, 9, 31, 'Limonada de Panela con Jengibre y Menta', 7000, 2, NULL, 14000, '2026-11-23 13:30:00'),
(28, 10, 26, 'Picada Criolla Mixta para 2 Personas', 53000, 1, NULL, 53000, '2026-11-24 19:52:44'),
(29, 10, 22, 'Churrasco a la Parrilla 350g con Chimichurri', 42000, 1, NULL, 42000, '2026-11-24 19:52:44'),
(30, 10, 30, 'Jugo Natural de Maracuyá Silvestre', 8500, 2, NULL, 17000, '2026-11-24 19:52:44'),
(31, 11, 24, 'Sancocho Trifásico Tradicional de Campo', 34000, 2, NULL, 68000, '2026-11-24 20:22:44'),
(32, 11, 27, 'Canasta de Empanaditas Vallunas (5 unidades)', 15000, 1, NULL, 15000, '2026-11-24 20:22:44');
/*!40000 ALTER TABLE `pedido_items` ENABLE KEYS */;
UNLOCK TABLES;

-- --------------------------------------------------------
-- Table structure for `pedidos`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `pedidos`;
CREATE TABLE `pedidos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `restaurante_id` bigint unsigned NOT NULL,
  `consecutivo` int unsigned NOT NULL,
  `codigo` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `modalidad` enum('mesa','domicilio','recoger') COLLATE utf8mb4_unicode_ci NOT NULL,
  `mesa_id` bigint unsigned DEFAULT NULL,
  `cliente_nombre` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cliente_telefono` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `barrio` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notas_entrega` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hora_recogida` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `metodo_pago` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subtotal` int unsigned NOT NULL,
  `costo_envio` int unsigned NOT NULL DEFAULT '0',
  `total` int unsigned NOT NULL,
  `estado` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'recibido',
  `mesa_liberada_at` datetime DEFAULT NULL,
  `ip` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_restaurante_consecutivo` (`restaurante_id`,`consecutivo`),
  KEY `idx_pedidos_restaurante_estado` (`restaurante_id`,`estado`,`created_at`),
  KEY `idx_pedidos_mesa_liberada` (`mesa_id`,`mesa_liberada_at`),
  CONSTRAINT `fk_pedidos_mesa` FOREIGN KEY (`mesa_id`) REFERENCES `mesas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_pedidos_restaurante` FOREIGN KEY (`restaurante_id`) REFERENCES `restaurantes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `pedidos` (11 rows)
LOCK TABLES `pedidos` WRITE;
/*!40000 ALTER TABLE `pedidos` DISABLE KEYS */;
INSERT INTO `pedidos` VALUES
(1, 1, 1, 'CM-001', 'mesa', 1, 'Mesa 1 (Carlos Ruiz)', 3157891234, NULL, NULL, NULL, NULL, 'acordar', 63000, 0, 63000, 'entregado', NULL, '127.0.0.1', '2026-11-23 10:15:00', '2026-11-23 10:15:00'),
(2, 1, 2, 'CM-002', 'domicilio', NULL, 'Laura Gómez', 3206549871, 'Calle 28 # 14-35 Barrio Alvernia', 'Alvernia', 'Timbre blanco al lado del porton negro', NULL, 'acordar', 54000, 4000, 58000, 'entregado', NULL, '127.0.0.1', '2026-11-23 16:30:00', '2026-11-23 16:30:00'),
(3, 1, 3, 'CM-003', 'mesa', 2, 'Mesa 2 (Felipe Mora)', 3114567890, NULL, NULL, NULL, NULL, 'acordar', 47000, 0, 47000, 'preparando', NULL, '127.0.0.1', '2026-11-24 18:37:44', '2026-11-24 18:37:44'),
(4, 1, 4, 'CM-004', 'recoger', NULL, 'Marcela Castro', 3189998877, NULL, NULL, NULL, '15:45', 'acordar', 38500, 0, 38500, 'recibido', NULL, '127.0.0.1', '2026-11-24 20:07:44', '2026-11-24 20:07:44'),
(5, 2, 1, 'PDR-001', 'mesa', 1, 'Mesa 1 (Familia Montoya)', 3123456789, NULL, NULL, NULL, NULL, 'acordar', 149000, 0, 149000, 'entregado', NULL, '127.0.0.1', '2026-11-23 19:45:00', '2026-11-23 19:45:00'),
(6, 2, 2, 'PDR-002', 'domicilio', NULL, 'Andrés Beltrán', 3174443322, 'Carrera 35 # 24-18 Apto 402', 'El Príncipe', 'Dejar en portería con el celador Nelson', NULL, 'acordar', 112000, 5000, 117000, 'entregado', NULL, '127.0.0.1', '2026-11-23 20:30:00', '2026-11-23 20:30:00'),
(7, 2, 3, 'PDR-003', 'mesa', 3, 'Mesa 3 (Valentina Osorio)', 3168887766, NULL, NULL, NULL, NULL, 'acordar', 90000, 0, 90000, 'preparando', NULL, '127.0.0.1', '2026-11-24 19:37:44', '2026-11-24 19:37:44'),
(8, 3, 1, 'SCC-001', 'mesa', 1, 'Mesa 1 (Don Hernando & Familia)', 3145556677, NULL, NULL, NULL, NULL, 'acordar', 159000, 0, 159000, 'entregado', NULL, '127.0.0.1', '2026-11-23 12:45:00', '2026-11-23 12:45:00'),
(9, 3, 2, 'SCC-002', 'domicilio', NULL, 'Dra. Carolina Pineda', 3109990011, 'Avenida Santander # 45-20 Clínica Santa Sofía', 'Centro', 'Piso 3 consultorio 304', NULL, 'acordar', 110000, 6000, 116000, 'entregado', NULL, '127.0.0.1', '2026-11-23 13:30:00', '2026-11-23 13:30:00'),
(10, 3, 3, 'SCC-003', 'mesa', 2, 'Mesa 2 (Jorge Rengifo)', 3152223344, NULL, NULL, NULL, NULL, 'acordar', 112000, 0, 112000, 'preparando', NULL, '127.0.0.1', '2026-11-24 19:52:44', '2026-11-24 19:52:44'),
(11, 3, 4, 'SCC-004', 'recoger', NULL, 'Claudia Murillo', 3137778899, NULL, NULL, NULL, '14:30', 'acordar', 83000, 0, 83000, 'recibido', NULL, '127.0.0.1', '2026-11-24 20:22:44', '2026-11-24 20:22:44');
/*!40000 ALTER TABLE `pedidos` ENABLE KEYS */;
UNLOCK TABLES;

-- --------------------------------------------------------
-- Table structure for `producto_grupos`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `producto_grupos`;
CREATE TABLE `producto_grupos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `producto_id` bigint unsigned NOT NULL,
  `grupo_id` bigint unsigned NOT NULL,
  `orden` int unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_producto_grupo` (`producto_id`,`grupo_id`),
  KEY `fk_pg_grupo` (`grupo_id`),
  CONSTRAINT `fk_pg_grupo` FOREIGN KEY (`grupo_id`) REFERENCES `grupos_modificadores` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pg_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `producto_grupos` (23 rows)
LOCK TABLES `producto_grupos` WRITE;
/*!40000 ALTER TABLE `producto_grupos` DISABLE KEYS */;
INSERT INTO `producto_grupos` VALUES
(1, 2, 1, 0),
(2, 2, 2, 0),
(3, 3, 1, 0),
(4, 3, 2, 0),
(5, 4, 3, 0),
(6, 5, 3, 0),
(7, 11, 4, 0),
(8, 11, 5, 0),
(9, 12, 4, 0),
(10, 12, 5, 0),
(11, 13, 4, 0),
(12, 13, 5, 0),
(13, 14, 4, 0),
(14, 14, 5, 0),
(15, 15, 6, 0),
(16, 16, 6, 0),
(17, 21, 7, 0),
(18, 22, 7, 0),
(19, 22, 8, 0),
(20, 23, 7, 0),
(21, 24, 7, 0),
(22, 29, 9, 0),
(23, 30, 9, 0);
/*!40000 ALTER TABLE `producto_grupos` ENABLE KEYS */;
UNLOCK TABLES;

-- --------------------------------------------------------
-- Table structure for `productos`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `productos`;
CREATE TABLE `productos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `restaurante_id` bigint unsigned NOT NULL,
  `categoria_id` bigint unsigned NOT NULL,
  `nombre` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `precio` int unsigned NOT NULL,
  `imagen_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `disponible` tinyint(1) NOT NULL DEFAULT '1',
  `destacado` tinyint(1) NOT NULL DEFAULT '0',
  `orden` int unsigned NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_productos_restaurante_cat` (`restaurante_id`,`categoria_id`,`orden`),
  KEY `fk_productos_categoria` (`categoria_id`),
  CONSTRAINT `fk_productos_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_productos_restaurante` FOREIGN KEY (`restaurante_id`) REFERENCES `restaurantes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `productos` (32 rows)
LOCK TABLES `productos` WRITE;
/*!40000 ALTER TABLE `productos` DISABLE KEYS */;
INSERT INTO `productos` VALUES
(1, 1, 1, 'Café Espresso Doble', 'Extracción doble de café arábica de origen Huila con notas achocolatadas y crema dorada densa.', 5500, 'uploads/cafe_macondo/espresso_doble.jpg', 1, 1, 1, '2026-11-24 14:37:44', '2026-11-24 15:21:12', NULL),
(2, 1, 1, 'Cappuccino Italiano', 'Espresso de especialidad combinado con leche texturizada sedosa y fina capa de microespuma.', 8500, 'uploads/cafe_macondo/cappuccino_italiano.jpg', 1, 1, 2, '2026-11-24 14:37:44', '2026-11-24 15:21:12', NULL),
(3, 1, 1, 'Latte Vainilla Bourbon', 'Café latte con infusión de sirope artesanal de vainilla natural y un toque sutil de canela.', 9500, 'uploads/cafe_macondo/latte_vainilla.jpg', 1, 0, 3, '2026-11-24 14:37:44', '2026-11-24 15:21:12', NULL),
(4, 1, 2, 'Tostada de Aguacate & Huevo Poché', 'Pan campesino de masa madre tostado, guacamole rústico, tomates cherry confitados y huevo poché con semillas de chía.', 16500, 'uploads/cafe_macondo/tostada_aguacate.jpg', 1, 1, 1, '2026-11-24 14:37:44', '2026-11-24 15:21:12', NULL),
(5, 1, 2, 'Tostada Francesa Brioche con Frutos Rojos', 'Pan brioche dorado a la mantequilla avellanada, reducción de frutos rojos silvestres, sirope de arce y azúcar glas.', 19000, 'uploads/cafe_macondo/tostada_francesa.jpg', 1, 1, 2, '2026-11-24 14:37:44', '2026-11-24 15:21:12', NULL),
(6, 1, 2, 'Sandwich Gourmet Jamón Serrano & Brie', 'Pan ciabatta horneado en casa, láminas de jamón serrano reserva, queso brie fundido, rúcula fresca y mermelada de higos.', 22000, 'uploads/cafe_macondo/sandwich_brie.jpg', 1, 0, 3, '2026-11-24 14:37:44', '2026-11-24 15:21:12', NULL),
(7, 1, 3, 'Croissant de Almendras Horneado', 'Hojaldre 100% mantequilla relleno con crema frangipane y cubierto con almendras laminadas tostadas.', 10000, 'uploads/cafe_macondo/croissant_almendras.jpg', 1, 1, 1, '2026-11-24 14:37:44', '2026-11-24 15:21:12', NULL),
(8, 1, 3, 'Tarta de Zanahoria & Nuez con Frosting', 'Bizcocho especiado con canela y nueces crujientes, cubierto con suave frosting de queso crema Philadelphia.', 12000, 'uploads/cafe_macondo/tarta_zanahoria.jpg', 1, 0, 2, '2026-11-24 14:37:44', '2026-11-24 15:21:12', NULL),
(9, 1, 4, 'Cold Brew Artesanal con Cítricos', 'Extracción en frío durante 18 horas servido sobre rocas de hielo y una fina rodaja de naranja deshidratada.', 10500, 'uploads/cafe_macondo/cold_brew.jpg', 1, 0, 1, '2026-11-24 14:37:44', '2026-11-24 15:21:12', NULL),
(10, 1, 4, 'Chai Latte Especiado Frío', 'Té negro con cardamomo, canela, clavo y jengibre mezclado con leche fría y canela en polvo.', 11000, 'uploads/cafe_macondo/chai_latte.jpg', 1, 0, 2, '2026-11-24 14:37:44', '2026-11-24 15:21:12', NULL),
(11, 2, 5, 'Pizza Margarita Clásica Napolitana', 'Salsa pomodoro San Marzano, mozzarella fior di latte fundida, albahaca fresca del huerto y aceite de oliva virgen extra.', 34000, 'uploads/pizzeria_di_roma/pizza_margarita.jpg', 1, 1, 1, '2026-11-24 14:37:44', '2026-11-24 15:21:12', NULL),
(12, 2, 5, 'Pizza Pepperoni Rústica Artesanal', 'Base pomodoro, doble capa de mozzarella hilada, abundante pepperoni italiano artesanal crocante y orégano seco.', 39000, 'uploads/pizzeria_di_roma/pizza_pepperoni.jpg', 1, 1, 2, '2026-11-24 14:37:44', '2026-11-24 15:21:12', NULL),
(13, 2, 5, 'Pizza Cuatro Quesos DOP Italiana', 'Exquisita combinación de mozzarella, gorgonzola cremoso, fontina fundida y parmigiano reggiano madurado.', 42000, 'uploads/pizzeria_di_roma/pizza_cuatro_quesos.jpg', 1, 1, 3, '2026-11-24 14:37:44', '2026-11-24 15:21:12', NULL),
(14, 2, 5, 'Pizza Prosciutto & Rúcula Fresca', 'Salsa pomodoro, mozzarella fresca, finas lonchas de jamón serrano prosciutto crudo, rúcula fresca y lascas de parmesano.', 46000, 'uploads/pizzeria_di_roma/pizza_prosciutto.jpg', 1, 0, 4, '2026-11-24 14:37:44', '2026-11-24 15:21:12', NULL),
(15, 2, 6, 'Lasagna Tradicional Boloñesa al Horno', 'Capas de pasta artesanal al huevo con ragú boloñesa de carne seleccionada a cocción lenta, bechamel cremosa y gratín de queso.', 32000, 'uploads/pizzeria_di_roma/lasagna_bolonesa.jpg', 1, 1, 1, '2026-11-24 14:37:44', '2026-11-24 15:21:12', NULL),
(16, 2, 6, 'Fettuccine Alfredo con Pollo & Champiñones', 'Pasta fresca bañada en cremosa salsa Alfredo a base de mantequilla y parmesano, pechuga a la plancha y champiñones salteados.', 30000, 'uploads/pizzeria_di_roma/fettuccine_alfredo.jpg', 1, 0, 2, '2026-11-24 14:37:44', '2026-11-24 15:21:12', NULL),
(17, 2, 7, 'Tiramisú Tradicional de Café & Mascarpone', 'Savoiardi empapados en café espresso fuerte y licor de café, capas de suave crema mascarpone y lluvia de cacao amargo.', 16000, 'uploads/pizzeria_di_roma/tiramisu_italiano.jpg', 1, 1, 1, '2026-11-24 14:37:44', '2026-11-24 15:21:12', NULL),
(18, 2, 7, 'Panna Cotta Artesanal con Frutos Rojos', 'Postre cremoso de crema de leche aromatizada con vainilla natural servido con coulis casero de moras y arándanos.', 14000, 'uploads/pizzeria_di_roma/panna_cotta.jpg', 1, 0, 2, '2026-11-24 14:37:44', '2026-11-24 15:21:12', NULL),
(19, 2, 8, 'Limonada de Coco Cremosita', 'Zumo de limón fresco batido con crema de coco natural y hielo frappé refrescante.', 11000, 'uploads/pizzeria_di_roma/limonada_coco.jpg', 1, 0, 1, '2026-11-24 14:37:44', '2026-11-24 15:21:12', NULL),
(20, 2, 8, 'Cerveza Moretti Italiana 330ml', 'Auténtica cerveza rubia italiana con notas maltosas suaves y final refrescante.', 15000, 'uploads/pizzeria_di_roma/cerveza_moretti.jpg', 1, 0, 2, '2026-11-24 14:37:44', '2026-11-24 15:21:12', NULL),
(21, 3, 9, 'Bandeja Paisa Tradicional Completa', 'El orgullo antioqueño: chicharrón carnudo de 6 patas, fríjoles con pezuña, carne molida sazonada, chorizo santarrosano, huevo frito criollo, tajada de maduro, porción de arroz, aguacate hass y arepa blanca.', 38000, 'uploads/sabor_criollo/bandeja_paisa.jpg', 1, 1, 1, '2026-11-24 14:37:44', '2026-11-24 15:21:12', NULL),
(22, 3, 9, 'Churrasco a la Parrilla 350g con Chimichurri', 'Corte tierno de res a la brasa de leña, acompañado de papa salada cocida, tajadas de plátano maduro con queso y ensalada fresca.', 42000, 'uploads/sabor_criollo/churrasco_parrilla.jpg', 1, 1, 2, '2026-11-24 14:37:44', '2026-11-24 15:21:12', NULL),
(23, 3, 10, 'Ajiaco Santafereño con Pollo Desmechado', 'Sopa tradicional bogotana con tres tipos de papa (criolla, pastusa y sabanera), pechuga desmechada, mazorca tierna, servido con alcaparras, crema de leche y aguacate.', 32000, 'uploads/sabor_criollo/ajiaco_santafereno.jpg', 1, 1, 1, '2026-11-24 14:37:44', '2026-11-24 15:21:12', NULL),
(24, 3, 10, 'Sancocho Trifásico Tradicional de Campo', 'Caldo espeso con carne de res, pollo de campo y cerdo, yuca harinosa, plátano verde, mazorca y cilantro fresco. Servido con arroz y aguacate.', 34000, 'uploads/sabor_criollo/sancocho_trifasico.jpg', 1, 1, 2, '2026-11-24 14:37:44', '2026-11-24 15:21:12', NULL),
(25, 3, 10, 'Cazuela de Mariscos del Pacífico Colombiano', 'Frutos del mar seleccionados (camarones, calamares, mejillones y pescado) guisados en leche de coco y especias criollas. Servido con arroz con coco y patacones.', 44000, 'uploads/sabor_criollo/cazuela_mariscos.jpg', 1, 0, 3, '2026-11-24 14:37:44', '2026-11-24 15:21:12', NULL),
(26, 3, 11, 'Picada Criolla Mixta para 2 Personas', 'Generosa picada con trozos de chicharrón crocante, costilla de cerdo ahumada, chorizo criollo, morcilla campesina, papas criollas doradas, plátano maduro y arepitas con ají.', 48000, 'uploads/sabor_criollo/picada_criolla.jpg', 1, 1, 1, '2026-11-24 14:37:44', '2026-11-24 15:21:12', NULL),
(27, 3, 11, 'Canasta de Empanaditas Vallunas (5 unidades)', 'Empanadas doradas y crocantes de masa de maíz amarillo rellenas de papa y carne criolla, acompañadas de ají casero picantico y limón.', 15000, 'uploads/sabor_criollo/empanadas_vallunas.jpg', 1, 0, 2, '2026-11-24 14:37:44', '2026-11-24 15:21:12', NULL),
(28, 3, 11, 'Arepa de Chócolo con Quesito Campesino', 'Arepa dulce de maíz tierno asada en parrilla con abundante mantequilla y generosa capa de quesito campesino antioqueño.', 12000, 'uploads/sabor_criollo/arepa_chocolo.jpg', 1, 0, 3, '2026-11-24 14:37:44', '2026-11-24 15:21:12', NULL),
(29, 3, 12, 'Jugo Natural de Lulo Colombiano', 'Pulpa 100% natural de lulo fresco del Valle, servido bien frío en agua o en leche cremosa según tu preferencia.', 8500, 'uploads/sabor_criollo/jugo_lulo.jpg', 1, 0, 1, '2026-11-24 14:37:44', '2026-11-24 15:21:12', NULL),
(30, 3, 12, 'Jugo Natural de Maracuyá Silvestre', 'Exquisita acidez y frescura de la maracuyá colombiana, batido al instante con hielo frappé.', 8500, 'uploads/sabor_criollo/jugo_maracuya.jpg', 1, 0, 2, '2026-11-24 14:37:44', '2026-11-24 15:21:12', NULL),
(31, 3, 12, 'Limonada de Panela con Jengibre y Menta', 'Bebida campesina de aguapanela fría con jugo de limón mandarino fresco, infusión ligera de jengibre y hojas de menta.', 7000, 'uploads/sabor_criollo/limonada_panela.jpg', 1, 0, 3, '2026-11-24 14:37:44', '2026-11-24 15:21:12', NULL),
(32, 3, 13, 'Brevas en Almíbar con Arequipe & Queso', 'Postre de tradición colonial: brevas tiernas en almíbar de panela y canela servidas con arequipe de leche y tajada de queso fresco.', 10000, 'uploads/sabor_criollo/brevas_arequipe.jpg', 1, 0, 1, '2026-11-24 14:37:44', '2026-11-24 15:21:12', NULL);
/*!40000 ALTER TABLE `productos` ENABLE KEYS */;
UNLOCK TABLES;

-- --------------------------------------------------------
-- Table structure for `restaurantes`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `restaurantes`;
CREATE TABLE `restaurantes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `banner_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color_primario` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#E85D04',
  `color_secundario` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#FFB703',
  `color_fondo` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#FFF7EA',
  `color_texto` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#1E1B18',
  `fuente` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Inter',
  `whatsapp` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `moneda` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'COP',
  `recibiendo_pedidos` tinyint(1) NOT NULL DEFAULT '1',
  `acepta_mesa` tinyint(1) NOT NULL DEFAULT '1',
  `acepta_domicilio` tinyint(1) NOT NULL DEFAULT '1',
  `acepta_recoger` tinyint(1) NOT NULL DEFAULT '1',
  `costo_envio` int unsigned NOT NULL DEFAULT '0',
  `pedido_minimo` int unsigned NOT NULL DEFAULT '0',
  `info_pago_texto` text COLLATE utf8mb4_unicode_ci,
  `precio` int unsigned NOT NULL DEFAULT '49000',
  `plan` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pro',
  `limite_mesas` int unsigned DEFAULT NULL,
  `limite_facturas_mes` int unsigned NOT NULL DEFAULT '500',
  `facturacion_activa` tinyint(1) NOT NULL DEFAULT '0',
  `notas_internas` text COLLATE utf8mb4_unicode_ci,
  `mostrar_marca` tinyint(1) NOT NULL DEFAULT '1',
  `ciclo` enum('mensual','anual') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'mensual',
  `fecha_vencimiento` date DEFAULT NULL,
  `dias_gracia` tinyint unsigned NOT NULL DEFAULT '3',
  `dia_corte` tinyint unsigned DEFAULT NULL,
  `en_prueba` tinyint(1) NOT NULL DEFAULT '0',
  `fecha_fin_prueba` date DEFAULT NULL,
  `estado_manual` enum('suspendido','cancelado') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ultimo_recordatorio_at` datetime DEFAULT NULL,
  `siguiente_consecutivo` int unsigned NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_restaurantes_slug` (`slug`),
  KEY `idx_restaurantes_vencimiento` (`fecha_vencimiento`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `restaurantes` (3 rows)
LOCK TABLES `restaurantes` WRITE;
/*!40000 ALTER TABLE `restaurantes` DISABLE KEYS */;
INSERT INTO `restaurantes` VALUES
(1, 'Café Macondo', 'cafe-macondo', 'Cafetería de especialidad, brunch de autor y repostería artesanal. Café 100% colombiano de origen tostado en casa.', 'uploads/cafe_macondo/logo_cafe_macondo.jpg', 'uploads/cafe_macondo/banner_cafe_macondo.jpg', '#B45309', '#F59E0B', '#FFFBEB', '#1E1B18', 'Inter', 573248670360, 'COP', 1, 1, 1, 1, 4000, 15000, NULL, 49000, 'pro', 20, 500, 0, NULL, 1, 'mensual', '2026-12-24', 3, NULL, 0, NULL, NULL, '2026-11-24 15:37:35', 1, '2026-11-24 14:37:44', '2026-11-24 15:37:35', NULL),
(2, 'Pizzería Di Roma', 'pizzeria-di-roma', 'Auténtica trattoria y pizzería napolitana artesanal. Masa madre con 48h de fermentación lenta, mozzarella di bufala y horno a la piedra.', 'uploads/pizzeria_di_roma/logo_pizzeria_di_roma.jpg', 'uploads/pizzeria_di_roma/banner_pizzeria_di_roma.jpg', '#DC2626', '#EF4444', '#FEF2F2', '#18181B', 'Inter', 573248670360, 'COP', 1, 1, 1, 1, 5000, 25000, NULL, 49000, 'pro', 25, 500, 0, NULL, 1, 'mensual', '2026-12-20', 3, NULL, 0, NULL, NULL, '2026-11-24 15:37:35', 1, '2026-11-24 14:37:44', '2026-11-24 15:37:35', NULL),
(3, 'Sabor Criollo Colombiano', 'sabor-criollo', 'Auténtica sazón tradicional colombiana. Bandeja paisa completa, ajiaco santafereño, sancocho de leña, picadas mixtas y jugos naturales.', 'uploads/sabor_criollo/logo_sabor_criollo.jpg', 'uploads/sabor_criollo/banner_sabor_criollo.jpg', '#EA580C', '#F97316', '#FFF7ED', '#1C1917', 'Inter', 573248670360, 'COP', 1, 1, 1, 1, 6000, 20000, NULL, 49000, 'pro', 30, 500, 0, NULL, 1, 'mensual', '2026-12-22', 3, NULL, 0, NULL, NULL, '2026-11-24 15:37:35', 1, '2026-11-24 14:37:44', '2026-11-24 15:37:35', NULL);
/*!40000 ALTER TABLE `restaurantes` ENABLE KEYS */;
UNLOCK TABLES;

-- --------------------------------------------------------
-- Table structure for `usuarios`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `restaurante_id` bigint unsigned DEFAULT NULL,
  `nombre` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rol` enum('super_admin','admin_negocio') COLLATE utf8mb4_unicode_ci NOT NULL,
  `debe_cambiar_password` tinyint(1) NOT NULL DEFAULT '0',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `ultimo_login_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_usuarios_restaurante` (`restaurante_id`),
  CONSTRAINT `fk_usuarios_restaurante` FOREIGN KEY (`restaurante_id`) REFERENCES `restaurantes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `usuarios` (4 rows)
LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES
(1, NULL, 'Super Admin Mesaly', 'admin@mesaly.co', '$argon2id$v=19$m=65536,t=4,p=1$RTRvNzJ3M25xRWNxeU16TA$gvDKzfxk9rmhQKcoTQunTB/j93gCua/kgiNiuVRf1/w', 'super_admin', 0, 1, '2026-11-24 15:37:35', '2026-11-24 14:37:44', '2026-11-24 15:37:35'),
(2, 1, 'Admin Café Macondo', 'admin@cafemacondo.co', '$argon2id$v=19$m=65536,t=4,p=1$anZNSXZ4eHhPcUJMWi8wVQ$1cROCxfqhN41mq30jTm2QLQjuw0kvJc3BCwdhoH/E0k', 'admin_negocio', 0, 1, '2026-11-24 20:54:02', '2026-11-24 14:37:44', '2026-11-24 14:54:02'),
(3, 2, 'Admin Pizzería Di Roma', 'admin@pizzeriadiroma.co', '$argon2id$v=19$m=65536,t=4,p=1$aU8vdFFKTFFxaHJTNkFwNw$qqFNb4tvUBKb9AYXVioom4tOwtS8UbCinnb8wUKYr2c', 'admin_negocio', 0, 1, '2026-11-24 15:06:06', '2026-11-24 14:37:44', '2026-11-24 15:07:05'),
(4, 3, 'Admin Sabor Criollo', 'admin@saborcriollo.co', '$argon2id$v=19$m=65536,t=4,p=1$eVFyTnFlbmFqTUxPYzhuVw$99w9cQ7Q9uRbUw8KmAckQCMCZUnqSpUosjX2d6TiTfs', 'admin_negocio', 0, 1, '2026-11-24 20:54:03', '2026-11-24 14:37:44', '2026-11-24 14:54:03');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;

SET FOREIGN_KEY_CHECKS = 1;
