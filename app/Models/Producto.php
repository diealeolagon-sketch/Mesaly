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

    /**
     * Obtiene todos los productos activos de un restaurante con sus grupos de modificadores y opciones para el menú público.
     */
    public function allForPublicCatalog(int $restauranteId): array
    {
        $pdo = self::getPdo();

        // 1. Obtener productos activos (no eliminados)
        $sql = "SELECT p.*, c.nombre AS categoria_nombre 
                FROM `{$this->table}` p
                INNER JOIN `categorias` c ON p.categoria_id = c.id
                WHERE p.restaurante_id = :tid 
                  AND p.deleted_at IS NULL 
                  AND c.deleted_at IS NULL
                  AND c.activa = 1
                ORDER BY c.orden ASC, p.orden ASC, p.nombre ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['tid' => $restauranteId]);
        $productos = $stmt->fetchAll();

        if (empty($productos)) {
            return [];
        }

        // 2. Obtener grupos y opciones para este restaurante
        $stmtGrupos = $pdo->prepare("
            SELECT pg.producto_id, g.id AS grupo_id, g.nombre AS grupo_nombre, g.tipo, g.obligatorio, g.min_selecciones, g.max_selecciones,
                   o.id AS opcion_id, o.nombre AS opcion_nombre, o.precio_extra, o.orden AS opcion_orden
            FROM `producto_grupos` pg
            INNER JOIN `grupos_modificadores` g ON pg.grupo_id = g.id AND g.deleted_at IS NULL
            LEFT JOIN `opciones_modificadores` o ON o.grupo_id = g.id AND o.deleted_at IS NULL AND o.activa = 1
            INNER JOIN `productos` p ON pg.producto_id = p.id
            WHERE p.restaurante_id = :tid
            ORDER BY pg.orden ASC, g.id ASC, o.orden ASC, o.nombre ASC
        ");
        $stmtGrupos->execute(['tid' => $restauranteId]);
        $rows = $stmtGrupos->fetchAll();

        // Organizar grupos y opciones indexados por producto_id
        $modificadoresPorProducto = [];
        foreach ($rows as $r) {
            $pid = (int)$r['producto_id'];
            $gid = (int)$r['grupo_id'];

            if (!isset($modificadoresPorProducto[$pid][$gid])) {
                $modificadoresPorProducto[$pid][$gid] = [
                    'id' => $gid,
                    'nombre' => $r['grupo_nombre'],
                    'tipo' => $r['tipo'],
                    'obligatorio' => (int)$r['obligatorio'],
                    'min_selecciones' => (int)$r['min_selecciones'],
                    'max_selecciones' => (int)$r['max_selecciones'],
                    'opciones' => [],
                ];
            }

            if (!empty($r['opcion_id'])) {
                $modificadoresPorProducto[$pid][$gid]['opciones'][] = [
                    'id' => (int)$r['opcion_id'],
                    'nombre' => $r['opcion_nombre'],
                    'precio_extra' => (int)$r['precio_extra'],
                ];
            }
        }

        // Asignar grupos ordenados a cada producto
        foreach ($productos as &$p) {
            $pid = (int)$p['id'];
            $p['grupos'] = isset($modificadoresPorProducto[$pid]) ? array_values($modificadoresPorProducto[$pid]) : [];
        }

        return $productos;
    }
}
