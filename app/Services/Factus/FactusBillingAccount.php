<?php

declare(strict_types=1);

namespace App\Services\Factus;

use DomainException;

final class FactusBillingAccount
{
    public const CREDENTIALS = ['username', 'password', 'client_id', 'client_secret'];

    public static function globalAllowed(int $tenant): bool
    {
        return $tenant > 0 && (string)$tenant === ($_ENV['FACTUS_SANDBOX_RESTAURANTE_ID'] ?? '');
    }

    public static function encrypt(int $tenant, array $credentials): string
    {
        $iv = random_bytes(12);
        $tag = '';
        $cipher = openssl_encrypt(json_encode($credentials, JSON_THROW_ON_ERROR), 'aes-256-gcm',
            self::key(), OPENSSL_RAW_DATA, $iv, $tag, self::scope($tenant));
        if ($cipher === false) {
            throw new DomainException('No se pudieron cifrar las credenciales.');
        }
        return base64_encode($iv . $tag . $cipher);
    }

    public static function connect(int $tenant, array $config): array
    {
        if (($config['ambiente'] ?? null) !== 'sandbox') {
            throw new DomainException('La facturación solo está disponible en Sandbox.');
        }
        $settings = require dirname(__DIR__, 3) . '/config/factus.php';
        if (!empty($config['credenciales'])) {
            $bytes = base64_decode($config['credenciales'], true);
            $plain = $bytes !== false && strlen($bytes) > 28
                ? openssl_decrypt(substr($bytes, 28), 'aes-256-gcm', self::key(), OPENSSL_RAW_DATA,
                    substr($bytes, 0, 12), substr($bytes, 12, 16), self::scope($tenant))
                : false;
            if ($plain === false) {
                throw new DomainException('No se pudieron leer las credenciales del restaurante.');
            }
            $credentials = json_decode($plain, true, 16, JSON_THROW_ON_ERROR);
            foreach (self::CREDENTIALS as $name) {
                if (!is_string($credentials[$name] ?? null)) {
                    throw new DomainException('Las credenciales del restaurante están incompletas.');
                }
                $settings[$name] = $credentials[$name];
            }
        } elseif (!self::globalAllowed($tenant)) {
            throw new DomainException('Configura las credenciales Sandbox de este restaurante.');
        }
        $settings['environment'] = 'sandbox';
        $settings['base_url'] = 'https://api-sandbox.factus.com.co';
        FactusConfig::fromArray($settings)->assertCredentialsConfigured();
        $account = hash_hmac('sha256', json_encode([
            self::scope($tenant), $settings['username'], $settings['client_id'],
        ], JSON_THROW_ON_ERROR), self::key());
        $cache = hash_hmac('sha256', $account . json_encode([
            $settings['password'], $settings['client_secret'],
        ], JSON_THROW_ON_ERROR), self::key());
        $path = dirname($settings['token_cache_path']) . '/factus-sandbox-' . $tenant . '-' . $cache;
        $settings['token_cache_path'] = $path . '.enc';
        $settings['auth_lock_path'] = $path . '.lock';
        return [$account, FactusFactory::authenticatedClient($settings)];
    }

    private static function scope(int $tenant): string
    {
        return 'mesaly:factus:sandbox:' . $tenant;
    }

    private static function key(): string
    {
        $encoded = $_ENV['FACTUS_TOKEN_ENCRYPTION_KEY'] ?? '';
        $key = str_starts_with($encoded, 'base64:') ? base64_decode(substr($encoded, 7), true) : false;
        if ($key === false || strlen($key) !== 32) {
            throw new DomainException('Configura la clave de cifrado de Factus.');
        }
        return hash_hmac('sha256', 'mesaly:factus:billing', $key, true);
    }
}
