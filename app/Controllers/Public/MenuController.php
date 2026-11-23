<?php

declare(strict_types=1);

namespace App\Controllers\Public;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\TenantContext;
use App\Core\View;
use App\Models\Categoria;
use App\Models\Horario;
use App\Models\MetodoPagoNegocio;
use App\Models\Pedido;
use App\Models\Producto;
use App\Models\Restaurante;
use App\Services\HorarioService;
use App\Services\WhatsAppService;

class MenuController extends Controller
{
    /**
     * Muestra el menú público del restaurante.
     */
    public function show(Request $request, array $params): Response
    {
        $slug = trim($params['slug'] ?? '');
        $restauranteModel = new Restaurante();
        $restaurante = $restauranteModel->findBySlug($slug);

        if (!$restaurante) {
            return $this->renderError(404, 'Restaurante no encontrado', 'El restaurante que buscas no existe o el enlace es incorrecto.');
        }

        // Verificar estado de suscripción
        if (!empty($restaurante['fecha_vencimiento'])) {
            $vencimiento = strtotime($restaurante['fecha_vencimiento'] . ' 23:59:59');
            // 3 días de gracia
            $gracia = $vencimiento + (3 * 86400);
            if (time() > $gracia && empty($restaurante['en_prueba'])) {
                return $this->renderError(403, 'Menú no disponible', 'Este menú digital se encuentra temporalmente inactivo.');
            }
        }

        $restauranteId = (int)$restaurante['id'];
        TenantContext::setTenant($restaurante);

        // Detección de Mesa:
        // - Si se solicita salir de mesa (?salir_mesa=1): se limpia la sesión y queda en modo general.
        // - Si viene explícitamente ?mesa=N en la URL: se activa la mesa indicada.
        // - Si NO viene ?mesa en la URL: es el enlace público normal (para domicilios y redes sociales),
        //   por lo que se limpia cualquier residuo previo de mesa para permitir pedir a domicilio con libertad.
        $mesaNumero = null;
        $sessionKey = 'mesaly_mesa_' . $restauranteId;
        $mesaParam = (int)$request->get('mesa', 0);
        $mesaModel = new \App\Models\Mesa();

        if ($request->query('salir_mesa') !== null) {
            unset($_SESSION[$sessionKey]);
            $mesaNumero = null;
        } elseif ($mesaParam > 0) {
            $mesaDb = $mesaModel->findByNumero($restauranteId, $mesaParam);
            if ($mesaDb && !empty($mesaDb['activa'])) {
                $mesaNumero = $mesaParam;
                $_SESSION[$sessionKey] = [
                    'numero' => $mesaNumero,
                    'id' => (int)$mesaDb['id'],
                    'expires_at' => time() + (4 * 3600), // 4 horas
                ];
            }
        } else {
            // Enlace general sin mesa (ej: /la-parrilla-paisa)
            unset($_SESSION[$sessionKey]);
            $mesaNumero = null;
        }

        // Consultar horarios y estado de apertura
        $horarioModel = new Horario();
        $horarios = $horarioModel->allByRestaurantId($restauranteId);
        $estadoHorario = HorarioService::estaAbierto($horarios);

        // Consultar métodos de pago aceptados
        $metodosPago = [];

        // Consultar categorías activas
        $categoriaModel = new Categoria();
        $categorias = $categoriaModel->allActiveByRestaurantId($restauranteId);

        // Consultar productos activos con sus grupos y opciones de modificadores
        $productoModel = new Producto();
        $todosLosProductos = $productoModel->allForPublicCatalog($restauranteId);

        // Agrupar productos por categoría
        $productosPorCategoria = [];
        $categoriaIdsConProductos = [];
        foreach ($categorias as $cat) {
            $productosPorCategoria[$cat['id']] = [];
        }

        foreach ($todosLosProductos as $prod) {
            $catId = (int)$prod['categoria_id'];
            if (isset($productosPorCategoria[$catId])) {
                $productosPorCategoria[$catId][] = $prod;
                $categoriaIdsConProductos[$catId] = true;
            }
        }

        // Filtrar categorías que contengan al menos 1 producto
        $categoriasConProductos = array_filter($categorias, function ($cat) use ($categoriaIdsConProductos) {
            return isset($categoriaIdsConProductos[$cat['id']]);
        });

        // Contar destacados
        $destacados = array_filter($todosLosProductos, function ($p) {
            return !empty($p['destacado']) && !empty($p['disponible']);
        });

        return $this->render('public/menu', [
            'restaurante' => $restaurante,
            'mesaNumero' => $mesaNumero,
            'estadoHorario' => $estadoHorario,
            'metodosPago' => $metodosPago,
            'categorias' => array_values($categoriasConProductos),
            'productosPorCategoria' => $productosPorCategoria,
            'destacados' => array_values($destacados),
            'todosLosProductos' => $todosLosProductos,
            'title' => $restaurante['nombre'] . ' — Menú Digital',
        ], 'menu');
    }

