<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\TenantModel;

class Horario extends TenantModel
{
    protected string $table = 'horarios';
    protected string $primaryKey = 'id';
    protected bool $usesSoftDeletes = false;

    public function allByTenant(): array
    {
        $tenantId = $this->getTenantId();
        return $this->allByRestaurantId($tenantId);
    }

    public function allByRestaurantId(int $restauranteId): array
    {
        $stmt = self::getPdo()->prepare("SELECT * FROM `{$this->table}` WHERE `restaurante_id` = :tid ORDER BY `dia_semana` ASC, `tramo` ASC");
        $stmt->execute(['tid' => $restauranteId]);
        return $stmt->fetchAll();
    }

    public function syncHorarios(array $horarios): void
    {
        $tenantId = $this->getTenantId();
        $pdo = self::getPdo();
        $pdo->prepare("DELETE FROM `{$this->table}` WHERE `restaurante_id` = :tid")->execute(['tid' => $tenantId]);

        $stmt = $pdo->prepare("INSERT INTO `{$this->table}` (`restaurante_id`, `dia_semana`, `abre`, `cierra`, `tramo`, `activo`) 
                               VALUES (:tid, :dia, :abre, :cierra, :tramo, :activo)");
        foreach ($horarios as $h) {
            $stmt->execute([
                'tid' => $tenantId,
                'dia' => (int)$h['dia_semana'],
                'abre' => $h['abre'],
                'cierra' => $h['cierra'],
                'tramo' => (int)($h['tramo'] ?? 1),
                'activo' => !empty($h['activo']) ? 1 : 0,
            ]);
        }
    }
}
