<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\Pedido;
use App\Models\Restaurante;

class ReporteController extends Controller
{
    private Pedido $pedidoModel;
    private Restaurante $restauranteModel;

    public function __construct(?Pedido $pedidoModel = null, ?Restaurante $restauranteModel = null)
    {
        $this->pedidoModel = $pedidoModel ?? new Pedido();
        $this->restauranteModel = $restauranteModel ?? new Restaurante();
    }

    /**
     * Pantalla interactiva de reportes financieros y analíticos
     * Separada en dos vistas: Gráficas de Negocio vs. Tabla de Datos & Cierre
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

        // 1. Resolver período y fechas con detección automática
        $periodo = trim((string)$request->query('periodo', ''));
        $fechaDesde = trim((string)$request->query('fecha_desde', ''));
        $fechaHasta = trim((string)$request->query('fecha_hasta', ''));
        $tab = trim((string)$request->query('tab', 'graficas'));
        if (!in_array($tab, ['graficas', 'datos'], true)) {
            $tab = 'graficas';
        }

        // Si se enviaron fechas explícitas (ej. desde el calendario), priorizar personalizado
        if (!empty($fechaDesde) && !empty($fechaHasta) && ($periodo === 'personalizado' || empty($periodo) || $periodo === 'este_mes')) {
            $periodo = 'personalizado';
        } elseif (empty($periodo)) {
            $periodo = 'este_mes';
        }

        $fechas = $this->resolverRangoFechas($periodo, $fechaDesde, $fechaHasta);
        $desde = $fechas['desde'];
        $hasta = $fechas['hasta'];
        $periodo = $fechas['periodo'];

        // 2. Filtros secundarios (Modalidad)
        $modalidad = trim((string)$request->query('modalidad', ''));
        $estado = trim((string)$request->query('estado', ''));

        $filtros = [
            'modalidad' => $modalidad,
            'metodo_pago' => '',
            'estado' => $estado,
        ];

        // 3. Consultar métricas del rango seleccionado
        $metricas = $this->pedidoModel->metricasRango($tenantId, $desde, $hasta, $filtros);
        $topProductos = $this->pedidoModel->topProductosRango($tenantId, $desde, $hasta, 5);
        $pedidos = $this->pedidoModel->listarParaReporte($tenantId, $desde, $hasta, $filtros);
        $serieDiaria = $this->pedidoModel->ventasPorDiaRango($tenantId, $desde, $hasta);
        $horasPico = $this->pedidoModel->horasPicoRango($tenantId, $desde, $hasta);
        $comparativaMeses = $this->pedidoModel->comparativaMeses($tenantId);

        return $this->render('admin/reportes/index', [
            'title' => 'Reportes y Cierre de Ventas — ' . ($restaurante['nombre'] ?? 'Mesaly'),
            'tenant' => $restaurante,
            'tab' => $tab,
            'periodo' => $periodo,
            'desde' => $desde,
            'hasta' => $hasta,
            'filtros' => $filtros,
            'metricas' => $metricas,
            'topProductos' => $topProductos,
            'pedidos' => $pedidos,
            'serieDiaria' => $serieDiaria,
            'horasPico' => $horasPico,
            'comparativaMeses' => $comparativaMeses,
        ], 'admin');
    }

    /**
     * Exportación de ventas a Excel (.CSV estructurado para Excel en español con opciones rápidas de mes/semana)
     */
    public function exportar(Request $request): void
    {
        $tenantId = Auth::tenantId();
        if (!$tenantId) {
            header('Location: ' . app_url('/login'));
            exit;
        }

        $restaurante = $this->restauranteModel->find($tenantId);
        $slug = $restaurante['slug'] ?? 'local';

        $preset = trim((string)$request->query('preset', ''));
        $periodo = trim((string)$request->query('periodo', ''));
        $fechaDesde = trim((string)$request->query('fecha_desde', ''));
        $fechaHasta = trim((string)$request->query('fecha_hasta', ''));

        if (!empty($preset)) {
            $periodo = $preset;
        } elseif (!empty($fechaDesde) && !empty($fechaHasta) && ($periodo === 'personalizado' || empty($periodo))) {
            $periodo = 'personalizado';
        } elseif (empty($periodo)) {
            $periodo = 'este_mes';
        }

        $fechas = $this->resolverRangoFechas($periodo, $fechaDesde, $fechaHasta);
        $desde = $fechas['desde'];
        $hasta = $fechas['hasta'];

        $modalidad = trim((string)$request->query('modalidad', ''));
        $metodoPago = trim((string)$request->query('metodo_pago', ''));
        $estado = trim((string)$request->query('estado', ''));

        $filtros = [
            'modalidad' => $modalidad,
            'metodo_pago' => $metodoPago,
            'estado' => $estado,
        ];

        $pedidos = $this->pedidoModel->listarParaReporte($tenantId, $desde, $hasta, $filtros);

        $nombreArchivo = sprintf('ventas_%s_%s_al_%s.csv', $slug, $desde, $hasta);

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $nombreArchivo . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $out = fopen('php://output', 'w');

        // BOM UTF-8 para que Excel en Windows reconozca tildes y caracteres en español
        fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Separador punto y coma (estándar Excel en Colombia / Español)
        $delimitador = ';';

        // 1. Fila de Encabezados
        fputcsv($out, [
            'Consecutivo',
            'Código Comanda',
            'Fecha',
            'Hora',
            'Modalidad',
            'Mesa / Destino',
            'Cliente',
            'Teléfono',
            'Dirección',
            'Platos e Ítems Consumidos',
            'Subtotal ($)',
            'Envío ($)',
            'Total ($)',
            'Base Gravable ($)',
            'Impoconsumo 8% ($)',
            'Estado',
        ], $delimitador);

        // 2. Filas de Datos
        foreach ($pedidos as $p) {
            $ts = strtotime($p['created_at']);
            $fecha = date('d/m/Y', $ts);
            $hora = date('h:i A', $ts);

            $ubicacion = ($p['modalidad'] === 'domicilio')
                ? 'Domicilio (' . ($p['barrio'] ?? 'General') . ')'
                : 'Mesa ' . ($p['mesa_numero'] ?? '—');

            $total = (int)$p['total'];
            $base = (int)round($total / 1.08);
            $impoconsumo = $total - $base;

            fputcsv($out, [
                '#' . str_pad((string)$p['consecutivo'], 4, '0', STR_PAD_LEFT),
                $p['codigo'],
                $fecha,
                $hora,
                ucfirst((string)$p['modalidad']),
                $ubicacion,
                $p['cliente_nombre'],
                $p['cliente_telefono'] ?? '—',
                $p['direccion'] ?? '—',
                $p['items_resumen'] ?? '—',
                $p['subtotal'],
                $p['costo_envio'],
                $total,
                $base,
                $impoconsumo,
                ucfirst((string)$p['estado']),
            ], $delimitador);
        }

        fclose($out);
        exit;
    }

