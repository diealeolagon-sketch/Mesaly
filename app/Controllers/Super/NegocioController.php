<?php

declare(strict_types=1);

namespace App\Controllers\Super;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Model;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Validator;
use App\Models\Auditoria;
use App\Models\Restaurante;
use App\Models\Usuario;
use App\Services\SuscripcionService;
use PDO;

class NegocioController extends Controller
{
    private Restaurante $restauranteModel;
    private Usuario $usuarioModel;

    public function __construct()
    {
        $this->restauranteModel = new Restaurante();
        $this->usuarioModel = new Usuario();
    }

    /**
     * Pantalla principal de Super Admin: "Vencimientos"
     * Lista ordenada por fecha de vencimiento ascendente con semáforos y filtros.
     */
    public function index(Request $request): Response
    {
        $filtro = $request->query('filtro', 'todos');
        $busqueda = trim((string)$request->query('q', ''));

        $todosNegocios = $this->restauranteModel->allForVencimientos(null, null);
        $negociosFiltrados = $this->restauranteModel->allForVencimientos($filtro === 'todos' ? null : $filtro, $busqueda ?: null);

        // Contadores para las pestañas de filtro
        $conteo = [
            'todos' => count($todosNegocios),
            'al_dia' => 0,
            'semana' => 0,
            'gracia' => 0,
            'bloqueados' => 0,
            'cancelados' => 0,
            'mrr_estimado' => 0,
        ];

        foreach ($todosNegocios as $n) {
            $est = $n['estado_suscripcion']['codigo'];
            if ($est === SuscripcionService::ESTADO_AL_DIA) $conteo['al_dia']++;
            if (!empty($n['estado_suscripcion']['por_vencer'])) $conteo['semana']++;
            if ($est === SuscripcionService::ESTADO_EN_GRACIA) $conteo['gracia']++;
            if ($est === SuscripcionService::ESTADO_BLOQUEADO || $est === SuscripcionService::ESTADO_SUSPENDIDO) $conteo['bloqueados']++;
            if ($est === SuscripcionService::ESTADO_CANCELADO) $conteo['cancelados']++;

            // Estimación de MRR (mensual recurrente)
            if ($est !== SuscripcionService::ESTADO_CANCELADO) {
                $precio = (int)($n['precio'] ?? 49000);
                $conteo['mrr_estimado'] += ($n['ciclo'] === 'anual') ? (int)round($precio / 12) : $precio;
            }
        }

        return $this->render('super/vencimientos/index', [
            'title' => 'Vencimientos y Negocios — Super Admin',
            'negocios' => $negociosFiltrados,
            'conteo' => $conteo,
            'filtroActivo' => $filtro,
            'busqueda' => $busqueda,
            'metodosPago' => SuscripcionService::METODOS_PAGO,
        ], 'super');
    }

    public function showCrear(Request $request): Response
    {
        return $this->render('super/negocios/crear', [
            'title' => 'Crear Nuevo Negocio — Super Admin',
        ], 'super');
    }

