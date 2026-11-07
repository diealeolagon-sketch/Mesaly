<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class OpcionModificador extends Model
{
    protected string $table = 'opciones_modificadores';
    protected string $primaryKey = 'id';

    public function findScopedByTenant(int $id, int $tenantId): ?array
    {
        $sql = "SELECT o.* 
                FROM `{$this->table}` o
                INNER JOIN `grupos_modificadores` g ON o.grupo_id = g.id
                WHERE o.id = :id 
                  AND g.restaurante_id = :tid 
                  AND o.deleted_at IS NULL 
                  AND g.deleted_at IS NULL
                LIMIT 1";
        $stmt = self::getPdo()->prepare($sql);
        $stmt->execute(['id' => $id, 'tid' => $tenantId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function deleteScoped(int $id, int $tenantId): bool
    {
        $opcion = $this->findScopedByTenant($id, $tenantId);
        if (!$opcion) {
            return false;
        }

        $stmt = self::getPdo()->prepare("UPDATE `{$this->table}` SET `deleted_at` = :now WHERE `id` = :id");
        return $stmt->execute([
            'now' => date('Y-m-d H:i:s'),
            'id' => $id,
        ]);
    }
}
