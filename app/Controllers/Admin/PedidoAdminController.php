<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Model;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\Pedido;
use App\Models\Restaurante;
use App\Services\HorarioService;

class PedidoAdminController extends Controller
{
    private Pedido $pedidoModel;
    private Restaurante $restauranteModel;

    public function __construct(?Pedido $pedidoModel = null, ?Restaurante $restauranteModel = null)
    {
        $this->pedidoModel = $pedidoModel ?? new Pedido();
        $this->restauranteModel = $restauranteModel ?? new Restaurante();
    }

    /**
     * Listado y gestión en tiempo real de pedidos recibidos
     */
    public function index(Request $request): Response
    {
        $tenantId = Auth::tenantId();
        if (!$tenantId) {
            return $this->redirect(app_url('/login'));
        }

        $restaurante = $this->restauranteModel->find($tenantId);
        if (!$restaurante) {
            return $this->redirect(app_url('/login'));
        }

        // Filtros y Vista Operativa (Separación de Domicilios vs Local)
        $vista = trim((string)$request->query('vista', ''));
        $estado = trim((string)$request->query('estado', ''));
        $modalidad = trim((string)$request->query('modalidad', ''));
        $busqueda = trim((string)$request->query('q', ''));
        $fechaDesde = trim((string)$request->query('fecha_desde', ''));
        $fechaHasta = trim((string)$request->query('fecha_hasta', ''));
        $jornada = trim((string)$request->query('jornada', 'actual'));
        if (!in_array($jornada, ['actual', 'ayer', 'todas'], true)) {
            $jornada = 'actual';
        }
        $pagina = max(1, (int)$request->query('page', 1));
        $porPagina = 15;

        // Si se seleccionó una pestaña de vista, sincronizar modalidad
        if ($vista === 'domicilios') {
            $modalidad = 'domicilio';
        } elseif ($vista === 'local') {
            $modalidad = 'local';
        } elseif ($vista === 'todos') {
            $modalidad = '';
        } else {
            // Deducir vista a partir de modalidad
            if ($modalidad === 'domicilio') {
                $vista = 'domicilios';
            } elseif ($modalidad === 'local' || $modalidad === 'mesa' || $modalidad === 'recoger') {
                $vista = 'local';
            } else {
                $vista = 'todos';
            }
        }

        // Pedidos en Vivo: Exclusivo y enfocado en la jornada operativa actual (Hoy)
        $rangoJornada = HorarioService::obtenerRangoJornada($tenantId, 'actual');

        $filtros = [
            'estado' => $estado,
            'modalidad' => $modalidad,
            'q' => $busqueda,
            'datetime_desde' => $rangoJornada['desde'],
            'datetime_hasta' => $rangoJornada['hasta'],
            'mantener_pendientes' => true,
        ];

        $pedidos = $this->pedidoModel->listarPorRestaurante($tenantId, $filtros, $pagina, $porPagina);
        $totalPedidos = $this->pedidoModel->contarPorRestaurante($tenantId, $filtros);
        $conteos = $this->pedidoModel->conteosPorEstado($tenantId, $filtros);
        $totalPaginas = max(1, (int)ceil($totalPedidos / $porPagina));

        // Enriquecer cada pedido con enlace directo a WhatsApp y datos de visualización
        foreach ($pedidos as &$p) {
            $p['wa_enlace'] = $this->generarEnlaceWhatsAppCliente($p, $restaurante['nombre']);
            $p['estado_info'] = Pedido::ESTADOS[$p['estado']] ?? Pedido::ESTADOS['recibido'];
            $p['modalidad_label'] = Pedido::MODALIDADES[$p['modalidad']] ?? ucfirst($p['modalidad']);
        }
        unset($p);

        // Separar pedidos en dos listas para vistas de doble tabla ordenada
        $pedidosDomicilio = array_values(array_filter($pedidos, static fn($p) => $p['modalidad'] === 'domicilio'));
        $pedidosLocal = array_values(array_filter($pedidos, static fn($p) => $p['modalidad'] !== 'domicilio'));

        return $this->render('admin/pedidos/index', [
            'title' => 'Gestión de Pedidos en Vivo — ' . $restaurante['nombre'],
            'tenant' => $restaurante,
            'pedidos' => $pedidos,
            'pedidosDomicilio' => $pedidosDomicilio,
            'pedidosLocal' => $pedidosLocal,
            'vista' => $vista,
            'rangoJornada' => $rangoJornada,
            'filtros' => $filtros,
            'conteos' => $conteos,
            'pagina' => $pagina,
            'totalPaginas' => $totalPaginas,
            'totalPedidos' => $totalPedidos,
            'estadosConfig' => Pedido::ESTADOS,
            'modalidadesConfig' => Pedido::MODALIDADES,
        ], 'admin');
    }