    public function crear(Request $request): Response
    {
        $nombre = trim((string)$request->post('nombre'));
        $slug = strtolower(trim((string)$request->post('slug')));
        $whatsapp = preg_replace('/\D/', '', (string)$request->post('whatsapp'));
        $precio = (int)str_replace(['.', ',', '$', ' '], '', (string)$request->post('precio', '49000'));
        $ciclo = (string)$request->post('ciclo', 'mensual');
        $diasGracia = (int)$request->post('dias_gracia', '3');
        $notasInternas = trim((string)$request->post('notas_internas', ''));
        $adminNombre = trim((string)$request->post('admin_nombre'));
        $adminEmail = strtolower(trim((string)$request->post('admin_email')));
        $enPrueba = (bool)$request->post('en_prueba');
        $plan = (string)$request->post('plan', 'basico');
        if (!in_array($plan, ['basico', 'pro', 'cadena', 'personalizado'], true)) {
            $plan = 'basico';
        }
        $limiteMesasRaw = $request->post('limite_mesas');
        if ($limiteMesasRaw !== null && $limiteMesasRaw !== '') {
            $limiteMesas = max(0, (int)$limiteMesasRaw);
            if ($limiteMesas === 0) {
                $limiteMesas = null;
            }
        } else {
            $limiteMesas = ($plan === 'basico') ? 6 : null;
        }
        $limiteFacturasMes = (int)$request->post('limite_facturas_mes', $plan === 'basico' ? 600 : ($plan === 'cadena' ? 3500 : 1500));
        // Facturacion electronica DIAN fija para todos los planes
        $facturacionActiva = 1;

        $validator = Validator::make([
            'nombre' => $nombre,
            'slug' => $slug,
            'whatsapp' => $whatsapp,
            'precio' => $precio,
            'plan' => $plan,
            'limite_mesas' => $limiteMesas,
            'limite_facturas_mes' => $limiteFacturasMes,
            'facturacion_activa' => $facturacionActiva,
            'ciclo' => $ciclo,
            'admin_nombre' => $adminNombre,
            'admin_email' => $adminEmail,
        ], [
            'nombre' => 'required|min:3|max:150',
            'slug' => 'required|slug|min:3|max:40',
            'whatsapp' => 'required|min:10|max:15',
            'precio' => 'required|numeric',
            'ciclo' => 'required|in:mensual,anual',
            'admin_nombre' => 'required|min:3|max:150',
            'admin_email' => 'required|email',
        ]);

        if ($validator->fails()) {
            Session::flash('error', $validator->firstError());
            return $this->back();
        }

        if (!$this->restauranteModel->isSlugAvailable($slug)) {
            Session::flash('error', "El slug '{$slug}' ya está en uso o reservado.");
            return $this->back();
        }

        if ($this->usuarioModel->findByEmail($adminEmail)) {
            Session::flash('error', "El correo electrónico '{$adminEmail}' ya está registrado.");
            return $this->back();
        }

        // Generar contraseña temporal segura
        $tempPassword = 'Mesaly' . rand(100, 999) . '!' . bin2hex(random_bytes(2));
        $passwordHash = Auth::hashPassword($tempPassword);

        // Vencimiento inicial
        if ($enPrueba) {
            $fechaFinPrueba = date('Y-m-d', strtotime('+15 days'));
            $fechaVencimiento = $fechaFinPrueba;
        } else {
            $fechaFinPrueba = null;
            $intervalo = $ciclo === 'anual' ? '+1 year' : '+1 month';
            $fechaVencimiento = date('Y-m-d', strtotime($intervalo));
        }

        Model::beginTransaction();
        try {
            $restauranteId = $this->restauranteModel->create([
                'nombre' => $nombre,
                'slug' => $slug,
                'whatsapp' => $whatsapp,
                'precio' => $precio,
                'plan' => $plan,
                'limite_mesas' => $limiteMesas,
                'limite_facturas_mes' => $limiteFacturasMes,
                'facturacion_activa' => $facturacionActiva,
                'ciclo' => $ciclo,
                'dias_gracia' => max(1, $diasGracia),
                'notas_internas' => $notasInternas ?: null,
                'mostrar_marca' => 1,
                'en_prueba' => $enPrueba ? 1 : 0,
                'fecha_fin_prueba' => $fechaFinPrueba,
                'fecha_vencimiento' => $fechaVencimiento,
                'dia_corte' => (int)date('d'),
                'moneda' => 'COP',
                'color_primario' => '#E85D04',
                'color_secundario' => '#FFB703',
                'color_fondo' => '#FFF7EA',
                'color_texto' => '#1E1B18',
                'fuente' => 'Inter',
                'recibiendo_pedidos' => 1,
                'acepta_mesa' => 1,
                'acepta_domicilio' => 1,
                'acepta_recoger' => 1,
                'siguiente_consecutivo' => 1,
            ]);

            // Crear usuario administrador del negocio
            $usuarioId = $this->usuarioModel->create([
                'restaurante_id' => $restauranteId,
                'nombre' => $adminNombre,
                'email' => $adminEmail,
                'password_hash' => $passwordHash,
                'rol' => Auth::ROLE_ADMIN_NEGOCIO,
                'debe_cambiar_password' => 1,
                'activo' => 1,
            ]);

            Auditoria::registrar(
                'crear_negocio',
                'restaurantes',
                $restauranteId,
                null,
                ['nombre' => $nombre, 'precio' => $precio, 'ciclo' => $ciclo, 'vence' => $fechaVencimiento],
                $restauranteId
            );

            Model::commit();

            return $this->render('super/negocios/credenciales', [
                'title' => 'Negocio Creado Exitosamente — Mesaly',
                'restaurante' => [
                    'id' => $restauranteId,
                    'nombre' => $nombre,
                    'slug' => $slug,
                ],
                'admin' => [
                    'nombre' => $adminNombre,
                    'email' => $adminEmail,
                    'tempPassword' => $tempPassword,
                ],
            ], 'super');

        } catch (\Throwable $e) {
            Model::rollBack();
            Session::flash('error', "Error al crear negocio: " . $e->getMessage());
            return $this->back();
        }
    }