    /**
     * Captura el número de mesa en sesión y redirige al menú limpio.
     */
    public function mesa(Request $request, array $params): Response
    {
        $slug = trim($params['slug'] ?? '');
        $numero = (int)($params['numero'] ?? 0);

        $restauranteModel = new Restaurante();
        $restaurante = $restauranteModel->findBySlug($slug);

        if (!$restaurante) {
            return $this->renderError(404, 'Restaurante no encontrado', 'El enlace de mesa no corresponde a un restaurante válido.');
        }

        if ($numero > 0) {
            $sessionKey = 'mesaly_mesa_' . $restaurante['id'];
            $_SESSION[$sessionKey] = [
                'numero' => $numero,
                'expires_at' => time() + (4 * 3600), // 4 horas
            ];
        }

        // Redirigir al menú indicando la mesa asignada en la URL
        header('Location: ' . app_url('/' . $slug . '?mesa=' . $numero));
        exit;
    }

    /**
     * Limpia la mesa asignada en sesión ("No estoy en mesa").
     */
    public function limpiarMesa(Request $request, array $params): Response
    {
        $slug = trim($params['slug'] ?? '');
        $restauranteModel = new Restaurante();
        $restaurante = $restauranteModel->findBySlug($slug);

        if ($restaurante) {
            $sessionKey = 'mesaly_mesa_' . $restaurante['id'];
            unset($_SESSION[$sessionKey]);
        }

        header('Location: ' . app_url('/' . $slug));
        exit;
    }