    /**
     * Actualiza el estado del pedido (soporta peticiones normales y AJAX)
     * Cuando el estado pasa a "en_camino", retorna datos estructurados para disparar popup de aviso por WhatsApp al cliente.
     */
    public function cambiarEstado(Request $request, array $params): Response
    {
        $tenantId = Auth::tenantId();
        $id = (int)($params['id'] ?? 0);
        $nuevoEstado = trim((string)$request->post('estado', ''));

        if ($id <= 0 || !isset(Pedido::ESTADOS[$nuevoEstado])) {
            if ($request->isAjax()) {
                return $this->json(['success' => false, 'error' => 'Estado inválido'], 400);
            }
            Session::flash('error', 'Estado de pedido inválido.');
            return $this->back();
        }

        try {
            $this->pedidoModel->cambiarEstado($id, $tenantId, $nuevoEstado);

            $pedido = $this->pedidoModel->find($id, $tenantId);
            $avisoWhatsapp = null;

            // Al pasar a 'en_camino', preparar datos para el popup de notificación por WhatsApp
            if ($nuevoEstado === 'en_camino' && $pedido) {
                $tel = preg_replace('/[^0-9]/', '', (string)($pedido['cliente_telefono'] ?? ''));
                if (!empty($tel)) {
                    if (str_starts_with($tel, '3') && strlen($tel) === 10) {
                        $tel = '57' . $tel;
                    }
                    $clienteNombre = !empty($pedido['cliente_nombre']) ? $pedido['cliente_nombre'] : 'Cliente';
                    $restaurante = $this->restauranteModel->find($tenantId);
                    $restauranteNombre = $restaurante['nombre'] ?? 'Mesaly';
                    $codigo = $pedido['codigo'] ?? "#{$id}";
                    $mensajeAviso = "Hola {$clienteNombre}, tu pedido #{$codigo} de {$restauranteNombre} ya va en camino a tu dirección con el domiciliario. ¡Pronto llegará!";
                    $waUrl = "https://wa.me/{$tel}?text=" . rawurlencode($mensajeAviso);

                    $avisoWhatsapp = [
                        'activo' => true,
                        'cliente_nombre' => $clienteNombre,
                        'cliente_telefono' => $tel,
                        'codigo' => $codigo,
                        'mensaje' => $mensajeAviso,
                        'wa_url' => $waUrl,
                    ];
                }
            }

            if ($request->isAjax()) {
                $estadoInfo = Pedido::ESTADOS[$nuevoEstado];
                $conteos = $this->pedidoModel->conteosPorEstado($tenantId);
                return $this->json([
                    'success' => true,
                    'estado' => $nuevoEstado,
                    'label' => $estadoInfo['label'],
                    'color' => $estadoInfo['color'],
                    'bg' => $estadoInfo['bg'],
                    'border' => $estadoInfo['border'] ?? '#CBD5E1',
                    'aviso_whatsapp' => $avisoWhatsapp,
                    'conteos' => $conteos,
                ]);
            }

            Session::flash('success', "Estado del pedido actualizado a: " . Pedido::ESTADOS[$nuevoEstado]['label']);
        } catch (\Throwable $e) {
            if ($request->isAjax()) {
                return $this->json(['success' => false, 'error' => $e->getMessage()], 500);
            }
            Session::flash('error', 'Error al cambiar estado: ' . $e->getMessage());
        }

        return $this->back();
    }

