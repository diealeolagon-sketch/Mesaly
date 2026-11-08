<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use PDO;
use RuntimeException;

class Pedido extends Model
{
    protected string $table = 'pedidos';
    protected string $primaryKey = 'id';

    /**
     * Verifica si una IP ha superado el límite de pedidos en una ventana de tiempo (Anti-Abuso).
     */
    public function haExcedidoRateLimit(string $ip, int $maxPedidos = 5, int $minutos = 10): bool
    {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM `{$this->table}` 
                               WHERE `ip` = :ip 
                                 AND `created_at` >= DATE_SUB(NOW(), INTERVAL :minutos MINUTE)");
        $stmt->bindValue(':ip', $ip, PDO::PARAM_STR);
        $stmt->bindValue(':minutos', $minutos, PDO::PARAM_INT);
        $stmt->execute();

        return (int)$stmt->fetchColumn() >= $maxPedidos;
    }

    /**
     * Crea un pedido completo con sus ítems y modificadores dentro de una transacción.
     * Retorna el registro del pedido creado con su código generado.
     */
    public function crearPedidoCompleto(array $restaurante, array $datosPedido, array $items): array
    {
        $pdo = self::getPdo();
        $pdo->beginTransaction();

        try {
            $restauranteId = (int)$restaurante['id'];

            // 1. Obtener y bloquear el siguiente consecutivo para el restaurante
            $stmtConsecutivo = $pdo->prepare("SELECT siguiente_consecutivo FROM `restaurantes` WHERE id = :id FOR UPDATE");
            $stmtConsecutivo->execute(['id' => $restauranteId]);
            $consecutivo = (int)$stmtConsecutivo->fetchColumn();
            if ($consecutivo < 1) {
                $consecutivo = 1;
            }

            // Incrementar siguiente consecutivo
            $stmtInc = $pdo->prepare("UPDATE `restaurantes` SET siguiente_consecutivo = :sig WHERE id = :id");
            $stmtInc->execute([
                'sig' => $consecutivo + 1,
                'id' => $restauranteId,
            ]);

            // Generar código amigable: 3 primeras letras del slug en mayúscula + consecutivo formateado a 4 dígitos
            $slugPrefix = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $restaurante['slug'] ?? 'PED'), 0, 3));
            if (strlen($slugPrefix) < 3) {
                $slugPrefix = 'PED';
            }
            $codigo = sprintf("%s-%04d", $slugPrefix, $consecutivo);

            // 2. Insertar cabecera del pedido
            $sqlPedido = "INSERT INTO `pedidos` (
                restaurante_id, consecutivo, codigo, modalidad, mesa_id, cliente_nombre,
                cliente_telefono, direccion, barrio, notas_entrega, hora_recogida,
                metodo_pago, subtotal, costo_envio, total, estado, ip, created_at, updated_at
            ) VALUES (
                :restaurante_id, :consecutivo, :codigo, :modalidad, :mesa_id, :cliente_nombre,
                :cliente_telefono, :direccion, :barrio, :notas_entrega, :hora_recogida,
                :metodo_pago, :subtotal, :costo_envio, :total, 'generado', :ip, NOW(), NOW()
            )";

            $stmtPedido = $pdo->prepare($sqlPedido);
            $stmtPedido->execute([
                'restaurante_id' => $restauranteId,
                'consecutivo' => $consecutivo,
                'codigo' => $codigo,
                'modalidad' => $datosPedido['modalidad'],
                'mesa_id' => !empty($datosPedido['mesa_id']) ? (int)$datosPedido['mesa_id'] : null,
                'cliente_nombre' => trim($datosPedido['cliente_nombre'] ?? 'Cliente'),
                'cliente_telefono' => !empty($datosPedido['cliente_telefono']) ? trim($datosPedido['cliente_telefono']) : null,
                'direccion' => !empty($datosPedido['direccion']) ? trim($datosPedido['direccion']) : null,
                'barrio' => !empty($datosPedido['barrio']) ? trim($datosPedido['barrio']) : null,
                'notas_entrega' => !empty($datosPedido['notas_entrega']) ? trim($datosPedido['notas_entrega']) : null,
                'hora_recogida' => !empty($datosPedido['hora_recogida']) ? trim($datosPedido['hora_recogida']) : null,
                'metodo_pago' => $datosPedido['metodo_pago'] ?? 'efectivo',
                'subtotal' => (int)$datosPedido['subtotal'],
                'costo_envio' => (int)($datosPedido['costo_envio'] ?? 0),
                'total' => (int)$datosPedido['total'],
                'ip' => $datosPedido['ip'] ?? '127.0.0.1',
            ]);

            $pedidoId = (int)$pdo->lastInsertId();

            // 3. Insertar ítems y sus opciones seleccionadas
            $stmtItem = $pdo->prepare("INSERT INTO `pedido_items` (
                pedido_id, producto_id, nombre_producto, precio_unitario, cantidad, nota, total_linea, created_at
            ) VALUES (
                :pedido_id, :producto_id, :nombre_producto, :precio_unitario, :cantidad, :nota, :total_linea, NOW()
            )");

            $stmtOpcion = $pdo->prepare("INSERT INTO `pedido_item_opciones` (
                pedido_item_id, nombre_grupo, nombre_opcion, precio_extra, created_at
            ) VALUES (
                :pedido_item_id, :nombre_grupo, :nombre_opcion, :precio_extra, NOW()
            )");

            foreach ($items as $item) {
                $stmtItem->execute([
                    'pedido_id' => $pedidoId,
                    'producto_id' => (int)$item['producto_id'],
                    'nombre_producto' => $item['nombre_producto'],
                    'precio_unitario' => (int)$item['precio_unitario'],
                    'cantidad' => (int)$item['cantidad'],
                    'nota' => !empty($item['nota']) ? trim((string)$item['nota']) : null,
                    'total_linea' => (int)$item['total_linea'],
                ]);

                $itemId = (int)$pdo->lastInsertId();

                if (!empty($item['opciones']) && is_array($item['opciones'])) {
                    foreach ($item['opciones'] as $opc) {
                        $stmtOpcion->execute([
                            'pedido_item_id' => $itemId,
                            'nombre_grupo' => $opc['nombre_grupo'],
                            'nombre_opcion' => $opc['nombre_opcion'],
                            'precio_extra' => (int)($opc['precio_extra'] ?? 0),
                        ]);
                    }
                }
            }

            $pdo->commit();

            return [
                'id' => $pedidoId,
                'codigo' => $codigo,
                'consecutivo' => $consecutivo,
                'subtotal' => (int)$datosPedido['subtotal'],
                'costo_envio' => (int)($datosPedido['costo_envio'] ?? 0),
                'total' => (int)$datosPedido['total'],
                'modalidad' => $datosPedido['modalidad'],
                'cliente_nombre' => $datosPedido['cliente_nombre'],
                'cliente_telefono' => $datosPedido['cliente_telefono'] ?? null,
                'direccion' => $datosPedido['direccion'] ?? null,
                'barrio' => $datosPedido['barrio'] ?? null,
                'notas_entrega' => $datosPedido['notas_entrega'] ?? null,
                'hora_recogida' => $datosPedido['hora_recogida'] ?? null,
                'metodo_pago' => $datosPedido['metodo_pago'],
                'mesa_numero' => $datosPedido['mesa_numero'] ?? null,
            ];
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw new RuntimeException("Error al registrar el pedido: " . $e->getMessage(), 0, $e);
        }
    }
}
