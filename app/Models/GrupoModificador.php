<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\TenantModel;

class GrupoModificador extends TenantModel
{
    protected string $table = 'grupos_modificadores';
    protected string $primaryKey = 'id';
    protected bool $usesSoftDeletes = true;

    public function allWithOptions(): array
    {
        $tenantId = $this->getTenantId();
        $grupos = $this->all();

        if (empty($grupos)) {
            return [];
        }

        $pdo = self::getPdo();
        foreach ($grupos as &$grupo) {
            $stmt = $pdo->prepare("SELECT * FROM `opciones_modificadores` 
                                   WHERE `grupo_id` = :gid AND `deleted_at` IS NULL 
                                   ORDER BY `orden` ASC, `nombre` ASC");
            $stmt->execute(['gid' => $grupo['id']]);
            $grupo['opciones'] = $stmt->fetchAll();
        }

        return $grupos;
    }
}