    /**
     * Retorna el detalle completo de un pedido en formato JSON para el modal
     */
    public function detalleJson(Request $request, array $params): Response
    {
        $tenantId = Auth::tenantId();
        $id = (int)($params['id'] ?? 0);

        $pedido = $this->pedidoModel->obtenerDetalleCompleto($id, $tenantId);
        if (!$pedido) {
            return $this->json(['success' => false, 'error' => 'Pedido no encontrado'], 404);
        }

        // Formatear montos para la vista
        $pedido['total_formateado'] = format_cop((int)$pedido['total']);
        $pedido['subtotal_formateado'] = format_cop((int)$pedido['subtotal']);
        $pedido['costo_envio_formateado'] = format_cop((int)$pedido['costo_envio']);
        $pedido['fecha_formateada'] = date('d/m/Y h:i A', strtotime($pedido['created_at']));
        $pedido['estado_info'] = Pedido::ESTADOS[$pedido['estado']] ?? Pedido::ESTADOS['recibido'];
        $pedido['modalidad_label'] = Pedido::MODALIDADES[$pedido['modalidad']] ?? ucfirst($pedido['modalidad']);

        foreach ($pedido['items'] as &$it) {
            $it['precio_unitario_formateado'] = format_cop((int)$it['precio_unitario']);
            $it['total_linea_formateado'] = format_cop((int)$it['total_linea']);
            foreach ($it['opciones'] as &$opc) {
                $opc['precio_extra_formateado'] = (int)$opc['precio_extra'] > 0 ? '+ ' . format_cop((int)$opc['precio_extra']) : 'Sin costo';
            }
            unset($opc);
        }
        unset($it);

        return $this->json([
            'success' => true,
            'pedido' => $pedido,
        ]);
    }

    /**
     * Redirige hacia la vista de pedidos abriendo directamente el modal de la comanda indicada
     */
    public function verComanda(Request $request, array $params): Response
    {
        $id = (int)($params['id'] ?? 0);
        return $this->redirect(app_url('/admin/pedidos?pedido=' . $id));
    }

    /**
     * Libera una mesa asociada a una comanda en curso
     */
    public function liberarMesa(Request $request, array $params): Response
    {
        $tenantId = Auth::tenantId();
        if (!$tenantId) {
            return $this->redirect(app_url('/login'));
        }

        $id = (int)($params['id'] ?? 0);
        $pedido = $this->pedidoModel->find($id, $tenantId);
        if ($pedido && !empty($pedido['mesa_id'])) {
            $this->pedidoModel->liberarMesaPorId((int)$pedido['mesa_id'], $tenantId);
        } else {
            $this->pedidoModel->liberarMesa($id, $tenantId);
        }

        if ($request->isAjax()) {
            return $this->json([
                'success' => true,
                'mensaje' => 'Mesa liberada exitosamente. Ahora aparece como disponible.',
            ]);
        }

        Session::flash('success', 'Mesa liberada exitosamente.');
        return $this->back();
    }

    /**
     * Libera una mesa directamente por su ID de mesa
     */
    public function liberarMesaPorId(Request $request, array $params): Response
    {
        $tenantId = Auth::tenantId();
        if (!$tenantId) {
            return $this->redirect(app_url('/login'));
        }

        $mesaId = (int)($params['id'] ?? 0);
        $this->pedidoModel->liberarMesaPorId($mesaId, $tenantId);

        if ($request->isAjax()) {
            return $this->json([
                'success' => true,
                'mensaje' => 'Mesa liberada y marcada como disponible.',
            ]);
        }

        Session::flash('success', 'Mesa liberada exitosamente.');
        return $this->back();
    }

