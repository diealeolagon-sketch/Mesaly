<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\HorarioService;
use App\Services\WhatsAppService;
use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\TestCase;

class HorarioAndWhatsAppServiceTest extends TestCase
{
    public function testHorarioServiceDetectsOpenAndClosed(): void
    {
        $horarios = [
            ['dia_semana' => 1, 'abre' => '11:00:00', 'cierra' => '22:00:00', 'activo' => 1],
        ];

        $tz = new DateTimeZone('America/Bogota');

        // Lunes 2:00 PM (abierto)
        $lunesAbierto = new DateTimeImmutable('2026-11-09 14:00:00', $tz);
        $resAbierto = HorarioService::estaAbierto($horarios, $lunesAbierto);
        $this->assertTrue($resAbierto['abierto']);
        $this->assertStringContainsString('Abierto', $resAbierto['mensaje']);

        // Lunes 10:00 AM (cerrado, abre a las 11:00 AM)
        $lunesCerrado = new DateTimeImmutable('2026-11-09 10:00:00', $tz);
        $resCerrado = HorarioService::estaAbierto($horarios, $lunesCerrado);
        $this->assertFalse($resCerrado['abierto']);
        $this->assertStringContainsString('Abre hoy', $resCerrado['mensaje']);

        // Martes (cerrado todo el día)
        $martes = new DateTimeImmutable('2026-11-10 14:00:00', $tz);
        $resMartes = HorarioService::estaAbierto($horarios, $martes);
        $this->assertFalse($resMartes['abierto']);
        $this->assertSame('Cerrado hoy', $resMartes['mensaje']);
    }

    public function testNormalizarTelefonoWhatsApp(): void
    {
        // 10 dígitos móvil Colombia -> anteponer 57
        $this->assertSame('573001234567', WhatsAppService::normalizarTelefono('3001234567'));
        $this->assertSame('573001234567', WhatsAppService::normalizarTelefono('+57 300 123 4567'));
        $this->assertSame('573109876543', WhatsAppService::normalizarTelefono('310-987-6543'));
    }

    public function testFormatearMensajePedidoWhatsApp(): void
    {
        $restaurante = [
            'nombre' => 'La Parrilla Paisa',
            'whatsapp' => '573001234567',
        ];

        $pedido = [
            'codigo' => 'PAR-0001',
            'modalidad' => 'mesa',
            'mesa_numero' => 4,
            'cliente_nombre' => 'Carlos',
            'subtotal' => 46000,
            'costo_envio' => 0,
            'total' => 46000,
            'metodo_pago' => 'efectivo',
        ];

        $items = [
            [
                'nombre_producto' => 'Churrasco al Carbón 350g',
                'cantidad' => 1,
                'total_linea' => 46000,
                'opciones' => [
                    ['nombre_grupo' => 'Término', 'nombre_opcion' => 'Tres Cuartos', 'precio_extra' => 0],
                    ['nombre_grupo' => 'Adicionales', 'nombre_opcion' => 'Tocineta', 'precio_extra' => 4000],
                ],
                'nota' => 'Bien jugoso',
            ],
        ];

        $mensaje = WhatsAppService::formatearMensajePedido($restaurante, $pedido, $items);

        $this->assertStringContainsString('NUEVO PEDIDO #PAR-0001', $mensaje);
        $this->assertStringContainsString('La Parrilla Paisa', $mensaje);
        $this->assertStringContainsString('Mesa #4', $mensaje);
        $this->assertStringContainsString('1x Churrasco al Carbón 350g', $mensaje);
        $this->assertStringContainsString('Término: Tres Cuartos', $mensaje);
        $this->assertStringContainsString('Adicionales: Tocineta', $mensaje);
        $this->assertStringContainsString('Bien jugoso', $mensaje);
        $this->assertStringContainsString('*TOTAL A PAGAR:* $ 46.000 COP', $mensaje);

        $enlace = WhatsAppService::generarEnlace('3001234567', $mensaje);
        $this->assertStringStartsWith('https://wa.me/573001234567?text=', $enlace);
    }
}
