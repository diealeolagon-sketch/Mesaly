<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\Validator;
use App\Models\Mesa;
use App\Models\Restaurante;
use App\Services\PdfTarjetasService;
use App\Services\QrService;

class MesaController extends Controller
{
    private Mesa $mesaModel;
    private Restaurante $restauranteModel;
    private QrService $qrService;
    private PdfTarjetasService $pdfService;

    public function __construct(
        ?Mesa $mesaModel = null,
        ?Restaurante $restauranteModel = null,
        ?QrService $qrService = null,
        ?PdfTarjetasService $pdfService = null
    ) {
        $this->mesaModel = $mesaModel ?? new Mesa();
        $this->restauranteModel = $restauranteModel ?? new Restaurante();
        $this->qrService = $qrService ?? new QrService();
        $this->pdfService = $pdfService ?? new PdfTarjetasService($this->qrService);
    }

    /**
     * Pantalla principal de administración de Mesas y Códigos QR
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

        $mesas = $this->mesaModel->allByRestaurante($tenantId);

        // Pre-generar QR general y URLs
        $urlGeneral = $this->qrService->urlGeneral($restaurante['slug']);
        $qrGeneralSvg = $this->qrService->generarSvg($urlGeneral);

        // Pre-generar thumbnails QR SVG para cada mesa
        $mesasConQr = [];
        $activasCount = 0;
        foreach ($mesas as $m) {
            if (!empty($m['activa'])) {
                $activasCount++;
            }
            $urlMesa = $this->qrService->urlMesa($restaurante['slug'], (int)$m['numero']);
            $qrSvg = $this->qrService->generarSvg($urlMesa);
            $mesasConQr[] = array_merge($m, [
                'url' => $urlMesa,
                'qr_svg' => $qrSvg,
            ]);
        }

        return $this->render('admin/mesas/index', [
            'title' => 'Mesas y Códigos QR — ' . $restaurante['nombre'],
            'tenant' => $restaurante,
            'mesas' => $mesasConQr,
            'urlGeneral' => $urlGeneral,
            'qrGeneralSvg' => $qrGeneralSvg,
            'totalMesas' => count($mesas),
            'activasCount' => $activasCount,
            'inactivasCount' => count($mesas) - $activasCount,
            'limiteMesas' => !empty($restaurante['limite_mesas']) ? (int)$restaurante['limite_mesas'] : null,
            'planNombre' => ucfirst($restaurante['plan'] ?? 'Pro'),
        ], 'admin');
    }

    /**
     * Crea una mesa individual
     */
    public function guardar(Request $request): Response
    {
        $tenantId = Auth::tenantId();
        $numero = (int)$request->post('numero', 0);
        $nombre = trim((string)$request->post('nombre', ''));

        $validator = Validator::make([
            'numero' => $numero,
            'nombre' => $nombre,
        ], [
            'numero' => 'required|numeric',
        ]);

        if ($validator->fails() || $numero < 1 || $numero > 999) {
            Session::flash('error', 'El número de mesa debe ser un entero entre 1 y 999.');
            return $this->back();
        }

        $restaurante = $this->restauranteModel->find($tenantId);
        $limiteMesas = !empty($restaurante['limite_mesas']) ? (int)$restaurante['limite_mesas'] : null;

        if ($limiteMesas !== null && $limiteMesas > 0) {
            if ($numero > $limiteMesas) {
                $plan = ucfirst($restaurante['plan'] ?? 'Básico');
                Session::flash('error', "El número de mesa ({$numero}) excede el tope de {$limiteMesas} mesas permitido en tu plan ({$plan}).");
                return $this->back();
            }
            $totalActual = $this->mesaModel->contarPorRestaurante($tenantId);
            $existente = $this->mesaModel->findByNumero($tenantId, $numero, false);
            if (!$existente && $totalActual >= $limiteMesas) {
                $plan = ucfirst($restaurante['plan'] ?? 'Básico');
                Session::flash('error', "Has alcanzado el límite de {$limiteMesas} mesas permitido en tu plan ({$plan}). Si necesitas más mesas para tu local, contacta a tu asesor para ampliar tu capacidad.");
                return $this->back();
            }
        }

        try {
            $this->mesaModel->crear($tenantId, $numero, $nombre ?: null);
            Session::flash('success', "Mesa {$numero} creada exitosamente.");
        } catch (\InvalidArgumentException $e) {
            Session::flash('error', $e->getMessage());
        } catch (\Throwable $e) {
            Session::flash('error', 'Error al crear la mesa: ' . $e->getMessage());
        }

        return $this->redirect(app_url('/admin/mesas'));
    }

