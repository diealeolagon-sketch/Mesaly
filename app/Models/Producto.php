<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\TenantModel;

class Producto extends TenantModel
{
    protected string $table = 'productos';
    protected string $primaryKey = 'id';
    protected bool $usesSoftDeletes = true;

    public function allWithCategory(): array
    {
        $tenantId = $this->getTenantId();
        $sql = "SELECT p.*, c.nombre AS categoria_nombre 
                FROM `{$this->table}` p
                INNER JOIN `categorias` c ON p.categoria_id = c.id
                WHERE p.restaurante_id = :tenant_id 
                  AND p.deleted_at IS NULL 
                  AND c.deleted_at IS NULL
                ORDER BY c.orden ASC, p.orden ASC, p.nombre ASC";
        $stmt = self::getPdo()->prepare($sql);
        $stmt->execute(['tenant_id' => $tenantId]);
        return $stmt->fetchAll();
    }

    public function findWithDetails(int $id): ?array
    {
        $tenantId = $this->getTenantId();
        $sql = "SELECT p.*, c.nombre AS categoria_nombre 
                FROM `{$this->table}` p
                INNER JOIN `categorias` c ON p.categoria_id = c.id
                WHERE p.id = :id 
                  AND p.restaurante_id = :tenant_id 
                  AND p.deleted_at IS NULL 
                  AND c.deleted_at IS NULL 
                LIMIT 1";
        $stmt = self::getPdo()->prepare($sql);
        $stmt->execute(['id' => $id, 'tenant_id' => $tenantId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getGroupIds(int $productoId): array
    {
        $sql = "SELECT grupo_id FROM `producto_grupos` WHERE `producto_id` = :pid ORDER BY `orden` ASC";
        $stmt = self::getPdo()->prepare($sql);
        $stmt->execute(['pid' => $productoId]);
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function syncGroups(int $productoId, array $groupIds): void
    {
        $pdo = self::getPdo();
        $pdo->prepare("DELETE FROM `producto_grupos` WHERE `producto_id` = :pid")->execute(['pid' => $productoId]);

        if (empty($groupIds)) {
            return;
        }

        $stmt = $pdo->prepare("INSERT INTO `producto_grupos` (`producto_id`, `grupo_id`, `orden`) VALUES (:pid, :gid, :orden)");
        $orden = 1;
        foreach ($groupIds as $gid) {
            $stmt->execute([
                'pid' => $productoId,
                'gid' => (int)$gid,
                'orden' => $orden++,
            ]);
        }
    }
}
