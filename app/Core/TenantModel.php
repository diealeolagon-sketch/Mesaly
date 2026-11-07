<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use RuntimeException;

abstract class TenantModel extends Model
{
    protected string $tenantColumn = 'restaurante_id';
    protected bool $usesSoftDeletes = true;

    protected function getTenantId(): int
    {
        $id = TenantContext::getTenantId();
        if ($id === null) {
            throw new RuntimeException("Operación multitenant denegada: no hay un restaurante activo en el contexto.");
        }
        return $id;
    }

    public function find(int $id): ?array
    {
        $tenantId = $this->getTenantId();
        $softDeleteSql = $this->usesSoftDeletes ? " AND `deleted_at` IS NULL" : "";

        $sql = "SELECT * FROM `{$this->table}` WHERE `{$this->primaryKey}` = :id AND `{$this->tenantColumn}` = :tenant_id{$softDeleteSql} LIMIT 1";
        $stmt = self::getPdo()->prepare($sql);
        $stmt->execute([
            'id' => $id,
            'tenant_id' => $tenantId,
        ]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function all(): array
    {
        $tenantId = $this->getTenantId();
        $softDeleteSql = $this->usesSoftDeletes ? " AND `deleted_at` IS NULL" : "";

        $sql = "SELECT * FROM `{$this->table}` WHERE `{$this->tenantColumn}` = :tenant_id{$softDeleteSql} ORDER BY `{$this->primaryKey}` DESC";
        $stmt = self::getPdo()->prepare($sql);
        $stmt->execute(['tenant_id' => $tenantId]);
        return $stmt->fetchAll();
    }

    public function count(): int
    {
        $tenantId = $this->getTenantId();
        $softDeleteSql = $this->usesSoftDeletes ? " AND `deleted_at` IS NULL" : "";

        $sql = "SELECT COUNT(*) FROM `{$this->table}` WHERE `{$this->tenantColumn}` = :tenant_id{$softDeleteSql}";
        $stmt = self::getPdo()->prepare($sql);
        $stmt->execute(['tenant_id' => $tenantId]);
        return (int)$stmt->fetchColumn();
    }

    public function create(array $data): int
    {
        $data[$this->tenantColumn] = $this->getTenantId();
        return parent::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $tenantId = $this->getTenantId();
        $data['updated_at'] = date('Y-m-d H:i:s');

        $sets = [];
        foreach (array_keys($data) as $field) {
            $sets[] = "`{$field}` = :{$field}";
        }
        $setString = implode(', ', $sets);

        $data['__id'] = $id;
        $data['__tenant_id'] = $tenantId;

        $softDeleteSql = $this->usesSoftDeletes ? " AND `deleted_at` IS NULL" : "";
        $sql = "UPDATE `{$this->table}` SET {$setString} WHERE `{$this->primaryKey}` = :__id AND `{$this->tenantColumn}` = :__tenant_id{$softDeleteSql}";

        $stmt = self::getPdo()->prepare($sql);
        $stmt->execute($data);
        return $stmt->rowCount() > 0;
    }

    public function delete(int $id): bool
    {
        $tenantId = $this->getTenantId();

        if ($this->usesSoftDeletes) {
            $sql = "UPDATE `{$this->table}` SET `deleted_at` = :now WHERE `{$this->primaryKey}` = :id AND `{$this->tenantColumn}` = :tenant_id AND `deleted_at` IS NULL";
            $stmt = self::getPdo()->prepare($sql);
            $stmt->execute([
                'now' => date('Y-m-d H:i:s'),
                'id' => $id,
                'tenant_id' => $tenantId,
            ]);
            return $stmt->rowCount() > 0;
        }

        $sql = "DELETE FROM `{$this->table}` WHERE `{$this->primaryKey}` = :id AND `{$this->tenantColumn}` = :tenant_id";
        $stmt = self::getPdo()->prepare($sql);
        $stmt->execute([
            'id' => $id,
            'tenant_id' => $tenantId,
        ]);
        return $stmt->rowCount() > 0;
    }
}