    /**
     * Construye enlace estructurado a WhatsApp para contactar al cliente sobre su orden
     */
    private function generarEnlaceWhatsAppCliente(array $pedido, string $nombreRestaurante): ?string
    {
        $tel = preg_replace('/\D/', '', (string)($pedido['cliente_telefono'] ?? ''));
        if (empty($tel) || strlen($tel) < 7) {
            return null;
        }

        // Agregar indicativo de Colombia si no lo tiene
        if (strlen($tel) === 10 && str_starts_with($tel, '3')) {
            $tel = '57' . $tel;
        }

        $mensaje = "Hola {$pedido['cliente_nombre']}, te saludamos de {$nombreRestaurante}. Te escribimos con relación a tu pedido #{$pedido['codigo']}";

        if ($pedido['estado'] === 'listo') {
            $mensaje .= ($pedido['modalidad'] === 'recoger')
                ? " ya está listo para que pases a recogerlo."
                : " ya se encuentra empacado y listo para salir.";
        } elseif ($pedido['estado'] === 'en_camino') {
            $mensaje .= " ya va en camino a tu dirección con nuestro domiciliario. ¡Pronto llegará!";
        } elseif ($pedido['estado'] === 'entregado') {
            $mensaje .= ". Tu pedido ha sido entregado exitosamente. ¡Muchas gracias por tu compra!";
        } elseif ($pedido['estado'] === 'rechazado') {
            $mensaje .= ". Lamentamos informarte que tu pedido no pudo ser aceptado por la cocina.";
        } else {
            $mensaje .= ". Hemos recibido tu orden y ya está en proceso de atención.";
        }

        return "https://wa.me/{$tel}?text=" . rawurlencode($mensaje);
    }