    public function showEditar(Request $request, array $params): Response
    {
        $id = (int)($params['id'] ?? 0);
        $negocio = $this->restauranteModel->find($id);

        if (!$negocio) {
            Session::flash('error', 'Negocio no encontrado.');
            return $this->redirect(app_url('/superadmin'));
        }

        $admin = $this->usuarioModel->findAdminByRestaurante($id);
        $mesaModel = new \App\Models\Mesa();
        $totalMesasCreadas = $mesaModel->contarPorRestaurante($id);

        return $this->render('super/negocios/editar', [
            'title' => 'Editar Negocio — Super Admin',
            'negocio' => $negocio,
            'admin' => $admin,
            'totalMesasCreadas' => $totalMesasCreadas,
        ], 'super');
    }

    public function editar(Request $request, array $params): Response
    {
        $id = (int)($params['id'] ?? 0);
        $negocio = $this->restauranteModel->find($id);

        if (!$negocio) {
            Session::flash('error', 'Negocio no encontrado.');
            return $this->redirect(app_url('/superadmin'));
        }

        $nombre = trim((string)$request->post('nombre'));
        $slug = strtolower(trim((string)$request->post('slug')));
        $whatsapp = preg_replace('/\D/', '', (string)$request->post('whatsapp'));
        $precio = (int)str_replace(['.', ',', '$', ' '], '', (string)$request->post('precio', (string)$negocio['precio']));
        $ciclo = (string)$request->post('ciclo', $negocio['ciclo']);
        $fechaVencimiento = trim((string)$request->post('fecha_vencimiento', $negocio['fecha_vencimiento']));
        $diasGracia = max(1, (int)$request->post('dias_gracia', (string)($negocio['dias_gracia'] ?? 3)));
        $notasInternas = trim((string)$request->post('notas_internas', ''));
        $mostrarMarca = !empty($request->post('mostrar_marca')) ? 1 : 0;
        $plan = (string)$request->post('plan', $negocio['plan'] ?? 'pro');
        if (!in_array($plan, ['basico', 'pro', 'cadena', 'personalizado'], true)) {
            $plan = 'pro';
        }
        $limiteMesasRaw = $request->post('limite_mesas');
        if ($limiteMesasRaw !== null && $limiteMesasRaw !== '') {
            $limiteMesas = max(0, (int)$limiteMesasRaw);
            if ($limiteMesas === 0) {
                $limiteMesas = null;
            }
        } else {
            $limiteMesas = null;
        }
        $limiteFacturasMes = (int)$request->post('limite_facturas_mes', (string)($negocio['limite_facturas_mes'] ?? ($plan === 'basico' ? 600 : ($plan === 'cadena' ? 3500 : 1500))));
        // Facturacion electronica DIAN fija para todos los planes
        $facturacionActiva = 1;

        $validator = Validator::make([
            'nombre' => $nombre,
            'slug' => $slug,
            'whatsapp' => $whatsapp,
            'precio' => $precio,
            'plan' => $plan,
            'limite_mesas' => $limiteMesas,
            'limite_facturas_mes' => $limiteFacturasMes,
            'facturacion_activa' => $facturacionActiva,
            'ciclo' => $ciclo,
            'fecha_vencimiento' => $fechaVencimiento,
        ], [
            'nombre' => 'required|min:3|max:150',
            'slug' => 'required|slug|min:3|max:40',
            'whatsapp' => 'required|min:10|max:15',
            'precio' => 'required|numeric',
            'ciclo' => 'required|in:mensual,anual',
            'fecha_vencimiento' => 'required|date',
        ]);

        if ($validator->fails()) {
            Session::flash('error', $validator->firstError());
            return $this->back();
        }

        if (!$this->restauranteModel->isSlugAvailable($slug, $id)) {
            Session::flash('error', "El slug '{$slug}' ya está en uso o reservado.");
            return $this->back();
        }

        $this->restauranteModel->update($id, [
            'nombre' => $nombre,
            'slug' => $slug,
            'whatsapp' => $whatsapp,
            'precio' => $precio,
            'plan' => $plan,
            'limite_mesas' => $limiteMesas,
            'limite_facturas_mes' => $limiteFacturasMes,
            'facturacion_activa' => $facturacionActiva,
            'ciclo' => $ciclo,
            'fecha_vencimiento' => $fechaVencimiento,
            'dias_gracia' => $diasGracia,
            'notas_internas' => $notasInternas ?: null,
            'mostrar_marca' => $mostrarMarca,
        ]);

        Auditoria::registrar(
            'editar_negocio',
            'restaurantes',
            $id,
            $negocio,
            ['nombre' => $nombre, 'precio' => $precio, 'ciclo' => $ciclo],
            $id
        );

        Session::flash('success', 'Negocio actualizado exitosamente.');
        return $this->redirect(app_url('/superadmin'));
    }

