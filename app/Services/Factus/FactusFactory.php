<?php

declare(strict_types=1);

namespace App\Services\Factus;

use App\Services\Factus\Http\CurlHttpTransport;

final class FactusFactory
{
    public static function authService(?array $settings = null): FactusAuthService
    {
        $config = self::config($settings);
        return self::buildAuthService($config, self::client($config));
    }

    public static function numberingRangeService(?array $settings = null): FactusNumberingRangeService
    {
        return new FactusNumberingRangeService(self::authenticatedClient($settings));
    }

    public static function authenticatedClient(?array $settings = null): AuthenticatedFactusClient
    {
        $config = self::config($settings);
        $client = self::client($config);
        $auth = self::buildAuthService($config, $client);
        return new AuthenticatedFactusClient($client, $auth);
    }

    private static function config(?array $settings): FactusConfig
    {
        $settings ??= require dirname(__DIR__, 3) . '/config/factus.php';
        return FactusConfig::fromArray($settings);
    }

    private static function client(FactusConfig $config): FactusClient
    {
        $transport = new CurlHttpTransport($config->connectTimeout, $config->timeout);
        return new FactusClient($config, $transport);
    }

    private static function buildAuthService(FactusConfig $config, FactusClient $client): FactusAuthService
    {
        $store = new EncryptedFileTokenStore($config->tokenCachePath, $config->tokenEncryptionKey);
        return new FactusAuthService($config, $client, $store, new SystemClock(), $config->authLockPath);
    }
}
