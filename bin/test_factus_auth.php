<?php

declare(strict_types=1);

use App\Services\Factus\FactusException;
use App\Services\Factus\FactusFactory;
use App\Services\Factus\FactusConfig;
use App\Services\Factus\FactusConfigurationException;
use App\Services\Factus\FactusConnectionException;
use App\Services\Factus\FactusTimeoutException;

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit(1);
}

ini_set('display_errors', '0');
ini_set('log_errors', '0');

function fail(string $message): never
{
    fwrite(STDERR, "Error: $message\n");
    exit(1);
}

if (PHP_VERSION_ID < 80200) {
    fail('Se requiere PHP 8.2 o superior. Comprueba la versión con php -v.');
}

$missing = array_filter(['curl', 'openssl', 'json', 'ctype'], static fn (string $name): bool => !extension_loaded($name));
if ($missing !== []) {
    fail('Faltan extensiones PHP: ' . implode(', ', $missing) . '. Ejecuta php --ini y habilítalas en el PHP de terminal.');
}

$root = dirname(__DIR__);
if (!is_file($root . '/vendor/autoload.php')) {
    fail('Faltan las dependencias. Ejecuta composer install desde la raíz de Mesaly.');
}

try {
    require $root . '/vendor/autoload.php';
    if (!class_exists(Dotenv\Dotenv::class) || !class_exists(FactusFactory::class)) {
        fail('La instalación de Composer está incompleta. Ejecuta composer install.');
    }
    fwrite(STDOUT, "PHP y dependencias disponibles.\n");

    if (!is_readable($root . '/.env')) {
        fail('No se puede leer .env. Revisa que exista en la raíz de Mesaly y que tengas permisos de lectura.');
    }
    Dotenv\Dotenv::createImmutable($root)->load();

    $settings = require $root . '/config/factus.php';
    FactusConfig::fromArray($settings)->assertCredentialsConfigured();
    $auth = FactusFactory::authService($settings);
    fwrite(STDOUT, "Configuración cargada. Conectando a Factus Sandbox por HTTPS...\n");

    $token = $auth->authenticateWithCredentials();
    fwrite(STDOUT, "Autenticación exitosa en Factus Sandbox.\n");
    fwrite(STDOUT, "Token de acceso recibido y guardado en la caché cifrada.\n");
    fwrite(STDOUT, "Vigencia informada por la API: {$token->expiresIn} segundos.\n");
    fwrite(STDOUT, 'Vencimiento estimado (UTC): ' . gmdate('Y-m-d H:i:s', $token->expiresAt) . "\n");
} catch (Dotenv\Exception\InvalidFileException) {
    fail('Revisa la sintaxis y las comillas de .env. Guarda el archivo en UTF-8.');
} catch (InvalidArgumentException|FactusConfigurationException $e) {
    fail($e->getMessage() . ' Revisa .env y, si corresponde, los permisos de storage/cache.');
} catch (FactusTimeoutException) {
    fail('Se agotó el tiempo de espera. Revisa la red y el proxy, y vuelve a intentar.');
} catch (FactusConnectionException $e) {
    fail(match ($e->details['curl_errno'] ?? null) {
        5, 6 => 'Falló la resolución DNS. Revisa el DNS y la configuración del proxy.',
        35, 51, 58, 59, 60, 77, 83, 90, 91 => 'Falló TLS. Revisa el reloj y configura certificados CA confiables en curl.cainfo del PHP de terminal.',
        default => 'Falló la conexión HTTPS. Revisa la red, el firewall y el proxy.',
    });
} catch (FactusException $e) {
    $status = $e->httpStatus;
    $message = match (true) {
        in_array($status, [400, 401, 422], true) => 'Factus rechazó la autenticación. Revisa las cuatro credenciales Sandbox en .env.',
        $status === 403 => 'Factus denegó el acceso. Verifica que el cliente Sandbox esté habilitado.',
        $status === 429 => 'Factus limitó las solicitudes. Espera antes de reintentar.',
        $status !== null && $status >= 500 => 'Factus Sandbox está temporalmente indisponible. Reintenta más tarde.',
        default => 'Factus devolvió una respuesta inesperada. Reintenta y, si persiste, contacta a soporte de Factus.',
    };
    fail(($status === null ? '' : "HTTP $status. ") . $message);
} catch (Throwable) {
    fail('Ocurrió un error local inesperado. Revisa PHP, Composer y los permisos del proyecto.');
}
