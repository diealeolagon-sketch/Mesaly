<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\TenantModel;

class Categoria extends TenantModel
{
    protected string $table = 'categorias';
    protected string $primaryKey = 'id';
    protected bool $usesSoftDeletes = true;

    public function allOrdered(): array
    {
        $tenantId = $this->getTenantId();
        $sql = "SELECT * FROM `{$this->table}` 
                WHERE `{$this->tenantColumn}` = :tenant_id AND `deleted_at` IS NULL 
                ORDER BY `orden` ASC, `nombre` ASC";
        $stmt = self::getPdo()->prepare($sql);
        $stmt->execute(['tenant_id' => $tenantId]);
        return $stmt->fetchAll();
    }

    public function allActiveOrdered(): array
    {
        $tenantId = $this->getTenantId();
        $sql = "SELECT * FROM `{$this->table}` 
                WHERE `{$this->tenantColumn}` = :tenant_id AND `activa` = 1 AND `deleted_at` IS NULL 
                ORDER BY `orden` ASC, `nombre` ASC";
        $stmt = self::getPdo()->prepare($sql);
        $stmt->execute(['tenant_id' => $tenantId]);
        return $stmt->fetchAll();
    }
}
