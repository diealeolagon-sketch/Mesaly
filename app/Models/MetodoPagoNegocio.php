<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\TenantModel;

class MetodoPagoNegocio extends TenantModel
{
    protected string $table = 'metodos_pago_negocio';
    protected string $primaryKey = 'id';
    protected bool $usesSoftDeletes = false;

    public function getMetodos(): array
    {
        $tenantId = $this->getTenantId();
        return $this->getMetodosByRestaurantId($tenantId);
    }

    public function getMetodosByRestaurantId(int $restauranteId): array
    {
        $stmt = self::getPdo()->prepare("SELECT metodo FROM `{$this->table}` WHERE `restaurante_id` = :tid");
        $stmt->execute(['tid' => $restauranteId]);
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function syncMetodos(array $metodos): void
    {
        $tenantId = $this->getTenantId();
        $pdo = self::getPdo();
        $pdo->prepare("DELETE FROM `{$this->table}` WHERE `restaurante_id` = :tid")->execute(['tid' => $tenantId]);

        $permitidos = ['efectivo', 'transferencia', 'datafono'];
        $stmt = $pdo->prepare("INSERT INTO `{$this->table}` (`restaurante_id`, `metodo`) VALUES (:tid, :m)");
        foreach ($metodos as $m) {
            if (in_array($m, $permitidos, true)) {
                $stmt->execute(['tid' => $tenantId, 'm' => $m]);
            }
        }
    }
}
