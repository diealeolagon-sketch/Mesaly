<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use DomainException;
use PDO;
use Throwable;

final class FacturaElectronica extends Model
{
    protected string $table = 'facturas_electronicas';

    public const STATES = [
        'sin_factura' => 'Pendiente de facturar', 'emitiendo' => 'En proceso de emisión',
        'pendiente' => 'Pendiente de validación', 'validada' => 'Facturada y validada',
        'rechazada' => 'Rechazada', 'incierta' => 'Pendiente de conciliación',
    ];

    private function query(string $sql, array $params): \PDOStatement
    {
        $stmt = self::getPdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function settings(int $tenant): ?array
    {
        $row = $this->query('SELECT * FROM facturacion_config WHERE restaurante_id = ?', [$tenant])->fetch();
        if (!$row) {
            return null;
        }
        foreach (['fiscal', 'impuestos'] as $field) {
            $row[$field] = json_decode($row[$field], true, 32, JSON_THROW_ON_ERROR);
        }
        return $row;
    }

    public function saveSettings(int $tenant, int $user, array $settings, bool $global): void
    {
        $this->transaction(function () use ($tenant, $user, $settings, $global): void {
            $this->query(
                'INSERT INTO facturacion_config (restaurante_id, habilitada, rango_id, prefijo, fiscal, impuestos, credenciales)
                 VALUES (?, ?, ?, ?, ?, ?, ?)
                 ON DUPLICATE KEY UPDATE habilitada = VALUES(habilitada), rango_id = VALUES(rango_id),
                 prefijo = VALUES(prefijo), fiscal = VALUES(fiscal), impuestos = VALUES(impuestos),
                 credenciales = IF(?, NULL, COALESCE(VALUES(credenciales), credenciales))',
                [$tenant, $settings['habilitada'], $settings['rango_id'], $settings['prefijo'],
                    $this->encode($settings['fiscal']), $this->encode($settings['impuestos']), $settings['credenciales'], (int)$global],
            );
            $this->event($tenant, null, $user, 'configuracion');
        });
    }

    public function products(int $tenant): array
    {
        return $this->query(
            'SELECT id, nombre, deleted_at FROM productos WHERE restaurante_id = ? ORDER BY deleted_at IS NOT NULL, nombre', [$tenant]
        )->fetchAll();
    }

    public function invoice(int $tenant, int $order): ?array
    {
        $row = $this->query(
            'SELECT *, updated_at <= DATE_SUB(NOW(), INTERVAL 5 MINUTE) AS puede_revisar
             FROM facturas_electronicas WHERE restaurante_id = ? AND pedido_id = ?', [$tenant, $order]
        )->fetch();
        if (!$row) {
            return null;
        }
        $row['snapshot'] = json_decode($row['snapshot'], true, 64, JSON_THROW_ON_ERROR);
        return $row;
    }

    public function listing(int $tenant, array $filters, int $page): array
    {
        $join = ' FROM pedidos p LEFT JOIN facturas_electronicas f ON f.pedido_id = p.id AND f.restaurante_id = p.restaurante_id';
        $where = " WHERE p.restaurante_id = ? AND (p.estado = 'entregado' OR f.id IS NOT NULL)";
        $params = [$tenant];
        if ($filters['estado'] === 'sin_factura') {
            $where .= ' AND f.id IS NULL';
        } elseif (isset(self::STATES[$filters['estado']])) {
            $where .= ' AND f.estado = ?';
            $params[] = $filters['estado'];
        }
        if ($filters['q'] !== '') {
            $like = '%' . strtr($filters['q'], ['!' => '!!', '%' => '!%', '_' => '!_']) . '%';
            $where .= " AND (p.codigo LIKE ? ESCAPE '!' OR CAST(p.consecutivo AS CHAR) LIKE ? ESCAPE '!'
                OR f.numero LIKE ? ESCAPE '!' OR COALESCE(f.comprador, p.cliente_nombre) LIKE ? ESCAPE '!')";
            array_push($params, $like, $like, $like, $like);
        }
        foreach (['desde' => '>=', 'hasta' => '<='] as $field => $operator) {
            if ($filters[$field] !== '') {
                $where .= " AND DATE(COALESCE(f.created_at, p.created_at)) {$operator} ?";
                $params[] = $filters[$field];
            }
        }
        $count = (int)$this->query('SELECT COUNT(*)' . $join . $where, $params)->fetchColumn();
        $pages = max(1, (int)ceil($count / 20));
        $page = min($page, $pages);
        $offset = ($page - 1) * 20;
        $rows = $this->query(
            'SELECT p.id, p.codigo, COALESCE(f.comprador,p.cliente_nombre) comprador, f.numero,
                    COALESCE(f.estado,\'sin_factura\') estado, COALESCE(f.total,p.total) total,
                    COALESCE(f.created_at,p.created_at) fecha'
            . $join . $where . ' ORDER BY fecha DESC, p.id DESC LIMIT 20 OFFSET ' . $offset, $params
        )->fetchAll();
        $metrics = $this->query(
            "SELECT COALESCE(f.estado,'sin_factura') estado, COUNT(*) cantidad, SUM(COALESCE(f.total,p.total)) total"
            . $join . " WHERE p.restaurante_id = ? AND (p.estado = 'entregado' OR f.id IS NOT NULL)
                GROUP BY COALESCE(f.estado,'sin_factura')",
            [$tenant],
        )->fetchAll(PDO::FETCH_UNIQUE | PDO::FETCH_ASSOC);
        return compact('rows', 'page', 'pages', 'metrics');
    }

    public function reserve(int $tenant, int $order, int $user, string $account, string $reference, array $snapshot): ?int
    {
        return $this->transaction(function () use ($tenant, $order, $user, $account, $reference, $snapshot): ?int {
            $state = $this->query('SELECT estado FROM pedidos WHERE id = ? AND restaurante_id = ? FOR UPDATE',
                [$order, $tenant])->fetchColumn();
            if ($state !== 'entregado') {
                throw new DomainException('Solo puedes facturar pedidos entregados.');
            }
            if ($this->invoice($tenant, $order) !== null) {
                return null;
            }
            $this->query(
                "INSERT INTO facturas_electronicas (restaurante_id,pedido_id,usuario_id,referencia,ambiente,cuenta,
                    rango_id,estado,comprador,snapshot,total) VALUES (?,?,?,?,'sandbox',?,?,'emitiendo',?,?,?)",
                [$tenant, $order, $user, $reference, $account, $snapshot['payload']['numbering_range_id'],
                    $snapshot['payload']['customer']['company'] ?? $snapshot['payload']['customer']['names'],
                    $this->encode($snapshot), $snapshot['preview']['total']],
            );
            $id = (int)self::getPdo()->lastInsertId();
            $this->event($tenant, $id, $user, 'emision_confirmada');
            return $id;
        });
    }

