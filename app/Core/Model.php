<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;

abstract class Model
{
    protected static ?PDO $pdo = null;
    protected string $table;
    protected string $primaryKey = 'id';

    public static function getPdo(): PDO
    {
        if (self::$pdo === null) {
            $config = require dirname(__DIR__, 2) . '/config/database.php';
            $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}";

            try {
                self::$pdo = new PDO($dsn, $config['username'], $config['password'], $config['options']);
            } catch (PDOException $e) {
                Logger::error("Database connection error: " . $e->getMessage());
                throw new \RuntimeException("No se pudo conectar a la base de datos.");
            }
        }
        return self::$pdo;
    }

    public static function setPdo(?PDO $pdo): void
    {
        self::$pdo = $pdo;
    }

    public static function beginTransaction(): bool
    {
        return self::getPdo()->beginTransaction();
    }

    public static function commit(): bool
    {
        return self::getPdo()->commit();
    }

    public static function rollBack(): bool
    {
        return self::getPdo()->rollBack();
    }

    public function find(int $id): ?array
    {
        $sql = "SELECT * FROM `{$this->table}` WHERE `{$this->primaryKey}` = :id LIMIT 1";
        $stmt = self::getPdo()->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function all(): array
    {
        $sql = "SELECT * FROM `{$this->table}` ORDER BY `{$this->primaryKey}` DESC";
        $stmt = self::getPdo()->query($sql);
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        if (!isset($data['updated_at'])) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }

        $fields = array_keys($data);
        $columns = '`' . implode('`, `', $fields) . '`';
        $placeholders = ':' . implode(', :', $fields);

        $sql = "INSERT INTO `{$this->table}` ({$columns}) VALUES ({$placeholders})";
        $stmt = self::getPdo()->prepare($sql);
        $stmt->execute($data);

        return (int)self::getPdo()->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $data['updated_at'] = date('Y-m-d H:i:s');

        $sets = [];
        foreach (array_keys($data) as $field) {
            $sets[] = "`{$field}` = :{$field}";
        }
        $setString = implode(', ', $sets);

        $data['__id'] = $id;
        $sql = "UPDATE `{$this->table}` SET {$setString} WHERE `{$this->primaryKey}` = :__id";
        $stmt = self::getPdo()->prepare($sql);
        return $stmt->execute($data);
    }

    public function delete(int $id): bool
    {
        $sql = "DELETE FROM `{$this->table}` WHERE `{$this->primaryKey}` = :id";
        $stmt = self::getPdo()->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}