    public function resetPassword(Request $request, array $params): Response
    {
        $id = (int)($params['id'] ?? 0);
        $negocio = $this->restauranteModel->find($id);
        $admin = $this->usuarioModel->findAdminByRestaurante($id);

        if (!$negocio || !$admin) {
            Session::flash('error', 'No se encontró el administrador del negocio.');
            return $this->redirect(app_url('/superadmin'));
        }

        $tempPassword = 'Reset' . rand(100, 999) . '!' . bin2hex(random_bytes(2));
        $hash = Auth::hashPassword($tempPassword);

        $this->usuarioModel->update((int)$admin['id'], [
            'password_hash' => $hash,
            'debe_cambiar_password' => 1,
        ]);

        Auditoria::registrar(
            'reset_password_admin',
            'usuarios',
            (int)$admin['id'],
            null,
            ['email' => $admin['email']],
            $id
        );

        return $this->render('super/negocios/credenciales', [
            'title' => 'Contraseña Restablecida — Super Admin',
            'restaurante' => $negocio,
            'admin' => [
                'nombre' => $admin['nombre'],
                'email' => $admin['email'],
                'tempPassword' => $tempPassword,
            ],
            'esReset' => true,
        ], 'super');
    }

    /**
     * Registro manual de pago de suscripción
     */
    public function registrarPago(Request $request, array $params): Response
    {
        $id = (int)($params['id'] ?? 0);

        try {
            $monto = (int)str_replace(['.', ',', '$', ' '], '', (string)$request->post('monto', '0'));
            $res = SuscripcionService::registrarPago($id, [
                'monto' => $monto,
                'fecha_pago' => (string)$request->post('fecha_pago', date('Y-m-d')),
                'metodo' => (string)$request->post('metodo', 'transferencia'),
                'periodos' => (int)$request->post('periodos', 1),
                'referencia' => (string)$request->post('referencia', ''),
                'nota' => (string)$request->post('nota', ''),
            ], (int)(Auth::id() ?? 1));

            Session::flash('success', "¡Pago registrado exitosamente! Nuevo vencimiento: " . date('d/m/Y', strtotime($res['nueva_fecha_vencimiento'])) . ".");
        } catch (\Throwable $e) {
            Session::flash('error', "Error al registrar pago: " . $e->getMessage());
        }

        return $this->redirect(app_url('/superadmin'));
    }

    /**
     * Extensión de días de gracia o cortesía
     */
    public function extenderDias(Request $request, array $params): Response
    {
        $id = (int)($params['id'] ?? 0);
        $dias = (int)$request->post('dias', 0);
        $motivo = trim((string)$request->post('motivo', ''));

        try {
            $nuevoVenc = SuscripcionService::extenderDias($id, $dias, $motivo ?: null, (int)(Auth::id() ?? 1));
            Session::flash('success', "Se extendieron {$dias} días de cortesía. Nuevo vencimiento: " . date('d/m/Y', strtotime($nuevoVenc)) . ".");
        } catch (\Throwable $e) {
            Session::flash('error', "Error al extender días: " . $e->getMessage());
        }

        return $this->redirect(app_url('/superadmin'));
    }

    /**
     * Suspender o reactivar negocio manualmente
     */
    public function cambiarEstado(Request $request, array $params): Response
    {
        $id = (int)($params['id'] ?? 0);
        $nuevoEstado = trim((string)$request->post('estado', ''));

        try {
            SuscripcionService::cambiarEstadoManual($id, $nuevoEstado ?: null, (int)(Auth::id() ?? 1));
            $msg = $nuevoEstado ? "Negocio marcado como {$nuevoEstado}." : "Negocio reactivado exitosamente.";
            Session::flash('success', $msg);
        } catch (\Throwable $e) {
            Session::flash('error', "Error al cambiar estado: " . $e->getMessage());
        }

        return $this->redirect(app_url('/superadmin'));
    }

