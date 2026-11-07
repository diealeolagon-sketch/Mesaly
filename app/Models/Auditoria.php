<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Auth;
use App\Core\Model;

class Auditoria extends Model
{
    protected string $table = 'auditoria';
    protected string $primaryKey = 'id';

    public static function registrar(
        string $accion,
        string $entidad,
        ?int $entidadId = null,
        ?array $antes = null,
        ?array $despues = null,
        ?int $restauranteId = null
    ): void {
        try {
            $pdo = self::getPdo();
            $sql = "INSERT INTO `auditoria` 
                    (`usuario_id`, `restaurante_id`, `accion`, `entidad`, `entidad_id`, `antes`, `despues`, `ip`, `created_at`) 
                    VALUES (:uid, :rid, :accion, :entidad, :eid, :antes, :despues, :ip, :now)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'uid' => Auth::id(),
                'rid' => $restauranteId,
                'accion' => $accion,
                'entidad' => $entidad,
                'eid' => $entidadId,
                'antes' => $antes !== null ? json_encode($antes, JSON_UNESCAPED_UNICODE) : null,
                'despues' => $despues !== null ? json_encode($despues, JSON_UNESCAPED_UNICODE) : null,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
                'now' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable) {
            // No interrumpir el flujo del usuario si la auditoría falla
        }
    }
}
