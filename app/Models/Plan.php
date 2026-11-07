<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Plan extends Model
{
    protected string $table = 'planes';
    protected string $primaryKey = 'id';

    public function allActive(): array
    {
        $stmt = self::getPdo()->query("SELECT * FROM `{$this->table}` WHERE `activo` = 1 ORDER BY `precio_mensual` ASC");
        return $stmt->fetchAll();
    }
}