    /**
     * Procesa la creación de un pedido desde el carrito y genera la URL a WhatsApp.
     */
    public function crearPedido(Request $request, array $params): Response
    {
        $slug = trim($params['slug'] ?? '');
        $restauranteModel = new Restaurante();
        $restaurante = $restauranteModel->findBySlug($slug);

        if (!$restaurante) {
            return $this->json(['ok' => false, 'error' => 'Restaurante no encontrado.'], 404);
        }

        $restauranteId = (int)$restaurante['id'];

        // Obtener datos (JSON o POST unificados vía Request)
        $input = $request->all();
        if (empty($input)) {
            $rawInput = file_get_contents('php://input');
            if (!empty($rawInput)) {
                $decoded = json_decode($rawInput, true);
                if (is_array($decoded)) {
                    $input = $decoded;
                }
            }
        }

        // 1. Anti-Bot Honeypot: el campo "empresa" o "website" debe estar vacío
        if (!empty($input['empresa']) || !empty($input['website'])) {
            return $this->json(['ok' => false, 'error' => 'Solicitud rechazada.'], 400);
        }

        // 2. Anti-Abuso: Rate limit por IP (máx 5 pedidos en 10 minutos)
        $ip = $request->ip();
        $pedidoModel = new Pedido();
        if ($pedidoModel->haExcedidoRateLimit($ip, 5, 10)) {
            return $this->json([
                'ok' => false,
                'error' => 'Has realizado varios pedidos recientemente. Por favor espera unos minutos o comunícate directamente con el restaurante.'
            ], 429);
        }

        // 3. Validar estado y horario de atención del restaurante
        if (empty($restaurante['recibiendo_pedidos'])) {
            return $this->json([
                'ok' => false,
                'error' => 'En este momento el restaurante no está recibiendo pedidos por la plataforma.'
            ], 400);
        }

        $horarioModel = new Horario();
        $horarios = $horarioModel->allByRestaurantId($restauranteId);
        $estadoHorario = HorarioService::estaAbierto($horarios);
        if (!$estadoHorario['abierto']) {
            return $this->json([
                'ok' => false,
                'error' => 'El local se encuentra cerrado en este momento (' . $estadoHorario['mensaje'] . '). No es posible realizar pedidos fuera del horario de atención.'
            ], 400);
        }

        // 4. Validar modalidad
        $modalidad = trim((string)($input['modalidad'] ?? 'mesa'));
        $modalidadesValidas = ['mesa', 'domicilio', 'recoger'];
        if (!in_array($modalidad, $modalidadesValidas, true)) {
            return $this->json(['ok' => false, 'error' => 'Modalidad de entrega no válida.'], 400);
        }

        if ($modalidad === 'mesa' && empty($restaurante['acepta_mesa'])) {
            return $this->json(['ok' => false, 'error' => 'El restaurante no tiene habilitada la atención en mesa.'], 400);
        }
        if ($modalidad === 'domicilio' && empty($restaurante['acepta_domicilio'])) {
            return $this->json(['ok' => false, 'error' => 'El restaurante no tiene habilitado el servicio a domicilio.'], 400);
        }
        if ($modalidad === 'recoger' && empty($restaurante['acepta_recoger'])) {
            return $this->json(['ok' => false, 'error' => 'El restaurante no tiene habilitado el servicio para llevar.'], 400);
        }

        // 5. Validar datos según modalidad
        $clienteNombre = trim((string)($input['cliente_nombre'] ?? ''));
        $clienteTelefono = trim((string)($input['cliente_telefono'] ?? ''));
        $mesaNumero = null;

        if ($modalidad === 'mesa') {
            $mesaInput = (int)($input['mesa_numero'] ?? 0);
            $sessionKey = 'mesaly_mesa_' . $restaurante['id'];
            if ($mesaInput > 0) {
                $mesaNumero = $mesaInput;
            } elseif (!empty($_SESSION[$sessionKey]['numero'])) {
                $mesaNumero = (int)$_SESSION[$sessionKey]['numero'];
            }

            if (!$mesaNumero || $mesaNumero <= 0) {
                return $this->json(['ok' => false, 'error' => 'Por favor indica el número de tu mesa.'], 400);
            }

            if (empty($clienteNombre)) {
                $clienteNombre = 'Mesa ' . $mesaNumero;
            }
        } elseif ($modalidad === 'domicilio') {
            if (empty($clienteNombre)) {
                return $this->json(['ok' => false, 'error' => 'Por favor escribe tu nombre completo.'], 400);
            }
            if (empty($clienteTelefono) || strlen(preg_replace('/[^0-9]/', '', $clienteTelefono)) < 7) {
                return $this->json(['ok' => false, 'error' => 'Por favor ingresa un número de teléfono o celular válido.'], 400);
            }
            if (empty($input['direccion'])) {
                return $this->json(['ok' => false, 'error' => 'Por favor escribe tu dirección de entrega.'], 400);
            }
        } elseif ($modalidad === 'recoger') {
            if (empty($clienteNombre)) {
                return $this->json(['ok' => false, 'error' => 'Por favor escribe tu nombre completo.'], 400);
            }
            if (empty($clienteTelefono) || strlen(preg_replace('/[^0-9]/', '', $clienteTelefono)) < 7) {
                return $this->json(['ok' => false, 'error' => 'Por favor ingresa un número de teléfono o celular válido.'], 400);
            }
        }

        // 6. Método de pago acordado directamente con el restaurante (contraentrega)
        $metodoPago = 'acordar';

        // 7. Validar ítems del carrito
        $rawItems = $input['items'] ?? [];
        if (!is_array($rawItems) || empty($rawItems)) {
            return $this->json(['ok' => false, 'error' => 'Tu carrito de compras está vacío.'], 400);
        }

        if (count($rawItems) > 20) {
            return $this->json(['ok' => false, 'error' => 'El pedido supera el máximo permitido de 20 platos por pedido.'], 400);
        }

        // Cargar productos del restaurante para recalcular precios de forma 100% segura en el servidor
        $productoModel = new Producto();
        $catalogo = $productoModel->allForPublicCatalog((int)$restaurante['id']);
        $catalogoPorId = [];
        foreach ($catalogo as $p) {
            $catalogoPorId[(int)$p['id']] = $p;
        }

        $itemsValidados = [];
        $subtotalCalculado = 0;

        foreach ($rawItems as $idx => $rawItem) {
            $prodId = (int)($rawItem['producto_id'] ?? 0);
            $cantidad = (int)($rawItem['cantidad'] ?? 1);
            $nota = trim((string)($rawItem['nota'] ?? ''));

            if (!isset($catalogoPorId[$prodId])) {
                return $this->json(['ok' => false, 'error' => "Uno de los productos seleccionados no existe o ya no está disponible."], 400);
            }

            $prod = $catalogoPorId[$prodId];

            if (empty($prod['disponible'])) {
                return $this->json(['ok' => false, 'error' => "El plato '{$prod['nombre']}' se encuentra agotado en este momento."], 400);
            }

            if ($cantidad < 1 || $cantidad > 10) {
                return $this->json(['ok' => false, 'error' => "La cantidad para '{$prod['nombre']}' debe ser entre 1 y 10 unidades."], 400);
            }

            // Validar modificadores del producto
            $opcionesValidadas = [];
            $extraOpcionesUnitario = 0;
            $rawOpcionesIds = $rawItem['opciones_ids'] ?? [];
            if (!is_array($rawOpcionesIds)) {
                $rawOpcionesIds = [];
            }

            // Mapear opciones permitidas para este producto
            $gruposMap = [];
            $opcionesMap = [];
            foreach ($prod['grupos'] as $grp) {
                $gruposMap[$grp['id']] = $grp;
                foreach ($grp['opciones'] as $opc) {
                    $opcionesMap[$opc['id']] = [
                        'grupo_id' => $grp['id'],
                        'nombre_grupo' => $grp['nombre'],
                        'nombre_opcion' => $opc['nombre'],
                        'precio_extra' => (int)$opc['precio_extra'],
                    ];
                }
            }

            // Contar selecciones por grupo para validar obligatoriedad y límites
            $seleccionesPorGrupo = [];
            foreach ($rawOpcionesIds as $opcId) {
                $opcId = (int)$opcId;
                if (isset($opcionesMap[$opcId])) {
                    $info = $opcionesMap[$opcId];
                    $gid = $info['grupo_id'];
                    $seleccionesPorGrupo[$gid] = ($seleccionesPorGrupo[$gid] ?? 0) + 1;
                    $extraOpcionesUnitario += $info['precio_extra'];
                    $opcionesValidadas[] = [
                        'nombre_grupo' => $info['nombre_grupo'],
                        'nombre_opcion' => $info['nombre_opcion'],
                        'precio_extra' => $info['precio_extra'],
                    ];
                }
            }

            // Validar grupos obligatorios
            foreach ($prod['grupos'] as $grp) {
                $gid = (int)$grp['id'];
                $cantSeleccionada = $seleccionesPorGrupo[$gid] ?? 0;

                if (!empty($grp['obligatorio']) && $cantSeleccionada < (int)$grp['min_selecciones']) {
                    return $this->json([
                        'ok' => false,
                        'error' => "Debes seleccionar las opciones requeridas en '{$grp['nombre']}' para '{$prod['nombre']}'."
                    ], 400);
                }

                if ($grp['tipo'] === 'unica' && $cantSeleccionada > 1) {
                    return $this->json([
                        'ok' => false,
                        'error' => "Solo puedes elegir una opción en '{$grp['nombre']}' para '{$prod['nombre']}'."
                    ], 400);
                }

                if ($grp['max_selecciones'] > 0 && $cantSeleccionada > (int)$grp['max_selecciones']) {
                    return $this->json([
                        'ok' => false,
                        'error' => "Has superado el máximo de opciones permitidas en '{$grp['nombre']}' para '{$prod['nombre']}'."
                    ], 400);
                }
            }

            $precioUnitario = (int)$prod['precio'] + $extraOpcionesUnitario;
            $totalLinea = $precioUnitario * $cantidad;
            $subtotalCalculado += $totalLinea;

            $itemsValidados[] = [
                'producto_id' => $prodId,
                'nombre_producto' => $prod['nombre'],
                'precio_unitario' => (int)$prod['precio'],
                'cantidad' => $cantidad,
                'nota' => mb_substr($nota, 0, 300),
                'total_linea' => $totalLinea,
                'opciones' => $opcionesValidadas,
            ];
        }

        // 8. Validar pedido mínimo en domicilio
        $costoEnvio = 0;
        if ($modalidad === 'domicilio') {
            $pedidoMinimo = (int)($restaurante['pedido_minimo'] ?? 0);
            if ($pedidoMinimo > 0 && $subtotalCalculado < $pedidoMinimo) {
                return $this->json([
                    'ok' => false,
                    'error' => 'El pedido mínimo para domicilio es de ' . format_cop($pedidoMinimo) . '. Tu subtotal actual es de ' . format_cop($subtotalCalculado) . '.'
                ], 400);
            }
            $costoEnvio = (int)($restaurante['costo_envio'] ?? 0);
        }

        $totalGeneral = $subtotalCalculado + $costoEnvio;

        // 9. Registrar el pedido en la base de datos
        $mesaId = null;
        if ($mesaNumero > 0) {
            $mesaModel = new \App\Models\Mesa();
            $mDb = $mesaModel->findByNumero($restauranteId, $mesaNumero);
            if ($mDb) {
                $mesaId = (int)$mDb['id'];
            } else {
                $mesaId = $mesaModel->crear($restauranteId, $mesaNumero, 'Mesa ' . $mesaNumero);
            }
        }

        $datosParaGuardar = [
            'modalidad' => $modalidad,
            'mesa_id' => $mesaId,
            'mesa_numero' => $mesaNumero,
            'cliente_nombre' => $clienteNombre,
            'cliente_telefono' => ($modalidad === 'mesa') ? null : ($clienteTelefono ?: null),
            'direccion' => ($modalidad === 'domicilio') ? (trim((string)($input['direccion'] ?? '')) ?: null) : null,
            'barrio' => ($modalidad === 'domicilio') ? (trim((string)($input['barrio'] ?? '')) ?: null) : null,
            'notas_entrega' => trim((string)($input['notas_entrega'] ?? '')) ?: null,
            'hora_recogida' => ($modalidad === 'recoger') ? (trim((string)($input['hora_recogida'] ?? '')) ?: null) : null,
            'metodo_pago' => $metodoPago,
            'subtotal' => $subtotalCalculado,
            'costo_envio' => $costoEnvio,
            'total' => $totalGeneral,
            'ip' => $ip,
        ];

        try {
            $pedidoGuardado = $pedidoModel->crearPedidoCompleto($restaurante, $datosParaGuardar, $itemsValidados);
        } catch (\Throwable $e) {
            return $this->json([
                'ok' => false,
                'error' => 'Ocurrió un error al procesar tu pedido. Por favor intenta nuevamente.'
            ], 500);
        }

        // 10. Construir el mensaje y enlace a WhatsApp
        $whatsappDestino = $restaurante['whatsapp'] ?: '573001234567';
        $mensajeWhatsApp = WhatsAppService::formatearMensajePedido($restaurante, $pedidoGuardado, $itemsValidados);
        $whatsappUrl = WhatsAppService::generarEnlace($whatsappDestino, $mensajeWhatsApp);

        return $this->json([
            'ok' => true,
            'codigo' => $pedidoGuardado['codigo'],
            'modalidad' => $modalidad,
            'mesa_numero' => $mesaNumero,
            'total' => $totalGeneral,
            'total_formateado' => format_cop($totalGeneral),
            'whatsapp_url' => $whatsappUrl,
            'mensaje' => '¡Pedido registrado con éxito!',
        ]);
    }

    /**
     * Renderiza una pantalla de error amigable dentro del layout error.
     */
    private function renderError(int $codigo, string $titulo, string $mensaje): Response
    {
        return $this->render('errors/error', [
            'code' => $codigo,
            'title' => $titulo,
            'message' => $mensaje,
        ], 'error')->setStatusCode($codigo);
    }
}
