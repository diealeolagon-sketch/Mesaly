<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Usuario extends Model
{
    protected string $table = 'usuarios';
    protected string $primaryKey = 'id';

    public function findByEmail(string $email): ?array
    {
        $stmt = self::getPdo()->prepare("SELECT * FROM `{$this->table}` WHERE `email` = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findAdminByRestaurante(int $restauranteId): ?array
    {
        $stmt = self::getPdo()->prepare("SELECT * FROM `{$this->table}` WHERE `restaurante_id` = :rid AND `rol` = 'admin_negocio' LIMIT 1");
        $stmt->execute(['rid' => $restauranteId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
