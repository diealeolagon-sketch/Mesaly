-- ==============================================================================
-- MESALY — Datos de Prueba y Configuración Inicial (Seeds)
-- ==============================================================================

SET NAMES utf8mb4;
SET time_zone = '-05:00';
SET foreign_key_checks = 0;

-- 1. Configuracion del Sistema
INSERT INTO `configuracion` (`clave`, `valor`) VALUES
('nombre_plataforma', 'Mesaly'),
('whatsapp_soporte', '573001234567'),
('dias_alerta', '5'),
('dias_gracia', '3')
ON DUPLICATE KEY UPDATE `valor` = VALUES(`valor`);

-- 2. Restaurante Demo
INSERT INTO `restaurantes` (
    `id`, `nombre`, `slug`, `descripcion`, `logo_path`, `banner_path`,
    `color_primario`, `color_secundario`, `color_fondo`, `color_texto`, `fuente`,
    `whatsapp`, `moneda`, `recibiendo_pedidos`, `acepta_mesa`, `acepta_domicilio`, `acepta_recoger`,
    `costo_envio`, `pedido_minimo`, `info_pago_texto`, `precio`, `notas_internas`, `mostrar_marca`,
    `ciclo`, `fecha_vencimiento`, `dias_gracia`, `dia_corte`, `en_prueba`, `siguiente_consecutivo`
) VALUES (
    1,
    'La Parrilla Paisa',
    'la-parrilla-paisa',
    'Los mejores cortes de carne, asados al carbón y comida típica colombiana en el corazón de la ciudad.',
    NULL,
    NULL,
    '#E85D04',
    '#FFB703',
    '#FFF7EA',
    '#1E1B18',
    'Inter',
    '573001234567',
    'COP',
    1, 1, 1, 1,
    4000,
    15000,
    'Transferencias por Nequi o Daviplata al 300 123 4567. También recibimos efectivo y datáfono contra entrega.',
    49000,
    'Cliente pionero de Medellín. Contacto: Carlos Restrepo.',
    1,
    'mensual',
    DATE_ADD(CURRENT_DATE, INTERVAL 30 DAY),
    3,
    DAY(CURRENT_DATE),
    0,
    1
)
ON DUPLICATE KEY UPDATE `nombre` = VALUES(`nombre`), `slug` = VALUES(`slug`);

-- 4. Usuario Super Administrador
-- Contraseña: "Password123*" (Argon2id)
INSERT INTO `usuarios` (
    `id`, `restaurante_id`, `nombre`, `email`, `password_hash`, `rol`, `debe_cambiar_password`, `activo`
) VALUES (
    1,
    NULL,
    'Super Admin Mesaly',
    'admin@mesaly.co',
    '$argon2id$v=19$m=65536,t=4,p=1$dEdIMzlSOUlVLkVxc0R6Vw$w/KBY5IF1vD5W5HGq7mPI30nZXtCxre2UYXF8v7hS9M',
    'super_admin',
    0,
    1
)
ON DUPLICATE KEY UPDATE `email` = VALUES(`email`);

-- 5. Usuario Admin del Restaurante Demo
-- Contraseña: "Password123*" (Argon2id)
INSERT INTO `usuarios` (
    `id`, `restaurante_id`, `nombre`, `email`, `password_hash`, `rol`, `debe_cambiar_password`, `activo`
) VALUES (
    2,
    1,
    'Carlos Restrepo',
    'restaurante@mesaly.co',
    '$argon2id$v=19$m=65536,t=4,p=1$dEdIMzlSOUlVLkVxc0R6Vw$w/KBY5IF1vD5W5HGq7mPI30nZXtCxre2UYXF8v7hS9M',
    'admin_negocio',
    0,
    1
)
ON DUPLICATE KEY UPDATE `email` = VALUES(`email`);

-- 5. Métodos de Pago Aceptados por el Restaurante
INSERT INTO `metodos_pago_negocio` (`restaurante_id`, `metodo`) VALUES
(1, 'efectivo'),
(1, 'transferencia'),
(1, 'datafono')
ON DUPLICATE KEY UPDATE `metodo` = VALUES(`metodo`);

