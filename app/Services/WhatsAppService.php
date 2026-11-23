<?php

declare(strict_types=1);

namespace App\Services;

class WhatsAppService
{
    /**
     * Limpia y formatea un número de teléfono colombiano para WhatsApp (E.164).
     */
    public static function normalizarTelefono(string $telefono): string
    {
        $limpio = preg_replace('/[^0-9]/', '', $telefono);

        // Si tiene 10 dígitos y empieza por 3 (número móvil Colombia), anteponer 57
        if (strlen($limpio) === 10 && str_starts_with($limpio, '3')) {
            $limpio = '57' . $limpio;
        }

        return $limpio;
    }

    /**
     * Construye el texto completo del pedido para WhatsApp con formato tipográfico limpio (sin emojis).
     */
    public static function formatearMensajePedido(array $restaurante, array $pedido, array $items): string
    {
        $lineas = [];

        // Encabezado
        $lineas[] = "*NUEVO PEDIDO #{$pedido['codigo']}*";
        $lineas[] = "*{$restaurante['nombre']}*";
        $lineas[] = date('d/m/Y h:i A');
        $lineas[] = "--------------------------------";

        // Modalidad
        switch ($pedido['modalidad']) {
            case 'mesa':
                $mesaTexto = !empty($pedido['mesa_numero']) ? "Mesa #{$pedido['mesa_numero']}" : "Mesa asignada";
                $lineas[] = "*Modalidad:* En Mesa ({$mesaTexto})";
                if (!empty($pedido['cliente_nombre'])) {
                    $lineas[] = "*Cliente:* {$pedido['cliente_nombre']}";
                }
                if (!empty($pedido['notas_entrega'])) {
                    $lineas[] = "*Notas cocina:* {$pedido['notas_entrega']}";
                }
                break;

            case 'domicilio':
                $lineas[] = "*Modalidad:* Domicilio";
                $lineas[] = "*Cliente:* {$pedido['cliente_nombre']}";
                if (!empty($pedido['cliente_telefono'])) {
                    $lineas[] = "*Teléfono:* {$pedido['cliente_telefono']}";
                }
                $lineas[] = "*Dirección:* {$pedido['direccion']}";
                if (!empty($pedido['barrio'])) {
                    $lineas[] = "*Barrio:* {$pedido['barrio']}";
                }
                if (!empty($pedido['notas_entrega'])) {
                    $lineas[] = "*Indicaciones:* {$pedido['notas_entrega']}";
                }
                break;

            case 'recoger':
                $lineas[] = "*Modalidad:* Para Llevar (Recoger)";
                $lineas[] = "*Cliente:* {$pedido['cliente_nombre']}";
                if (!empty($pedido['cliente_telefono'])) {
                    $lineas[] = "*Teléfono:* {$pedido['cliente_telefono']}";
                }
                if (!empty($pedido['hora_recogida'])) {
                    $lineas[] = "*Hora estimada de recogida:* {$pedido['hora_recogida']}";
                }
                break;
        }

        $lineas[] = "--------------------------------";
        $lineas[] = "*DETALLE DEL PEDIDO:*";

        // Listado de ítems
        foreach ($items as $item) {
            $subtotalItem = format_cop((int)$item['total_linea']);
            $lineas[] = "- *{$item['cantidad']}x {$item['nombre_producto']}* ({$subtotalItem})";

            if (!empty($item['opciones']) && is_array($item['opciones'])) {
                foreach ($item['opciones'] as $opc) {
                    $extraStr = !empty($opc['precio_extra']) ? " (+" . format_cop((int)$opc['precio_extra']) . ")" : "";
                    $lineas[] = "   * {$opc['nombre_grupo']}: {$opc['nombre_opcion']}{$extraStr}";
                }
            }

            if (!empty($item['nota'])) {
                $lineas[] = "   - _Nota: {$item['nota']}_";
            }
        }

        $lineas[] = "--------------------------------";
        $lineas[] = "*Subtotal:* " . format_cop((int)$pedido['subtotal']);

        if ($pedido['modalidad'] === 'domicilio' && (int)$pedido['costo_envio'] > 0) {
            $lineas[] = "*Costo de envío:* " . format_cop((int)$pedido['costo_envio']);
        }

        $lineas[] = "*TOTAL A PAGAR:* " . format_cop((int)$pedido['total']) . " COP";
        $lineas[] = "--------------------------------";

        $lineas[] = "\n_¡Hola! Acabo de armar este pedido en su menú interactivo de Mesaly. ¿Me confirman recepción y tiempo de preparación?_";

        return implode("\n", $lineas);
    }

    /**
     * Genera el enlace wa.me para abrir la conversación en WhatsApp.
     */
    public static function generarEnlace(string $telefono, string $mensaje): string
    {
        $tel = self::normalizarTelefono($telefono);
        return "https://wa.me/{$tel}?text=" . rawurlencode($mensaje);
    }
}
