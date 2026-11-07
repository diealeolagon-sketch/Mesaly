<?php

declare(strict_types=1);

if (php_sapi_name() !== 'cli') {
    die("Este script solo puede ejecutarse desde la línea de comandos (CLI).\n");
}

require_once dirname(__DIR__) . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();

use App\Core\Auth;
use App\Core\Model;

echo "=====================================================\n";
echo "       MESALY — Creación de Usuario Super Admin      \n";
echo "=====================================================\n\n";

$args = $argv;
array_shift($args); // Quitar nombre de script

$nombre = $args[0] ?? null;
$email = $args[1] ?? null;
$password = $args[2] ?? null;

if (!$nombre) {
    echo "Ingrese el nombre completo [Super Administrador]: ";
    $input = trim((string)fgets(STDIN));
    $nombre = $input !== '' ? $input : 'Super Administrador';
}

if (!$email) {
    do {
        echo "Ingrese el correo electrónico: ";
        $email = trim((string)fgets(STDIN));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo " [!] Correo electrónico no válido. Intente de nuevo.\n";
            $email = null;
        }
    } while (!$email);
}

if (!$password) {
    do {
        echo "Ingrese la contraseña (mínimo 10 caracteres): ";
        $password = trim((string)fgets(STDIN));
        if (strlen($password) < 10) {
            echo " [!] La contraseña debe tener al menos 10 caracteres.\n";
            $password = null;
        }
    } while (!$password);
}

if (strlen($password) < 10) {
    fwrite(STDERR, "Error: La contraseña debe tener al menos 10 caracteres.\n");
    exit(1);
}

try {
    $pdo = Model::getPdo();
    
    // Verificar si el correo ya existe
    $stmt = $pdo->prepare("SELECT `id`, `rol` FROM `usuarios` WHERE `email` = :email LIMIT 1");
    $stmt->execute(['email' => $email]);
    $existente = $stmt->fetch();

    $hash = Auth::hashPassword($password);
    $now = date('Y-m-d H:i:s');

    if ($existente) {
        $updateStmt = $pdo->prepare("UPDATE `usuarios` 
            SET `nombre` = :nombre,
                `password_hash` = :hash,
                `rol` = :rol,
                `restaurante_id` = NULL,
                `debe_cambiar_password` = 0,
                `activo` = 1,
                `updated_at` = :now
            WHERE `id` = :id");
        $updateStmt->execute([
            'nombre' => $nombre,
            'hash' => $hash,
            'rol' => Auth::ROLE_SUPER_ADMIN,
            'now' => $now,
            'id' => $existente['id'],
        ]);
        echo "\n[✓] Usuario existente actualizado exitosamente como Super Admin.\n";
    } else {
        $insertStmt = $pdo->prepare("INSERT INTO `usuarios` 
            (`restaurante_id`, `nombre`, `email`, `password_hash`, `rol`, `debe_cambiar_password`, `activo`, `created_at`, `updated_at`) 
            VALUES (NULL, :nombre, :email, :hash, :rol, 0, 1, :created_at, :updated_at)");
        $insertStmt->execute([
            'nombre' => $nombre,
            'email' => $email,
            'hash' => $hash,
            'rol' => Auth::ROLE_SUPER_ADMIN,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        echo "\n[✓] Super Admin creado exitosamente en la base de datos.\n";
    }

    echo "    Nombre: {$nombre}\n";
    echo "    Email:  {$email}\n";
    echo "    Rol:    super_admin\n\n";

} catch (\Throwable $e) {
    fwrite(STDERR, "\n[X] Error creando Super Admin: " . $e->getMessage() . "\n");
    exit(1);
}
