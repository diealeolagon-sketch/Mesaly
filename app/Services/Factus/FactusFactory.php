<?php

declare(strict_types=1);

namespace App\Services\Factus;

use App\Services\Factus\Http\CurlHttpTransport;

final class FactusFactory
{
    public static function authService(?array $settings = null): FactusAuthService
    {
        $settings ??= require dirname(__DIR__, 3) . '/config/factus.php';
        $config = FactusConfig::fromArray($settings);
        $transport = new CurlHttpTransport($config->connectTimeout, $config->timeout);
        $client = new FactusClient($config, $transport);
        $store = new EncryptedFileTokenStore($config->tokenCachePath, $config->tokenEncryptionKey);
        return new FactusAuthService($config, $client, $store, new SystemClock(), $config->authLockPath);
    }
}
