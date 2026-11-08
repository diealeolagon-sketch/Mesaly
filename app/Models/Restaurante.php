<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use App\Services\SuscripcionService;

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

    /**
     * Obtiene todos los negocios con la información de su último pago
     * y cálculo de estado en tiempo real para la pantalla Vencimientos de Super Admin.
     */
    public function allForVencimientos(?string $filtro = null, ?string $busqueda = null): array
    {
        $sql = "SELECT r.*,
                       u.email AS admin_email,
                       u.id AS admin_usuario_id,
                       u.nombre AS admin_nombre,
                       p.fecha_pago AS ultimo_pago_fecha,
                       p.monto AS ultimo_pago_monto,
                       p.metodo AS ultimo_pago_metodo
                FROM `{$this->table}` r
                LEFT JOIN `usuarios` u ON u.restaurante_id = r.id AND u.rol = 'admin_negocio' AND u.activo = 1
                LEFT JOIN `pagos_suscripcion` p ON p.id = (
                    SELECT ps.id FROM `pagos_suscripcion` ps 
                    WHERE ps.restaurante_id = r.id AND ps.anulado = 0 
                    ORDER BY ps.fecha_pago DESC, ps.id DESC LIMIT 1
                )
                WHERE r.deleted_at IS NULL";

        $params = [];

        if (!empty($busqueda)) {
            $sql .= " AND (r.nombre LIKE :busq OR r.slug LIKE :busq2)";
            $params['busq'] = "%{$busqueda}%";
            $params['busq2'] = "%{$busqueda}%";
        }

        $sql .= " ORDER BY (r.fecha_vencimiento IS NULL) ASC, r.fecha_vencimiento ASC, r.nombre ASC";

        $stmt = self::getPdo()->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        // Calcular estado en tiempo real
        $resultado = [];
        foreach ($rows as $row) {
            $row['estado_suscripcion'] = SuscripcionService::estadoDe($row);

            // Filtrado en memoria según el filtro seleccionado
            if (!empty($filtro)) {
                $codigo = $row['estado_suscripcion']['codigo'];
                if ($filtro === 'semana' && !($row['estado_suscripcion']['por_vencer'] ?? false)) {
                    continue;
                }
                if ($filtro === 'gracia' && $codigo !== SuscripcionService::ESTADO_EN_GRACIA) {
                    continue;
                }
                if ($filtro === 'bloqueados' && $codigo !== SuscripcionService::ESTADO_BLOQUEADO) {
                    continue;
                }
                if ($filtro === 'cancelados' && $codigo !== SuscripcionService::ESTADO_CANCELADO) {
                    continue;
                }
                if ($filtro === 'al_dia' && $codigo !== SuscripcionService::ESTADO_AL_DIA) {
                    continue;
                }
            }

            $resultado[] = $row;
        }

        return $resultado;
    }
}
