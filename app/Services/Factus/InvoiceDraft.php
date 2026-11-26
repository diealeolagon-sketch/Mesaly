<?php

declare(strict_types=1);

namespace App\Services\Factus;

use DomainException;

final class InvoiceDraft
{
    public const DOCUMENTS = [
        '11' => 'Registro civil', '12' => 'Tarjeta de identidad', '13' => 'Cédula de ciudadanía',
        '21' => 'Tarjeta de extranjería', '22' => 'Cédula de extranjería', '31' => 'NIT',
        '41' => 'Pasaporte', '42' => 'Documento extranjero', '47' => 'PEP', '48' => 'PPT',
        '50' => 'NIT de otro país', '91' => 'NUIP',
    ];
    public const ORGANIZATIONS = ['1' => 'Persona jurídica', '2' => 'Persona natural'];
    public const METHODS = [
        '10' => 'Efectivo', '42' => 'Consignación', '20' => 'Cheque', '47' => 'Transferencia',
        '71' => 'Bonos', '72' => 'Vales', '1' => 'Sin definir', '49' => 'Tarjeta débito',
        '48' => 'Tarjeta crédito', 'ZZZ' => 'Otro',
    ];
    public const RESPONSIBILITIES = [
        'O-13' => 'Gran contribuyente', 'O-15' => 'Autorretenedor', 'O-23' => 'Agente de retención de IVA',
        'O-47' => 'Régimen simple', 'R-99-PN' => 'No responsable',
    ];
    public const TAXES = ['01' => 'IVA', '04' => 'INC', '35' => 'ICUI', 'excluded' => 'IVA excluido'];

    public static function customer(array $input): array
    {
        $type = self::field($input, 'buyer_type', 12);
        if ($type === 'final') {
            return [
                'identification_document_code' => '13', 'identification' => '22222222222',
                'legal_organization_code' => '2', 'names' => 'Consumidor Final',
            ];
        }
        $document = self::field($input, 'document', 2);
        $organization = self::field($input, 'organization', 1);
        $id = self::field($input, 'identification', 30);
        $name = self::field($input, 'name', 255);
        if ($type !== 'identified' || !isset(self::DOCUMENTS[$document], self::ORGANIZATIONS[$organization])
            || !preg_match('/\A[A-Za-z0-9]+\z/', $id) || ($document === '31' && !ctype_digit($id))
            || $id === '22222222222' || $name === '') {
            throw new DomainException('Completa el documento, identificación, organización y nombre del comprador.');
        }
        $customer = [
            'identification_document_code' => $document, 'identification' => $id,
            'legal_organization_code' => $organization, $organization === '1' ? 'company' : 'names' => $name,
        ];
        $responsibilities = $input['responsibilities'] ?? [];
        if (!is_array($responsibilities) || count($responsibilities) > count(self::RESPONSIBILITIES)) {
            throw new DomainException('Responsabilidades fiscales inválidas.');
        }
        foreach ($responsibilities as $code) {
            if (!is_string($code) || !isset(self::RESPONSIBILITIES[$code])) {
                throw new DomainException('Responsabilidades fiscales inválidas.');
            }
        }
        if (in_array('R-99-PN', $responsibilities, true) && count(array_unique($responsibilities)) > 1) {
            throw new DomainException('Selecciona responsabilidades fiscales compatibles.');
        }
        if ($responsibilities !== []) {
            $customer['responsibilities'] = array_values(array_unique($responsibilities));
        }
        return $customer;
    }

    public static function payment(array $input, string $total): array
    {
        $form = self::field($input, 'payment_form', 1);
        $method = self::field($input, 'payment_method', 3);
        if (!in_array($form, ['1', '2'], true) || !isset(self::METHODS[$method])) {
            throw new DomainException('Selecciona una forma y un medio de pago válidos.');
        }
        $payment = ['payment_form' => $form, 'payment_method_code' => $method, 'amount' => $total];
        if ($form === '2') {
            $date = self::field($input, 'due_date', 10);
            if (!self::validDate($date) || $date < date('Y-m-d')) {
                throw new DomainException('Indica una fecha de vencimiento válida para el pago a crédito.');
            }
            $payment['due_date'] = $date;
        }
        return $payment;
    }

