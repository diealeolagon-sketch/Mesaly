<?php

declare(strict_types=1);

namespace App\Services\Factus;

final class FactusToken
{
    public function __construct(
        public readonly string $accessToken,
        public readonly string $refreshToken,
        public readonly string $tokenType,
        public readonly int $expiresAt,
        public readonly ?int $expiresIn = null,
    ) {
    }

    public function isUsable(int $now, int $leeway): bool
    {
        return $this->accessToken !== '' && $this->expiresAt > ($now + $leeway);
    }

    public function toArray(): array
    {
        return [
            'access_token' => $this->accessToken,
            'refresh_token' => $this->refreshToken,
            'token_type' => $this->tokenType,
            'expires_at' => $this->expiresAt,
        ];
    }

    public static function fromArray(array $data): ?self
    {
        if (!is_string($data['access_token'] ?? null) || !is_int($data['expires_at'] ?? null)) {
            return null;
        }
        return new self(
            $data['access_token'],
            is_string($data['refresh_token'] ?? null) ? $data['refresh_token'] : '',
            is_string($data['token_type'] ?? null) ? $data['token_type'] : 'Bearer',
            $data['expires_at'],
        );
    }
}
