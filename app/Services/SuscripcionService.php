<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Model;
use App\Models\Auditoria;
use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;
use PDO;

class SuscripcionService
{
    public const ESTADO_AL_DIA = 'AL_DIA';
    public const ESTADO_POR_VENCER = 'POR_VENCER';
    public const ESTADO_EN_GRACIA = 'EN_GRACIA';
    public const ESTADO_BLOQUEADO = 'BLOQUEADO';
    public const ESTADO_SUSPENDIDO = 'SUSPENDIDO';
    public const ESTADO_CANCELADO = 'CANCELADO';

    public const METODOS_PAGO = [
        'wompi' => 'Wompi (Pasarela Oficial)',
        'bancolombia' => 'Bancolombia / Transferencia',
        'nequi' => 'Nequi',
        'daviplata' => 'Daviplata',
        'efectivo' => 'Efectivo',
        'otro' => 'Otro Medio',
    ];

    /**
     * Calcula en tiempo real el estado de suscripción de un restaurante.
     */
    public static function estadoDe(array $restaurante, ?DateTimeImmutable $ahora = null): array
    {
        $tz = new DateTimeZone('America/Bogota');
        $hoy = ($ahora ?? new DateTimeImmutable('now', $tz))->setTime(0, 0, 0);

        // 1. Estados manuales prioritarios
        $manual = strtolower(trim((string)($restaurante['estado_manual'] ?? '')));
        if ($manual === 'cancelado') {
            return [
                'codigo' => self::ESTADO_CANCELADO,
                'label' => 'Cancelado',
                'color' => '#6c757d',
                'bg_color' => '#f1f3f5',
                'dias_restantes' => 0,
                'puede_operar' => false,
                'bloqueado' => true,
                'en_gracia' => false,
                'por_vencer' => false,
            ];
        }

        if ($manual === 'suspendido') {
            return [
                'codigo' => self::ESTADO_SUSPENDIDO,
                'label' => 'Suspendido',
                'color' => '#dc3545',
                'bg_color' => '#fde8e8',
                'dias_restantes' => 0,
                'puede_operar' => false,
                'bloqueado' => true,
                'en_gracia' => false,
                'por_vencer' => false,
            ];
        }

        // 2. Cálculo por fechas
        $fechaVencRaw = $restaurante['fecha_vencimiento'] ?? null;
        if (empty($fechaVencRaw)) {
            // Si no tiene fecha, se asume al día en período de gracia inicial
            return [
                'codigo' => self::ESTADO_AL_DIA,
                'label' => 'Al Día',
                'color' => '#28a745',
                'bg_color' => '#e8f5e9',
                'dias_restantes' => 30,
                'puede_operar' => true,
                'bloqueado' => false,
                'en_gracia' => false,
                'por_vencer' => false,
            ];
        }

        $vencimiento = (new DateTimeImmutable($fechaVencRaw, $tz))->setTime(0, 0, 0);
        $diasGracia = (int)($restaurante['dias_gracia'] ?? 3);
        $fechaFinGracia = $vencimiento->modify("+{$diasGracia} days");

        // Días restantes respecto a la fecha de corte
        $diff = $hoy->diff($vencimiento);
        $diasRestantes = $diff->invert ? -$diff->days : $diff->days;

        if ($hoy > $fechaFinGracia) {
            // Venció y superó los días de gracia
            return [
                'codigo' => self::ESTADO_BLOQUEADO,
                'label' => 'Bloqueado',
                'color' => '#dc3545',
                'bg_color' => '#fde8e8',
                'dias_restantes' => $diasRestantes,
                'puede_operar' => false,
                'bloqueado' => true,
                'en_gracia' => false,
                'por_vencer' => false,
            ];
        }

        if ($hoy > $vencimiento) {
            // Venció pero está dentro del margen de gracia (días de gracia restantes)
            $graciaDiff = $hoy->diff($fechaFinGracia);
            $diasGraciaRestantes = max(0, $graciaDiff->invert ? 0 : $graciaDiff->days);

            return [
                'codigo' => self::ESTADO_EN_GRACIA,
                'label' => "En Gracia ({$diasGraciaRestantes}d)",
                'color' => '#fd7e14',
                'bg_color' => '#fff3e0',
                'dias_restantes' => $diasRestantes,
                'dias_gracia_restantes' => $diasGraciaRestantes,
                'puede_operar' => true,
                'bloqueado' => false,
                'en_gracia' => true,
                'por_vencer' => false,
            ];
        }

        if ($diasRestantes <= 5) {
            return [
                'codigo' => self::ESTADO_POR_VENCER,
                'label' => $diasRestantes === 0 ? 'Vence Hoy' : "Vence en {$diasRestantes}d",
                'color' => '#f59f00',
                'bg_color' => '#fff9db',
                'dias_restantes' => $diasRestantes,
                'puede_operar' => true,
                'bloqueado' => false,
                'en_gracia' => false,
                'por_vencer' => true,
            ];
        }

        return [
            'codigo' => self::ESTADO_AL_DIA,
            'label' => 'Al Día',
            'color' => '#28a745',
            'bg_color' => '#e8f5e9',
            'dias_restantes' => $diasRestantes,
            'puede_operar' => true,
            'bloqueado' => false,
            'en_gracia' => false,
            'por_vencer' => false,
        ];
    }

