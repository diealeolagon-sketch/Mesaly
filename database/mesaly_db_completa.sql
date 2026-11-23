-- Mesaly Complete Database Dump
-- Generated: 2026-11-23 20:53:05
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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `auditoria` (12 rows)
LOCK TABLES `auditoria` WRITE;
/*!40000 ALTER TABLE `auditoria` DISABLE KEYS */;
INSERT INTO `auditoria` VALUES
(1, 2, 1, 'actualizar_configuracion', 'restaurantes', 1, '{\"id\": 1, \"plan\": \"basico\", \"slug\": \"cafe-macondo\", \"ciclo\": \"mensual\", \"fuente\": \"Inter\", \"moneda\": \"COP\", \"nombre\": \"Café Macondo\", \"precio\": 79000, \"whatsapp\": \"573001234567\", \"dia_corte\": 18, \"en_prueba\": 0, \"logo_path\": \"uploads/cafe_macondo/logo_cafe_macondo.jpg\", \"created_at\": \"2026-11-18 13:36:06\", \"deleted_at\": null, \"updated_at\": \"2026-11-19 10:53:53\", \"acepta_mesa\": 1, \"banner_path\": \"uploads/cafe_macondo/banner_cafe_macondo.jpg\", \"color_fondo\": \"#FFFBEB\", \"color_texto\": \"#1E1B18\", \"costo_envio\": 0, \"descripcion\": \"Cafetería de especialidad, repostería artesanal y desayunos.\", \"dias_gracia\": 3, \"limite_mesas\": 6, \"estado_manual\": null, \"mostrar_marca\": 1, \"pedido_minimo\": 0, \"acepta_recoger\": 1, \"color_primario\": \"#D97706\", \"notas_internas\": \"Plan Básico estándar: 6 mesas a $79.000 COP/mes\", \"info_pago_texto\": null, \"acepta_domicilio\": 1, \"color_secundario\": \"#B45309\", \"fecha_fin_prueba\": null, \"fecha_vencimiento\": \"2026-12-18\", \"facturacion_activa\": 1, \"recibiendo_pedidos\": 1, \"limite_facturas_mes\": 600, \"siguiente_consecutivo\": 1, \"ultimo_recordatorio_at\": \"2026-11-19 10:21:19\"}', '{\"fuente\": \"Inter\", \"nombre\": \"Café Macondo\", \"whatsapp\": \"573001234567\", \"logo_path\": \"uploads/cafe_macondo/logo_cafe_macondo.jpg\", \"acepta_mesa\": 1, \"banner_path\": \"uploads/cafe_macondo/banner_cafe_macondo.jpg\", \"color_fondo\": \"#FFFBEB\", \"color_texto\": \"#1E1B18\", \"costo_envio\": 0, \"descripcion\": \"Cafetería de especialidad, repostería artesanal y desayunos.\", \"pedido_minimo\": 0, \"acepta_recoger\": 0, \"color_primario\": \"#D97706\", \"info_pago_texto\": \"\", \"acepta_domicilio\": 1, \"color_secundario\": \"#B45309\", \"recibiendo_pedidos\": 1}', '::1', '2026-11-19 11:07:02'),
(2, 2, 1, 'actualizar_configuracion', 'restaurantes', 1, '{\"id\": 1, \"plan\": \"basico\", \"slug\": \"cafe-macondo\", \"ciclo\": \"mensual\", \"fuente\": \"Inter\", \"moneda\": \"COP\", \"nombre\": \"Café Macondo\", \"precio\": 79000, \"whatsapp\": \"573001234567\", \"dia_corte\": 18, \"en_prueba\": 0, \"logo_path\": \"uploads/cafe_macondo/logo_cafe_macondo.jpg\", \"created_at\": \"2026-11-18 13:36:06\", \"deleted_at\": null, \"updated_at\": \"2026-11-19 11:07:01\", \"acepta_mesa\": 1, \"banner_path\": \"uploads/cafe_macondo/banner_cafe_macondo.jpg\", \"color_fondo\": \"#FFFBEB\", \"color_texto\": \"#1E1B18\", \"costo_envio\": 0, \"descripcion\": \"Cafetería de especialidad, repostería artesanal y desayunos.\", \"dias_gracia\": 3, \"limite_mesas\": 6, \"estado_manual\": null, \"mostrar_marca\": 1, \"pedido_minimo\": 0, \"acepta_recoger\": 0, \"color_primario\": \"#D97706\", \"notas_internas\": \"Plan Básico estándar: 6 mesas a $79.000 COP/mes\", \"info_pago_texto\": \"\", \"acepta_domicilio\": 1, \"color_secundario\": \"#B45309\", \"fecha_fin_prueba\": null, \"fecha_vencimiento\": \"2026-12-18\", \"facturacion_activa\": 1, \"recibiendo_pedidos\": 1, \"limite_facturas_mes\": 600, \"siguiente_consecutivo\": 1, \"ultimo_recordatorio_at\": \"2026-11-19 10:21:19\"}', '{\"fuente\": \"Outfit\", \"nombre\": \"Café Macondo\", \"whatsapp\": \"573001234567\", \"logo_path\": \"uploads/cafe_macondo/logo_cafe_macondo.jpg\", \"acepta_mesa\": 1, \"banner_path\": \"uploads/cafe_macondo/banner_cafe_macondo.jpg\", \"color_fondo\": \"#FFFBEB\", \"color_texto\": \"#1E1B18\", \"costo_envio\": 0, \"descripcion\": \"Cafetería de especialidad, repostería artesanal y desayunos.\", \"pedido_minimo\": 0, \"acepta_recoger\": 0, \"color_primario\": \"#D97706\", \"info_pago_texto\": \"\", \"acepta_domicilio\": 1, \"color_secundario\": \"#B45309\", \"recibiendo_pedidos\": 1}', '::1', '2026-11-19 11:07:29'),
(3, 2, 1, 'actualizar_configuracion', 'restaurantes', 1, '{\"id\": 1, \"plan\": \"basico\", \"slug\": \"cafe-macondo\", \"ciclo\": \"mensual\", \"fuente\": \"Outfit\", \"moneda\": \"COP\", \"nombre\": \"Café Macondo\", \"precio\": 79000, \"whatsapp\": \"573001234567\", \"dia_corte\": 18, \"en_prueba\": 0, \"logo_path\": \"uploads/cafe_macondo/logo_cafe_macondo.jpg\", \"created_at\": \"2026-11-18 13:36:06\", \"deleted_at\": null, \"updated_at\": \"2026-11-19 11:07:29\", \"acepta_mesa\": 1, \"banner_path\": \"uploads/cafe_macondo/banner_cafe_macondo.jpg\", \"color_fondo\": \"#FFFBEB\", \"color_texto\": \"#1E1B18\", \"costo_envio\": 0, \"descripcion\": \"Cafetería de especialidad, repostería artesanal y desayunos.\", \"dias_gracia\": 3, \"limite_mesas\": 6, \"estado_manual\": null, \"mostrar_marca\": 1, \"pedido_minimo\": 0, \"acepta_recoger\": 0, \"color_primario\": \"#D97706\", \"notas_internas\": \"Plan Básico estándar: 6 mesas a $79.000 COP/mes\", \"info_pago_texto\": \"\", \"acepta_domicilio\": 1, \"color_secundario\": \"#B45309\", \"fecha_fin_prueba\": null, \"fecha_vencimiento\": \"2026-12-18\", \"facturacion_activa\": 1, \"recibiendo_pedidos\": 1, \"limite_facturas_mes\": 600, \"siguiente_consecutivo\": 1, \"ultimo_recordatorio_at\": \"2026-11-19 10:21:19\"}', '{\"fuente\": \"Cormorant Garamond\", \"nombre\": \"Café Macondo\", \"whatsapp\": \"573001234567\", \"logo_path\": \"uploads/cafe_macondo/logo_cafe_macondo.jpg\", \"acepta_mesa\": 1, \"banner_path\": \"uploads/cafe_macondo/banner_cafe_macondo.jpg\", \"color_fondo\": \"#FFFBEB\", \"color_texto\": \"#1E1B18\", \"costo_envio\": 0, \"descripcion\": \"Cafetería de especialidad, repostería artesanal y desayunos.\", \"pedido_minimo\": 0, \"acepta_recoger\": 0, \"color_primario\": \"#D97706\", \"info_pago_texto\": \"\", \"acepta_domicilio\": 1, \"color_secundario\": \"#B45309\", \"recibiendo_pedidos\": 1}', '::1', '2026-11-19 11:07:40'),
(4, 2, 1, 'actualizar_configuracion', 'restaurantes', 1, '{\"id\": 1, \"plan\": \"basico\", \"slug\": \"cafe-macondo\", \"ciclo\": \"mensual\", \"fuente\": \"Cormorant Garamond\", \"moneda\": \"COP\", \"nombre\": \"Café Macondo\", \"precio\": 79000, \"whatsapp\": \"573001234567\", \"dia_corte\": 18, \"en_prueba\": 0, \"logo_path\": \"uploads/cafe_macondo/logo_cafe_macondo.jpg\", \"created_at\": \"2026-11-18 13:36:06\", \"deleted_at\": null, \"updated_at\": \"2026-11-19 11:07:40\", \"acepta_mesa\": 1, \"banner_path\": \"uploads/cafe_macondo/banner_cafe_macondo.jpg\", \"color_fondo\": \"#FFFBEB\", \"color_texto\": \"#1E1B18\", \"costo_envio\": 0, \"descripcion\": \"Cafetería de especialidad, repostería artesanal y desayunos.\", \"dias_gracia\": 3, \"limite_mesas\": 6, \"estado_manual\": null, \"mostrar_marca\": 1, \"pedido_minimo\": 0, \"acepta_recoger\": 0, \"color_primario\": \"#D97706\", \"notas_internas\": \"Plan Básico estándar: 6 mesas a $79.000 COP/mes\", \"info_pago_texto\": \"\", \"acepta_domicilio\": 1, \"color_secundario\": \"#B45309\", \"fecha_fin_prueba\": null, \"fecha_vencimiento\": \"2026-12-18\", \"facturacion_activa\": 1, \"recibiendo_pedidos\": 1, \"limite_facturas_mes\": 600, \"siguiente_consecutivo\": 1, \"ultimo_recordatorio_at\": \"2026-11-19 10:21:19\"}', '{\"fuente\": \"System\", \"nombre\": \"Café Macondo\", \"whatsapp\": \"573001234567\", \"logo_path\": \"uploads/cafe_macondo/logo_cafe_macondo.jpg\", \"acepta_mesa\": 1, \"banner_path\": \"uploads/cafe_macondo/banner_cafe_macondo.jpg\", \"color_fondo\": \"#FFFBEB\", \"color_texto\": \"#1E1B18\", \"costo_envio\": 0, \"descripcion\": \"Cafetería de especialidad, repostería artesanal y desayunos.\", \"pedido_minimo\": 0, \"acepta_recoger\": 0, \"color_primario\": \"#D97706\", \"info_pago_texto\": \"\", \"acepta_domicilio\": 1, \"color_secundario\": \"#B45309\", \"recibiendo_pedidos\": 1}', '::1', '2026-11-19 11:08:03'),
(5, 2, 1, 'actualizar_configuracion', 'restaurantes', 1, '{\"id\": 1, \"plan\": \"basico\", \"slug\": \"cafe-macondo\", \"ciclo\": \"mensual\", \"fuente\": \"System\", \"moneda\": \"COP\", \"nombre\": \"Café Macondo\", \"precio\": 79000, \"whatsapp\": \"573001234567\", \"dia_corte\": 18, \"en_prueba\": 0, \"logo_path\": \"uploads/cafe_macondo/logo_cafe_macondo.jpg\", \"created_at\": \"2026-11-18 13:36:06\", \"deleted_at\": null, \"updated_at\": \"2026-11-19 11:08:03\", \"acepta_mesa\": 1, \"banner_path\": \"uploads/cafe_macondo/banner_cafe_macondo.jpg\", \"color_fondo\": \"#FFFBEB\", \"color_texto\": \"#1E1B18\", \"costo_envio\": 0, \"descripcion\": \"Cafetería de especialidad, repostería artesanal y desayunos.\", \"dias_gracia\": 3, \"limite_mesas\": 6, \"estado_manual\": null, \"mostrar_marca\": 1, \"pedido_minimo\": 0, \"acepta_recoger\": 0, \"color_primario\": \"#D97706\", \"notas_internas\": \"Plan Básico estándar: 6 mesas a $79.000 COP/mes\", \"info_pago_texto\": \"\", \"acepta_domicilio\": 1, \"color_secundario\": \"#B45309\", \"fecha_fin_prueba\": null, \"fecha_vencimiento\": \"2026-12-18\", \"facturacion_activa\": 1, \"recibiendo_pedidos\": 1, \"limite_facturas_mes\": 600, \"siguiente_consecutivo\": 1, \"ultimo_recordatorio_at\": \"2026-11-19 10:21:19\"}', '{\"fuente\": \"System\", \"nombre\": \"Café Macondo\", \"whatsapp\": \"573001234567\", \"logo_path\": \"uploads/cafe_macondo/logo_cafe_macondo.jpg\", \"acepta_mesa\": 1, \"banner_path\": \"uploads/cafe_macondo/banner_cafe_macondo.jpg\", \"color_fondo\": \"#FFFBEB\", \"color_texto\": \"#1E1B18\", \"costo_envio\": 0, \"descripcion\": \"Cafetería de especialidad, repostería artesanal y desayunos.\", \"pedido_minimo\": 0, \"acepta_recoger\": 0, \"color_primario\": \"#D97706\", \"info_pago_texto\": \"\", \"acepta_domicilio\": 1, \"color_secundario\": \"#B45309\", \"recibiendo_pedidos\": 1}', '::1', '2026-11-19 11:08:32'),
(6, 2, 1, 'actualizar_configuracion', 'restaurantes', 1, '{\"id\": 1, \"plan\": \"basico\", \"slug\": \"cafe-macondo\", \"ciclo\": \"mensual\", \"fuente\": \"System\", \"moneda\": \"COP\", \"nombre\": \"Café Macondo\", \"precio\": 79000, \"whatsapp\": \"573001234567\", \"dia_corte\": 18, \"en_prueba\": 0, \"logo_path\": \"uploads/cafe_macondo/logo_cafe_macondo.jpg\", \"created_at\": \"2026-11-18 13:36:06\", \"deleted_at\": null, \"updated_at\": \"2026-11-19 11:08:32\", \"acepta_mesa\": 1, \"banner_path\": \"uploads/cafe_macondo/banner_cafe_macondo.jpg\", \"color_fondo\": \"#FFFBEB\", \"color_texto\": \"#1E1B18\", \"costo_envio\": 0, \"descripcion\": \"Cafetería de especialidad, repostería artesanal y desayunos.\", \"dias_gracia\": 3, \"limite_mesas\": 6, \"estado_manual\": null, \"mostrar_marca\": 1, \"pedido_minimo\": 0, \"acepta_recoger\": 0, \"color_primario\": \"#D97706\", \"notas_internas\": \"Plan Básico estándar: 6 mesas a $79.000 COP/mes\", \"info_pago_texto\": \"\", \"acepta_domicilio\": 1, \"color_secundario\": \"#B45309\", \"fecha_fin_prueba\": null, \"fecha_vencimiento\": \"2026-12-18\", \"facturacion_activa\": 1, \"recibiendo_pedidos\": 1, \"limite_facturas_mes\": 600, \"siguiente_consecutivo\": 1, \"ultimo_recordatorio_at\": \"2026-11-19 10:21:19\"}', '{\"fuente\": \"Plus Jakarta Sans\", \"nombre\": \"Café Macondo\", \"whatsapp\": \"573001234567\", \"logo_path\": \"uploads/cafe_macondo/logo_cafe_macondo.jpg\", \"acepta_mesa\": 1, \"banner_path\": \"uploads/cafe_macondo/banner_cafe_macondo.jpg\", \"color_fondo\": \"#FFFBEB\", \"color_texto\": \"#1E1B18\", \"costo_envio\": 0, \"descripcion\": \"Cafetería de especialidad, repostería artesanal y desayunos.\", \"pedido_minimo\": 0, \"acepta_recoger\": 0, \"color_primario\": \"#D97706\", \"info_pago_texto\": \"\", \"acepta_domicilio\": 1, \"color_secundario\": \"#B45309\", \"recibiendo_pedidos\": 1}', '::1', '2026-11-19 11:10:18'),
(7, 3, 2, 'actualizar_configuracion', 'restaurantes', 2, '{\"id\": 2, \"plan\": \"basico\", \"slug\": \"pizzeria-di-roma\", \"ciclo\": \"mensual\", \"fuente\": \"Inter\", \"moneda\": \"COP\", \"nombre\": \"Pizzería Di Roma\", \"precio\": 89000, \"whatsapp\": \"573009876543\", \"dia_corte\": 18, \"en_prueba\": 0, \"logo_path\": \"uploads/pizzeria_di_roma/logo_pizzeria_di_roma.jpg\", \"created_at\": \"2026-11-18 13:36:06\", \"deleted_at\": null, \"updated_at\": \"2026-11-19 10:53:53\", \"acepta_mesa\": 1, \"banner_path\": \"uploads/pizzeria_di_roma/banner_pizzeria_di_roma.jpg\", \"color_fondo\": \"#FEF2F2\", \"color_texto\": \"#1E1B18\", \"costo_envio\": 0, \"descripcion\": \"Auténtica pizza al horno de leña, pastas frescas y aperitivos italianos.\", \"dias_gracia\": 3, \"limite_mesas\": 8, \"estado_manual\": null, \"mostrar_marca\": 1, \"pedido_minimo\": 0, \"acepta_recoger\": 1, \"color_primario\": \"#DC2626\", \"notas_internas\": \"Plan Básico extendido: 8 mesas (tarifa acordada por +2 mesas a $89.000 COP/mes)\", \"info_pago_texto\": null, \"acepta_domicilio\": 1, \"color_secundario\": \"#B91C1C\", \"fecha_fin_prueba\": null, \"fecha_vencimiento\": \"2026-12-18\", \"facturacion_activa\": 1, \"recibiendo_pedidos\": 1, \"limite_facturas_mes\": 600, \"siguiente_consecutivo\": 1, \"ultimo_recordatorio_at\": \"2026-11-19 10:21:19\"}', '{\"fuente\": \"Inter\", \"nombre\": \"Pizzería Di Roma ciudad de tulua\", \"whatsapp\": \"573009876543\", \"logo_path\": \"uploads/pizzeria_di_roma/logo_pizzeria_di_roma.jpg\", \"acepta_mesa\": 1, \"banner_path\": \"uploads/pizzeria_di_roma/banner_pizzeria_di_roma.jpg\", \"color_fondo\": \"#FEF2F2\", \"color_texto\": \"#1E1B18\", \"costo_envio\": 0, \"descripcion\": \"Auténtica pizza al horno de leña, pastas frescas y aperitivos italianos.\", \"pedido_minimo\": 0, \"acepta_recoger\": 0, \"color_primario\": \"#DC2626\", \"info_pago_texto\": \"\", \"acepta_domicilio\": 1, \"color_secundario\": \"#B91C1C\", \"recibiendo_pedidos\": 1}', '::1', '2026-11-19 11:24:33'),
(8, 3, 2, 'actualizar_configuracion', 'restaurantes', 2, '{\"id\": 2, \"plan\": \"basico\", \"slug\": \"pizzeria-di-roma\", \"ciclo\": \"mensual\", \"fuente\": \"Inter\", \"moneda\": \"COP\", \"nombre\": \"Pizzería Di Roma ciudad de tulua\", \"precio\": 89000, \"whatsapp\": \"573009876543\", \"dia_corte\": 18, \"en_prueba\": 0, \"logo_path\": \"uploads/pizzeria_di_roma/logo_pizzeria_di_roma.jpg\", \"created_at\": \"2026-11-18 13:36:06\", \"deleted_at\": null, \"updated_at\": \"2026-11-19 13:22:31\", \"acepta_mesa\": 1, \"banner_path\": \"uploads/pizzeria_di_roma/banner_pizzeria_di_roma.jpg\", \"color_fondo\": \"#FEF2F2\", \"color_texto\": \"#1E1B18\", \"costo_envio\": 0, \"descripcion\": \"Auténtica pizza al horno de leña, pastas frescas y aperitivos italianos.\", \"dias_gracia\": 3, \"limite_mesas\": 8, \"estado_manual\": null, \"mostrar_marca\": 1, \"pedido_minimo\": 0, \"acepta_recoger\": 0, \"color_primario\": \"#DC2626\", \"notas_internas\": \"Plan Básico extendido: 8 mesas (tarifa acordada por +2 mesas a $89.000 COP/mes)\", \"info_pago_texto\": \"\", \"acepta_domicilio\": 1, \"color_secundario\": \"#B91C1C\", \"fecha_fin_prueba\": null, \"fecha_vencimiento\": \"2026-12-18\", \"facturacion_activa\": 1, \"recibiendo_pedidos\": 1, \"limite_facturas_mes\": 600, \"siguiente_consecutivo\": 1, \"ultimo_recordatorio_at\": \"2026-11-19 13:22:31\"}', '{\"fuente\": \"Inter\", \"nombre\": \"Pizzería Di Roma ciudad de tulua\", \"whatsapp\": \"573009876543\", \"logo_path\": \"uploads/pizzeria_di_roma/logo_pizzeria_di_roma.jpg\", \"acepta_mesa\": 1, \"banner_path\": \"uploads/pizzeria_di_roma/banner_pizzeria_di_roma.jpg\", \"color_fondo\": \"#FEF2F2\", \"color_texto\": \"#1E1B18\", \"costo_envio\": 0, \"descripcion\": \"Auténtica pizza al horno de leña, pastas frescas y aperitivos italianos.\", \"pedido_minimo\": 0, \"acepta_recoger\": 0, \"color_primario\": \"#DC2626\", \"info_pago_texto\": \"\", \"acepta_domicilio\": 1, \"color_secundario\": \"#B91C1C\", \"recibiendo_pedidos\": 1}', '::1', '2026-11-19 21:56:18'),
(9, 3, 2, 'crear_grupo_modificador', 'grupos_modificadores', 1, NULL, '{\"tipo\": \"unica\", \"nombre\": \"Termino de la carne\", \"obligatorio\": 0, \"max_selecciones\": 1, \"min_selecciones\": 0}', '::1', '2026-11-19 22:02:09'),
(10, 3, 2, 'editar_producto', 'productos', 6, '{\"id\": 6, \"orden\": 1, \"nombre\": \"Limonada de Coco\", \"precio\": 10000, \"destacado\": 0, \"created_at\": \"2026-11-18 13:36:06\", \"deleted_at\": null, \"disponible\": 1, \"updated_at\": \"2026-11-19 10:53:53\", \"descripcion\": \"Refrescante limonada frappé con crema de coco natural.\", \"imagen_path\": \"uploads/pizzeria_di_roma/limonada_coco.jpg\", \"categoria_id\": 4, \"restaurante_id\": 2}', '{\"orden\": 1, \"nombre\": \"Limonada de Coco\", \"precio\": 10000, \"destacado\": 0, \"disponible\": 1, \"descripcion\": \"Refrescante limonada frappé con crema de coco natural.\", \"imagen_path\": \"uploads/pizzeria_di_roma/limonada_coco.jpg\", \"categoria_id\": 4}', '::1', '2026-11-19 22:02:52'),
(11, 3, 2, 'actualizar_configuracion', 'restaurantes', 2, '{\"id\": 2, \"plan\": \"basico\", \"slug\": \"pizzeria-di-roma\", \"ciclo\": \"mensual\", \"fuente\": \"Inter\", \"moneda\": \"COP\", \"nombre\": \"Pizzería Di Roma ciudad de tulua\", \"precio\": 89000, \"whatsapp\": \"573009876543\", \"dia_corte\": 18, \"en_prueba\": 0, \"logo_path\": \"uploads/pizzeria_di_roma/logo_pizzeria_di_roma.jpg\", \"created_at\": \"2026-11-18 13:36:06\", \"deleted_at\": null, \"updated_at\": \"2026-11-19 21:56:40\", \"acepta_mesa\": 1, \"banner_path\": \"uploads/pizzeria_di_roma/banner_pizzeria_di_roma.jpg\", \"color_fondo\": \"#FEF2F2\", \"color_texto\": \"#1E1B18\", \"costo_envio\": 0, \"descripcion\": \"Auténtica pizza al horno de leña, pastas frescas y aperitivos italianos.\", \"dias_gracia\": 3, \"limite_mesas\": 8, \"estado_manual\": null, \"mostrar_marca\": 1, \"pedido_minimo\": 0, \"acepta_recoger\": 0, \"color_primario\": \"#DC2626\", \"notas_internas\": \"Plan Básico extendido: 8 mesas (tarifa acordada por +2 mesas a $89.000 COP/mes)\", \"info_pago_texto\": \"\", \"acepta_domicilio\": 1, \"color_secundario\": \"#B91C1C\", \"fecha_fin_prueba\": null, \"fecha_vencimiento\": \"2026-12-18\", \"facturacion_activa\": 1, \"recibiendo_pedidos\": 1, \"limite_facturas_mes\": 600, \"siguiente_consecutivo\": 2, \"ultimo_recordatorio_at\": \"2026-11-19 13:22:31\"}', '{\"fuente\": \"Inter\", \"nombre\": \"Pizzería Di Roma ciudad de tulua\", \"whatsapp\": \"573009876543\", \"logo_path\": \"uploads/pizzeria_di_roma/logo_pizzeria_di_roma.jpg\", \"acepta_mesa\": 1, \"banner_path\": \"uploads/pizzeria_di_roma/banner_pizzeria_di_roma.jpg\", \"color_fondo\": \"#FEF2F2\", \"color_texto\": \"#1E1B18\", \"costo_envio\": 0, \"descripcion\": \"Auténtica pizza al horno de leña, pastas frescas y aperitivos italianos.\", \"pedido_minimo\": 0, \"acepta_recoger\": 0, \"color_primario\": \"#DC2626\", \"info_pago_texto\": \"\", \"acepta_domicilio\": 1, \"color_secundario\": \"#B91C1C\", \"recibiendo_pedidos\": 1}', '::1', '2026-11-20 09:28:59'),
(12, 3, 2, 'actualizar_configuracion', 'restaurantes', 2, '{\"id\": 2, \"plan\": \"basico\", \"slug\": \"pizzeria-di-roma\", \"ciclo\": \"mensual\", \"fuente\": \"Inter\", \"moneda\": \"COP\", \"nombre\": \"Pizzería Di Roma ciudad de tulua\", \"precio\": 89000, \"whatsapp\": \"573158945612\", \"dia_corte\": 18, \"en_prueba\": 0, \"logo_path\": \"uploads/pizzeria_di_roma/logo_pizzeria_di_roma.jpg\", \"created_at\": \"2026-11-18 13:36:06\", \"deleted_at\": null, \"updated_at\": \"2026-11-21 08:42:36\", \"acepta_mesa\": 1, \"banner_path\": \"uploads/pizzeria_di_roma/banner_pizzeria_di_roma.jpg\", \"color_fondo\": \"#FEF2F2\", \"color_texto\": \"#1E1B18\", \"costo_envio\": 4500, \"descripcion\": \"Auténtica pizza al horno de leña, pastas frescas y aperitivos italianos.\", \"dias_gracia\": 3, \"limite_mesas\": 8, \"estado_manual\": null, \"mostrar_marca\": 1, \"pedido_minimo\": 25000, \"acepta_recoger\": 0, \"color_primario\": \"#DC2626\", \"notas_internas\": \"Plan Básico extendido: 8 mesas (tarifa acordada por +2 mesas a $89.000 COP/mes)\", \"info_pago_texto\": \"Transferencias directas por Nequi, Daviplata o Bancolombia a la cuenta 315 894 5612. También recibimos efectivo contra entrega.\", \"acepta_domicilio\": 1, \"color_secundario\": \"#B91C1C\", \"fecha_fin_prueba\": null, \"fecha_vencimiento\": \"2026-12-18\", \"facturacion_activa\": 1, \"recibiendo_pedidos\": 1, \"limite_facturas_mes\": 600, \"siguiente_consecutivo\": 12, \"ultimo_recordatorio_at\": \"2026-11-19 13:22:31\"}', '{\"fuente\": \"Plus Jakarta Sans\", \"nombre\": \"Pizzería Di Roma ciudad de tulua\", \"whatsapp\": \"573158945612\", \"logo_path\": \"uploads/pizzeria_di_roma/logo_pizzeria_di_roma.jpg\", \"acepta_mesa\": 1, \"banner_path\": \"uploads/pizzeria_di_roma/banner_pizzeria_di_roma.jpg\", \"color_fondo\": \"#FEF2F2\", \"color_texto\": \"#1E1B18\", \"costo_envio\": 4500, \"descripcion\": \"Auténtica pizza al horno de leña, pastas frescas y aperitivos italianos.\", \"pedido_minimo\": 25000, \"acepta_recoger\": 0, \"color_primario\": \"#DC2626\", \"info_pago_texto\": \"\", \"acepta_domicilio\": 1, \"color_secundario\": \"#B91C1C\", \"recibiendo_pedidos\": 1}', '::1', '2026-11-21 08:51:13');
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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `categorias` (10 rows)
LOCK TABLES `categorias` WRITE;
/*!40000 ALTER TABLE `categorias` DISABLE KEYS */;
INSERT INTO `categorias` VALUES
(1, 1, 'Cafetería de Especialidad', 1, 1, '2026-11-18 13:36:06', '2026-11-18 13:36:06', NULL),
(2, 1, 'Repostería Artesanal', 2, 1, '2026-11-18 13:36:06', '2026-11-18 13:36:06', NULL),
(3, 2, 'Pizzas Artesanales', 1, 1, '2026-11-18 13:36:06', '2026-11-18 13:36:06', NULL),
(4, 2, 'Bebidas', 2, 1, '2026-11-18 13:36:06', '2026-11-18 13:36:06', NULL),
(5, 3, 'Burgers Gourmet', 1, 1, '2026-11-19 10:53:53', '2026-11-19 10:53:53', NULL),
(6, 3, 'Cortes & Parrilla', 2, 1, '2026-11-19 10:53:53', '2026-11-19 10:53:53', NULL),
(7, 3, 'Entradas', 3, 1, '2026-11-19 10:53:53', '2026-11-19 10:53:53', NULL),
(8, 3, 'Bebidas', 4, 1, '2026-11-19 10:53:53', '2026-11-19 10:53:53', NULL),
(9, 3, 'Postres', 5, 1, '2026-11-19 10:53:53', '2026-11-19 10:53:53', NULL),
(10, 2, 'Postres & Dolce', 3, 1, '2026-11-20 14:43:50', '2026-11-20 14:43:50', NULL);
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
('whatsapp_soporte', 573001234567, '2026-11-07 17:28:00', '2026-11-07 17:28:00');
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
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `grupos_modificadores` (7 rows)
LOCK TABLES `grupos_modificadores` WRITE;
/*!40000 ALTER TABLE `grupos_modificadores` DISABLE KEYS */;
INSERT INTO `grupos_modificadores` VALUES
(10, 3, 'Término de la Carne', 'unica', 1, 1, 1, '2026-11-20 10:21:56', '2026-11-20 10:21:56', NULL),
(11, 3, 'Adición Opcional', 'multiple', 0, 0, 2, '2026-11-20 10:21:56', '2026-11-20 10:21:56', NULL),
(12, 2, 'Tipo de Masa', 'unica', 1, 1, 1, '2026-11-20 10:21:56', '2026-11-20 10:21:56', NULL),
(13, 2, 'Borde de Queso', 'unica', 0, 0, 1, '2026-11-20 10:21:56', '2026-11-20 10:21:56', NULL),
(14, 1, 'Tipo de Leche', 'unica', 1, 1, 1, '2026-11-20 14:43:50', '2026-11-20 14:43:50', NULL),
(15, 1, 'Endulzante', 'unica', 0, 0, 1, '2026-11-20 14:43:50', '2026-11-20 14:43:50', NULL),
(16, 1, 'Acompañamiento Opcional', 'multiple', 0, 0, 2, '2026-11-20 14:43:50', '2026-11-20 14:43:50', NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=92 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `horarios` (21 rows)
LOCK TABLES `horarios` WRITE;
/*!40000 ALTER TABLE `horarios` DISABLE KEYS */;
INSERT INTO `horarios` VALUES
(64, 1, 0, '08:00:00', '23:59:00', 1, 1, '2026-11-20 09:51:59', '2026-11-20 09:51:59'),
(65, 1, 1, '08:00:00', '23:59:00', 1, 1, '2026-11-20 09:51:59', '2026-11-20 09:51:59'),
(66, 1, 2, '08:00:00', '23:59:00', 1, 1, '2026-11-20 09:51:59', '2026-11-20 09:51:59'),
(67, 1, 3, '08:00:00', '23:59:00', 1, 1, '2026-11-20 09:51:59', '2026-11-20 09:51:59'),
(68, 1, 4, '08:00:00', '23:59:00', 1, 1, '2026-11-20 09:51:59', '2026-11-20 09:51:59'),
(69, 1, 5, '08:00:00', '23:59:00', 1, 1, '2026-11-20 09:51:59', '2026-11-20 09:51:59'),
(70, 1, 6, '08:00:00', '23:59:00', 1, 1, '2026-11-20 09:51:59', '2026-11-20 09:51:59'),
(78, 3, 0, '08:00:00', '23:59:00', 1, 1, '2026-11-20 09:51:59', '2026-11-20 09:51:59'),
(79, 3, 1, '08:00:00', '23:59:00', 1, 1, '2026-11-20 09:51:59', '2026-11-20 09:51:59'),
(80, 3, 2, '08:00:00', '23:59:00', 1, 1, '2026-11-20 09:51:59', '2026-11-20 09:51:59'),
(81, 3, 3, '08:00:00', '23:59:00', 1, 1, '2026-11-20 09:51:59', '2026-11-20 09:51:59'),
(82, 3, 4, '08:00:00', '23:59:00', 1, 1, '2026-11-20 09:51:59', '2026-11-20 09:51:59'),
(83, 3, 5, '08:00:00', '23:59:00', 1, 1, '2026-11-20 09:51:59', '2026-11-20 09:51:59'),
(84, 3, 6, '08:00:00', '23:59:00', 1, 1, '2026-11-20 09:51:59', '2026-11-20 09:51:59'),
(85, 2, 0, '08:00:00', '23:59:00', 1, 1, '2026-11-21 08:51:12', '2026-11-21 08:51:12'),
(86, 2, 1, '08:00:00', '23:59:00', 1, 1, '2026-11-21 08:51:12', '2026-11-21 08:51:12'),
(87, 2, 2, '08:00:00', '23:59:00', 1, 1, '2026-11-21 08:51:12', '2026-11-21 08:51:12'),
(88, 2, 3, '08:00:00', '23:59:00', 1, 1, '2026-11-21 08:51:12', '2026-11-21 08:51:12'),
(89, 2, 4, '08:00:00', '23:59:00', 1, 1, '2026-11-21 08:51:12', '2026-11-21 08:51:12'),
(90, 2, 5, '08:00:00', '23:59:00', 1, 1, '2026-11-21 08:51:12', '2026-11-21 08:51:12'),
(91, 2, 6, '08:00:00', '23:59:00', 1, 1, '2026-11-21 08:51:12', '2026-11-21 08:51:12');
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
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `intentos_login` (61 rows)
LOCK TABLES `intentos_login` WRITE;
/*!40000 ALTER TABLE `intentos_login` DISABLE KEYS */;
INSERT INTO `intentos_login` VALUES
(1, '::1', 'admin@mesaly.co', 1, '2026-11-18 13:36:28'),
(2, '::1', 'admin@cafemacondo.co', 1, '2026-11-18 13:36:32'),
(3, '::1', 'admin@pizzeriadiroma.co', 1, '2026-11-18 13:36:36'),
(4, '::1', 'admin@pizzeriadiroma.co', 1, '2026-11-19 08:56:42'),
(5, '::1', 'admin@cafemacondo.co', 1, '2026-11-19 08:58:42'),
(6, '::1', 'admin@mesaly.co', 1, '2026-11-19 08:59:30'),
(7, '::1', 'admin@mesaly.co', 1, '2026-11-19 09:20:08'),
(8, '::1', 'admin@pizzeriadiroma.co', 1, '2026-11-19 09:34:16'),
(9, '::1', 'admin@pizzeriadiroma.co', 1, '2026-11-19 09:45:49'),
(10, '::1', 'admin@fuegourbano.co', 1, '2026-11-19 09:45:56'),
(11, '::1', 'admin@pizzeriadiroma.co', 1, '2026-11-19 09:52:42'),
(12, '::1', 'admin@pizzeriadiroma.co', 1, '2026-11-19 09:53:48'),
(13, '::1', 'admin@pizzeriadiroma.co', 1, '2026-11-19 09:55:25'),
(14, '::1', 'admin@cafemacondo.co', 1, '2026-11-19 09:55:30'),
(15, '::1', 'admin@fuegourbano.co', 1, '2026-11-19 09:55:34'),
(16, '::1', 'admin@cafemacondo.co', 1, '2026-11-19 09:56:03'),
(17, '::1', 'admin@mesaly.co', 1, '2026-11-19 10:07:26'),
(18, '::1', 'admin@mesaly.co', 1, '2026-11-19 10:18:26'),
(19, '::1', 'admin@mesaly.co', 1, '2026-11-19 10:20:47'),
(20, '::1', 'admin@mesaly.co', 1, '2026-11-19 10:21:19'),
(21, '::1', 'admin@cafemacondo.co', 1, '2026-11-19 10:56:03'),
(22, '::1', 'admin@cafemacondo.co', 1, '2026-11-19 11:06:49'),
(23, '::1', 'admin@pizzeriadiroma.co', 1, '2026-11-19 11:23:32'),
(24, '::1', 'admin@mesaly.co', 1, '2026-11-19 11:50:01'),
(25, '::1', 'admin@mesaly.co', 1, '2026-11-19 13:22:29'),
(26, '::1', 'admin@pizzeriadiroma.co', 1, '2026-11-19 13:27:48'),
(27, '::1', 'admin@pizzeriadiroma.co', 1, '2026-11-19 13:28:33'),
(28, '::1', 'admin@pizzeriadiroma.co', 1, '2026-11-19 13:30:25'),
(29, '::1', 'admin@pizzeriadiroma.co', 1, '2026-11-19 20:54:29'),
(30, '::1', 'admin@pizzeriadiroma.co', 1, '2026-11-19 20:58:33'),
(31, '::1', 'admin@pizzeriadiroma.co', 1, '2026-11-19 21:01:08'),
(32, '::1', 'admin@pizzeriadiroma.co', 1, '2026-11-19 21:06:15'),
(33, '::1', 'admin@pizzeriadiroma.co', 1, '2026-11-19 21:10:21'),
(34, '::1', 'admin@pizzeriadiroma.co', 1, '2026-11-19 21:10:32'),
(35, '::1', 'admin@pizzeriadiroma.co', 1, '2026-11-19 21:16:08'),
(36, '::1', 'admin@pizzeriadiroma.co', 1, '2026-11-19 21:23:04'),
(37, '::1', 'admin@pizzeriadiroma.co', 1, '2026-11-19 21:23:05'),
(38, '::1', 'admin@pizzeriadiroma.co', 1, '2026-11-19 21:24:07'),
(39, '::1', 'admin@pizzeriadiroma.co', 1, '2026-11-19 21:31:18'),
(40, '::1', 'admin@pizzeriadiroma.co', 1, '2026-11-19 21:43:17'),
(41, '2803:2d30:c00:28f:2c52:c7e5:94e1:6e1f', 'admin@pizzeriadiroma.co', 1, '2026-11-19 21:48:33'),
(42, '::1', 'admin@pizzeriadiroma.co', 1, '2026-11-19 21:55:51'),
(43, '::1', 'admin@pizzeriadiroma.co', 1, '2026-11-19 22:12:38'),
(44, '::1', 'admin@pizzeriadiroma.co', 1, '2026-11-20 09:03:20'),
(45, '::1', 'restaurante@mesaly.co', 0, '2026-11-20 09:14:08'),
(46, '::1', 'admin@pizzeriadiroma.co', 1, '2026-11-20 09:15:17'),
(47, '::1', 'admin@pizzeriadiroma.co', 1, '2026-11-20 09:16:36'),
(48, '::1', 'admin@pizzeriadiroma.co', 1, '2026-11-20 09:16:53'),
(49, '::1', 'admin@pizzeriadiroma.co', 1, '2026-11-20 09:17:12'),
(50, '::1', 'admin@pizzeriadiroma.co', 1, '2026-11-20 09:25:00');
INSERT INTO `intentos_login` VALUES
(51, '::1', 'admin@pizzeriadiroma.co', 1, '2026-11-20 11:45:25'),
(52, '::1', 'admin@pizzeriadiroma.co', 1, '2026-11-20 12:03:08'),
(53, '::1', 'admin@fuegourbano.co', 1, '2026-11-20 14:44:57'),
(54, '::1', 'admin@cafemacondo.co', 1, '2026-11-20 14:47:59'),
(55, '::1', 'admin@pizzeriadiroma.co', 1, '2026-11-20 14:48:10'),
(56, '::1', 'admin@pizzeriadiroma.co', 1, '2026-11-20 14:51:51'),
(57, '::1', 'admin@pizzeriadiroma.co', 1, '2026-11-21 08:42:08'),
(58, '::1', 'admin@pizzeriadiroma.co', 1, '2026-11-21 08:51:01'),
(59, '::1', 'admin@pizzeriadiroma.co', 1, '2026-11-23 13:09:23'),
(60, '::1', 'admin@pizzeriadiroma.co', 1, '2026-11-23 14:20:16'),
(61, '::1', 'admin@cafemacondo.co', 1, '2026-11-23 14:25:56');
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
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `mesas` (26 rows)
LOCK TABLES `mesas` WRITE;
/*!40000 ALTER TABLE `mesas` DISABLE KEYS */;
INSERT INTO `mesas` VALUES
(1, 1, 1, NULL, 1, '2026-11-18 13:36:06', '2026-11-19 09:59:49', NULL),
(2, 1, 2, 'Terraza 2', 1, '2026-11-18 13:36:06', '2026-11-18 13:36:06', NULL),
(3, 1, 3, 'Salón 3', 1, '2026-11-18 13:36:06', '2026-11-18 13:36:06', NULL),
(4, 1, 4, 'Salón 4', 1, '2026-11-18 13:36:06', '2026-11-18 13:36:06', NULL),
(5, 1, 5, 'Barra 5', 1, '2026-11-18 13:36:06', '2026-11-18 13:36:06', NULL),
(6, 1, 6, NULL, 1, '2026-11-18 13:36:06', '2026-11-19 09:56:07', NULL),
(7, 2, 1, 'Salón Principal 1', 1, '2026-11-18 13:36:06', '2026-11-18 13:36:06', NULL),
(8, 2, 2, 'Salón Principal 2', 1, '2026-11-18 13:36:06', '2026-11-18 13:36:06', NULL),
(9, 2, 3, 'Salón Principal 3', 1, '2026-11-18 13:36:06', '2026-11-18 13:36:06', NULL),
(10, 2, 4, 'Ventanal 4', 1, '2026-11-18 13:36:06', '2026-11-18 13:36:06', NULL),
(11, 2, 5, 'Ventanal 5', 1, '2026-11-18 13:36:06', '2026-11-18 13:36:06', NULL),
(12, 2, 6, 'Patio 6', 1, '2026-11-18 13:36:06', '2026-11-18 13:36:06', NULL),
(13, 3, 1, 'Mesa 1', 1, '2026-11-19 09:19:37', '2026-11-19 09:19:37', NULL),
(14, 3, 2, 'Mesa 2', 1, '2026-11-19 09:19:37', '2026-11-19 09:19:37', NULL),
(15, 3, 3, 'Mesa 3', 1, '2026-11-19 09:19:37', '2026-11-19 09:19:37', NULL),
(16, 3, 4, 'Mesa 4', 1, '2026-11-19 09:19:37', '2026-11-19 09:19:37', NULL),
(17, 3, 5, 'Mesa 5', 1, '2026-11-19 09:19:37', '2026-11-19 09:19:37', NULL),
(18, 3, 6, 'Mesa 6', 1, '2026-11-19 09:19:37', '2026-11-19 09:19:37', NULL),
(19, 3, 7, 'Mesa 7', 1, '2026-11-19 09:19:37', '2026-11-19 09:19:37', NULL),
(20, 3, 8, 'Mesa 8', 1, '2026-11-19 09:19:37', '2026-11-19 09:19:37', NULL),
(21, 3, 9, 'Mesa 9', 1, '2026-11-19 09:19:37', '2026-11-19 09:19:37', NULL),
(22, 3, 10, 'Mesa 10', 1, '2026-11-19 09:19:37', '2026-11-19 09:19:37', NULL),
(23, 3, 11, 'Mesa 11', 1, '2026-11-19 09:19:37', '2026-11-19 09:19:37', NULL),
(24, 3, 12, 'Mesa 12', 1, '2026-11-19 09:19:37', '2026-11-19 09:19:37', NULL),
(29, 2, 7, NULL, 1, '2026-11-19 21:58:34', '2026-11-19 21:58:34', NULL),
(30, 2, 8, NULL, 1, '2026-11-19 21:58:42', '2026-11-19 21:58:42', NULL);
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
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `opciones_modificadores` (18 rows)
LOCK TABLES `opciones_modificadores` WRITE;
/*!40000 ALTER TABLE `opciones_modificadores` DISABLE KEYS */;
INSERT INTO `opciones_modificadores` VALUES
(31, 10, 'Término Medio', 0, 1, 1, '2026-11-20 10:21:56', '2026-11-20 10:21:56', NULL),
(32, 10, 'Tres Cuartos', 0, 2, 1, '2026-11-20 10:21:56', '2026-11-20 10:21:56', NULL),
(33, 10, 'Bien Asado', 0, 3, 1, '2026-11-20 10:21:56', '2026-11-20 10:21:56', NULL),
(34, 11, 'Queso Cheddar Extra', 3500, 1, 1, '2026-11-20 10:21:56', '2026-11-20 10:21:56', NULL),
(35, 11, 'Tocineta Ahumada', 4000, 2, 1, '2026-11-20 10:21:56', '2026-11-20 10:21:56', NULL),
(36, 12, 'Masa Tradicional', 0, 1, 1, '2026-11-20 10:21:56', '2026-11-20 10:21:56', NULL),
(37, 12, 'Masa Delgada Crujiente', 0, 2, 1, '2026-11-20 10:21:56', '2026-11-20 10:21:56', NULL),
(38, 13, 'Sin borde de queso', 0, 1, 1, '2026-11-20 10:21:56', '2026-11-20 10:21:56', NULL),
(39, 13, 'Borde Relleno de Mozzarella', 5000, 2, 1, '2026-11-20 10:21:56', '2026-11-20 10:21:56', NULL),
(40, 14, 'Leche Entera de Origen', 0, 1, 1, '2026-11-20 14:43:50', '2026-11-20 14:43:50', NULL),
(41, 14, 'Leche Deslactosada', 0, 2, 1, '2026-11-20 14:43:50', '2026-11-20 14:43:50', NULL),
(42, 14, 'Bebida de Almendras', 2500, 3, 1, '2026-11-20 14:43:50', '2026-11-20 14:43:50', NULL),
(43, 14, 'Bebida de Avena', 2500, 4, 1, '2026-11-20 14:43:50', '2026-11-20 14:43:50', NULL),
(44, 15, 'Sin Azúcar', 0, 1, 1, '2026-11-20 14:43:50', '2026-11-20 14:43:50', NULL),
(45, 15, 'Azúcar Morena Orgánica', 0, 2, 1, '2026-11-20 14:43:50', '2026-11-20 14:43:50', NULL),
(46, 15, 'Miel de Abejas Pura', 1500, 3, 1, '2026-11-20 14:43:50', '2026-11-20 14:43:50', NULL),
(47, 16, 'Bola de Helado Artesanal', 4000, 1, 1, '2026-11-20 14:43:50', '2026-11-20 14:43:50', NULL),
(48, 16, 'Topping de Frutos Rojos', 2500, 2, 1, '2026-11-20 14:43:50', '2026-11-20 14:43:50', NULL);
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `pedido_item_opciones` (34 rows)
LOCK TABLES `pedido_item_opciones` WRITE;
/*!40000 ALTER TABLE `pedido_item_opciones` DISABLE KEYS */;
INSERT INTO `pedido_item_opciones` VALUES
(4, 7, 'Tipo de Masa', 'Masa Tradicional', 0, '2026-11-20 12:45:00'),
(5, 7, 'Borde de Queso', 'Borde Relleno de Mozzarella', 5000, '2026-11-20 12:45:00'),
(6, 9, 'Tipo de Masa', 'Masa Delgada Crujiente', 0, '2026-11-20 13:10:00'),
(7, 11, 'Tipo de Masa', 'Masa Tradicional', 0, '2026-11-20 13:30:00'),
(8, 13, 'Tipo de Masa', 'Masa Tradicional', 0, '2026-11-20 11:30:00'),
(9, 14, 'Tipo de Masa', 'Masa Tradicional', 0, '2026-11-20 11:30:00'),
(10, 15, 'Tipo de Masa', 'Masa Tradicional', 0, '2026-11-19 20:15:00'),
(11, 17, 'Tipo de Masa', 'Masa Tradicional', 0, '2026-11-19 19:40:00'),
(12, 17, 'Borde de Queso', 'Borde Relleno de Mozzarella', 5000, '2026-11-19 19:40:00'),
(13, 18, 'Tipo de Masa', 'Masa Delgada Crujiente', 0, '2026-11-19 13:00:00'),
(14, 20, 'Tipo de Masa', 'Masa Tradicional', 0, '2026-11-17 20:30:00'),
(15, 21, 'Tipo de Masa', 'Masa Tradicional', 0, '2026-11-15 21:00:00'),
(16, 23, 'Tipo de Masa', 'Masa Delgada Crujiente', 0, '2026-11-10 19:15:00'),
(17, 24, 'Término de la Carne', 'Término Medio', 0, '2026-11-20 13:20:00'),
(18, 24, 'Adición Opcional', 'Tocineta Ahumada', 4000, '2026-11-20 13:20:00'),
(19, 27, 'Término de la Carne', 'Tres Cuartos', 0, '2026-11-20 13:40:00'),
(20, 27, 'Adición Opcional', 'Queso Cheddar Extra', 3500, '2026-11-20 13:40:00'),
(21, 33, 'Término de la Carne', 'Tres Cuartos', 0, '2026-11-20 12:00:00'),
(22, 35, 'Término de la Carne', 'Término Medio', 0, '2026-11-19 21:10:00'),
(23, 38, 'Término de la Carne', 'Tres Cuartos', 0, '2026-11-19 20:00:00'),
(24, 38, 'Adición Opcional', 'Tocineta Ahumada', 4000, '2026-11-19 20:00:00'),
(25, 40, 'Término de la Carne', 'Bien Asado', 0, '2026-11-19 13:45:00'),
(26, 42, 'Término de la Carne', 'Término Medio', 0, '2026-11-17 21:30:00'),
(27, 44, 'Término de la Carne', 'Tres Cuartos', 0, '2026-11-15 19:40:00'),
(28, 46, 'Término de la Carne', 'Tres Cuartos', 0, '2026-11-10 22:00:00'),
(29, 48, 'Tipo de Leche', 'Bebida de Almendras', 2500, '2026-11-20 09:15:00'),
(30, 48, 'Endulzante', 'Sin Azúcar', 0, '2026-11-20 09:15:00'),
(31, 50, 'Tipo de Leche', 'Leche Entera de Origen', 0, '2026-11-20 09:40:00'),
(32, 51, 'Acompañamiento Opcional', 'Bola de Helado Artesanal', 4000, '2026-11-20 09:40:00'),
(33, 53, 'Tipo de Leche', 'Leche Deslactosada', 0, '2026-11-20 08:30:00'),
(34, 55, 'Tipo de Leche', 'Leche Entera de Origen', 0, '2026-11-19 16:30:00'),
(35, 59, 'Tipo de Leche', 'Bebida de Avena', 2500, '2026-11-17 15:45:00'),
(36, 65, 'Tipo de Masa', 'Masa Tradicional', 0, '2026-11-21 08:42:36'),
(37, 66, 'Tipo de Masa', 'Masa Tradicional', 0, '2026-11-23 14:21:16');
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
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `pedido_items` (60 rows)
LOCK TABLES `pedido_items` WRITE;
/*!40000 ALTER TABLE `pedido_items` DISABLE KEYS */;
INSERT INTO `pedido_items` VALUES
(7, 14, 6, 'Limonada de Coco', 10000, 1, NULL, 15000, '2026-11-20 12:45:00'),
(8, 14, 9, 'Pizza Cuatro Quesos DOP', 42000, 2, NULL, 84000, '2026-11-20 12:45:00'),
(9, 15, 10, 'Tiramisú Tradicional', 16000, 1, NULL, 16000, '2026-11-20 13:10:00'),
(10, 15, 7, 'Tostada de Aguacate & Huevo Poché', 14500, 2, NULL, 29000, '2026-11-20 13:10:00'),
(11, 16, 8, 'Tarta de Zanahoria & Nuez', 11000, 1, NULL, 11000, '2026-11-20 13:30:00'),
(12, 16, 9, 'Pizza Cuatro Quesos DOP', 42000, 1, NULL, 42000, '2026-11-20 13:30:00'),
(13, 17, 6, 'Limonada de Coco', 10000, 1, NULL, 10000, '2026-11-20 11:30:00'),
(14, 17, 10, 'Tiramisú Tradicional', 16000, 1, NULL, 16000, '2026-11-20 11:30:00'),
(15, 18, 6, 'Limonada de Coco', 10000, 1, NULL, 10000, '2026-11-19 20:15:00'),
(16, 18, 7, 'Tostada de Aguacate & Huevo Poché', 14500, 1, NULL, 14500, '2026-11-19 20:15:00'),
(17, 19, 10, 'Tiramisú Tradicional', 16000, 2, NULL, 42000, '2026-11-19 19:40:00'),
(18, 20, 8, 'Tarta de Zanahoria & Nuez', 11000, 2, NULL, 22000, '2026-11-19 13:00:00'),
(19, 20, 9, 'Pizza Cuatro Quesos DOP', 42000, 2, NULL, 84000, '2026-11-19 13:00:00'),
(20, 21, 6, 'Limonada de Coco', 10000, 2, NULL, 20000, '2026-11-17 20:30:00'),
(21, 22, 10, 'Tiramisú Tradicional', 16000, 1, NULL, 16000, '2026-11-15 21:00:00'),
(22, 22, 7, 'Tostada de Aguacate & Huevo Poché', 14500, 1, NULL, 14500, '2026-11-15 21:00:00'),
(23, 23, 8, 'Tarta de Zanahoria & Nuez', 11000, 3, NULL, 33000, '2026-11-10 19:15:00'),
(24, 24, 11, 'Burger Doble Fuego', 32000, 2, NULL, 72000, '2026-11-20 13:20:00'),
(25, 24, 17, 'Papas Rústicas a la Trufa', 16000, 1, NULL, 16000, '2026-11-20 13:20:00'),
(26, 24, 18, 'Cerveza Artesanal IPA (330ml)', 14000, 2, NULL, 28000, '2026-11-20 13:20:00'),
(27, 25, 12, 'Smash Trufada', 29000, 1, NULL, 32500, '2026-11-20 13:40:00'),
(28, 25, 13, 'Chicken Crispy Master', 27000, 1, NULL, 27000, '2026-11-20 13:40:00'),
(29, 25, 19, 'Soda de Frutos Rojos', 10000, 2, NULL, 20000, '2026-11-20 13:40:00'),
(30, 26, 15, 'Costillas BBQ Ahumadas', 44000, 1, NULL, 44000, '2026-11-20 13:00:00'),
(31, 26, 16, 'Alitas BBQ Ahumadas (8 uds)', 22000, 1, NULL, 22000, '2026-11-20 13:00:00'),
(32, 26, 18, 'Cerveza Artesanal IPA (330ml)', 14000, 2, NULL, 28000, '2026-11-20 13:00:00'),
(33, 27, 11, 'Burger Doble Fuego', 32000, 1, NULL, 32000, '2026-11-20 12:00:00'),
(34, 27, 20, 'Volcán de Chocolate Fundido', 16000, 1, NULL, 16000, '2026-11-20 12:00:00'),
(35, 28, 14, 'Bife de Chorizo Angus (400g)', 48000, 2, NULL, 96000, '2026-11-19 21:10:00'),
(36, 28, 17, 'Papas Rústicas a la Trufa', 16000, 1, NULL, 16000, '2026-11-19 21:10:00'),
(37, 28, 18, 'Cerveza Artesanal IPA (330ml)', 14000, 3, NULL, 42000, '2026-11-19 21:10:00'),
(38, 29, 12, 'Smash Trufada', 29000, 2, NULL, 66000, '2026-11-19 20:00:00'),
(39, 29, 19, 'Soda de Frutos Rojos', 10000, 2, NULL, 20000, '2026-11-19 20:00:00'),
(40, 30, 11, 'Burger Doble Fuego', 32000, 1, NULL, 32000, '2026-11-19 13:45:00'),
(41, 30, 16, 'Alitas BBQ Ahumadas (8 uds)', 22000, 1, NULL, 22000, '2026-11-19 13:45:00'),
(42, 31, 14, 'Bife de Chorizo Angus (400g)', 48000, 1, NULL, 48000, '2026-11-17 21:30:00'),
(43, 31, 15, 'Costillas BBQ Ahumadas', 44000, 1, NULL, 44000, '2026-11-17 21:30:00'),
(44, 32, 12, 'Smash Trufada', 29000, 2, NULL, 58000, '2026-11-15 19:40:00'),
(45, 32, 20, 'Volcán de Chocolate Fundido', 16000, 2, NULL, 32000, '2026-11-15 19:40:00'),
(46, 33, 11, 'Burger Doble Fuego', 32000, 3, NULL, 96000, '2026-11-10 22:00:00'),
(47, 33, 18, 'Cerveza Artesanal IPA (330ml)', 14000, 4, NULL, 56000, '2026-11-10 22:00:00'),
(48, 34, 5, 'Pizza Pepperoni Rustica', 39000, 2, NULL, 83000, '2026-11-20 09:15:00'),
(49, 34, 1, 'Café Espresso Doble', 5500, 1, NULL, 5500, '2026-11-20 09:15:00'),
(50, 35, 3, 'Croissant de Almendras', 9000, 1, NULL, 9000, '2026-11-20 09:40:00'),
(51, 35, 4, 'Pizza Margarita Clásica', 34000, 2, NULL, 76000, '2026-11-20 09:40:00'),
(52, 35, 2, 'Cappuccino Italiano', 8500, 1, NULL, 8500, '2026-11-20 09:40:00'),
(53, 36, 5, 'Pizza Pepperoni Rustica', 39000, 2, NULL, 78000, '2026-11-20 08:30:00'),
(54, 36, 1, 'Café Espresso Doble', 5500, 2, NULL, 11000, '2026-11-20 08:30:00'),
(55, 37, 5, 'Pizza Pepperoni Rustica', 39000, 1, NULL, 39000, '2026-11-19 16:30:00'),
(56, 37, 2, 'Cappuccino Italiano', 8500, 2, NULL, 17000, '2026-11-19 16:30:00');
INSERT INTO `pedido_items` VALUES
(57, 38, 3, 'Croissant de Almendras', 9000, 2, NULL, 18000, '2026-11-19 10:15:00'),
(58, 38, 4, 'Pizza Margarita Clásica', 34000, 2, NULL, 68000, '2026-11-19 10:15:00'),
(59, 39, 5, 'Pizza Pepperoni Rustica', 39000, 2, NULL, 83000, '2026-11-17 15:45:00'),
(60, 39, 4, 'Pizza Margarita Clásica', 34000, 2, NULL, 68000, '2026-11-17 15:45:00'),
(61, 40, 1, 'Café Espresso Doble', 5500, 2, NULL, 11000, '2026-11-15 09:00:00'),
(62, 40, 5, 'Pizza Pepperoni Rustica', 39000, 2, NULL, 78000, '2026-11-15 09:00:00'),
(63, 41, 3, 'Croissant de Almendras', 9000, 2, NULL, 18000, '2026-11-10 17:00:00'),
(64, 41, 2, 'Cappuccino Italiano', 8500, 1, NULL, 8500, '2026-11-10 17:00:00'),
(65, 42, 9, 'Pizza Cuatro Quesos DOP', 42000, 1, NULL, 42000, '2026-11-21 08:42:36'),
(66, 43, 4, 'Pizza Margarita Clásica', 34000, 1, NULL, 34000, '2026-11-23 14:21:16');
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
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `pedidos` (30 rows)
LOCK TABLES `pedidos` WRITE;
/*!40000 ALTER TABLE `pedidos` DISABLE KEYS */;
INSERT INTO `pedidos` VALUES
(14, 2, 1, '#PIZ-0001', 'mesa', 8, 'Valentina Osorio', 3157771234, NULL, NULL, 'Bien tostada la masa por favor', NULL, 'Efectivo', 99000, 0, 99000, 'entregado', '2026-11-21 08:42:12', '127.0.0.1', '2026-11-20 12:45:00', '2026-11-21 08:42:12'),
(15, 2, 2, '#PIZ-0002', 'mesa', 10, 'Sebastián Gómez', 3182223344, NULL, NULL, 'Servir con servilletas y salsas', NULL, 'Datáfono (Tarjeta Débito/Crédito)', 45000, 0, 45000, 'entregado', '2026-11-21 08:42:13', '127.0.0.1', '2026-11-20 13:10:00', '2026-11-21 08:42:13'),
(16, 2, 3, '#PIZ-0003', 'domicilio', NULL, 'Carlos Andrés Valencia', 3001234567, 'Carrera 26 # 32-15, Apto 402', 'Alvernia', 'Timbre 402, llamar al llegar', NULL, 'Nequi / Daviplata', 53000, 4500, 57500, 'entregado', NULL, '127.0.0.1', '2026-11-20 13:30:00', '2026-11-21 08:42:52'),
(17, 2, 4, '#PIZ-0004', 'domicilio', NULL, 'Laura Daniela Vargas', 3109876543, 'Calle 28 # 38-42', 'El Príncipe', 'Casa blanca reja negra', NULL, 'Efectivo', 26000, 4500, 30500, 'entregado', NULL, '127.0.0.1', '2026-11-20 11:30:00', '2026-11-20 14:43:50'),
(18, 2, 5, '#PIZ-0005', 'mesa', 7, 'Juan David Henao', 3123456789, NULL, NULL, NULL, NULL, 'Efectivo', 24500, 0, 24500, 'entregado', '2026-11-19 20:15:00', '127.0.0.1', '2026-11-19 20:15:00', '2026-11-20 14:43:50'),
(19, 2, 6, '#PIZ-0006', 'domicilio', NULL, 'Mariana Jaramillo', 3176543210, 'Calle 22 # 24-50, Condominio San José', 'Salesianos', 'Portería torre 2', NULL, 'Nequi / Daviplata', 42000, 4500, 46500, 'entregado', NULL, '127.0.0.1', '2026-11-19 19:40:00', '2026-11-20 14:43:51'),
(20, 2, 7, '#PIZ-0007', 'mesa', 9, 'Andrés Felipe Caicedo', 3151112233, NULL, NULL, NULL, NULL, 'Datáfono (Tarjeta Débito/Crédito)', 106000, 0, 106000, 'entregado', '2026-11-19 13:00:00', '127.0.0.1', '2026-11-19 13:00:00', '2026-11-20 14:43:51'),
(21, 2, 8, '#PIZ-0008', 'domicilio', NULL, 'Santiago Arango', 3194445566, 'Carrera 30 # 25-10', 'Victoria', NULL, NULL, 'Efectivo', 20000, 4500, 24500, 'entregado', NULL, '127.0.0.1', '2026-11-17 20:30:00', '2026-11-20 14:43:51'),
(22, 2, 9, '#PIZ-0009', 'mesa', 11, 'Daniela Londoño', 3118889900, NULL, NULL, NULL, NULL, 'Transferencia Bancolombia', 30500, 0, 30500, 'entregado', '2026-11-15 21:00:00', '127.0.0.1', '2026-11-15 21:00:00', '2026-11-20 14:43:51'),
(23, 2, 10, '#PIZ-0010', 'domicilio', NULL, 'Felipe Ortiz', 3167778899, 'Carrera 19 # 27-30', 'Maracaibo', NULL, NULL, 'Nequi / Daviplata', 33000, 4500, 37500, 'entregado', NULL, '127.0.0.1', '2026-11-10 19:15:00', '2026-11-20 14:43:51'),
(24, 3, 1, '#FUE-0001', 'mesa', 13, 'Mateo Ramírez', 3145556677, NULL, NULL, 'Término medio exacto, salsas aparte', NULL, 'Datáfono (Tarjeta Débito/Crédito)', 116000, 0, 116000, 'recibido', NULL, '127.0.0.1', '2026-11-20 13:20:00', '2026-11-20 14:43:51'),
(25, 3, 2, '#FUE-0002', 'mesa', 15, 'Juliana Echeverry', 3168887766, NULL, NULL, 'Papas bien doradas', NULL, 'Nequi / Daviplata', 79500, 0, 79500, 'en_preparacion', NULL, '127.0.0.1', '2026-11-20 13:40:00', '2026-11-20 14:43:51'),
(26, 3, 3, '#FUE-0003', 'domicilio', NULL, 'Diego Fernando Castro', 3105554433, 'Carrera 27 # 30-10, Apto 301', 'Alvernia', 'Dejar en portería con vigilancia', NULL, 'Transferencia Bancolombia', 94000, 5000, 99000, 'en_camino', NULL, '127.0.0.1', '2026-11-20 13:00:00', '2026-11-20 14:43:51'),
(27, 3, 4, '#FUE-0004', 'domicilio', NULL, 'Natalia Giraldo', 3189990011, 'Calle 26 # 35-22', 'El Príncipe', 'Casa de dos pisos', NULL, 'Efectivo', 48000, 5000, 53000, 'entregado', NULL, '127.0.0.1', '2026-11-20 12:00:00', '2026-11-20 14:43:51'),
(28, 3, 5, '#FUE-0005', 'mesa', 17, 'Alejandra Muñoz', 3112229988, NULL, NULL, NULL, NULL, 'Datáfono (Tarjeta Débito/Crédito)', 154000, 0, 154000, 'entregado', '2026-11-19 21:10:00', '127.0.0.1', '2026-11-19 21:10:00', '2026-11-20 14:43:51'),
(29, 3, 6, '#FUE-0006', 'domicilio', NULL, 'Carlos Andrés Valencia', 3001234567, 'Calle 23 # 25-18', 'Salesianos', NULL, NULL, 'Nequi / Daviplata', 86000, 5000, 91000, 'entregado', NULL, '127.0.0.1', '2026-11-19 20:00:00', '2026-11-20 14:43:51'),
(30, 3, 7, '#FUE-0007', 'mesa', 14, 'Sebastián Gómez', 3182223344, NULL, NULL, NULL, NULL, 'Efectivo', 54000, 0, 54000, 'entregado', '2026-11-19 13:45:00', '127.0.0.1', '2026-11-19 13:45:00', '2026-11-20 14:43:51'),
(31, 3, 8, '#FUE-0008', 'mesa', 16, 'Santiago Arango', 3194445566, NULL, NULL, NULL, NULL, 'Datáfono (Tarjeta Débito/Crédito)', 92000, 0, 92000, 'entregado', '2026-11-17 21:30:00', '127.0.0.1', '2026-11-17 21:30:00', '2026-11-20 14:43:51'),
(32, 3, 9, '#FUE-0009', 'domicilio', NULL, 'Mariana Jaramillo', 3176543210, 'Carrera 31 # 24-10', 'Victoria', NULL, NULL, 'Nequi / Daviplata', 90000, 5000, 95000, 'entregado', NULL, '127.0.0.1', '2026-11-15 19:40:00', '2026-11-20 14:43:51'),
(33, 3, 10, '#FUE-0010', 'mesa', 20, 'Felipe Ortiz', 3167778899, NULL, NULL, NULL, NULL, 'Efectivo', 152000, 0, 152000, 'entregado', '2026-11-10 22:00:00', '127.0.0.1', '2026-11-10 22:00:00', '2026-11-20 14:43:51'),
(34, 1, 1, '#MAC-0001', 'mesa', 1, 'Mariana Jaramillo', 3176543210, NULL, NULL, 'Café bien caliente por favor', NULL, 'Nequi / Daviplata', 88500, 0, 88500, 'recibido', NULL, '127.0.0.1', '2026-11-20 09:15:00', '2026-11-20 14:43:51'),
(35, 1, 2, '#MAC-0002', 'mesa', 3, 'Carlos Andrés Valencia', 3001234567, NULL, NULL, 'Croissant recién calentado', NULL, 'Efectivo', 93500, 0, 93500, 'en_preparacion', NULL, '127.0.0.1', '2026-11-20 09:40:00', '2026-11-20 14:43:51'),
(36, 1, 3, '#MAC-0003', 'domicilio', NULL, 'Valentina Osorio', 3157771234, 'Carrera 25 # 29-40, Apto 201', 'Alvernia', 'Dejar en recepción', NULL, 'Transferencia Bancolombia', 89000, 3500, 92500, 'entregado', NULL, '127.0.0.1', '2026-11-20 08:30:00', '2026-11-20 14:43:51'),
(37, 1, 4, '#MAC-0004', 'mesa', 2, 'Laura Daniela Vargas', 3109876543, NULL, NULL, NULL, NULL, 'Datáfono (Tarjeta Débito/Crédito)', 56000, 0, 56000, 'entregado', '2026-11-19 16:30:00', '127.0.0.1', '2026-11-19 16:30:00', '2026-11-20 14:43:51'),
(38, 1, 5, '#MAC-0005', 'domicilio', NULL, 'Sebastián Gómez', 3182223344, 'Calle 27 # 36-10', 'El Príncipe', NULL, NULL, 'Nequi / Daviplata', 86000, 3500, 89500, 'entregado', NULL, '127.0.0.1', '2026-11-19 10:15:00', '2026-11-20 14:43:51'),
(39, 1, 6, '#MAC-0006', 'mesa', 4, 'Andrés Felipe Caicedo', 3151112233, NULL, NULL, NULL, NULL, 'Efectivo', 151000, 0, 151000, 'entregado', '2026-11-17 15:45:00', '127.0.0.1', '2026-11-17 15:45:00', '2026-11-20 14:43:51'),
(40, 1, 7, '#MAC-0007', 'domicilio', NULL, 'Natalia Giraldo', 3189990011, 'Carrera 24 # 21-30', 'Salesianos', NULL, NULL, 'Nequi / Daviplata', 89000, 3500, 92500, 'entregado', NULL, '127.0.0.1', '2026-11-15 09:00:00', '2026-11-20 14:43:51'),
(41, 1, 8, '#MAC-0008', 'mesa', 5, 'Diego Fernando Castro', 3105554433, NULL, NULL, NULL, NULL, 'Transferencia Bancolombia', 26500, 0, 26500, 'entregado', '2026-11-10 17:00:00', '127.0.0.1', '2026-11-10 17:00:00', '2026-11-20 14:43:51'),
(42, 2, 11, 'PIZ-0011', 'domicilio', NULL, 'Diego Olaya', 3228991339, '123sad', 'jasa', NULL, NULL, 'acordar', 42000, 4500, 46500, 'entregado', NULL, '::1', '2026-11-21 08:42:36', '2026-11-21 08:42:49'),
(43, 2, 12, 'PIZ-0012', 'mesa', 7, 'Mesa 1', NULL, NULL, NULL, NULL, NULL, 'acordar', 34000, 0, 34000, 'recibido', NULL, '::1', '2026-11-23 14:21:16', '2026-11-23 14:21:16');
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
) ENGINE=InnoDB AUTO_INCREMENT=83 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `producto_grupos` (32 rows)
LOCK TABLES `producto_grupos` WRITE;
/*!40000 ALTER TABLE `producto_grupos` DISABLE KEYS */;
INSERT INTO `producto_grupos` VALUES
(51, 11, 10, 1),
(52, 11, 11, 2),
(53, 12, 10, 1),
(54, 12, 11, 2),
(55, 13, 10, 1),
(56, 13, 11, 2),
(57, 14, 10, 1),
(58, 14, 11, 2),
(59, 15, 10, 1),
(60, 15, 11, 2),
(61, 16, 10, 1),
(62, 16, 11, 2),
(63, 17, 10, 1),
(64, 17, 11, 2),
(65, 18, 10, 1),
(66, 18, 11, 2),
(67, 19, 10, 1),
(68, 19, 11, 2),
(69, 20, 10, 1),
(70, 20, 11, 2),
(71, 9, 12, 1),
(72, 9, 13, 2),
(73, 4, 12, 1),
(74, 4, 13, 2),
(75, 5, 12, 1),
(76, 5, 13, 2),
(77, 1, 14, 1),
(78, 1, 15, 2),
(79, 2, 14, 1),
(80, 2, 15, 2),
(81, 8, 16, 1),
(82, 3, 16, 1);
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
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `productos` (20 rows)
LOCK TABLES `productos` WRITE;
/*!40000 ALTER TABLE `productos` DISABLE KEYS */;
INSERT INTO `productos` VALUES
(1, 1, 1, 'Café Espresso Doble', 'Extracción pura de granos colombianos de altura.', 5500, 'uploads/cafe_macondo/espresso_doble.jpg', 1, 1, 1, '2026-11-18 13:36:06', '2026-11-19 10:53:53', NULL),
(2, 1, 1, 'Cappuccino Italiano', 'Espresso con leche texturizada y arte latte.', 8500, 'uploads/cafe_macondo/cappuccino_italiano.jpg', 1, 1, 2, '2026-11-18 13:36:06', '2026-11-19 10:53:53', NULL),
(3, 1, 2, 'Croissant de Almendras', 'Hojaldre horneado diariamente relleno de crema de almendras.', 9000, 'uploads/cafe_macondo/croissant_almendras.jpg', 1, 0, 1, '2026-11-18 13:36:06', '2026-11-19 10:53:53', NULL),
(4, 2, 3, 'Pizza Margarita Clásica', 'Salsa de tomate San Marzano, mozzarella fior di latte y albahaca.', 34000, 'uploads/pizzeria_di_roma/pizza_margarita.jpg', 1, 1, 1, '2026-11-18 13:36:06', '2026-11-19 10:53:53', NULL),
(5, 2, 3, 'Pizza Pepperoni Rustica', 'Doble pepperoni curado, queso mozzarella y orégano fresco.', 39000, 'uploads/pizzeria_di_roma/pizza_pepperoni.jpg', 1, 1, 2, '2026-11-18 13:36:06', '2026-11-19 10:53:53', NULL),
(6, 2, 4, 'Limonada de Coco', 'Refrescante limonada frappé con crema de coco natural.', 10000, 'uploads/pizzeria_di_roma/limonada_coco.jpg', 1, 0, 1, '2026-11-18 13:36:06', '2026-11-19 22:02:52', NULL),
(7, 1, 1, 'Tostada de Aguacate & Huevo Poché', 'Pan masa madre tostado, aguacate hass macerado, huevo de granja y semillas de chía.', 14500, 'uploads/cafe_macondo/tostada_aguacate.jpg', 1, 1, 0, '2026-11-19 10:53:53', '2026-11-19 10:53:53', NULL),
(8, 1, 2, 'Tarta de Zanahoria & Nuez', 'Bizcocho especiado con canela, nuez de nogal y cobertura de queso crema frosting.', 11000, 'uploads/cafe_macondo/tarta_zanahoria.jpg', 1, 0, 0, '2026-11-19 10:53:53', '2026-11-19 10:53:53', NULL),
(9, 2, 3, 'Pizza Cuatro Quesos DOP', 'Mozzarella fior di latte, gorgonzola cremoso, fontina fundida y parmesano reggiano 24 meses.', 42000, 'uploads/pizzeria_di_roma/pizza_cuatro_quesos.jpg', 1, 1, 0, '2026-11-19 10:53:53', '2026-11-19 10:53:53', NULL),
(10, 2, 10, 'Tiramisú Tradicional', 'Bizcochos savoiardi bañados en espresso, crema de mascarpone y cacao amargo.', 16000, 'uploads/pizzeria_di_roma/tiramisu_italiano.jpg', 1, 0, 0, '2026-11-19 10:53:53', '2026-11-20 14:43:50', NULL),
(11, 3, 5, 'Burger Doble Fuego', 'Doble carne angus madurada (300g), queso cheddar fundido, tocineta ahumada y salsa especial.', 32000, 'uploads/fuego_urbano/burger_doble_fuego.jpg', 1, 1, 0, '2026-11-19 10:53:53', '2026-11-19 10:53:53', NULL),
(12, 3, 5, 'Smash Trufada', 'Doble smash burger caramelizada con bordes crocantes, alioli de trufa negra y cebolla crispy.', 29000, 'uploads/fuego_urbano/smash_trufada.jpg', 1, 1, 0, '2026-11-19 10:53:53', '2026-11-19 10:53:53', NULL),
(13, 3, 5, 'Chicken Crispy Master', 'Pechuga marinada frita estilo sureño con ensalada coleslaw fresca y pepinillos agridulces.', 27000, 'uploads/fuego_urbano/chicken_crispy.jpg', 1, 0, 0, '2026-11-19 10:53:53', '2026-11-19 10:53:53', NULL),
(14, 3, 6, 'Bife de Chorizo Angus (400g)', 'Corte jugoso a la brasa servido con mantequilla de romero y chimichurri de la casa.', 48000, 'uploads/fuego_urbano/bife_de_chorizo.jpg', 1, 1, 0, '2026-11-19 10:53:53', '2026-11-19 10:53:53', NULL),
(15, 3, 6, 'Costillas BBQ Ahumadas', 'Costillar de cerdo cocinado 8 horas a fuego lento, glaseado con reducción de BBQ y miel de caña.', 44000, 'uploads/fuego_urbano/costillas_bbq.jpg', 1, 0, 0, '2026-11-19 10:53:53', '2026-11-19 10:53:53', NULL),
(16, 3, 7, 'Alitas BBQ Ahumadas (8 uds)', 'Alitas de pollo glaseadas con salsa BBQ casera acompañadas de bastones de apio y aderezo blue cheese.', 22000, 'uploads/fuego_urbano/alitas_bbq_ahumadas.jpg', 1, 0, 0, '2026-11-19 10:53:53', '2026-11-19 10:53:53', NULL),
(17, 3, 7, 'Papas Rústicas a la Trufa', 'Papas rústicas cortadas a mano, aromatizadas con aceite de trufa y parmesano reggiano rallado.', 16000, 'uploads/fuego_urbano/papas_rusticas_trufa.jpg', 1, 1, 0, '2026-11-19 10:53:53', '2026-11-19 10:53:53', NULL),
(18, 3, 8, 'Cerveza Artesanal IPA (330ml)', 'Cerveza rubia aromática con notas cítricas, lupulado intenso y final refrescante.', 14000, 'uploads/fuego_urbano/cerveza_artesanal_ipa.jpg', 1, 0, 0, '2026-11-19 10:53:53', '2026-11-19 10:53:53', NULL),
(19, 3, 8, 'Soda de Frutos Rojos', 'Refresco artesanal con fresas, moras silvestres, menta fresca y soda.', 10000, 'uploads/fuego_urbano/soda_frutos_rojos.jpg', 1, 0, 0, '2026-11-19 10:53:53', '2026-11-19 10:53:53', NULL),
(20, 3, 9, 'Volcán de Chocolate Fundido', 'Bizcocho tibio de chocolate amargo 70% con corazón líquido y helado de vainilla.', 16000, 'uploads/fuego_urbano/volcan_de_chocolate.jpg', 1, 1, 0, '2026-11-19 10:53:53', '2026-11-19 10:53:53', NULL);
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
(1, 'Café Macondo', 'cafe-macondo', 'Cafetería de especialidad, repostería artesanal y desayunos.', 'uploads/cafe_macondo/logo_cafe_macondo.jpg', 'uploads/cafe_macondo/banner_cafe_macondo.jpg', '#D97706', '#B45309', '#FFFBEB', '#1E1B18', 'Plus Jakarta Sans', 573169874523, 'COP', 1, 1, 1, 0, 3500, 15000, 'Pagos por Nequi y Daviplata al 316 987 4523. En el local aceptamos tarjetas débito, crédito y efectivo.', 79000, 'basico', 6, 600, 1, 'Plan Básico estándar: 6 mesas a $79.000 COP/mes', 1, 'mensual', '2026-12-18', 3, 18, 0, NULL, NULL, '2026-11-19 13:22:31', 9, '2026-11-18 13:36:06', '2026-11-20 14:43:51', NULL),
(2, 'Pizzería Di Roma ciudad de tulua', 'pizzeria-di-roma', 'Auténtica pizza al horno de leña, pastas frescas y aperitivos italianos.', 'uploads/pizzeria_di_roma/logo_pizzeria_di_roma.jpg', 'uploads/pizzeria_di_roma/banner_pizzeria_di_roma.jpg', '#DC2626', '#B91C1C', '#FEF2F2', '#1E1B18', 'Plus Jakarta Sans', 573158945612, 'COP', 1, 1, 1, 0, 4500, 25000, '', 89000, 'basico', 8, 600, 1, 'Plan Básico extendido: 8 mesas (tarifa acordada por +2 mesas a $89.000 COP/mes)', 1, 'mensual', '2026-12-18', 3, 18, 0, NULL, NULL, '2026-11-19 13:22:31', 13, '2026-11-18 13:36:06', '2026-11-23 14:21:16', NULL),
(3, 'Fuego Urbano — Burger & Grill', 'fuego-urbano', 'Hamburguesas artesanales a la parrilla y cortes selectos.', 'uploads/fuego_urbano/logo_fuego_urbano.jpg', 'uploads/fuego_urbano/banner_fuego_urbano.jpg', '#E85D04', '#DC2F02', '#FFF7EA', '#1E1B18', 'Inter', 573187654321, 'COP', 1, 1, 1, 1, 5000, 30000, 'Aceptamos transferencias por Nequi y Daviplata al 318 765 4321, datáfono contra entrega o efectivo.', 149000, 'pro', NULL, 1500, 1, 'Plan Pro con tarifa fija de catálogo ($149.000 COP/mes).', 1, 'mensual', '2026-12-19', 3, 19, 0, NULL, NULL, '2026-11-19 13:22:31', 11, '2026-11-19 09:19:37', '2026-11-20 14:43:51', NULL);
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
(1, NULL, 'Super Admin Mesaly', 'admin@mesaly.co', '$argon2id$v=19$m=65536,t=4,p=1$OTFnMUVSL1pNWUZaelBjQw$srDFpS2e6RVYoRwdj6yzQkZdegDUQQ1tAWhtfM6LT38', 'super_admin', 0, 1, '2026-11-19 13:22:29', '2026-11-18 13:36:06', '2026-11-19 13:22:29'),
(2, 1, 'Admin Café Macondo', 'admin@cafemacondo.co', '$argon2id$v=19$m=65536,t=4,p=1$VE81dGY0ZThDZTVHeE9rUQ$9+RQt8u3bDllT7HMruEQsgZHRbK/QkFtdDUs4uALZYo', 'admin_negocio', 0, 1, '2026-11-23 14:25:56', '2026-11-18 13:36:06', '2026-11-23 14:25:56'),
(3, 2, 'Admin Pizzería Di Roma', 'admin@pizzeriadiroma.co', '$argon2id$v=19$m=65536,t=4,p=1$WVZua2ZWc2NWMVRqUXE2VA$ADvDjORUjUkVKnqOCFOG9pb5BTlH2fTUJLRCZnNr50I', 'admin_negocio', 0, 1, '2026-11-23 14:20:16', '2026-11-18 13:36:06', '2026-11-23 14:20:16'),
(4, 3, 'Admin Fuego Urbano', 'admin@fuegourbano.co', '$argon2id$v=19$m=65536,t=4,p=1$azBUdG1qREt6emdJa2hvWA$z8RC1P3eblm5Afx0YByfDQTtRXoL5thSGYcY00UiesE', 'admin_negocio', 0, 1, '2026-11-20 14:44:57', '2026-11-19 09:19:37', '2026-11-20 14:44:57');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;

SET FOREIGN_KEY_CHECKS = 1;