    public function record(int $tenant, int $id, int $user, int $version, array $result): void
    {
        $this->transaction(function () use ($tenant, $id, $user, $version, $result): void {
            $state = $result['estado'];
            $updated = $this->query(
                "UPDATE facturas_electronicas SET estado = ?, externo_id = COALESCE(?,externo_id),
                    numero = COALESCE(?,numero), cufe = COALESCE(?,cufe), pdf = COALESCE(?,pdf), xml = COALESCE(?,xml), error = ?,
                    emitida_at = COALESCE(?, emitida_at, IF(? IS NULL,NULL,NOW())),
                    validada_at = IF(? = 'validada',COALESCE(?,NOW()),validada_at), version = version + 1
                 WHERE restaurante_id = ? AND id = ? AND version = ? AND estado <> 'validada'",
                [$state, $result['externo_id'] ?? null, $result['numero'] ?? null, $result['cufe'] ?? null,
                    $result['pdf'] ?? null, $result['xml'] ?? null, $result['error'] ?? null,
                    $result['emitida_at'] ?? null, $result['numero'] ?? null, $state,
                    $result['validada_at'] ?? null, $tenant, $id, $version],
            )->rowCount();
            if ($updated === 1) {
                $this->event($tenant, $id, $user, $state, $result['error'] ?? null);
            }
        });
    }

    public function claimRetry(int $tenant, int $id, int $user, int $version): bool
    {
        return $this->transaction(function () use ($tenant, $id, $user, $version): bool {
            $updated = $this->query(
                "UPDATE facturas_electronicas SET estado = 'emitiendo', error = NULL, version = version + 1
                 WHERE restaurante_id = ? AND id = ? AND version = ? AND estado = 'incierta' AND numero IS NULL
                   AND updated_at <= DATE_SUB(NOW(), INTERVAL 5 MINUTE)
                   AND EXISTS (SELECT 1 FROM pedidos p WHERE p.id = facturas_electronicas.pedido_id
                       AND p.restaurante_id = facturas_electronicas.restaurante_id AND p.estado = 'entregado')",
                [$tenant, $id, $version],
            )->rowCount();
            if ($updated === 1) {
                $this->event($tenant, $id, $user, 'reintento_confirmado');
            }
            return $updated === 1;
        });
    }

    public function events(int $tenant, int $invoice): array
    {
        return $this->query(
            'SELECT accion, detalle, usuario_id, created_at FROM facturacion_eventos
             WHERE restaurante_id = ? AND factura_id = ? ORDER BY id DESC', [$tenant, $invoice]
        )->fetchAll();
    }

    public function event(int $tenant, ?int $invoice, int $user, string $action, ?string $detail = null): void
    {
        $this->query('INSERT INTO facturacion_eventos (restaurante_id,factura_id,usuario_id,accion,detalle)
            VALUES (?,?,?,?,?)', [$tenant, $invoice, $user, $action, $detail]);
    }

    private function transaction(callable $callback): mixed
    {
        $pdo = self::getPdo();
        $pdo->beginTransaction();
        try {
            $result = $callback();
            $pdo->commit();
            return $result;
        } catch (Throwable $error) {
            $pdo->rollBack();
            throw $error;
        }
    }

    private function encode(array $data): string
    {
        return json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
    }
}