    /**
     * Registra un pago manual de suscripción y extiende el vencimiento.
     */
    public static function registrarPago(int $restauranteId, array $datos, int $superAdminId): array
    {
        $pdo = Model::getPdo();

        $stmt = $pdo->prepare("SELECT * FROM `restaurantes` WHERE `id` = :id AND `deleted_at` IS NULL LIMIT 1");
        $stmt->execute(['id' => $restauranteId]);
        $restaurante = $stmt->fetch();

        if (!$restaurante) {
            throw new InvalidArgumentException("El restaurante no existe.");
        }

        $monto = (int)($datos['monto'] ?? 0);
        if ($monto <= 0) {
            throw new InvalidArgumentException("El monto del pago debe ser mayor a cero.");
        }

        $fechaPagoStr = trim((string)($datos['fecha_pago'] ?? date('Y-m-d')));
        $periodos = max(1, (int)($datos['periodos'] ?? 1));
        $metodo = trim((string)($datos['metodo'] ?? 'transferencia'));
        $referencia = !empty($datos['referencia']) ? trim((string)$datos['referencia']) : null;
        $nota = !empty($datos['nota']) ? trim((string)$datos['nota']) : null;

        $tz = new DateTimeZone('America/Bogota');
        $fechaPago = new DateTimeImmutable($fechaPagoStr, $tz);

        // Base para extender: si el vencimiento actual sigue vigente, extendemos desde ese día;
        // si ya venció o no tiene, extendemos desde la fecha de este pago.
        $vencActualRaw = $restaurante['fecha_vencimiento'] ?? null;
        $baseDate = $fechaPago;
        if (!empty($vencActualRaw)) {
            $vencActual = new DateTimeImmutable($vencActualRaw, $tz);
            if ($vencActual >= $fechaPago) {
                $baseDate = $vencActual;
            }
        }

        $ciclo = ($restaurante['ciclo'] ?? 'mensual') === 'anual' ? 'anual' : 'mensual';
        $diasPorPeriodo = $periodos * 30;
        $intervalStr = $ciclo === 'anual' ? "+{$periodos} year" : "+{$diasPorPeriodo} days";
        $nuevaFechaVenc = $baseDate->modify($intervalStr)->format('Y-m-d');

        $pdo->beginTransaction();
        try {
            // Insertar en pagos_suscripcion
            $insertSql = "INSERT INTO `pagos_suscripcion` 
                (`restaurante_id`, `fecha_pago`, `monto`, `metodo`, `periodos`, `fecha_vencimiento_anterior`, `fecha_vencimiento_nueva`, `referencia`, `nota`, `registrado_por`, `created_at`)
                VALUES
                (:restaurante_id, :fecha_pago, :monto, :metodo, :periodos, :anterior, :nueva, :referencia, :nota, :registrado_por, NOW())";

            $stmtIns = $pdo->prepare($insertSql);
            $stmtIns->execute([
                'restaurante_id' => $restauranteId,
                'fecha_pago' => $fechaPagoStr,
                'monto' => $monto,
                'metodo' => $metodo,
                'periodos' => $periodos,
                'anterior' => $vencActualRaw,
                'nueva' => $nuevaFechaVenc,
                'referencia' => $referencia,
                'nota' => $nota,
                'registrado_por' => $superAdminId,
            ]);
            $pagoId = (int)$pdo->lastInsertId();

            // Actualizar restaurante: nueva fecha y rehabilitar si estaba suspendido
            $updSql = "UPDATE `restaurantes` 
                       SET `fecha_vencimiento` = :venc, 
                           `estado_manual` = NULL,
                           `updated_at` = NOW() 
                       WHERE `id` = :id";
            $stmtUpd = $pdo->prepare($updSql);
            $stmtUpd->execute([
                'venc' => $nuevaFechaVenc,
                'id' => $restauranteId,
            ]);

            // Auditoría
            Auditoria::registrar(
                'pago_suscripcion_registrado',
                'pagos_suscripcion',
                $pagoId,
                ['vencimiento' => $vencActualRaw],
                ['monto' => $monto, 'vencimiento' => $nuevaFechaVenc],
                $restauranteId
            );

            $pdo->commit();

            return [
                'pago_id' => $pagoId,
                'nueva_fecha_vencimiento' => $nuevaFechaVenc,
                'monto' => $monto,
            ];
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    /**
     * Otorga días adicionales de cortesía sin registrar movimiento de caja.
     */
    public static function extenderDias(int $restauranteId, int $dias, ?string $motivo, int $superAdminId): string
    {
        if ($dias <= 0) {
            throw new InvalidArgumentException("La cantidad de días a extender debe ser mayor a cero.");
        }

        $pdo = Model::getPdo();
        $stmt = $pdo->prepare("SELECT * FROM `restaurantes` WHERE `id` = :id AND `deleted_at` IS NULL LIMIT 1");
        $stmt->execute(['id' => $restauranteId]);
        $restaurante = $stmt->fetch();

        if (!$restaurante) {
            throw new InvalidArgumentException("El restaurante no existe.");
        }

        $tz = new DateTimeZone('America/Bogota');
        $hoy = (new DateTimeImmutable('now', $tz))->setTime(0, 0, 0);

        $vencActualRaw = $restaurante['fecha_vencimiento'] ?? null;
        $baseDate = $hoy;
        if (!empty($vencActualRaw)) {
            $vencActual = (new DateTimeImmutable($vencActualRaw, $tz))->setTime(0, 0, 0);
            if ($vencActual >= $hoy) {
                $baseDate = $vencActual;
            }
        }

        $nuevaFechaVenc = $baseDate->modify("+{$dias} days")->format('Y-m-d');

        $updSql = "UPDATE `restaurantes` 
                   SET `fecha_vencimiento` = :venc, 
                       `estado_manual` = NULL,
                       `updated_at` = NOW() 
                   WHERE `id` = :id";
        $stmtUpd = $pdo->prepare($updSql);
        $stmtUpd->execute([
            'venc' => $nuevaFechaVenc,
            'id' => $restauranteId,
        ]);

        $motivoTexto = !empty($motivo) ? " Motivo: {$motivo}." : "";
        Auditoria::registrar(
            'vencimiento_extendido',
            'restaurantes',
            $restauranteId,
            ['vencimiento' => $vencActualRaw],
            ['vencimiento' => $nuevaFechaVenc, 'dias' => $dias, 'motivo' => $motivo],
            $restauranteId
        );

        return $nuevaFechaVenc;
    }

    /**
     * Suspende o reactiva manualmente un restaurante.
     */
    public static function cambiarEstadoManual(int $restauranteId, ?string $nuevoEstado, int $superAdminId): void
    {
        $valido = in_array($nuevoEstado, ['suspendido', 'cancelado', null], true);
        if (!$valido) {
            throw new InvalidArgumentException("Estado manual no válido.");
        }

        $pdo = Model::getPdo();
        $stmt = $pdo->prepare("UPDATE `restaurantes` SET `estado_manual` = :estado, `updated_at` = NOW() WHERE `id` = :id");
        $stmt->execute([
            'estado' => $nuevoEstado,
            'id' => $restauranteId,
        ]);

        $accion = $nuevoEstado ? "negocio_{$nuevoEstado}" : "negocio_reactivado";
        Auditoria::registrar(
            $accion,
            'restaurantes',
            $restauranteId,
            null,
            ['estado_manual' => $nuevoEstado ?? 'activo'],
            $restauranteId
        );
    }

    /**
     * Construye el mensaje y enlace wa.me para recordar el pago de la suscripción al restaurante.
     */
    public static function generarEnlaceRecordatorioWhatsApp(array $restaurante, string $nombreSuperAdmin = 'Mesaly'): string
    {
        $telefono = WhatsAppService::normalizarTelefono((string)($restaurante['whatsapp'] ?? ''));
        $nombreNegocio = $restaurante['nombre'] ?? 'Restaurante';
        $precio = format_cop((int)($restaurante['precio'] ?? 49000));
        $fechaVenc = !empty($restaurante['fecha_vencimiento']) 
            ? date('d/m/Y', strtotime($restaurante['fecha_vencimiento'])) 
            : 'pronto';

        $mensaje = "*Recordatorio de Suscripción Mesaly*\n\n"
                 . "Hola equipo de *{$nombreNegocio}*,\n\n"
                 . "Les escribimos para recordarles que su suscripción al menú digital de Mesaly vence el *{$fechaVenc}*.\n\n"
                 . "*Monto a renovar:* {$precio} COP\n\n"
                 . "Por favor infórmennos por este medio una vez realizada la transferencia para registrar el pago y mantener su carta activa sin interrupciones.\n\n"
                 . "¡Muchas gracias por confiar en Mesaly!";

        // Marcar fecha de último recordatorio
        $pdo = Model::getPdo();
        $stmt = $pdo->prepare("UPDATE `restaurantes` SET `ultimo_recordatorio_at` = NOW() WHERE `id` = :id");
        $stmt->execute(['id' => $restaurante['id']]);

        return "https://wa.me/{$telefono}?text=" . rawurlencode($mensaje);
    }
}
