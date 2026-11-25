<?php

declare(strict_types=1);

namespace App\Services\Factus;

interface FactusClientInterface
{
    public function requestJson(string $method, string $path, array $data = [], ?string $accessToken = null): array;

    public function requestForm(string $method, string $path, array $data, ?string $accessToken = null): array;
}
