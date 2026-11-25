<?php

declare(strict_types=1);

namespace App\Services\Factus;

final class SensitiveDataRedactor
{
    private const SENSITIVE_KEYS = [
        'authorization', 'password', 'client_secret', 'access_token', 'refresh_token', 'token',
    ];

    public static function redact(array $data): array
    {
        $clean = [];
        foreach ($data as $key => $value) {
            $normalized = strtolower(str_replace('-', '_', (string)$key));
            if (in_array($normalized, self::SENSITIVE_KEYS, true)) {
                $clean[$key] = '[REDACTED]';
                continue;
            }
            $clean[$key] = is_array($value) ? self::redact($value) : self::redactString($value);
        }
        return $clean;
    }

    private static function redactString(mixed $value): mixed
    {
        if (!is_string($value)) {
            return $value;
        }
        return preg_replace('/Bearer\s+[A-Za-z0-9._~+\/=\-]+/i', 'Bearer [REDACTED]', $value);
    }
}
