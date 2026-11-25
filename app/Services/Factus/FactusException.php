<?php

declare(strict_types=1);

namespace App\Services\Factus;

use RuntimeException;

class FactusException extends RuntimeException
{
    public function __construct(
        string $message,
        public readonly ?int $httpStatus = null,
        public readonly array $details = [],
        public readonly ?int $retryAfter = null,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }
}
