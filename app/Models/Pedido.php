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

    public const ESTADOS = [
        'generado' => [
            'label' => 'Generado',
            'color' => '#2563EB',
            'bg' => '#EFF6FF',
            'border' => '#BFDBFE',
            'final' => false,
        ],
        'en_preparacion' => [
            'label' => 'En Preparación',
            'color' => '#D97706',
            'bg' => '#FFFBEB',
            'border' => '#FDE68A',
            'final' => false,
        ],
        'listo' => [
            'label' => 'Listo',
            'color' => '#059669',
            'bg' => '#ECFDF5',
            'border' => '#A7F3D0',
            'final' => false,
        ],
        'entregado' => [
            'label' => 'Entregado',
            'color' => '#475569',
            'bg' => '#F1F5F9',
            'border' => '#CBD5E1',
            'final' => true,
        ],
        'cancelado' => [
            'label' => 'Cancelado',
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

        if (!empty($filtros['modalidad']) && isset(self::MODALIDADES[$filtros['modalidad']])) {
            $sql .= " AND p.modalidad = :modalidad";
            $params['modalidad'] = $filtros['modalidad'];
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

        if (!empty($filtros['modalidad']) && isset(self::MODALIDADES[$filtros['modalidad']])) {
            $sql .= " AND p.modalidad = :modalidad";
            $params['modalidad'] = $filtros['modalidad'];
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
        return $stmt->execute([
            'estado' => $nuevoEstado,
            'id' => $pedidoId,
            'rid' => $restauranteId,
        ]);
    }

    /**
     * Conteo agrupado de pedidos por estado
     */
    public function conteosPorEstado(int $restauranteId): array
    {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare(
            "SELECT `estado`, COUNT(*) AS total 
             FROM `{$this->table}` 
             WHERE `restaurante_id` = :rid 
             GROUP BY `estado`"
        );
        $stmt->execute(['rid' => $restauranteId]);
        $filas = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        $resultado = [
            'todos' => 0,
            'generado' => 0,
            'en_preparacion' => 0,
            'listo' => 0,
            'entregado' => 0,
            'cancelado' => 0,
            'pendientes' => 0,
        ];

        foreach ($filas as $est => $cant) {
            $cnt = (int)$cant;
            $resultado[$est] = $cnt;
            $resultado['todos'] += $cnt;
            if ($est === 'generado' || $est === 'en_preparacion') {
                $resultado['pendientes'] += $cnt;
            }
        }

        return $resultado;
    }

    /**
     * Métricas del mes para el restaurante (excluyendo pedidos cancelados)
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
               AND `estado` != 'cancelado'"
        );
        $stmt->execute(['rid' => $restauranteId, 'ym' => $ym]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $totalPedidos = (int)($row['total_pedidos'] ?? 0);
        $totalVentas = (int)($row['total_ventas'] ?? 0);
        $ticketPromedio = $totalPedidos > 0 ? (int)round($totalVentas / $totalPedidos) : 0;

        // Conteo de pedidos pendientes de atención
        $stmtPend = $pdo->prepare(
            "SELECT COUNT(*) FROM `{$this->table}`
             WHERE `restaurante_id` = :rid AND `estado` IN ('generado', 'en_preparacion')"
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
               AND `estado` != 'cancelado'
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
             WHERE `restaurante_id` = :rid AND `estado` != 'cancelado'
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
             WHERE p.restaurante_id = :rid AND p.estado != 'cancelado'
             GROUP BY pi.producto_id, pi.nombre_producto
             ORDER BY cantidad_vendida DESC, total_ventas DESC
             LIMIT :limite"
        );
        $stmt->bindValue(':rid', $restauranteId, PDO::PARAM_INT);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
