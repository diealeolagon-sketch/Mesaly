<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Restaurante extends Model
{
    protected string $table = 'restaurantes';
    protected string $primaryKey = 'id';

    public function findBySlug(string $slug): ?array
    {
        $stmt = self::getPdo()->prepare("SELECT * FROM `{$this->table}` WHERE `slug` = :slug AND `deleted_at` IS NULL LIMIT 1");
        $stmt->execute(['slug' => $slug]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function isSlugAvailable(string $slug, ?int $ignoreId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM `{$this->table}` WHERE `slug` = :slug";
        $params = ['slug' => $slug];

        if ($ignoreId !== null) {
            $sql .= " AND `id` != :id";
            $params['id'] = $ignoreId;
        }

        $stmt = self::getPdo()->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn() === 0;
    }

    public function allWithPlan(): array
    {
        $sql = "SELECT r.*, p.nombre AS plan_nombre, p.precio_mensual, p.precio_anual
                FROM `{$this->table}` r
                LEFT JOIN `planes` p ON r.plan_id = p.id
                WHERE r.deleted_at IS NULL
                ORDER BY r.created_at DESC";
        return self::getPdo()->query($sql)->fetchAll();
    }
}