    /**
     * Agrega la siguiente mesa consecutiva en 1 solo clic hasta el tope del plan
     */
    public function generarRapida(Request $request): Response
    {
        $tenantId = Auth::tenantId();
        $restaurante = $this->restauranteModel->find($tenantId);
        $limiteMesas = !empty($restaurante['limite_mesas']) ? (int)$restaurante['limite_mesas'] : null;

        $totalActual = $this->mesaModel->contarPorRestaurante($tenantId);
        if ($limiteMesas !== null && $totalActual >= $limiteMesas) {
            $plan = ucfirst($restaurante['plan'] ?? 'Básico');
            Session::flash('error', "Has alcanzado el límite de {$limiteMesas} mesas de tu plan ({$plan}).");
            return $this->back();
        }

        // Buscar el menor número de mesa libre
        $todas = $this->mesaModel->allByRestaurante($tenantId);
        $ocupados = array_map(fn($m) => (int)$m['numero'], $todas);
        $siguienteNumero = 1;
        while (in_array($siguienteNumero, $ocupados, true)) {
            $siguienteNumero++;
        }

        if ($limiteMesas !== null && $siguienteNumero > $limiteMesas) {
            Session::flash('error', "No puedes agregar más mesas porque alcanzarías el tope de {$limiteMesas} mesas de tu plan.");
            return $this->back();
        }

        try {
            $this->mesaModel->crear($tenantId, $siguienteNumero, null);
            Session::flash('success', "Mesa {$siguienteNumero} agregada exitosamente.");
        } catch (\Throwable $e) {
            Session::flash('error', 'Error al agregar la mesa: ' . $e->getMessage());
        }

        return $this->redirect(app_url('/admin/mesas'));
    }

    /**
     * Genera automáticamente todas las mesas faltantes hasta el tope del plan en 1 solo clic
     */
    public function completarTope(Request $request): Response
    {
        $tenantId = Auth::tenantId();
        $restaurante = $this->restauranteModel->find($tenantId);
        $limiteMesas = !empty($restaurante['limite_mesas']) ? (int)$restaurante['limite_mesas'] : null;

        if ($limiteMesas === null) {
            Session::flash('error', 'Tu plan tiene mesas ilimitadas. Usa la opción "Generar en Lote".');
            return $this->back();
        }

        $totalActual = $this->mesaModel->contarPorRestaurante($tenantId);
        if ($totalActual >= $limiteMesas) {
            Session::flash('error', "Ya tienes configuradas las {$limiteMesas} mesas de tu plan.");
            return $this->back();
        }

        try {
            $resultado = $this->mesaModel->crearLote($tenantId, 1, $limiteMesas);
            Session::flash('success', "¡Listo! Se completaron las {$limiteMesas} mesas de tu plan.");
        } catch (\Throwable $e) {
            Session::flash('error', 'Error al completar las mesas: ' . $e->getMessage());
        }

        return $this->redirect(app_url('/admin/mesas'));
    }

    /**
     * Genera un lote consecutivo de mesas (ej: 1 a la 15)
     */
    public function generarLote(Request $request): Response
    {
        $tenantId = Auth::tenantId();
        $desde = (int)$request->post('desde', 1);
        $hasta = (int)$request->post('hasta', 10);

        if ($desde < 1 || $hasta < $desde || ($hasta - $desde) > 100) {
            Session::flash('error', 'El rango de mesas no es válido (máximo 100 mesas por lote).');
            return $this->back();
        }

        $restaurante = $this->restauranteModel->find($tenantId);
        $limiteMesas = !empty($restaurante['limite_mesas']) ? (int)$restaurante['limite_mesas'] : null;

        if ($limiteMesas !== null && $limiteMesas > 0) {
            if ($hasta > $limiteMesas) {
                $plan = ucfirst($restaurante['plan'] ?? 'Básico');
                Session::flash('error', "El número de mesa final ({$hasta}) excede el tope de {$limiteMesas} mesas de tu plan ({$plan}).");
                return $this->back();
            }
            $totalActual = $this->mesaModel->contarPorRestaurante($tenantId);
            $aCrearEstimadas = ($hasta - $desde + 1);
            if (($totalActual + $aCrearEstimadas) > $limiteMesas) {
                $disponibles = max(0, $limiteMesas - $totalActual);
                $plan = ucfirst($restaurante['plan'] ?? 'Básico');
                Session::flash('error', "No es posible generar este lote: tu plan ({$plan}) tiene un límite de {$limiteMesas} mesas (tienes {$totalActual} creadas y te quedan {$disponibles} cupos disponibles).");
                return $this->back();
            }
        }

        try {
            $resultado = $this->mesaModel->crearLote($tenantId, $desde, $hasta);
            $msg = "Se generaron {$resultado['creadas']} mesas nuevas.";
            if ($resultado['existentes'] > 0) {
                $msg .= " ({$resultado['existentes']} mesas ya existían en tu restaurante).";
            }
            Session::flash('success', $msg);
        } catch (\Throwable $e) {
            Session::flash('error', 'Error al generar mesas en lote: ' . $e->getMessage());
        }

        return $this->redirect(app_url('/admin/mesas'));
    }

