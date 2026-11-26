<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit(1);
}

try {
    require dirname(__DIR__) . '/vendor/autoload.php';
    Dotenv\Dotenv::createImmutable(dirname(__DIR__))->load();
    $pdo = App\Core\Model::getPdo();
    $sql = file_get_contents(dirname(__DIR__) . '/database/migrations/002_facturacion_electronica.sql');
    foreach (explode(';', $sql) as $statement) {
        if (trim($statement) !== '') {
            $pdo->exec($statement);
        }
    }
    fwrite(STDOUT, "Tablas de facturación verificadas.\n");
} catch (Throwable) {
    fwrite(STDERR, "No se pudo aplicar la migración. Revisa la conexión y los permisos de la base de datos.\n");
    exit(1);
}
