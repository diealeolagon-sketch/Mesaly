<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use PDO;

class Mesa extends Model
{
    protected string $table = 'mesas';
    protected string $primaryKey = 'id';

    /**
     * Retorna el conteo total de mesas activas/no borradas del restaurante
     */
    public function contarPorRestaurante(int $restauranteId): int
    {
        $stmt = self::getPdo()->prepare(
            "SELECT COUNT(*) FROM `mesas` WHERE `restaurante_id` = :rid AND `deleted_at` IS NULL"
        );
        $stmt->execute(['rid' => $restauranteId]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Retorna todas las mesas del restaurante especificado
     *
     * @return array<array<string, mixed>>
     */
    public function allByRestaurante(int $restauranteId, bool $soloActivas = false): array
    {
        $sql = "SELECT * FROM `mesas` 
                WHERE `restaurante_id` = :rid AND `deleted_at` IS NULL";
        
        if ($soloActivas) {
            $sql .= " AND `activa` = 1";
        }

        $sql .= " ORDER BY `numero` ASC";

        $stmt = self::getPdo()->prepare($sql);
        $stmt->execute(['rid' => $restauranteId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Busca una mesa por ID asegurando pertenencia al tenant
     */
    public function findByIdAndRestaurante(int $id, int $restauranteId): ?array
    {
        $stmt = self::getPdo()->prepare(
            "SELECT * FROM `mesas` 
             WHERE `id` = :id AND `restaurante_id` = :rid AND `deleted_at` IS NULL 
             LIMIT 1"
        );
        $stmt->execute(['id' => $id, 'rid' => $restauranteId]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res ?: null;
    }

    /**
     * Busca una mesa por número dentro del restaurante
     */
    public function findByNumero(int $restauranteId, int $numero, bool $incluirBorradas = false): ?array
    {
        $sql = "SELECT * FROM `mesas` WHERE `restaurante_id` = :rid AND `numero` = :num";
        if (!$incluirBorradas) {
            $sql .= " AND `deleted_at` IS NULL";
        }
        $sql .= " LIMIT 1";

        $stmt = self::getPdo()->prepare($sql);
        $stmt->execute(['rid' => $restauranteId, 'num' => $numero]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res ?: null;
    }

    /**
     * Crea o restaura una mesa para el restaurante
     */
    public function crear(int $restauranteId, int $numero, ?string $nombre = null): int
    {
        // Verificar si ya existe (incluso borrada lógicamente) para respetar UNIQUE KEY
        $existente = $this->findByNumero($restauranteId, $numero, true);

        if ($existente) {
            if ($existente['deleted_at'] === null) {
                throw new \InvalidArgumentException("La mesa número {$numero} ya existe en tu restaurante.");
            }

            // Restaurar mesa borrada previamente
            $stmt = self::getPdo()->prepare(
                "UPDATE `mesas` 
                 SET `nombre` = :nombre, `activa` = 1, `deleted_at` = NULL, `updated_at` = NOW() 
                 WHERE `id` = :id"
            );
            $stmt->execute([
                'nombre' => $nombre ?: null,
                'id' => $existente['id'],
            ]);
            return (int)$existente['id'];
        }

        $stmt = self::getPdo()->prepare(
            "INSERT INTO `mesas` (`restaurante_id`, `numero`, `nombre`, `activa`, `created_at`, `updated_at`) 
             VALUES (:rid, :num, :nombre, 1, NOW(), NOW())"
        );
        $stmt->execute([
            'rid' => $restauranteId,
            'num' => $numero,
            'nombre' => $nombre ?: null,
        ]);

        return (int)self::getPdo()->lastInsertId();
    }

    /**
     * Crea un lote de mesas consecutivas (ej: de la 1 a la 10)
     *
     * @return array{creadas: int, existentes: int}
     */
    public function crearLote(int $restauranteId, int $desde, int $hasta): array
    {
        if ($desde < 1 || $hasta < $desde) {
            throw new \InvalidArgumentException("El rango de mesas no es válido.");
        }

        $creadas = 0;
        $existentes = 0;

        for ($num = $desde; $num <= $hasta; $num++) {
            $prev = $this->findByNumero($restauranteId, $num, true);
            if ($prev && $prev['deleted_at'] === null) {
                $existentes++;
                continue;
            }

            $this->crear($restauranteId, $num, null);
            $creadas++;
        }

        return [
            'creadas' => $creadas,
            'existentes' => $existentes,
        ];
    }

    /**
     * Actualiza datos de una mesa
     */
    public function actualizarMesa(int $id, int $restauranteId, int $nuevoNumero, ?string $nombre): bool
    {
        // Validar que el nuevo número no colisione con otra mesa del mismo restaurante
        $colision = $this->findByNumero($restauranteId, $nuevoNumero, true);
        if ($colision && (int)$colision['id'] !== $id) {
            throw new \InvalidArgumentException("El número {$nuevoNumero} ya está ocupado por otra mesa.");
        }

        $stmt = self::getPdo()->prepare(
            "UPDATE `mesas` 
             SET `numero` = :num, `nombre` = :nombre, `updated_at` = NOW() 
             WHERE `id` = :id AND `restaurante_id` = :rid"
        );
        return $stmt->execute([
            'num' => $nuevoNumero,
            'nombre' => $nombre ?: null,
            'id' => $id,
            'rid' => $restauranteId,
        ]);
    }

    /**
     * Alterna estado activa / inactiva
     */
    public function toggleActiva(int $id, int $restauranteId): bool
    {
        $stmt = self::getPdo()->prepare(
            "UPDATE `mesas` 
             SET `activa` = IF(`activa` = 1, 0, 1), `updated_at` = NOW() 
             WHERE `id` = :id AND `restaurante_id` = :rid"
        );
        return $stmt->execute(['id' => $id, 'rid' => $restauranteId]);
    }

    /**
     * Borrado lógico de mesa
     */
    public function eliminarLogico(int $id, int $restauranteId): bool
    {
        $stmt = self::getPdo()->prepare(
            "UPDATE `mesas` 
             SET `deleted_at` = NOW(), `activa` = 0 
             WHERE `id` = :id AND `restaurante_id` = :rid"
        );
        return $stmt->execute(['id' => $id, 'rid' => $restauranteId]);
    }

    /**
     * Cuenta mesas activas del restaurante
     */
    public function countActivas(int $restauranteId): int
    {
        $stmt = self::getPdo()->prepare(
            "SELECT COUNT(*) FROM `mesas` 
             WHERE `restaurante_id` = :rid AND `activa` = 1 AND `deleted_at` IS NULL"
        );
        $stmt->execute(['rid' => $restauranteId]);
        return (int)$stmt->fetchColumn();
    }
}
