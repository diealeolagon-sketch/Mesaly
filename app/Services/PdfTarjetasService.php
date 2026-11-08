<?php

declare(strict_types=1);

namespace App\Services;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfTarjetasService
{
    private QrService $qrService;

    public function __construct(?QrService $qrService = null)
    {
        $this->qrService = $qrService ?? new QrService();
    }

    /**
     * Genera el documento PDF con las tarjetas de mesa listas para imprimir
     *
     * @param array<string, mixed> $restaurante
     * @param array<array<string, mixed>> $mesas
     * @return string Contenido binario del PDF
     */
    public function generar(array $restaurante, array $mesas): string
    {
        // 1. Procesar logo para embeberlo directamente en Base64
        $logoBase64 = null;
        if (!empty($restaurante['logo_path'])) {
            $logoFsPath = dirname(__DIR__, 2) . '/public' . $restaurante['logo_path'];
            if (file_exists($logoFsPath) && is_readable($logoFsPath)) {
                $mime = mime_content_type($logoFsPath) ?: 'image/png';
                $logoBytes = file_get_contents($logoFsPath);
                if ($logoBytes !== false) {
                    $logoBase64 = 'data:' . $mime . ';base64,' . base64_encode($logoBytes);
                }
            }
        }

        // 2. Pre-generar los códigos QR en Base64 PNG para cada mesa
        $tarjetas = [];
        $slug = (string)($restaurante['slug'] ?? '');
        $colorBrand = $restaurante['color_primario'] ?? '#E85D04';
        if (!preg_match('/^#[0-9a-fA-F]{6}$/', $colorBrand)) {
            $colorBrand = '#E85D04';
        }

        foreach ($mesas as $mesa) {
            $urlMesa = $this->qrService->urlMesa($slug, (int)$mesa['numero']);
            $qrDataUri = $this->qrService->generarPngBase64($urlMesa, 10);
            
            $tarjetas[] = [
                'numero' => (int)$mesa['numero'],
                'nombre' => !empty($mesa['nombre']) ? (string)$mesa['nombre'] : null,
                'qr' => $qrDataUri,
                'url' => $urlMesa,
            ];
        }

        // 3. Renderizar la vista HTML interna del PDF
        ob_start();
        $mostrarMarca = !empty($restaurante['mostrar_marca']);
        $nombreRestaurante = (string)($restaurante['nombre'] ?? 'Restaurante');
        require dirname(__DIR__) . '/Views/admin/mesas/pdf_tarjetas.php';
        $html = ob_get_clean();

        // 4. Configurar Dompdf
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', false); // No requiere llamadas externas
        $options->set('defaultFont', 'Helvetica');
        $options->set('dpi', 150);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('letter', 'portrait');
        $dompdf->render();

        return $dompdf->output() ?: '';
    }
}