    /**
     * Actualiza datos de una mesa
     */
    public function actualizar(Request $request, array $params): Response
    {
        $tenantId = Auth::tenantId();
        $id = (int)($params['id'] ?? 0);
        $numero = (int)$request->post('numero', 0);
        $nombre = trim((string)$request->post('nombre', ''));

        if ($id <= 0 || $numero < 1 || $numero > 999) {
            Session::flash('error', 'Datos de mesa inválidos.');
            return $this->back();
        }

        $mesa = $this->mesaModel->findByIdAndRestaurante($id, $tenantId);
        if (!$mesa) {
            Session::flash('error', 'Mesa no encontrada.');
            return $this->back();
        }

        try {
            $this->mesaModel->actualizarMesa($id, $tenantId, $numero, $nombre ?: null);
            Session::flash('success', "Mesa actualizada exitosamente.");
        } catch (\InvalidArgumentException $e) {
            Session::flash('error', $e->getMessage());
        } catch (\Throwable $e) {
            Session::flash('error', 'Error al actualizar la mesa: ' . $e->getMessage());
        }

        return $this->redirect(app_url('/admin/mesas'));
    }

    /**
     * Alterna estado activa / inactiva
     */
    public function toggle(Request $request, array $params): Response
    {
        $tenantId = Auth::tenantId();
        $id = (int)($params['id'] ?? 0);

        $mesa = $this->mesaModel->findByIdAndRestaurante($id, $tenantId);
        if (!$mesa) {
            Session::flash('error', 'Mesa no encontrada.');
            return $this->back();
        }

        $this->mesaModel->toggleActiva($id, $tenantId);
        $nuevoEstado = empty($mesa['activa']) ? 'activada' : 'desactivada';
        Session::flash('success', "Mesa {$mesa['numero']} {$nuevoEstado}.");

        return $this->redirect(app_url('/admin/mesas'));
    }

    /**
     * Elimina lógicamente una mesa
     */
    public function eliminar(Request $request, array $params): Response
    {
        $tenantId = Auth::tenantId();
        $id = (int)($params['id'] ?? 0);

        $mesa = $this->mesaModel->findByIdAndRestaurante($id, $tenantId);
        if (!$mesa) {
            Session::flash('error', 'Mesa no encontrada.');
            return $this->back();
        }

        $this->mesaModel->eliminarLogico($id, $tenantId);
        Session::flash('success', "Mesa {$mesa['numero']} eliminada correctamente.");

        return $this->redirect(app_url('/admin/mesas'));
    }

    /**
     * Descarga el QR de una mesa en archivo PNG de alta resolución
     */
    public function descargarQr(Request $request, array $params): Response
    {
        $tenantId = Auth::tenantId();
        $id = (int)($params['id'] ?? 0);

        $mesa = $this->mesaModel->findByIdAndRestaurante($id, $tenantId);
        if (!$mesa) {
            return new Response('Mesa no encontrada', 404);
        }

        $restaurante = $this->restauranteModel->find($tenantId);
        $urlMesa = $this->qrService->urlMesa($restaurante['slug'], (int)$mesa['numero']);
        $pngBytes = $this->qrService->generarPngBinario($urlMesa, 14);

        $filename = 'qr-mesa-' . str_pad((string)$mesa['numero'], 2, '0', STR_PAD_LEFT) . '-' . $restaurante['slug'] . '.png';

        header('Content-Type: image/png');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($pngBytes));
        header('Cache-Control: no-cache, no-store, must-revalidate');
        echo $pngBytes;
        exit;
    }

    /**
     * Descarga el QR general del menú del restaurante en PNG
     */
    public function descargarQrGeneral(Request $request): Response
    {
        $tenantId = Auth::tenantId();
        $restaurante = $this->restauranteModel->find($tenantId);
        if (!$restaurante) {
            return new Response('Restaurante no encontrado', 404);
        }

        $urlGeneral = $this->qrService->urlGeneral($restaurante['slug']);
        $pngBytes = $this->qrService->generarPngBinario($urlGeneral, 14);

        $filename = 'qr-menu-general-' . $restaurante['slug'] . '.png';

        header('Content-Type: image/png');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($pngBytes));
        header('Cache-Control: no-cache, no-store, must-revalidate');
        echo $pngBytes;
        exit;
    }

    /**
     * Genera y descarga el archivo PDF imprimible con todas las tarjetas de mesa
     */
    public function descargarPdf(Request $request): Response
    {
        $tenantId = Auth::tenantId();
        $restaurante = $this->restauranteModel->find($tenantId);
        if (!$restaurante) {
            return new Response('Restaurante no encontrado', 404);
        }

        // Obtener mesas activas para imprimir
        $mesas = $this->mesaModel->allByRestaurante($tenantId, true);
        if (empty($mesas)) {
            Session::flash('error', 'No tienes mesas activas para exportar a PDF. Activa o crea al menos una mesa.');
            return $this->redirect(app_url('/admin/mesas'));
        }

        $pdfBytes = $this->pdfService->generar($restaurante, $mesas);
        $filename = 'tarjetas-mesas-' . $restaurante['slug'] . '.pdf';

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($pdfBytes));
        header('Cache-Control: no-cache, no-store, must-revalidate');
        echo $pdfBytes;
        exit;
    }
}
