<?php

declare(strict_types=1);

namespace App\Services\Factus;

interface ClockInterface
{
    public function now(): int;
}
