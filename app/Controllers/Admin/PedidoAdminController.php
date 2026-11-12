<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\Pedido;
use App\Models\Restaurante;

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

        // Filtros
        $estado = trim((string)$request->query('estado', ''));
        $modalidad = trim((string)$request->query('modalidad', ''));
        $busqueda = trim((string)$request->query('q', ''));
        $fechaDesde = trim((string)$request->query('fecha_desde', ''));
        $fechaHasta = trim((string)$request->query('fecha_hasta', ''));
        $pagina = max(1, (int)$request->query('page', 1));
        $porPagina = 15;

        $filtros = [
            'estado' => $estado,
            'modalidad' => $modalidad,
            'q' => $busqueda,
            'fecha_desde' => $fechaDesde,
            'fecha_hasta' => $fechaHasta,
        ];

        $pedidos = $this->pedidoModel->listarPorRestaurante($tenantId, $filtros, $pagina, $porPagina);
        $totalPedidos = $this->pedidoModel->contarPorRestaurante($tenantId, $filtros);
        $conteos = $this->pedidoModel->conteosPorEstado($tenantId);
        $totalPaginas = max(1, (int)ceil($totalPedidos / $porPagina));

        // Enriquecer cada pedido con enlace directo a WhatsApp y datos de visualización
        foreach ($pedidos as &$p) {
            $p['wa_enlace'] = $this->generarEnlaceWhatsAppCliente($p, $restaurante['nombre']);
            $p['estado_info'] = Pedido::ESTADOS[$p['estado']] ?? Pedido::ESTADOS['generado'];
            $p['modalidad_label'] = Pedido::MODALIDADES[$p['modalidad']] ?? ucfirst($p['modalidad']);
        }
        unset($p);

        return $this->render('admin/pedidos/index', [
            'title' => 'Gestión de Pedidos — ' . $restaurante['nombre'],
            'tenant' => $restaurante,
            'pedidos' => $pedidos,
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

            if ($request->isAjax()) {
                $estadoInfo = Pedido::ESTADOS[$nuevoEstado];
                return $this->json([
                    'success' => true,
                    'estado' => $nuevoEstado,
                    'label' => $estadoInfo['label'],
                    'color' => $estadoInfo['color'],
                    'bg' => $estadoInfo['bg'],
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
        $pedido['estado_info'] = Pedido::ESTADOS[$pedido['estado']] ?? Pedido::ESTADOS['generado'];
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
                : " ya está listo y va en camino a tu dirección.";
        } elseif ($pedido['estado'] === 'en_preparacion') {
            $mensaje .= " ya se encuentra en cocina siendo preparado con mucho gusto.";
        } else {
            $mensaje .= ". ¡Muchas gracias por tu compra!";
        }

        return "https://wa.me/{$tel}?text=" . rawurlencode($mensaje);
    }
}
