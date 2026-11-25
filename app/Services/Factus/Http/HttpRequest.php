<?php

declare(strict_types=1);

namespace App\Services\Factus\Http;

final class HttpRequest
{
    public function __construct(
        public readonly string $method,
        public readonly string $url,
        public readonly array $headers = [],
        public readonly ?string $body = null,
    ) {
    }
}
