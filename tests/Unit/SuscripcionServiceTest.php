<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\SuscripcionService;
use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\TestCase;

class SuscripcionServiceTest extends TestCase
{
    private DateTimeImmutable $hoy;
    private DateTimeZone $tz;

    protected function setUp(): void
    {
        $this->tz = new DateTimeZone('America/Bogota');
        $this->hoy = new DateTimeImmutable('2026-11-09 12:00:00', $this->tz);
    }

    public function testEstadoAlDia(): void
    {
        $restaurante = [
            'fecha_vencimiento' => '2026-11-25',
            'dias_gracia' => 3,
            'estado_manual' => null,
        ];

        $res = SuscripcionService::estadoDe($restaurante, $this->hoy);
        $this->assertSame(SuscripcionService::ESTADO_AL_DIA, $res['codigo']);
        $this->assertTrue($res['puede_operar']);
        $this->assertFalse($res['bloqueado']);
        $this->assertSame(16, $res['dias_restantes']);
    }

    public function testEstadoPorVencer(): void
    {
        $restaurante = [
            'fecha_vencimiento' => '2026-11-12',
            'dias_gracia' => 3,
            'estado_manual' => null,
        ];

        $res = SuscripcionService::estadoDe($restaurante, $this->hoy);
        $this->assertSame(SuscripcionService::ESTADO_POR_VENCER, $res['codigo']);
        $this->assertTrue($res['puede_operar']);
        $this->assertTrue($res['por_vencer']);
        $this->assertSame(3, $res['dias_restantes']);
    }

    public function testEstadoVenceHoy(): void
    {
        $restaurante = [
            'fecha_vencimiento' => '2026-11-09',
            'dias_gracia' => 3,
            'estado_manual' => null,
        ];

        $res = SuscripcionService::estadoDe($restaurante, $this->hoy);
        $this->assertSame(SuscripcionService::ESTADO_POR_VENCER, $res['codigo']);
        $this->assertSame('Vence Hoy', $res['label']);
        $this->assertTrue($res['puede_operar']);
    }

    public function testEstadoEnGracia(): void
    {
        // Venció hace 1 día, pero tiene 3 días de gracia
        $restaurante = [
            'fecha_vencimiento' => '2026-11-08',
            'dias_gracia' => 3,
            'estado_manual' => null,
        ];

        $res = SuscripcionService::estadoDe($restaurante, $this->hoy);
        $this->assertSame(SuscripcionService::ESTADO_EN_GRACIA, $res['codigo']);
        $this->assertTrue($res['puede_operar']);
        $this->assertTrue($res['en_gracia']);
        $this->assertFalse($res['bloqueado']);
    }

    public function testEstadoBloqueadoPorSuperarGracia(): void
    {
        // Venció hace 5 días, con 3 días de gracia
        $restaurante = [
            'fecha_vencimiento' => '2026-11-04',
            'dias_gracia' => 3,
            'estado_manual' => null,
        ];

        $res = SuscripcionService::estadoDe($restaurante, $this->hoy);
        $this->assertSame(SuscripcionService::ESTADO_BLOQUEADO, $res['codigo']);
        $this->assertFalse($res['puede_operar']);
        $this->assertTrue($res['bloqueado']);
    }

    public function testEstadoManualSuspendido(): void
    {
        $restaurante = [
            'fecha_vencimiento' => '2026-11-25',
            'dias_gracia' => 3,
            'estado_manual' => 'suspendido',
        ];

        $res = SuscripcionService::estadoDe($restaurante, $this->hoy);
        $this->assertSame(SuscripcionService::ESTADO_SUSPENDIDO, $res['codigo']);
        $this->assertFalse($res['puede_operar']);
        $this->assertTrue($res['bloqueado']);
    }

    public function testEstadoManualCancelado(): void
    {
        $restaurante = [
            'fecha_vencimiento' => '2026-11-25',
            'dias_gracia' => 3,
            'estado_manual' => 'cancelado',
        ];

        $res = SuscripcionService::estadoDe($restaurante, $this->hoy);
        $this->assertSame(SuscripcionService::ESTADO_CANCELADO, $res['codigo']);
        $this->assertFalse($res['puede_operar']);
        $this->assertTrue($res['bloqueado']);
    }
}