-- 6. Horarios de Atención (Lunes a Domingo, 11:30 a 22:00)
INSERT INTO `horarios` (`restaurante_id`, `dia_semana`, `abre`, `cierra`, `tramo`, `activo`) VALUES
(1, 0, '11:30:00', '21:00:00', 1, 1),
(1, 1, '11:30:00', '22:00:00', 1, 1),
(1, 2, '11:30:00', '22:00:00', 1, 1),
(1, 3, '11:30:00', '22:00:00', 1, 1),
(1, 4, '11:30:00', '23:00:00', 1, 1),
(1, 5, '11:30:00', '23:30:00', 1, 1),
(1, 6, '11:30:00', '23:30:00', 1, 1)
ON DUPLICATE KEY UPDATE `abre` = VALUES(`abre`), `cierra` = VALUES(`cierra`);

-- 7. Categorías
INSERT INTO `categorias` (`id`, `restaurante_id`, `nombre`, `orden`, `activa`) VALUES
(1, 1, 'Entradas y Típicos', 1, 1),
(2, 1, 'Carnes y Platos Fuertes', 2, 1),
(3, 1, 'Bebidas Naturales', 3, 1)
ON DUPLICATE KEY UPDATE `nombre` = VALUES(`nombre`);

-- 8. Productos
INSERT INTO `productos` (
    `id`, `restaurante_id`, `categoria_id`, `nombre`, `descripcion`, `precio`, `disponible`, `destacado`, `orden`
) VALUES
(1, 1, 1, 'Empanadas Colombianas (3 uds)', 'Crujientes empanadas de maíz rellenas de carne desmechada con papa criolla, acompañadas de ají casero y limón.', 9000, 1, 1, 1),
(2, 1, 1, 'Chicharrón Carnudo Criollo', 'Porción de 200g de tocino crocante con arepa paisa y limón mandarina.', 14000, 1, 0, 2),
(3, 1, 2, 'Bandeja Paisa Tradicional', 'Frijoles cargamanto, arroz blanco, carne molida sazonada, chicharrón crocante, chorizo campesino, huevo frito, tajada de plátano maduro, aguacate y arepa.', 38000, 1, 1, 1),
(4, 1, 2, 'Churrasco al Carbón 350g', 'Corte jugoso de lomo ancho asado al carbón con chimichurri de la casa, papas a la francesa y ensalada fresca.', 42000, 1, 1, 2),
(5, 1, 3, 'Limonada de Coco', 'Bebida cremosa y refrescante a base de limón tahití, crema de coco y leche condensada.', 9000, 1, 1, 1),
(6, 1, 3, 'Jugo Natural en Agua o Leche', 'Frutas frescas colombianas a elegir: Maracuyá, Mora, Mango, Lulo o Guanábana.', 7500, 1, 0, 2)
ON DUPLICATE KEY UPDATE `nombre` = VALUES(`nombre`), `precio` = VALUES(`precio`);

-- 9. Grupos de Modificadores
INSERT INTO `grupos_modificadores` (
    `id`, `restaurante_id`, `nombre`, `tipo`, `obligatorio`, `min_selecciones`, `max_selecciones`
) VALUES
(1, 1, 'Término de la Carne', 'unica', 1, 1, 1),
(2, 1, 'Adicionales Especiales', 'multiple', 0, 0, 3)
ON DUPLICATE KEY UPDATE `nombre` = VALUES(`nombre`);

-- 10. Opciones de Modificadores
INSERT INTO `opciones_modificadores` (`id`, `grupo_id`, `nombre`, `precio_extra`, `orden`, `activa`) VALUES
(1, 1, 'Término Medio (Jugoso)', 0, 1, 1),
(2, 1, 'Tres Cuartos (Punto Ideal)', 0, 2, 1),
(3, 1, 'Bien Cocido', 0, 3, 1),
(4, 2, 'Porción de Tocineta Crocante', 4000, 1, 1),
(5, 2, 'Queso Mozzarella Fundido', 3500, 2, 1),
(6, 2, 'Huevo Frito Adicional', 2500, 3, 1)
ON DUPLICATE KEY UPDATE `nombre` = VALUES(`nombre`), `precio_extra` = VALUES(`precio_extra`);

-- 11. Relación Producto con Grupos de Modificadores
INSERT INTO `producto_grupos` (`producto_id`, `grupo_id`, `orden`) VALUES
(4, 1, 1), -- Churrasco con Término
(4, 2, 2), -- Churrasco con Adicionales
(3, 2, 1)  -- Bandeja con Adicionales
ON DUPLICATE KEY UPDATE `orden` = VALUES(`orden`);

SET foreign_key_checks = 1;