    /**
     * "Ver como" (Impersonar negocio con auditoría)
     */
    public function verComo(Request $request, array $params): Response
    {
        $id = (int)($params['id'] ?? 0);
        $negocio = $this->restauranteModel->find($id);

        if (!$negocio) {
            Session::flash('error', 'Negocio no encontrado.');
            return $this->redirect(app_url('/superadmin'));
        }

        Session::set('impersonating_tenant_id', $id);
        Session::set('impersonating_tenant_name', $negocio['nombre']);

        Auditoria::registrar(
            'soporte_ver_como',
            'restaurantes',
            $id,
            null,
            ['nombre' => $negocio['nombre']],
            $id
        );

        Session::flash('success', "Modo soporte activado: ahora estás viendo el panel de {$negocio['nombre']}.");
        return $this->redirect(app_url('/admin'));
    }

    /**
     * Salir del modo "Ver como" y regresar a Super Admin
     */
    public function dejarVerComo(Request $request): Response
    {
        $id = Session::get('impersonating_tenant_id');
        Session::remove('impersonating_tenant_id');
        Session::remove('impersonating_tenant_name');

        if ($id) {
            Auditoria::registrar(
                'soporte_salir_ver_como',
                'restaurantes',
                (int)$id,
                null,
                null,
                (int)$id
            );
        }

        Session::flash('info', 'Has regresado al panel Super Admin.');
        return $this->redirect(app_url('/superadmin'));
    }

    /**
     * Historial general de todos los pagos registrados en la plataforma
     */
    public function pagos(Request $request): Response
    {
        $pdo = Model::getPdo();
        $busqueda = trim((string)$request->query('q', ''));
        $metodoFiltro = trim((string)$request->query('metodo', ''));

        $sql = "SELECT p.*, r.nombre AS restaurante_nombre, r.slug AS restaurante_slug, u.nombre AS superadmin_nombre
                FROM `pagos_suscripcion` p
                JOIN `restaurantes` r ON p.restaurante_id = r.id
                JOIN `usuarios` u ON p.registrado_por = u.id";

        $where = [];
        $params = [];

        if (!empty($busqueda)) {
            $where[] = "(r.nombre LIKE :q OR r.slug LIKE :q OR p.nota LIKE :q)";
            $params['q'] = "%{$busqueda}%";
        }
        if (!empty($metodoFiltro)) {
            $where[] = "p.metodo = :metodo";
            $params['metodo'] = $metodoFiltro;
        }

        if (!empty($where)) {
            $sql .= " WHERE " . implode(' AND ', $where);
        }

        $sql .= " ORDER BY p.fecha_pago DESC, p.id DESC LIMIT 100";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $pagos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Totales
        $mesActual = date('Y-m');
        $totalMes = (int)$pdo->query("SELECT COALESCE(SUM(monto), 0) FROM `pagos_suscripcion` WHERE DATE_FORMAT(fecha_pago, '%Y-%m') = '{$mesActual}' AND anulado = 0")->fetchColumn();
        $totalHistorico = (int)$pdo->query("SELECT COALESCE(SUM(monto), 0) FROM `pagos_suscripcion` WHERE anulado = 0")->fetchColumn();
        $conteoTransacciones = (int)$pdo->query("SELECT COUNT(*) FROM `pagos_suscripcion` WHERE anulado = 0")->fetchColumn();

        return $this->render('super/pagos/index', [
            'title' => 'Historial de Pagos — Super Admin',
            'pagos' => $pagos,
            'busqueda' => $busqueda,
            'metodoFiltro' => $metodoFiltro,
            'totalMes' => $totalMes,
            'totalHistorico' => $totalHistorico,
            'conteoTransacciones' => $conteoTransacciones,
            'metodosPago' => SuscripcionService::METODOS_PAGO,
        ], 'super');
    }

