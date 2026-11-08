<?php

declare(strict_types=1);

namespace App\Services;

use DateTimeImmutable;
use DateTimeZone;

class HorarioService
{
    private const TIMEZONE = 'America/Bogota';

    /**
     * Determina si el restaurante está actualmente abierto según sus horarios configurados.
     *
     * @param array $horarios Registros de la tabla horarios
     * @param DateTimeImmutable|null $ahora Momento actual (inyectable para tests)
     * @return array{abierto: bool, mensaje: string}
     */
    public static function estaAbierto(array $horarios, ?DateTimeImmutable $ahora = null): array
    {
        if (empty($horarios)) {
            // Si no tiene horarios configurados, por defecto se asume abierto
            return [
                'abierto' => true,
                'mensaje' => 'Abierto hoy',
            ];
        }

        $tz = new DateTimeZone(self::TIMEZONE);
        $ahora = $ahora ?? new DateTimeImmutable('now', $tz);
        $diaSemana = (int)$ahora->format('w'); // 0=Domingo, 1=Lunes, ..., 6=Sábado
        $horaActual = $ahora->format('H:i:s');

        // Filtrar horarios activos para hoy
        $horariosHoy = array_filter($horarios, function ($h) use ($diaSemana) {
            return (int)$h['dia_semana'] === $diaSemana && !empty($h['activo']);
        });

        if (empty($horariosHoy)) {
            return [
                'abierto' => false,
                'mensaje' => 'Cerrado hoy',
            ];
        }

        foreach ($horariosHoy as $tramo) {
            $abre = $tramo['abre'];
            $cierra = $tramo['cierra'];

            // Caso horario normal (ej: 11:30 a 22:00)
            if ($abre <= $cierra) {
                if ($horaActual >= $abre && $horaActual <= $cierra) {
                    $cierraAmPm = date('g:i A', strtotime($cierra));
                    return [
                        'abierto' => true,
                        'mensaje' => "Abierto hasta las {$cierraAmPm}",
                    ];
                }
            } else {
                // Caso horario que cruza medianoche (ej: 18:00 a 02:00)
                if ($horaActual >= $abre || $horaActual <= $cierra) {
                    $cierraAmPm = date('g:i A', strtotime($cierra));
                    return [
                        'abierto' => true,
                        'mensaje' => "Abierto hasta las {$cierraAmPm}",
                    ];
                }
            }
        }

        // Buscar próxima apertura hoy si aplica
        $primerAbre = null;
        foreach ($horariosHoy as $tramo) {
            if ($tramo['abre'] > $horaActual) {
                if ($primerAbre === null || $tramo['abre'] < $primerAbre) {
                    $primerAbre = $tramo['abre'];
                }
            }
        }

        if ($primerAbre !== null) {
            $abreAmPm = date('g:i A', strtotime($primerAbre));
            return [
                'abierto' => false,
                'mensaje' => "Abre hoy a las {$abreAmPm}",
            ];
        }

        return [
            'abierto' => false,
            'mensaje' => 'Cerrado por hoy',
        ];
    }
}
