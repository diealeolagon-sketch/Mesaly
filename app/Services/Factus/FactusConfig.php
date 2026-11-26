<?php

declare(strict_types=1);

namespace App\Services\Factus;

use InvalidArgumentException;

final class FactusConfig
{
    private const SANDBOX_URL = 'https://api-sandbox.factus.com.co';

    public function __construct(
        public readonly string $environment,
        public readonly string $baseUrl,
        public readonly string $clientId,
        public readonly string $clientSecret,
        public readonly string $username,
        public readonly string $password,
        public readonly string $tokenEncryptionKey,
        public readonly int $connectTimeout = 5,
        public readonly int $timeout = 30,
        public readonly int $tokenLeeway = 120,
        public readonly string $tokenCachePath = '',
        public readonly string $authLockPath = '',
    ) {
        if ($environment !== 'sandbox') {
            throw new InvalidArgumentException('Solo está habilitado el ambiente Sandbox de Factus.');
        }

        if (rtrim($baseUrl, '/') !== self::SANDBOX_URL) {
            throw new InvalidArgumentException('La URL de Factus no corresponde al Sandbox permitido.');
        }

        if ($connectTimeout < 1 || $timeout < 1 || $connectTimeout > $timeout) {
            throw new InvalidArgumentException('Los tiempos de espera de Factus son inválidos.');
        }

        if ($tokenLeeway < 0) {
            throw new InvalidArgumentException('El margen de renovación del token es inválido.');
        }
    }

    public static function fromArray(array $config): self
    {
        return new self(
            (string)($config['environment'] ?? 'sandbox'),
            rtrim((string)($config['base_url'] ?? ''), '/'),
            (string)($config['client_id'] ?? ''),
            (string)($config['client_secret'] ?? ''),
            (string)($config['username'] ?? ''),
            (string)($config['password'] ?? ''),
            (string)($config['token_encryption_key'] ?? ''),
            (int)($config['connect_timeout'] ?? 5),
            (int)($config['timeout'] ?? 30),
            (int)($config['token_leeway'] ?? 120),
            (string)($config['token_cache_path'] ?? ''),
            (string)($config['auth_lock_path'] ?? ''),
        );
    }

    public function assertCredentialsConfigured(): void
    {
        $credentials = [
            'FACTUS_USERNAME' => $this->username,
            'FACTUS_PASSWORD' => $this->password,
            'FACTUS_CLIENT_ID' => $this->clientId,
            'FACTUS_CLIENT_SECRET' => $this->clientSecret,
        ];
        $missing = [];
        foreach ($credentials as $name => $value) {
            if (trim($value) === '' || str_starts_with(strtoupper(trim($value)), 'REEMPLAZAR_')) {
                $missing[] = $name;
            }
        }
        if ($missing !== []) {
            throw new FactusConfigurationException('Completa estas variables en .env: ' . implode(', ', $missing) . '.');
        }
    }
}
