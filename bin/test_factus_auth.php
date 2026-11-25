<?php

declare(strict_types=1);

use App\Services\Factus\FactusException;
use App\Services\Factus\FactusFactory;

require dirname(__DIR__) . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "Este comando solo puede ejecutarse desde CLI.\n");
    exit(1);
}

try {
    FactusFactory::authService()->getAccessToken(true);
    fwrite(STDOUT, "Autenticación Factus Sandbox exitosa. El token se almacenó cifrado y no fue mostrado.\n");
    exit(0);
} catch (FactusException $e) {
    fwrite(STDERR, "No fue posible autenticar con Factus Sandbox: {$e->getMessage()}\n");
    exit(1);
} catch (Throwable) {
    fwrite(STDERR, "No fue posible autenticar con Factus Sandbox por un error local inesperado.\n");
    exit(1);
}