    /**
     * Polling liviano para notificaciones en vivo y campanita de alertas
     * Retorna si hay nuevos pedidos desde since_id, conteos actuales y el max_id
     */
    public function pollNuevos(Request $request): Response
    {
        $tenantId = Auth::tenantId();
        if (!$tenantId) {
            return $this->json(['success' => false, 'error' => 'No autorizado'], 401);
        }

        $sinceId = (int)$request->get('since_id', 0);

        try {
            $pdo = Model::getPdo();

            // ID más alto actual
            $stmtMax = $pdo->prepare("SELECT COALESCE(MAX(id), 0) FROM pedidos WHERE restaurante_id = :rid");
            $stmtMax->execute(['rid' => $tenantId]);
            $maxId = (int)$stmtMax->fetchColumn();

            // Pedidos nuevos desde sinceId
            $nuevosPedidos = [];
            if ($sinceId > 0 && $maxId > $sinceId) {
                $stmtNuevos = $pdo->prepare(
                    "SELECT p.id, p.consecutivo, p.codigo, p.cliente_nombre, p.modalidad, p.total, p.estado, p.created_at, p.mesa_id,
                            m.numero AS mesa_numero
                     FROM pedidos p
                     LEFT JOIN mesas m ON m.id = p.mesa_id
                     WHERE p.restaurante_id = :rid AND p.id > :since 
                     ORDER BY p.id DESC"
                );
                $stmtNuevos->execute(['rid' => $tenantId, 'since' => $sinceId]);
                $nuevosPedidos = $stmtNuevos->fetchAll(\PDO::FETCH_ASSOC);
            }

            // Conteos de la jornada en vivo (Hoy) para sincronizar las insignias en tiempo real
            $rangoHoy = HorarioService::obtenerRangoJornada($tenantId, 'actual');
            $filtrosPoll = [
                'datetime_desde' => $rangoHoy['desde'],
                'datetime_hasta' => $rangoHoy['hasta'],
                'mantener_pendientes' => true,
            ];
            $conteos = $this->pedidoModel->conteosPorEstado($tenantId, $filtrosPoll);
            $metricasMes = $this->pedidoModel->metricasMes($tenantId);
            $metricasHoy = $this->pedidoModel->metricasHoy($tenantId);
            $mesasEnVivo = $this->pedidoModel->mesasEstadoEnVivo($tenantId);
            $comandasActivas = $this->pedidoModel->listarComandasActivas($tenantId, 6);
            $restaurante = $this->restauranteModel->find($tenantId);
            $slug = $restaurante['slug'] ?? '';
            $csrfToken = csrf_token();

            return $this->json([
                'success' => true,
                'max_id' => $maxId,
                'nuevos_count' => count($nuevosPedidos),
                'nuevos_pedidos' => $nuevosPedidos,
                'conteos' => $conteos,
                'csrf_token' => $csrfToken,
                'metricas_hoy' => [
                    'ventas_hoy' => format_cop((int)$metricasHoy['ventas_hoy']),
                    'pedidos_hoy' => (int)$metricasHoy['pedidos_hoy'],
                    'ticket_promedio_hoy' => format_cop((int)$metricasHoy['ticket_promedio_hoy']),
                    'mesas_ocupadas' => (int)$metricasHoy['mesas_ocupadas'],
                    'total_mesas' => (int)$metricasHoy['total_mesas'],
                    'pendientes' => (int)$metricasHoy['pendientes'],
                ],
                'metricas_mes' => [
                    'ventas_mes' => format_cop((int)$metricasMes['ventas_mes']),
                    'pedidos_mes' => (int)$metricasMes['pedidos_mes'],
                    'ticket_promedio' => format_cop((int)$metricasMes['ticket_promedio']),
                    'pendientes' => (int)$metricasMes['pendientes'],
                ],
                'mesas' => array_map(function ($m) use ($slug) {
                    return [
                        'id' => (int)$m['id'],
                        'numero' => (string)$m['numero'],
                        'nombre' => $m['nombre'],
                        'ocupada' => (bool)$m['ocupada'],
                        'pedido_id' => !empty($m['pedido_id']) ? (int)$m['pedido_id'] : null,
                        'consecutivo' => !empty($m['consecutivo']) ? (int)$m['consecutivo'] : null,
                        'codigo' => $m['codigo'],
                        'total_mesa' => (int)$m['total_mesa'],
                        'total_formateado' => format_cop((int)$m['total_mesa']),
                        'estado_pedido' => $m['estado_pedido'],
                        'tiempo_min' => isset($m['tiempo_min']) ? (int)$m['tiempo_min'] : null,
                        'url_qr' => app_url('/' . $slug . '?mesa=' . urlencode($m['numero'])),
                        'url_comanda' => !empty($m['pedido_id']) ? app_url('/admin/pedidos?pedido=' . $m['pedido_id']) : null,
                        'url_liberar' => app_url('/admin/mesas/' . $m['id'] . '/liberar'),
                    ];
                }, $mesasEnVivo),
                'comandas_activas' => array_map(function ($c) {
                    $min = (int)($c['minutos_transcurridos'] ?? 0);
                    return [
                        'id' => (int)$c['id'],
                        'codigo' => $c['codigo'],
                        'consecutivo' => $c['consecutivo'] ?? $c['codigo'],
                        'modalidad' => $c['modalidad'],
                        'mesa_nombre' => $c['mesa_nombre'] ?? $c['mesa_numero'] ?? null,
                        'cliente_nombre' => $c['cliente_nombre'] ?? 'Cliente',
                        'total_items' => (int)($c['total_items'] ?? 1),
                        'estado' => $c['estado'],
                        'estado_formateado' => ucfirst(str_replace('_', ' ', (string)$c['estado'])),
                        'minutos_transcurridos' => $min,
                        'tiempo_texto' => $min === 0 ? 'Justo ahora' : 'Hace ' . $min . ' min',
                        'url_comanda' => app_url('/admin/pedidos?pedido=' . $c['id']),
                    ];
                }, $comandasActivas),
            ]);
        } catch (\Throwable $e) {
            return $this->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
