<?php
require_once __DIR__ . '/../../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
$dotenv->safeLoad();

$dsn = "mysql:host={$_ENV['DB_HOST']};port={$_ENV['DB_PORT']};dbname={$_ENV['DB_DATABASE']};charset=utf8mb4";
$pdo = new PDO($dsn, $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

echo "Iniciando migracion 001: Eliminar planes y migrar a suscripciones directas...\n";

// 1. Agregar columnas a restaurantes si no existen
$cols = $pdo->query("DESCRIBE restaurantes")->fetchAll(PDO::FETCH_COLUMN);

if (!in_array('precio', $cols)) {
    echo "- Agregando columna 'precio' a restaurantes...\n";
    $pdo->exec("ALTER TABLE `restaurantes` ADD COLUMN `precio` INT UNSIGNED NOT NULL DEFAULT 49000 AFTER `info_pago_texto`");
}

if (!in_array('notas_internas', $cols)) {
    echo "- Agregando columna 'notas_internas' a restaurantes...\n";
    $pdo->exec("ALTER TABLE `restaurantes` ADD COLUMN `notas_internas` TEXT NULL AFTER `precio`");
}

if (!in_array('mostrar_marca', $cols)) {
    echo "- Agregando columna 'mostrar_marca' a restaurantes...\n";
    $pdo->exec("ALTER TABLE `restaurantes` ADD COLUMN `mostrar_marca` TINYINT(1) NOT NULL DEFAULT 1 AFTER `notas_internas`");
}

if (!in_array('dias_gracia', $cols)) {
    echo "- Agregando columna 'dias_gracia' a restaurantes...\n";
    $pdo->exec("ALTER TABLE `restaurantes` ADD COLUMN `dias_gracia` TINYINT UNSIGNED NOT NULL DEFAULT 3 AFTER `fecha_vencimiento`");
}

// 2. Migrar precios de planes si la tabla planes existe
$tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
if (in_array('planes', $tables) && in_array('plan_id', $cols)) {
    echo "- Migrando precios de planes a restaurantes.precio...\n";
    $pdo->exec("UPDATE `restaurantes` r 
                LEFT JOIN `planes` p ON r.plan_id = p.id 
                SET r.precio = COALESCE(p.precio_mensual, 49000) 
                WHERE r.precio = 49000 OR r.precio = 0");
    
    echo "- Eliminando foreign key y columna plan_id...\n";
    try {
        $pdo->exec("ALTER TABLE `restaurantes` DROP FOREIGN KEY `fk_restaurantes_plan`");
    } catch (Exception $e) {
        echo "  (Foreign key ya no existia o tenia otro nombre: {$e->getMessage()})\n";
    }
    
    try {
        $pdo->exec("ALTER TABLE `restaurantes` DROP COLUMN `plan_id`");
    } catch (Exception $e) {
        echo "  (Columna plan_id ya habia sido eliminada)\n";
    }

    echo "- Eliminando tabla planes...\n";
    $pdo->exec("DROP TABLE IF EXISTS `planes`");
}

// 3. Verificar estructura de pagos_suscripcion
if (!in_array('pagos_suscripcion', $tables)) {
    echo "- Creando tabla pagos_suscripcion...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS `pagos_suscripcion` (
        `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `restaurante_id` BIGINT UNSIGNED NOT NULL,
        `fecha_pago` DATE NOT NULL,
        `monto` INT UNSIGNED NOT NULL,
        `metodo` VARCHAR(50) NOT NULL,
        `periodos` INT UNSIGNED NOT NULL DEFAULT 1,
        `fecha_vencimiento_anterior` DATE NULL,
        `fecha_vencimiento_nueva` DATE NOT NULL,
        `referencia` VARCHAR(100) NULL,
        `nota` VARCHAR(500) NULL,
        `registrado_por` BIGINT UNSIGNED NOT NULL,
        `anulado` TINYINT(1) NOT NULL DEFAULT 0,
        `anulado_motivo` VARCHAR(500) NULL,
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        KEY `idx_pagos_restaurante` (`restaurante_id`),
        CONSTRAINT `fk_pagos_restaurante` FOREIGN KEY (`restaurante_id`) REFERENCES `restaurantes` (`id`) ON DELETE CASCADE,
        CONSTRAINT `fk_pagos_usuario` FOREIGN KEY (`registrado_por`) REFERENCES `usuarios` (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
} else {
    $pagoCols = $pdo->query("DESCRIBE pagos_suscripcion")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('referencia', $pagoCols)) {
        echo "- Agregando columna 'referencia' a pagos_suscripcion...\n";
        $pdo->exec("ALTER TABLE `pagos_suscripcion` ADD COLUMN `referencia` VARCHAR(100) NULL AFTER `fecha_vencimiento_nueva`");
    }
}

echo "Migracion 001 completada con exito!\n";