    /**
     * Retorna el historial de pagos de un restaurante en formato JSON para el modal
     */
    public function historialPagosJson(Request $request, array $params): Response
    {
        $id = (int)($params['id'] ?? 0);
        $pdo = Model::getPdo();

        $stmt = $pdo->prepare("SELECT p.*, u.nombre AS superadmin_nombre
                               FROM `pagos_suscripcion` p
                               JOIN `usuarios` u ON p.registrado_por = u.id
                               WHERE p.restaurante_id = :id
                               ORDER BY p.fecha_pago DESC, p.id DESC");
        $stmt->execute(['id' => $id]);
        $pagos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->json([
            'success' => true,
            'pagos' => $pagos,
        ]);
    }

    /**
     * Métricas globales de la plataforma (Sección 5.6)
     */
    public function metricas(Request $request): Response
    {
        $pdo = Model::getPdo();
        $todosNegocios = $this->restauranteModel->allForVencimientos(null, null);

        $mrr = 0;
        $conteoEstados = [
            'al_dia' => 0,
            'por_vencer' => 0,
            'en_gracia' => 0,
            'bloqueado' => 0,
            'suspendido' => 0,
            'cancelado' => 0,
        ];

        foreach ($todosNegocios as $n) {
            $est = $n['estado_suscripcion']['codigo'];
            if (isset($conteoEstados[$est])) {
                $conteoEstados[$est]++;
            }
            if (!empty($n['estado_suscripcion']['por_vencer'])) {
                $conteoEstados['por_vencer']++;
            }
            if ($est !== SuscripcionService::ESTADO_CANCELADO) {
                $precio = (int)($n['precio'] ?? 49000);
                $mrr += ($n['ciclo'] === 'anual') ? (int)round($precio / 12) : $precio;
            }
        }

        // Métricas de pedidos en la red
        $totalPedidos = (int)$pdo->query("SELECT COUNT(*) FROM `pedidos` WHERE `estado` != 'cancelado'")->fetchColumn();
        $volumenVentasRed = (int)$pdo->query("SELECT COALESCE(SUM(total), 0) FROM `pedidos` WHERE `estado` != 'cancelado'")->fetchColumn();
        $totalMesas = (int)$pdo->query("SELECT COUNT(*) FROM `mesas` WHERE `activa` = 1 AND `deleted_at` IS NULL")->fetchColumn();
        $totalProductos = (int)$pdo->query("SELECT COUNT(*) FROM `productos` WHERE `deleted_at` IS NULL")->fetchColumn();

        // Pagos recaudados mes actual e histórico 6 meses
        $mesActual = date('Y-m');
        $recaudadoMes = (int)$pdo->query("SELECT COALESCE(SUM(monto), 0) FROM `pagos_suscripcion` WHERE DATE_FORMAT(fecha_pago, '%Y-%m') = '{$mesActual}' AND anulado = 0")->fetchColumn();

        $historicoMeses = [];
        $mesesEspanol = ['Jan' => 'Ene', 'Feb' => 'Feb', 'Mar' => 'Mar', 'Apr' => 'Abr', 'May' => 'May', 'Jun' => 'Jun', 'Jul' => 'Jul', 'Aug' => 'Ago', 'Sep' => 'Sep', 'Oct' => 'Oct', 'Nov' => 'Nov', 'Dec' => 'Dic'];
        for ($i = 5; $i >= 0; $i--) {
            $ts = strtotime("-{$i} months");
            $ym = date('Y-m', $ts);
            $engMes = date('M', $ts);
            $label = ($mesesEspanol[$engMes] ?? $engMes) . ' ' . date('y', $ts);
            $stmtM = $pdo->prepare("SELECT COALESCE(SUM(monto), 0) FROM `pagos_suscripcion` WHERE DATE_FORMAT(fecha_pago, '%Y-%m') = :ym AND anulado = 0");
            $stmtM->execute(['ym' => $ym]);
            $historicoMeses[] = [
                'periodo' => $ym,
                'label' => $label,
                'total' => (int)$stmtM->fetchColumn(),
            ];
        }

        $totalNegocios = count($todosNegocios);
        $tasaCobranza = $totalNegocios > 0 ? (float)round(($conteoEstados['al_dia'] / $totalNegocios) * 100, 1) : 0.0;
        $arpu = $totalNegocios > 0 ? (int)round($mrr / $totalNegocios) : 0;

        return $this->render('super/metricas/index', [
            'title' => 'Métricas Globales de la Red — Super Admin',
            'mrr' => $mrr,
            'arr' => $mrr * 12,
            'conteoEstados' => $conteoEstados,
            'totalNegocios' => $totalNegocios,
            'tasaCobranza' => $tasaCobranza,
            'arpu' => $arpu,
            'totalPedidos' => $totalPedidos,
            'volumenVentasRed' => $volumenVentasRed,
            'totalMesas' => $totalMesas,
            'totalProductos' => $totalProductos,
            'recaudadoMes' => $recaudadoMes,
            'historicoMeses' => $historicoMeses,
        ], 'super');
    }
}
