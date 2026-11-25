<?php

declare(strict_types=1);

namespace App\Services\Factus;

interface TokenStoreInterface
{
    public function load(): ?FactusToken;

    public function save(FactusToken $token): void;

    public function clear(): void;
}
