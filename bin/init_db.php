<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

$host = $_ENV['DB_HOST'] ?? '127.0.0.1';
$port = $_ENV['DB_PORT'] ?? '3306';
$user = $_ENV['DB_USERNAME'] ?? 'root';
$pass = $_ENV['DB_PASSWORD'] ?? 'root';
$dbname = $_ENV['DB_DATABASE'] ?? 'mesaly_db';

try {
    $pdo = new PDO("mysql:host={$host};port={$port}", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbname}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
    echo "Base de datos '{$dbname}' verificada/creada.\n";

    $pdo->exec("USE `{$dbname}`;");
    $schema = file_get_contents(__DIR__ . '/../database/schema.sql');
    $pdo->exec($schema);
    echo "Esquema importado exitosamente.\n";

    if (file_exists(__DIR__ . '/../database/seeds.sql')) {
        $seeds = file_get_contents(__DIR__ . '/../database/seeds.sql');
        $pdo->exec($seeds);
        echo "Seeds iniciales importados exitosamente.\n";
    }
} catch (PDOException $e) {
    fwrite(STDERR, "Error conectando a la BD: " . $e->getMessage() . "\n");
    exit(1);
}
