<?php

declare(strict_types=1);

namespace App\Services;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class QrService
{
    /**
     * Retorna la URL pública general del menú digital
     */
    public function urlGeneral(string $slug): string
    {
        return app_url('/' . ltrim($slug, '/'));
    }

    /**
     * Retorna la URL parametrizada para una mesa específica (?mesa=N)
     */
    public function urlMesa(string $slug, int $numeroMesa): string
    {
        return app_url('/' . ltrim($slug, '/') . '?mesa=' . $numeroMesa);
    }

    /**
     * Genera el código QR en formato SVG (ideal para renderizado vectorial nítido en navegador)
     */
    public function generarSvg(string $url): string
    {
        $options = new QROptions([
            'outputType'   => QRCode::OUTPUT_MARKUP_SVG,
            'eccLevel'     => QRCode::ECC_M,
            'addQuietzone' => true,
            'svgUseFill'   => true,
            'imageBase64'  => false,
        ]);

        return (new QRCode($options))->render($url);
    }

    /**
     * Genera el código QR en Data URI Base64 PNG (ideal para <img> y PDF de Dompdf)
     */
    public function generarPngBase64(string $url, int $scale = 8): string
    {
        $options = new QROptions([
            'outputType'   => QRCode::OUTPUT_IMAGE_PNG,
            'eccLevel'     => QRCode::ECC_M,
            'scale'        => max(4, $scale),
            'addQuietzone' => true,
        ]);

        return (new QRCode($options))->render($url);
    }

    /**
     * Genera los bytes binarios puros del archivo PNG para descarga de archivo
     */
    public function generarPngBinario(string $url, int $scale = 12): string
    {
        $dataUri = $this->generarPngBase64($url, $scale);
        
        // El formato de render() para IMAGE_PNG retorna una Data URI "data:image/png;base64,..."
        if (str_starts_with($dataUri, 'data:image/png;base64,')) {
            $base64 = substr($dataUri, strlen('data:image/png;base64,'));
            return base64_decode($base64) ?: '';
        }

        return $dataUri;
    }
}
