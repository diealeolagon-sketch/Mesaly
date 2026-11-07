<?php

declare(strict_types=1);

namespace App\Core;

class TenantContext
{
    private static ?int $tenantId = null;
    private static ?array $tenant = null;

    public static function setTenantId(?int $id): void
    {
        self::$tenantId = $id;
    }

    public static function getTenantId(): ?int
    {
        return self::$tenantId;
    }

    public static function setTenant(?array $tenant): void
    {
        self::$tenant = $tenant;
        self::$tenantId = isset($tenant['id']) ? (int)$tenant['id'] : null;
    }

    public static function getTenant(): ?array
    {
        return self::$tenant;
    }

    public static function clear(): void
    {
        self::$tenantId = null;
        self::$tenant = null;
    }
}