    /**
     * Resuelve el rango de fechas a partir del período predefinido o personalizado
     *
     * @return array{desde: string, hasta: string, periodo: string}
     */
    private function resolverRangoFechas(string $periodo, string $fechaDesde, string $fechaHasta): array
    {
        $hoy = date('Y-m-d');

        switch ($periodo) {
            case 'hoy':
                return ['desde' => $hoy, 'hasta' => $hoy, 'periodo' => 'hoy'];

            case 'ayer':
                $ayer = date('Y-m-d', strtotime('-1 day'));
                return ['desde' => $ayer, 'hasta' => $ayer, 'periodo' => 'ayer'];

            case 'esta_semana':
                $lunes = date('Y-m-d', strtotime('monday this week'));
                return ['desde' => $lunes, 'hasta' => $hoy, 'periodo' => 'esta_semana'];

            case 'mes_anterior':
                $primerDia = date('Y-m-01', strtotime('first day of last month'));
                $ultimoDia = date('Y-m-t', strtotime('last day of last month'));
                return ['desde' => $primerDia, 'hasta' => $ultimoDia, 'periodo' => 'mes_anterior'];

            case 'personalizado':
                $d = !empty($fechaDesde) ? $fechaDesde : date('Y-m-01');
                $h = !empty($fechaHasta) ? $fechaHasta : $hoy;
                if ($d > $h) {
                    $temp = $d;
                    $d = $h;
                    $h = $temp;
                }
                return ['desde' => $d, 'hasta' => $h, 'periodo' => 'personalizado'];

            case 'este_mes':
            default:
                $primerDiaMes = date('Y-m-01');
                return ['desde' => $primerDiaMes, 'hasta' => $hoy, 'periodo' => 'este_mes'];
        }
    }
}
