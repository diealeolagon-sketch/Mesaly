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

    /**
     * Calcula el rango de fecha/hora de la jornada operativa del restaurante.
     * Protege el turno nocturno (pedidos de madrugada 1am-4am pertenecen al turno iniciado el día anterior).
     *
     * @param int $restauranteId
     * @param string $tipo 'actual' | 'ayer' | 'todas'
     * @param DateTimeImmutable|null $ahora
     * @return array{tipo: string, desde: ?string, hasta: ?string, etiqueta: string, hora_apertura: string}
     */
    public static function obtenerRangoJornada(int $restauranteId, string $tipo = 'actual', ?DateTimeImmutable $ahora = null): array
    {
        $tz = new DateTimeZone(self::TIMEZONE);
        $ahora = $ahora ?? new DateTimeImmutable('now', $tz);

        if ($tipo === 'todas') {
            return [
                'tipo' => 'todas',
                'desde' => null,
                'hasta' => null,
                'etiqueta' => 'Historial Completo',
                'hora_apertura' => '00:00:00',
            ];
        }

        // Hora límite de corte de turno nocturno / madrugada (05:00 AM)
        // Pedidos entre 00:00 y 04:59 pertenecen a la jornada nocturna iniciada el día anterior.
        // A partir de las 05:00 AM comienza la jornada operativa del día actual.
        $corteHora = 5;
        $horaInt = (int)$ahora->format('H');

        if ($horaInt < $corteHora) {
            $fechaOperativa = $ahora->modify('-1 day');
        } else {
            $fechaOperativa = $ahora;
        }

        $inicioActual = $fechaOperativa->setTime($corteHora, 0, 0);
        $inicioAyer = $inicioActual->modify('-1 day');
        $finAyer = $inicioActual;

        if ($tipo === 'ayer') {
            return [
                'tipo' => 'ayer',
                'desde' => $inicioAyer->format('Y-m-d H:i:s'),
                'hasta' => $finAyer->format('Y-m-d H:i:s'),
                'etiqueta' => 'Día Anterior (' . $inicioAyer->format('d/m') . ')',
                'hora_apertura' => sprintf('%02d:00:00', $corteHora),
            ];
        }

        return [
            'tipo' => 'actual',
            'desde' => $inicioActual->format('Y-m-d H:i:s'),
            'hasta' => null,
            'etiqueta' => 'Jornada de Hoy (' . $fechaOperativa->format('d/m') . ')',
            'hora_apertura' => sprintf('%02d:00:00', $corteHora),
        ];
    }
}
