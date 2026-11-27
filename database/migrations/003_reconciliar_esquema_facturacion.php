<?php

declare(strict_types=1);

use App\Core\Model;
use Dotenv\Dotenv;

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../../vendor/autoload.php';
Dotenv::createImmutable(dirname(__DIR__, 2))->load();
$pdo = Model::getPdo();

$columns = $pdo->query("SELECT COLUMN_NAME FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'facturas_electronicas'")->fetchAll(PDO::FETCH_COLUMN);

if (in_array('clave_idempotencia', $columns, true) && !in_array('referencia', $columns, true)) {
    // No apartar facturas históricas que deban seguir evitando una segunda emisión.
    if ((int)$pdo->query('SELECT EXISTS (SELECT 1 FROM facturas_electronicas)')->fetchColumn() !== 0) {
        throw new RuntimeException('El esquema anterior contiene facturas que requieren reconciliación antes de migrar.');
    }
    $pdo->exec('RENAME TABLE facturas_electronicas TO facturas_electronicas_legacy');
}

foreach (explode(';', file_get_contents(__DIR__ . '/002_facturacion_electronica.sql')) as $statement) {
    if (trim($statement) !== '') {
        $pdo->exec($statement);
    }
}

// MariaDB y MySQL conservan las referencias a la tabla renombrada.
$legacyKeys = $pdo->query("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'facturacion_eventos' AND COLUMN_NAME = 'factura_id'
    AND REFERENCED_TABLE_SCHEMA = DATABASE() AND REFERENCED_TABLE_NAME = 'facturas_electronicas_legacy'")
    ->fetchAll(PDO::FETCH_COLUMN);
foreach ($legacyKeys as $key) {
    $identifier = '`' . str_replace('`', '``', $key) . '`';
    $pdo->exec("ALTER TABLE facturacion_eventos DROP FOREIGN KEY {$identifier},
        ADD FOREIGN KEY (factura_id) REFERENCES facturas_electronicas(id)");
}

echo "Migración 003 de facturación aplicada.\n";
