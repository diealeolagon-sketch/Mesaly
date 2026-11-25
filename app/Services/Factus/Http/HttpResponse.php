<?php

declare(strict_types=1);

namespace App\Services\Factus\Http;

final class HttpResponse
{
    public function __construct(
        public readonly int $statusCode,
        public readonly array $headers,
        public readonly string $body,
    ) {
    }

    public function header(string $name): ?string
    {
        $normalized = strtolower($name);
        return isset($this->headers[$normalized]) ? (string)$this->headers[$normalized] : null;
    }
}
