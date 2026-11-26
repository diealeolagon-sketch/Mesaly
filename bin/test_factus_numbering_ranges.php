<?php

declare(strict_types=1);

use App\Services\Factus\FactusConfigurationException;
use App\Services\Factus\FactusConnectionException;
use App\Services\Factus\FactusException;
use App\Services\Factus\FactusFactory;
use App\Services\Factus\FactusInvalidResponseException;
use App\Services\Factus\FactusTimeoutException;
use Dotenv\Dotenv;
use Dotenv\Exception\InvalidFileException;
use Dotenv\Exception\InvalidPathException;

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit(1);
}

ini_set('display_errors', '0');
ini_set('log_errors', '0');

$root = dirname(__DIR__);
if (PHP_VERSION_ID < 80200 || !extension_loaded('curl') || !extension_loaded('openssl')) {
    fwrite(STDERR, "Se requiere PHP 8.2 o superior con cURL y OpenSSL habilitados. Revisa php --ini.\n");
    exit(1);
}
if (!is_file($root . '/vendor/autoload.php')) {
    fwrite(STDERR, "Faltan dependencias de Composer. Ejecuta composer install desde la raíz de Mesaly.\n");
    exit(1);
}

try {
    require $root . '/vendor/autoload.php';
    Dotenv::createImmutable($root)->load();
    $ranges = FactusFactory::numberingRangeService()->listInvoiceRanges();
} catch (InvalidFileException|InvalidPathException|InvalidArgumentException|FactusConfigurationException) {
    fwrite(STDERR, "Revisa .env, la configuración de Factus Sandbox y el acceso a la caché cifrada.\n");
    exit(1);
} catch (FactusException $exception) {
    $status = $exception->httpStatus;
    $message = match (true) {
        $exception instanceof FactusTimeoutException => 'Se agotó el tiempo de espera. Revisa la red y el proxy.',
        $exception instanceof FactusConnectionException => 'Falló la conexión HTTPS. Revisa DNS, proxy y certificados CA de PHP.',
        $status === 401 || $status === 403 => 'Factus denegó el acceso. Revisa las credenciales y los permisos de Sandbox.',
        $status === 429 => 'Factus limitó las solicitudes. Espera antes de reintentar.',
        $status !== null && $status >= 500 => 'Factus Sandbox no está disponible. Reintenta más tarde.',
        $exception instanceof FactusInvalidResponseException => $exception->getMessage(),
        default => 'Factus rechazó la solicitud. Revisa la configuración de Sandbox.',
    };
    fwrite(STDERR, ($status === null ? '' : "HTTP $status. ") . $message . PHP_EOL);
    exit(1);
} catch (Throwable) {
    fwrite(STDERR, "Error local inesperado. Revisa PHP, Composer y los permisos del proyecto.\n");
    exit(1);
}

$available = count(array_filter($ranges, static fn (array $range): bool => $range['is_available']));
fwrite(STDOUT, sprintf("Factus Sandbox | Factura electrónica de venta | %d rango(s), %d disponible(s).\n", count($ranges), $available));

foreach ($ranges as $range) {
    $state = match (true) {
        $range['deleted_at'] !== null => 'Eliminado',
        $range['is_expired'] === true => 'Vencido',
        $range['is_active'] === false => 'Inactivo',
        $range['is_exhausted'] => 'Agotado',
        $range['is_available'] => 'Activo y vigente',
        default => 'Sin confirmar',
    };
    $fields = [
        'ID' => $range['id'],
        'Tipo de documento' => $range['document_name'] ?? $range['document'],
        'Prefijo' => $range['prefix'],
        'Número de resolución' => $range['resolution_number'],
        'Número inicial' => $range['from'],
        'Número final' => $range['to'],
        'Consecutivo actual' => $range['current'],
        'Fecha de inicio' => $range['start_date'],
        'Fecha de vencimiento' => $range['end_date'],
        'Estado' => $state,
    ];
    fwrite(STDOUT, PHP_EOL);
    foreach ($fields as $label => $value) {
        $value = preg_replace('/[\x00-\x1F\x7F]/', '', (string)$value);
        fwrite(STDOUT, $label . ': ' . ($value === '' ? 'No informado' : $value) . PHP_EOL);
    }
}
