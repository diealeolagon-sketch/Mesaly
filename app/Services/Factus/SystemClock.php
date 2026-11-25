<?php

declare(strict_types=1);

namespace App\Services\Factus;

final class SystemClock implements ClockInterface
{
    public function now(): int
    {
        return time();
    }
}
