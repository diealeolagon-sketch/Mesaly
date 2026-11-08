<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Env;
use App\Models\Mesa;
use App\Services\PdfTarjetasService;
use App\Services\QrService;
use PHPUnit\Framework\TestCase;

class MesaAndQrServiceTest extends TestCase
{
    private QrService $qrService;
    private Mesa $mesaModel;

    protected function setUp(): void
    {
        parent::setUp();
        $dotenv = \Dotenv\Dotenv::createImmutable(dirname(__DIR__, 2));
        $dotenv->safeLoad();

        $this->qrService = new QrService();
        $this->mesaModel = new Mesa();
    }

    public function testQrServiceUrlsFormat(): void
    {
        $urlGen = $this->qrService->urlGeneral('mi-restaurante');
        $this->assertStringContainsString('mi-restaurante', $urlGen);

        $urlMesa = $this->qrService->urlMesa('mi-restaurante', 7);
        $this->assertStringContainsString('mi-restaurante?mesa=7', $urlMesa);
    }

    public function testQrServiceGeneratesSvgAndPng(): void
    {
        $url = 'https://mesaly.co/demo?mesa=3';

        $svg = $this->qrService->generarSvg($url);
        $this->assertNotEmpty($svg);
        $this->assertStringContainsString('<svg', $svg);

        $base64Png = $this->qrService->generarPngBase64($url);
        $this->assertStringStartsWith('data:image/png;base64,', $base64Png);

        $binPng = $this->qrService->generarPngBinario($url);
        $this->assertNotEmpty($binPng);
        // Verificar firma mágica de archivo PNG (\x89PNG\r\n\x1a\n)
        $this->assertStringStartsWith("\x89PNG\r\n\x1a\n", $binPng);
    }

    public function testMesaCrudAndTenantScoping(): void
    {
        $tenantId = 1;
        $testNumero = 888;

        // Limpiar previo si existiera
        $existente = $this->mesaModel->findByNumero($tenantId, $testNumero, true);
        if ($existente) {
            $this->mesaModel->eliminarLogico((int)$existente['id'], $tenantId);
        }

        // 1. Crear mesa
        $id = $this->mesaModel->crear($tenantId, $testNumero, 'Zona VIP Test');
        $this->assertGreaterThan(0, $id);

        // 2. Buscar por ID y tenant
        $mesa = $this->mesaModel->findByIdAndRestaurante($id, $tenantId);
        $this->assertNotNull($mesa);
        $this->assertSame($testNumero, (int)$mesa['numero']);
        $this->assertSame('Zona VIP Test', $mesa['nombre']);
        $this->assertSame(1, (int)$mesa['activa']);

        // 3. Aislamiento multitenant: otro tenant (999) no puede verla
        $mesaAjena = $this->mesaModel->findByIdAndRestaurante($id, 999);
        $this->assertNull($mesaAjena);

        // 4. Toggle estado
        $this->mesaModel->toggleActiva($id, $tenantId);
        $mesaInactiva = $this->mesaModel->findByIdAndRestaurante($id, $tenantId);
        $this->assertSame(0, (int)$mesaInactiva['activa']);

        // 5. Borrado lógico
        $this->mesaModel->eliminarLogico($id, $tenantId);
        $mesaBorrada = $this->mesaModel->findByIdAndRestaurante($id, $tenantId);
        $this->assertNull($mesaBorrada);
    }

    public function testPdfTarjetasServiceGeneratesValidPdf(): void
    {
        $pdfService = new PdfTarjetasService($this->qrService);
        
        $restaurante = [
            'id' => 1,
            'nombre' => 'Restaurante Test',
            'slug' => 'restaurante-test',
            'color_primario' => '#E85D04',
            'logo_path' => null,
            'mostrar_marca' => 1,
        ];

        $mesas = [
            ['numero' => 1, 'nombre' => 'Interior'],
            ['numero' => 2, 'nombre' => 'Terraza'],
        ];

        $pdf = $pdfService->generar($restaurante, $mesas);
        $this->assertNotEmpty($pdf);
        // Encabezado estándar de archivo PDF (%PDF-)
        $this->assertStringStartsWith('%PDF-', $pdf);
    }
}