    public static function totals(array $order, array $taxes): array
    {
        $items = [];
        $lines = [];
        $net = $taxTotal = $gross = 0;
        foreach ($order['items'] as $item) {
            $product = (string)$item['producto_id'];
            $quantity = (int)$item['cantidad'];
            $line = (int)$item['total_linea'];
            if ($quantity < 1 || $line < 1 || $line % $quantity !== 0
                || intdiv($line, $quantity) < (int)$item['precio_unitario']) {
                throw new DomainException('Hay una línea histórica que requiere revisión fiscal.');
            }
            $items[] = [$product, 'P-' . $product, $item['nombre_producto'], $quantity, intdiv($line, $quantity)];
        }
        $shipping = (int)$order['costo_envio'];
        if ($shipping > 0) {
            $items[] = ['envio', 'ENVIO', 'Domicilio', 1, $shipping];
        }
        $payload = [];
        foreach ($items as [$key, $code, $name, $quantity, $unitPesos]) {
            $tax = $taxes[$key] ?? null;
            if (!is_array($tax)) {
                throw new DomainException('Falta configuración tributaria para ' . $code . '.');
            }
            $tax = self::tax($tax);
            $rate = $tax['basis_points'];
            $unit = $unitPesos * 100;
            $netUnit = self::roundRatio($unit * 10000, 10000 + $rate);
            $lineNet = $netUnit * $quantity;
            $lineTax = self::roundRatio($lineNet * $rate, 10000);
            $lineGross = $unit * $quantity;
            if ($lineNet + $lineTax !== $lineGross) {
                throw new DomainException('El redondeo fiscal de ' . $code . ' no conserva el total histórico.');
            }
            $net += $lineNet;
            $taxTotal += $lineTax;
            $gross += $lineGross;
            $payload[] = [
                'code_reference' => $code, 'name' => $name, 'quantity' => (string)$quantity,
                'price' => self::money($netUnit), 'unit_measure_code' => '94', 'standard_code' => '999',
                'taxes' => [[
                    'code' => $tax['code'] === 'excluded' ? '01' : $tax['code'],
                    'rate' => self::money($rate), 'is_excluded' => $tax['code'] === 'excluded',
                ]],
            ];
            $lines[] = [
                'code' => $code, 'name' => $name, 'quantity' => $quantity, 'unit' => self::money($unit),
                'discount' => '0.00', 'base' => self::money($tax['code'] === 'excluded' ? 0 : $lineNet),
                'tax' => self::money($lineTax),
                'tax_label' => self::TAXES[$tax['code']] . ' ' . self::money($rate) . '%', 'total' => self::money($lineGross),
            ];
        }
        if ($payload === [] || $gross !== (int)$order['total'] * 100
            || $gross - $shipping * 100 !== (int)$order['subtotal'] * 100) {
            throw new DomainException('La suma de las líneas no coincide con los importes históricos del pedido.');
        }
        return ['items' => $payload, 'lines' => $lines, 'net' => self::money($net),
            'tax' => self::money($taxTotal), 'total' => self::money($gross)];
    }

    public static function tax(array $input): array
    {
        $code = self::field($input, 'code', 8);
        $rate = self::field($input, 'rate', 6);
        if (!isset(self::TAXES[$code]) || !preg_match('/\A\d{1,3}(?:\.\d{1,2})?\z/', $rate)) {
            throw new DomainException('Selecciona el impuesto y su tarifa con hasta dos decimales.');
        }
        [$integer, $decimals] = array_pad(explode('.', $rate, 2), 2, '');
        $points = (int)$integer * 100 + (int)str_pad($decimals, 2, '0');
        if ($points > 10000 || ($code === 'excluded' && $points !== 0)) {
            throw new DomainException('La tarifa seleccionada no es válida para el impuesto.');
        }
        return ['code' => $code, 'rate' => self::money($points), 'basis_points' => $points];
    }

    public static function field(array $input, string $key, int $max): string
    {
        $value = $input[$key] ?? '';
        if (!is_string($value) || !mb_check_encoding($value, 'UTF-8') || mb_strlen($value) > $max
            || preg_match('/[\x00-\x1F\x7F]/', $value)) {
            throw new DomainException('Hay un campo de facturación inválido.');
        }
        return trim($value);
    }

    public static function validDate(string $value): bool
    {
        return preg_match('/\A(\d{4})-(\d{2})-(\d{2})\z/', $value, $parts) === 1
            && checkdate((int)$parts[2], (int)$parts[3], (int)$parts[1]);
    }

    private static function roundRatio(int $numerator, int $denominator): int
    {
        return intdiv($numerator + intdiv($denominator, 2), $denominator);
    }

    private static function money(int $cents): string
    {
        return sprintf('%d.%02d', intdiv($cents, 100), $cents % 100);
    }
}
