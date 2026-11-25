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

            // Garantizar que consecutivo sea estrictamente mayor que cualquier pedido existente para evitar colisiones
            $stmtMax = $pdo->prepare("SELECT COALESCE(MAX(consecutivo), 0) FROM `pedidos` WHERE restaurante_id = :id");
            $stmtMax->execute(['id' => $restauranteId]);
            $maxActual = (int)$stmtMax->fetchColumn();

            if ($consecutivo <= $maxActual) {
                $consecutivo = $maxActual + 1;
            }
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
                :metodo_pago, :subtotal, :costo_envio, :total, 'recibido', :ip, NOW(), NOW()
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

    public const ESTADOS = [
        'recibido' => [
            'label' => 'Recibido',
            'color' => '#2563EB',
            'bg' => '#EFF6FF',
            'border' => '#BFDBFE',
            'final' => false,
        ],
        'listo' => [
            'label' => 'Listo',
            'color' => '#D97706',
            'bg' => '#FFFBEB',
            'border' => '#FDE68A',
            'final' => false,
        ],
        'en_camino' => [
            'label' => 'En Camino',
            'color' => '#7C3AED',
            'bg' => '#F5F3FF',
            'border' => '#DDD6FE',
            'final' => false,
        ],
        'entregado' => [
            'label' => 'Entregado',
            'color' => '#059669',
            'bg' => '#ECFDF5',
            'border' => '#A7F3D0',
            'final' => true,
        ],
        'rechazado' => [
            'label' => 'Rechazado',
            'color' => '#DC2626',
            'bg' => '#FEF2F2',
            'border' => '#FECACA',
            'final' => true,
        ],
    ];

    /**
     * Alias cómodo para crear pedidos recibiendo el ID o array de restaurante
     */
    public function crear(int|array $restaurante, array $datosPedido, array $items = []): array
    {
        $restauranteData = is_array($restaurante) ? $restaurante : (new Restaurante())->find($restaurante);
        if (!$restauranteData) {
            $restauranteData = ['id' => is_int($restaurante) ? $restaurante : 1, 'slug' => 'PED'];
        }
        return $this->crearPedidoCompleto($restauranteData, $datosPedido, $items);
    }

    public const MODALIDADES = [
        'mesa' => 'En Mesa',
        'domicilio' => 'Domicilio',
        'recoger' => 'Para Recoger',
    ];

    /**
     * Lista pedidos con filtros y paginación para el restaurante
     *
     * @param array{estado?: string, modalidad?: string, q?: string, fecha_desde?: string, fecha_hasta?: string} $filtros
     */
    public function listarPorRestaurante(int $restauranteId, array $filtros = [], int $pagina = 1, int $porPagina = 20): array
    {
        $pdo = self::getPdo();
        $offset = max(0, ($pagina - 1) * $porPagina);

        $sql = "SELECT p.*, m.numero AS mesa_numero_rel, m.nombre AS mesa_nombre_rel
                FROM `{$this->table}` p
                LEFT JOIN `mesas` m ON p.mesa_id = m.id
                WHERE p.restaurante_id = :rid";

        $params = ['rid' => $restauranteId];

        if (!empty($filtros['estado']) && isset(self::ESTADOS[$filtros['estado']])) {
            $sql .= " AND p.estado = :estado";
            $params['estado'] = $filtros['estado'];
        }

        if (!empty($filtros['modalidad'])) {
            if ($filtros['modalidad'] === 'local') {
                $sql .= " AND p.modalidad IN ('mesa', 'recoger')";
            } elseif (isset(self::MODALIDADES[$filtros['modalidad']])) {
                $sql .= " AND p.modalidad = :modalidad";
                $params['modalidad'] = $filtros['modalidad'];
            }
        }

        if (!empty($filtros['q'])) {
            $sql .= " AND (p.codigo LIKE :q OR p.cliente_nombre LIKE :q OR p.cliente_telefono LIKE :q)";
            $params['q'] = '%' . trim((string)$filtros['q']) . '%';
        }

        if (!empty($filtros['fecha_desde'])) {
            $sql .= " AND DATE(p.created_at) >= :f_desde";
            $params['f_desde'] = $filtros['fecha_desde'];
        }

        if (!empty($filtros['fecha_hasta'])) {
            $sql .= " AND DATE(p.created_at) <= :f_hasta";
            $params['f_hasta'] = $filtros['fecha_hasta'];
        }

        if (!empty($filtros['datetime_desde'])) {
            if (!empty($filtros['mantener_pendientes'])) {
                $sql .= " AND (p.created_at >= :dt_desde OR p.estado IN ('recibido', 'en_camino'))";
            } else {
                $sql .= " AND p.created_at >= :dt_desde";
            }
            $params['dt_desde'] = $filtros['datetime_desde'];
        }

        if (!empty($filtros['datetime_hasta'])) {
            $sql .= " AND p.created_at < :dt_hasta";
            $params['dt_hasta'] = $filtros['datetime_hasta'];
        }

        $sql .= " ORDER BY p.created_at DESC, p.id DESC LIMIT :limit OFFSET :offset";

        $stmt = $pdo->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue(':' . $k, $v);
        }
        $stmt->bindValue(':limit', $porPagina, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cuenta el total de pedidos que cumplen con los filtros
     */
    public function contarPorRestaurante(int $restauranteId, array $filtros = []): int
    {
        $pdo = self::getPdo();
        $sql = "SELECT COUNT(*) FROM `{$this->table}` p WHERE p.restaurante_id = :rid";
        $params = ['rid' => $restauranteId];

        if (!empty($filtros['estado']) && isset(self::ESTADOS[$filtros['estado']])) {
            $sql .= " AND p.estado = :estado";
            $params['estado'] = $filtros['estado'];
        }

        if (!empty($filtros['modalidad'])) {
            if ($filtros['modalidad'] === 'local') {
                $sql .= " AND p.modalidad IN ('mesa', 'recoger')";
            } elseif (isset(self::MODALIDADES[$filtros['modalidad']])) {
                $sql .= " AND p.modalidad = :modalidad";
                $params['modalidad'] = $filtros['modalidad'];
            }
        }

        if (!empty($filtros['q'])) {
            $sql .= " AND (p.codigo LIKE :q OR p.cliente_nombre LIKE :q OR p.cliente_telefono LIKE :q)";
            $params['q'] = '%' . trim((string)$filtros['q']) . '%';
        }

        if (!empty($filtros['fecha_desde'])) {
            $sql .= " AND DATE(p.created_at) >= :f_desde";
            $params['f_desde'] = $filtros['fecha_desde'];
        }

        if (!empty($filtros['fecha_hasta'])) {
            $sql .= " AND DATE(p.created_at) <= :f_hasta";
            $params['f_hasta'] = $filtros['fecha_hasta'];
        }

        if (!empty($filtros['datetime_desde'])) {
            if (!empty($filtros['mantener_pendientes'])) {
                $sql .= " AND (p.created_at >= :dt_desde OR p.estado IN ('recibido', 'en_camino'))";
            } else {
                $sql .= " AND p.created_at >= :dt_desde";
            }
            $params['dt_desde'] = $filtros['datetime_desde'];
        }

        if (!empty($filtros['datetime_hasta'])) {
            $sql .= " AND p.created_at < :dt_hasta";
            $params['dt_hasta'] = $filtros['datetime_hasta'];
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Retorna el detalle completo de un pedido incluyendo sus ítems y modificadores
     */
    public function obtenerDetalleCompleto(int $pedidoId, int $restauranteId): ?array
    {
        $pdo = self::getPdo();

        $stmt = $pdo->prepare(
            "SELECT p.*, m.numero AS mesa_numero_rel, m.nombre AS mesa_nombre_rel
             FROM `{$this->table}` p
             LEFT JOIN `mesas` m ON p.mesa_id = m.id
             WHERE p.id = :id AND p.restaurante_id = :rid
             LIMIT 1"
        );
        $stmt->execute(['id' => $pedidoId, 'rid' => $restauranteId]);
        $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$pedido) {
            return null;
        }

        // Obtener ítems
        $stmtItems = $pdo->prepare(
            "SELECT * FROM `pedido_items` WHERE `pedido_id` = :pid ORDER BY `id` ASC"
        );
        $stmtItems->execute(['pid' => $pedidoId]);
        $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

        // Obtener opciones de modificadores
        if (!empty($items)) {
            $itemIds = array_column($items, 'id');
            $inClause = implode(',', array_fill(0, count($itemIds), '?'));
            $stmtOpciones = $pdo->prepare(
                "SELECT * FROM `pedido_item_opciones` 
                 WHERE `pedido_item_id` IN ({$inClause}) 
                 ORDER BY `id` ASC"
            );
            $stmtOpciones->execute($itemIds);
            $todasOpciones = $stmtOpciones->fetchAll(PDO::FETCH_ASSOC);

            $opcionesPorItem = [];
            foreach ($todasOpciones as $opc) {
                $opcionesPorItem[(int)$opc['pedido_item_id']][] = $opc;
            }

            foreach ($items as &$it) {
                $it['opciones'] = $opcionesPorItem[(int)$it['id']] ?? [];
            }
            unset($it);
        }

        $pedido['items'] = $items;
        return $pedido;
    }

    /**
     * Cambia el estado de un pedido (con validación de pertenencia al tenant)
     */
    public function cambiarEstado(int $pedidoId, int $restauranteId, string $nuevoEstado): bool
    {
        if (!isset(self::ESTADOS[$nuevoEstado])) {
            throw new \InvalidArgumentException("Estado de pedido inválido: {$nuevoEstado}");
        }

        $pdo = self::getPdo();
        $stmt = $pdo->prepare(
            "UPDATE `{$this->table}` 
             SET `estado` = :estado, `updated_at` = NOW() 
             WHERE `id` = :id AND `restaurante_id` = :rid"
        );
        $ejecutado = $stmt->execute([
            'estado' => $nuevoEstado,
            'id' => $pedidoId,
            'rid' => $restauranteId,
        ]);

        if (!$ejecutado) {
            return false;
        }

        if ($stmt->rowCount() > 0) {
            return true;
        }

        // Cero filas afectadas también puede significar que el estado ya era igual.
        // La consulta mantiene el aislamiento: no revela pedidos de otro tenant.
        $stmtExiste = $pdo->prepare(
            "SELECT 1
             FROM `{$this->table}`
             WHERE `id` = :id AND `restaurante_id` = :rid
             LIMIT 1"
        );
        $stmtExiste->execute([
            'id' => $pedidoId,
            'rid' => $restauranteId,
        ]);

        return $stmtExiste->fetchColumn() !== false;
    }

    /**
     * Conteo agrupado de pedidos por estado
     */
    public function conteosPorEstado(int $restauranteId, array $filtros = []): array
    {
        $pdo = self::getPdo();
        $sql = "SELECT `estado`, COUNT(*) AS total 
                FROM `{$this->table}` 
                WHERE `restaurante_id` = :rid";
        $params = ['rid' => $restauranteId];

        if (!empty($filtros['datetime_desde'])) {
            if (!empty($filtros['mantener_pendientes'])) {
                $sql .= " AND (created_at >= :dt_desde OR estado IN ('recibido', 'en_camino'))";
            } else {
                $sql .= " AND created_at >= :dt_desde";
            }
            $params['dt_desde'] = $filtros['datetime_desde'];
        }

        if (!empty($filtros['datetime_hasta'])) {
            $sql .= " AND created_at < :dt_hasta";
            $params['dt_hasta'] = $filtros['datetime_hasta'];
        }

        if (!empty($filtros['fecha_desde'])) {
            $sql .= " AND DATE(created_at) >= :f_desde";
            $params['f_desde'] = $filtros['fecha_desde'];
        }

        if (!empty($filtros['fecha_hasta'])) {
            $sql .= " AND DATE(created_at) <= :f_hasta";
            $params['f_hasta'] = $filtros['fecha_hasta'];
        }

        $sql .= " GROUP BY `estado`";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $filas = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        $resultado = [
            'todos' => 0,
            'recibido' => 0,
            'listo' => 0,
            'en_camino' => 0,
            'entregado' => 0,
            'rechazado' => 0,
            'pendientes' => 0,
        ];

        foreach ($filas as $est => $cant) {
            $cnt = (int)$cant;
            if (isset($resultado[$est])) {
                $resultado[$est] += $cnt;
            } elseif ($est === 'generado' || $est === 'en_preparacion') {
                $resultado['recibido'] += $cnt;
            } elseif ($est === 'cancelado') {
                $resultado['rechazado'] += $cnt;
            }
            $resultado['todos'] += $cnt;
            if (in_array($est, ['recibido', 'listo', 'en_camino', 'generado', 'en_preparacion'], true)) {
                $resultado['pendientes'] += $cnt;
            }
        }

        // Conteos por modalidad para los tabs de salón vs domicilios (solo pedidos activos en vivo)
        $stmtMod = $pdo->prepare(
            "SELECT `modalidad`, COUNT(*) AS total
             FROM `{$this->table}`
             WHERE `restaurante_id` = :rid
               AND `estado` IN ('recibido', 'listo', 'en_camino', 'generado', 'en_preparacion')
             GROUP BY `modalidad`"
        );
        $stmtMod->execute(['rid' => $restauranteId]);
        $filasMod = $stmtMod->fetchAll(PDO::FETCH_KEY_PAIR);

        $resultado['modalidad_domicilio'] = (int)($filasMod['domicilio'] ?? 0);
        $resultado['modalidad_mesa'] = (int)($filasMod['mesa'] ?? 0);
        $resultado['modalidad_recoger'] = (int)($filasMod['recoger'] ?? 0);
        $resultado['modalidad_local'] = $resultado['modalidad_mesa'] + $resultado['modalidad_recoger'];

        return $resultado;
    }

    /**
     * Métricas del mes para el restaurante (excluyendo pedidos cancelados/rechazados)
     *
     * @return array{ventas_mes: int, pedidos_mes: int, ticket_promedio: int, pendientes: int}
     */
    public function metricasMes(int $restauranteId, ?string $anioMes = null): array
    {
        $pdo = self::getPdo();
        $ym = $anioMes ?? date('Y-m');

        $stmt = $pdo->prepare(
            "SELECT 
                COUNT(*) AS total_pedidos,
                COALESCE(SUM(total), 0) AS total_ventas
             FROM `{$this->table}`
             WHERE `restaurante_id` = :rid 
               AND DATE_FORMAT(`created_at`, '%Y-%m') = :ym
               AND `estado` NOT IN ('cancelado', 'rechazado')"
        );
        $stmt->execute(['rid' => $restauranteId, 'ym' => $ym]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $totalPedidos = (int)($row['total_pedidos'] ?? 0);
        $totalVentas = (int)($row['total_ventas'] ?? 0);
        $ticketPromedio = $totalPedidos > 0 ? (int)round($totalVentas / $totalPedidos) : 0;

        // Conteo de pedidos pendientes de atención
        $stmtPend = $pdo->prepare(
            "SELECT COUNT(*) FROM `{$this->table}`
             WHERE `restaurante_id` = :rid AND `estado` IN ('recibido', 'listo', 'en_camino', 'generado', 'en_preparacion')"
        );
        $stmtPend->execute(['rid' => $restauranteId]);
        $pendientes = (int)$stmtPend->fetchColumn();

        return [
            'ventas_mes' => $totalVentas,
            'pedidos_mes' => $totalPedidos,
            'ticket_promedio' => $ticketPromedio,
            'pendientes' => $pendientes,
        ];
    }

    /**
     * Ventas y pedidos agrupados por día (últimos N días) para Chart.js
     *
     * @return array<array{fecha: string, label: string, total: int, pedidos: int}>
     */
    public function ventasPorDia(int $restauranteId, int $dias = 30): array
    {
        $pdo = self::getPdo();

        $stmt = $pdo->prepare(
            "SELECT 
                DATE(`created_at`) AS dia,
                COALESCE(SUM(total), 0) AS total_dia,
                COUNT(*) AS pedidos_dia
             FROM `{$this->table}`
             WHERE `restaurante_id` = :rid 
               AND `created_at` >= DATE_SUB(CURDATE(), INTERVAL :dias DAY)
               AND `estado` NOT IN ('cancelado', 'rechazado')
             GROUP BY DATE(`created_at`)
             ORDER BY dia ASC"
        );
        $stmt->bindValue(':rid', $restauranteId, PDO::PARAM_INT);
        $stmt->bindValue(':dias', $dias, PDO::PARAM_INT);
        $stmt->execute();
        $dbRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $mapRows = [];
        foreach ($dbRows as $r) {
            $mapRows[$r['dia']] = [
                'total' => (int)$r['total_dia'],
                'pedidos' => (int)$r['pedidos_dia'],
            ];
        }

        // Construir la serie continua día por día
        $serie = [];
        for ($i = $dias - 1; $i >= 0; $i--) {
            $ts = strtotime("-{$i} days");
            $diaStr = date('Y-m-d', $ts);
            $label = date('d M', $ts);

            $serie[] = [
                'fecha' => $diaStr,
                'label' => $label,
                'total' => $mapRows[$diaStr]['total'] ?? 0,
                'pedidos' => $mapRows[$diaStr]['pedidos'] ?? 0,
            ];
        }

        return $serie;
    }

    /**
     * Pedidos agrupados por día de la semana (1 = Lunes, 7 = Domingo)
     *
     * @return array<string, int>
     */
    public function pedidosPorDiaSemana(int $restauranteId): array
    {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare(
            "SELECT DAYOFWEEK(`created_at`) AS dia_semana, COUNT(*) AS total
             FROM `{$this->table}`
             WHERE `restaurante_id` = :rid AND `estado` NOT IN ('cancelado', 'rechazado')
             GROUP BY dia_semana"
        );
        $stmt->execute(['rid' => $restauranteId]);
        $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $mapNombres = [
            2 => 'Lunes',
            3 => 'Martes',
            4 => 'Miércoles',
            5 => 'Jueves',
            6 => 'Viernes',
            7 => 'Sábado',
            1 => 'Domingo',
        ];

        $resultado = [];
        foreach ($mapNombres as $k => $nombre) {
            $resultado[$nombre] = 0;
        }

        foreach ($filas as $f) {
            $dw = (int)$f['dia_semana'];
            if (isset($mapNombres[$dw])) {
                $resultado[$mapNombres[$dw]] = (int)$f['total'];
            }
        }

        return $resultado;
    }

    /**
     * Top 5 productos más vendidos del restaurante
     *
     * @return array<array{producto_id: int, nombre: string, cantidad: int, total_ventas: int}>
     */
    public function topProductos(int $restauranteId, int $limite = 5): array
    {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare(
            "SELECT 
                pi.producto_id,
                pi.nombre_producto AS nombre,
                SUM(pi.cantidad) AS cantidad_vendida,
                SUM(pi.total_linea) AS total_ventas
             FROM `pedido_items` pi
             JOIN `pedidos` p ON pi.pedido_id = p.id
             WHERE p.restaurante_id = :rid AND p.estado NOT IN ('cancelado', 'rechazado')
             GROUP BY pi.producto_id, pi.nombre_producto
             ORDER BY cantidad_vendida DESC, total_ventas DESC
             LIMIT :limite"
        );
        $stmt->bindValue(':rid', $restauranteId, PDO::PARAM_INT);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Métricas consolidadas en un rango de fechas para el módulo de Reportes
     *
     * @return array{
     *     total_ventas: int,
     *     total_pedidos: int,
     *     ticket_promedio: int,
     *     base_gravable: int,
     *     impoconsumo: int,
     *     ventas_efectivo: int,
     *     pedidos_efectivo: int,
     *     ventas_transferencia: int,
     *     pedidos_transferencia: int,
     *     ventas_datafono: int,
     *     pedidos_datafono: int,
     *     ventas_mesa: int,
     *     pedidos_mesa: int,
     *     ventas_domicilio: int,
     *     pedidos_domicilio: int
     * }
     */
    public function metricasRango(int $restauranteId, string $desde, string $hasta, array $filtros = []): array
    {
        $pdo = self::getPdo();
        $params = [
            'rid' => $restauranteId,
            'desde' => $desde . ' 00:00:00',
            'hasta' => $hasta . ' 23:59:59',
        ];

        $sql = "SELECT 
                    COUNT(*) AS total_pedidos,
                    COALESCE(SUM(total), 0) AS total_ventas,
                    COALESCE(SUM(CASE WHEN metodo_pago = 'efectivo' THEN total ELSE 0 END), 0) AS ventas_efectivo,
                    COALESCE(SUM(CASE WHEN metodo_pago = 'efectivo' THEN 1 ELSE 0 END), 0) AS pedidos_efectivo,
                    COALESCE(SUM(CASE WHEN metodo_pago IN ('transferencia', 'nequi', 'daviplata') THEN total ELSE 0 END), 0) AS ventas_transferencia,
                    COALESCE(SUM(CASE WHEN metodo_pago IN ('transferencia', 'nequi', 'daviplata') THEN 1 ELSE 0 END), 0) AS pedidos_transferencia,
                    COALESCE(SUM(CASE WHEN metodo_pago IN ('datafono', 'tarjeta') THEN total ELSE 0 END), 0) AS ventas_datafono,
                    COALESCE(SUM(CASE WHEN metodo_pago IN ('datafono', 'tarjeta') THEN 1 ELSE 0 END), 0) AS pedidos_datafono,
                    COALESCE(SUM(CASE WHEN modalidad IN ('mesa', 'local') THEN total ELSE 0 END), 0) AS ventas_mesa,
                    COALESCE(SUM(CASE WHEN modalidad IN ('mesa', 'local') THEN 1 ELSE 0 END), 0) AS pedidos_mesa,
                    COALESCE(SUM(CASE WHEN modalidad = 'domicilio' THEN total ELSE 0 END), 0) AS ventas_domicilio,
                    COALESCE(SUM(CASE WHEN modalidad = 'domicilio' THEN 1 ELSE 0 END), 0) AS pedidos_domicilio
                FROM `{$this->table}`
                WHERE `restaurante_id` = :rid
                  AND `created_at` BETWEEN :desde AND :hasta
                  AND `estado` NOT IN ('cancelado', 'rechazado')";

        if (!empty($filtros['modalidad'])) {
            $sql .= " AND `modalidad` = :modalidad";
            $params['modalidad'] = $filtros['modalidad'];
        }
        if (!empty($filtros['metodo_pago'])) {
            $sql .= " AND `metodo_pago` = :metodo_pago";
            $params['metodo_pago'] = $filtros['metodo_pago'];
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC) ?: [];

        $totalVentas = (int)($row['total_ventas'] ?? 0);
        $totalPedidos = (int)($row['total_pedidos'] ?? 0);
        $ticketPromedio = $totalPedidos > 0 ? (int)round($totalVentas / $totalPedidos) : 0;
        $baseGravable = (int)round($totalVentas / 1.08);
        $impoconsumo = $totalVentas - $baseGravable;

        return [
            'total_ventas' => $totalVentas,
            'total_pedidos' => $totalPedidos,
            'ticket_promedio' => $ticketPromedio,
            'base_gravable' => $baseGravable,
            'impoconsumo' => $impoconsumo,
            'ventas_efectivo' => (int)($row['ventas_efectivo'] ?? 0),
            'pedidos_efectivo' => (int)($row['pedidos_efectivo'] ?? 0),
            'ventas_transferencia' => (int)($row['ventas_transferencia'] ?? 0),
            'pedidos_transferencia' => (int)($row['pedidos_transferencia'] ?? 0),
            'ventas_datafono' => (int)($row['ventas_datafono'] ?? 0),
            'pedidos_datafono' => (int)($row['pedidos_datafono'] ?? 0),
            'ventas_mesa' => (int)($row['ventas_mesa'] ?? 0),
            'pedidos_mesa' => (int)($row['pedidos_mesa'] ?? 0),
            'ventas_domicilio' => (int)($row['ventas_domicilio'] ?? 0),
            'pedidos_domicilio' => (int)($row['pedidos_domicilio'] ?? 0),
        ];
    }

    /**
     * Serie continua de ventas por día en un rango de fechas específico
     *
     * @return array<array{fecha: string, label: string, total: int, pedidos: int}>
     */
    public function ventasPorDiaRango(int $restauranteId, string $desde, string $hasta): array
    {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare(
            "SELECT 
                DATE(`created_at`) AS dia,
                COALESCE(SUM(total), 0) AS total_dia,
                COUNT(*) AS pedidos_dia
             FROM `{$this->table}`
             WHERE `restaurante_id` = :rid 
               AND `created_at` BETWEEN :desde AND :hasta
               AND `estado` NOT IN ('cancelado', 'rechazado')
             GROUP BY DATE(`created_at`)
             ORDER BY dia ASC"
        );
        $stmt->execute([
            'rid' => $restauranteId,
            'desde' => $desde . ' 00:00:00',
            'hasta' => $hasta . ' 23:59:59',
        ]);
        $filas = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $mapRows = [];
        foreach ($filas as $r) {
            $mapRows[$r['dia']] = [
                'total' => (int)$r['total_dia'],
                'pedidos' => (int)$r['pedidos_dia'],
            ];
        }

        $inicio = new \DateTime($desde);
        $fin = new \DateTime($hasta);
        $fin->modify('+1 day');
        $intervalo = new \DateInterval('P1D');
        $periodo = new \DatePeriod($inicio, $intervalo, $fin);

        $serie = [];
        foreach ($periodo as $dt) {
            $diaStr = $dt->format('Y-m-d');
            $label = $dt->format('d M');
            $serie[] = [
                'fecha' => $diaStr,
                'label' => $label,
                'total' => $mapRows[$diaStr]['total'] ?? 0,
                'pedidos' => $mapRows[$diaStr]['pedidos'] ?? 0,
            ];
        }

        return $serie;
    }

    /**
     * Comparativa de rendimiento financiero entre el mes actual y el mes anterior
     *
     * @return array{
     *     actual: array{total: int, pedidos: int, ticket_promedio: int},
     *     anterior: array{total: int, pedidos: int, ticket_promedio: int},
     *     diferencia_ventas: int,
     *     pct_cambio_ventas: float,
     *     tendencia: string
     * }
     */
    public function comparativaMeses(int $restauranteId): array
    {
        $pdo = self::getPdo();

        $inicioMesActual = date('Y-m-01 00:00:00');
        $finMesActual = date('Y-m-t 23:59:59');

        $inicioMesAnterior = date('Y-m-01 00:00:00', strtotime('first day of last month'));
        $finMesAnterior = date('Y-m-t 23:59:59', strtotime('last day of last month'));

        $sql = "SELECT 
                    COUNT(*) AS pedidos, 
                    COALESCE(SUM(total), 0) AS total 
                FROM `{$this->table}` 
                WHERE `restaurante_id` = :rid 
                  AND `created_at` BETWEEN :ini AND :fin 
                  AND `estado` NOT IN ('cancelado', 'rechazado')";

        $stmtAct = $pdo->prepare($sql);
        $stmtAct->execute(['rid' => $restauranteId, 'ini' => $inicioMesActual, 'fin' => $finMesActual]);
        $rowAct = $stmtAct->fetch(\PDO::FETCH_ASSOC) ?: [];

        $stmtAnt = $pdo->prepare($sql);
        $stmtAnt->execute(['rid' => $restauranteId, 'ini' => $inicioMesAnterior, 'fin' => $finMesAnterior]);
        $rowAnt = $stmtAnt->fetch(\PDO::FETCH_ASSOC) ?: [];

        $totAct = (int)($rowAct['total'] ?? 0);
        $pedAct = (int)($rowAct['pedidos'] ?? 0);
        $tickAct = $pedAct > 0 ? (int)round($totAct / $pedAct) : 0;

        $totAnt = (int)($rowAnt['total'] ?? 0);
        $pedAnt = (int)($rowAnt['pedidos'] ?? 0);
        $tickAnt = $pedAnt > 0 ? (int)round($totAnt / $pedAnt) : 0;

        $dif = $totAct - $totAnt;
        $esPrimerMes = false;
        if ($totAnt > 0) {
            $pct = round((($totAct - $totAnt) / $totAnt) * 100, 1);
        } elseif ($totAct > 0) {
            $pct = 100.0;
            $esPrimerMes = true;
        } else {
            $pct = 0.0;
        }
        $tendencia = $dif >= 0 ? 'arriba' : 'abajo';

        return [
            'actual' => ['total' => $totAct, 'pedidos' => $pedAct, 'ticket_promedio' => $tickAct],
            'anterior' => ['total' => $totAnt, 'pedidos' => $pedAnt, 'ticket_promedio' => $tickAnt],
            'diferencia_ventas' => $dif,
            'pct_cambio_ventas' => $pct,
            'es_primer_mes' => $esPrimerMes,
            'tendencia' => $tendencia,
        ];
    }

    /**
     * Top productos en un rango de fechas
     */
    public function topProductosRango(int $restauranteId, string $desde, string $hasta, int $limite = 5): array
    {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare(
            "SELECT 
                pi.producto_id,
                pi.nombre_producto AS nombre,
                SUM(pi.cantidad) AS cantidad_vendida,
                SUM(pi.total_linea) AS total_ventas
             FROM `pedido_items` pi
             JOIN `pedidos` p ON pi.pedido_id = p.id
             WHERE p.restaurante_id = :rid 
               AND p.created_at BETWEEN :desde AND :hasta
               AND p.estado NOT IN ('cancelado', 'rechazado')
             GROUP BY pi.producto_id, pi.nombre_producto
             ORDER BY cantidad_vendida DESC, total_ventas DESC
             LIMIT :limite"
        );
        $stmt->bindValue(':rid', $restauranteId, \PDO::PARAM_INT);
        $stmt->bindValue(':desde', $desde . ' 00:00:00');
        $stmt->bindValue(':hasta', $hasta . ' 23:59:59');
        $stmt->bindValue(':limite', $limite, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Listado detallado de pedidos para reporte en pantalla y exportación Excel
     */
    public function listarParaReporte(int $restauranteId, string $desde, string $hasta, array $filtros = []): array
    {
        $pdo = self::getPdo();
        $params = [
            'rid' => $restauranteId,
            'desde' => $desde . ' 00:00:00',
            'hasta' => $hasta . ' 23:59:59',
        ];

        $sql = "SELECT 
                    p.id,
                    p.consecutivo,
                    p.codigo,
                    p.modalidad,
                    p.mesa_id,
                    m.numero AS mesa_numero,
                    m.nombre AS mesa_nombre,
                    p.cliente_nombre,
                    p.cliente_telefono,
                    p.direccion,
                    p.barrio,
                    p.metodo_pago,
                    p.subtotal,
                    p.costo_envio,
                    p.total,
                    p.estado,
                    p.created_at,
                    (
                        SELECT GROUP_CONCAT(CONCAT(pi.cantidad, 'x ', pi.nombre_producto) SEPARATOR ' + ')
                        FROM `pedido_items` pi
                        WHERE pi.pedido_id = p.id
                    ) AS items_resumen
                FROM `pedidos` p
                LEFT JOIN `mesas` m ON p.mesa_id = m.id
                WHERE p.restaurante_id = :rid
                  AND p.created_at BETWEEN :desde AND :hasta";

        if (!empty($filtros['modalidad'])) {
            $sql .= " AND p.modalidad = :modalidad";
            $params['modalidad'] = $filtros['modalidad'];
        }
        if (!empty($filtros['metodo_pago'])) {
            $sql .= " AND p.metodo_pago = :metodo_pago";
            $params['metodo_pago'] = $filtros['metodo_pago'];
        }
        if (!empty($filtros['estado'])) {
            $sql .= " AND p.estado = :estado";
            $params['estado'] = $filtros['estado'];
        }

        $sql .= " ORDER BY p.created_at DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Métricas en tiempo real de HOY para el Dashboard operativo ultrarrápido (< 5ms)
     *
     * @return array{ventas_hoy: int, pedidos_hoy: int, ticket_promedio_hoy: int, pendientes: int, mesas_ocupadas: int, total_mesas: int}
     */
    public function metricasHoy(int $restauranteId): array
    {
        $pdo = self::getPdo();
        $hoy = date('Y-m-d');

        // 1. Ventas y pedidos del día de hoy
        $stmt = $pdo->prepare(
            "SELECT 
                COUNT(*) AS total_pedidos,
                COALESCE(SUM(total), 0) AS total_ventas
             FROM `{$this->table}`
             WHERE `restaurante_id` = :rid 
               AND DATE(`created_at`) = :hoy
               AND `estado` NOT IN ('cancelado', 'rechazado')"
        );
        $stmt->execute(['rid' => $restauranteId, 'hoy' => $hoy]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        $totalPedidos = (int)($row['total_pedidos'] ?? 0);
        $totalVentas = (int)($row['total_ventas'] ?? 0);
        $ticketPromedio = $totalPedidos > 0 ? (int)round($totalVentas / $totalPedidos) : 0;

        // 2. Comandas activas en cocina / pendientes de entrega
        $stmtPend = $pdo->prepare(
            "SELECT COUNT(*) FROM `{$this->table}`
             WHERE `restaurante_id` = :rid 
               AND `estado` IN ('recibido', 'listo', 'en_camino', 'generado', 'en_preparacion')"
        );
        $stmtPend->execute(['rid' => $restauranteId]);
        $pendientes = (int)$stmtPend->fetchColumn();

        // 3. Mesas ocupadas en este momento vs total mesas
        $stmtMesas = $pdo->prepare(
            "SELECT 
                COUNT(DISTINCT p.mesa_id) AS mesas_ocupadas,
                (SELECT COUNT(*) FROM `mesas` WHERE `restaurante_id` = :rid2 AND `activa` = 1 AND `deleted_at` IS NULL) AS total_mesas
             FROM `pedidos` p
             WHERE p.restaurante_id = :rid1
               AND p.modalidad = 'mesa'
               AND p.mesa_liberada_at IS NULL
               AND p.estado NOT IN ('cancelado', 'rechazado')"
        );
        $stmtMesas->execute(['rid1' => $restauranteId, 'rid2' => $restauranteId]);
        $rowMesas = $stmtMesas->fetch(\PDO::FETCH_ASSOC);

        return [
            'ventas_hoy' => $totalVentas,
            'pedidos_hoy' => $totalPedidos,
            'ticket_promedio_hoy' => $ticketPromedio,
            'pendientes' => $pendientes,
            'mesas_ocupadas' => (int)($rowMesas['mesas_ocupadas'] ?? 0),
            'total_mesas' => (int)($rowMesas['total_mesas'] ?? 0),
        ];
    }

    /**
     * Distribución de demanda por horas pico en un rango de fechas para Reportes
     *
     * @return array<int, array{hora: int, hora_label: string, pedidos: int, ventas: int}>
     */
    public function horasPicoRango(int $restauranteId, string $desde, string $hasta): array
    {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare(
            "SELECT 
                HOUR(`created_at`) AS hora,
                COUNT(*) AS pedidos,
                COALESCE(SUM(total), 0) AS ventas
             FROM `{$this->table}`
             WHERE `restaurante_id` = :rid
               AND `created_at` BETWEEN :desde AND :hasta
               AND `estado` NOT IN ('cancelado', 'rechazado')
             GROUP BY HOUR(`created_at`)
             ORDER BY hora ASC"
        );
        $stmt->execute([
            'rid' => $restauranteId,
            'desde' => $desde . ' 00:00:00',
            'hasta' => $hasta . ' 23:59:59',
        ]);
        $filas = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $map = [];
        foreach ($filas as $f) {
            $h = (int)$f['hora'];
            $map[$h] = [
                'pedidos' => (int)$f['pedidos'],
                'ventas' => (int)$f['ventas'],
            ];
        }

        // Construir franjas horarias comerciales típicas (11:00 am a 11:00 pm)
        $resultado = [];
        for ($h = 10; $h <= 23; $h++) {
            $amPm = $h >= 12 ? 'PM' : 'AM';
            $h12 = $h > 12 ? ($h - 12) : ($h === 0 ? 12 : $h);
            $resultado[] = [
                'hora' => $h,
                'hora_label' => $h12 . ':00 ' . $amPm,
                'pedidos' => $map[$h]['pedidos'] ?? 0,
                'ventas' => $map[$h]['ventas'] ?? 0,
            ];
        }

        return $resultado;
    }

    /**
     * Estado en vivo de todas las mesas para el mapa de salón del Dashboard
     *
     * @return array<int, array{id: int, numero: string, nombre: ?string, ocupada: bool, pedido_id: ?int, consecutivo: ?int, codigo: ?string, total_mesa: int, estado_pedido: ?string, tiempo_min: ?int}>
     */
    public function mesasEstadoEnVivo(int $restauranteId): array
    {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare(
            "SELECT 
                m.id,
                m.numero,
                m.nombre,
                MAX(p.id) AS pedido_id,
                MAX(p.consecutivo) AS consecutivo,
                MAX(p.codigo) AS codigo,
                COALESCE(SUM(p.total), 0) AS total_mesa,
                SUBSTRING_INDEX(GROUP_CONCAT(p.estado ORDER BY p.id DESC), ',', 1) AS estado_pedido,
                MIN(TIMESTAMPDIFF(MINUTE, p.created_at, NOW())) AS tiempo_min,
                COUNT(p.id) AS pedidos_activos
             FROM `mesas` m
             LEFT JOIN `pedidos` p ON p.mesa_id = m.id 
                  AND p.restaurante_id = m.restaurante_id
                  AND p.modalidad = 'mesa'
                  AND p.mesa_liberada_at IS NULL
                  AND p.estado NOT IN ('cancelado', 'rechazado')
             WHERE m.restaurante_id = :rid 
               AND m.activa = 1
               AND m.deleted_at IS NULL
             GROUP BY m.id, m.numero, m.nombre
             ORDER BY CAST(m.numero AS UNSIGNED) ASC, m.numero ASC"
        );
        $stmt->execute(['rid' => $restauranteId]);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $mesas = [];
        foreach ($rows as $r) {
            $mesas[] = [
                'id' => (int)$r['id'],
                'numero' => (string)$r['numero'],
                'nombre' => !empty($r['nombre']) ? (string)$r['nombre'] : null,
                'ocupada' => !empty($r['pedido_id']),
                'pedido_id' => !empty($r['pedido_id']) ? (int)$r['pedido_id'] : null,
                'consecutivo' => !empty($r['consecutivo']) ? (int)$r['consecutivo'] : null,
                'codigo' => $r['codigo'] ?? null,
                'total_mesa' => (int)($r['total_mesa'] ?? 0),
                'estado_pedido' => $r['estado_pedido'] ?? null,
                'tiempo_min' => isset($r['tiempo_min']) ? (int)$r['tiempo_min'] : null,
            ];
        }

        return $mesas;
    }

    /**
     * Libera una comanda/mesa activa por ID de pedido
     */
    public function liberarMesa(int $pedidoId, int $restauranteId): bool
    {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare(
            "UPDATE `{$this->table}` 
             SET `mesa_liberada_at` = NOW(),
                 `estado` = CASE WHEN `estado` IN ('recibido', 'listo', 'en_preparacion') THEN 'entregado' ELSE `estado` END,
                 `updated_at` = NOW()
             WHERE `id` = :id AND `restaurante_id` = :rid"
        );
        return $stmt->execute(['id' => $pedidoId, 'rid' => $restauranteId]);
    }

    /**
     * Libera una mesa específica por ID de mesa
     */
    public function liberarMesaPorId(int $mesaId, int $restauranteId): bool
    {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare(
            "UPDATE `{$this->table}` 
             SET `mesa_liberada_at` = NOW(),
                 `estado` = CASE WHEN `estado` IN ('recibido', 'listo', 'en_preparacion') THEN 'entregado' ELSE `estado` END,
                 `updated_at` = NOW()
             WHERE `mesa_id` = :mid AND `restaurante_id` = :rid AND `mesa_liberada_at` IS NULL"
        );
        return $stmt->execute(['mid' => $mesaId, 'rid' => $restauranteId]);
    }

    /**
     * Listado rápido de comandas activas para el Dashboard operativo
     *
     * @return array<int, array<string, mixed>>
     */
    public function listarComandasActivas(int $restauranteId, int $limite = 6): array
    {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare(
            "SELECT p.*, m.numero AS mesa_nombre,
                    (SELECT COUNT(*) FROM `pedido_items` pi WHERE pi.pedido_id = p.id) AS total_items,
                    TIMESTAMPDIFF(MINUTE, p.created_at, NOW()) AS minutos_transcurridos
             FROM `{$this->table}` p
             LEFT JOIN `mesas` m ON m.id = p.mesa_id
             WHERE p.restaurante_id = :rid
               AND p.estado IN ('recibido', 'en_preparacion', 'listo', 'generado', 'en_camino')
             ORDER BY p.created_at ASC
             LIMIT :limite"
        );
        $stmt->bindValue(':rid', $restauranteId, \PDO::PARAM_INT);
        $stmt->bindValue(':limite', $limite, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
