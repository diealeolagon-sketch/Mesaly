<?php

declare(strict_types=1);

namespace App\Services\Factus;

final class FactusInvoiceService
{
    public function __construct(private readonly AuthenticatedFactusClient $client)
    {
    }

    public function issue(array $payload): array
    {
        return $this->result($this->client->requestJson('POST', '/v2/bills/validate', $payload, false), $payload['reference_code']);
    }

    public function availableRange(int $id, string $prefix): array
    {
        $today = date('Y-m-d');
        foreach ((new FactusNumberingRangeService($this->client))->listInvoiceRanges() as $range) {
            if ((string)$range['id'] === (string)$id && $range['prefix'] === $prefix && $range['is_available']
                && is_string($range['start_date']) && InvoiceDraft::validDate($range['start_date'])
                && is_string($range['end_date']) && InvoiceDraft::validDate($range['end_date'])
                && $range['start_date'] <= $today && $range['end_date'] >= $today) {
                return $range;
            }
        }
        throw new \DomainException('El rango y prefijo configurados no están disponibles en la cuenta Sandbox.');
    }

    public function find(string $reference): ?array
    {
        $query = http_build_query(['filter' => ['reference_code' => $reference]]);
        $response = $this->client->requestJson('GET', '/v2/bills?' . $query);
        $data = $response['data'] ?? null;
        $rows = $data['data'] ?? $data;
        if (!is_array($rows) || !array_is_list($rows)) {
            throw new FactusInvalidResponseException('Listado de facturas inválido.');
        }
        foreach ($rows as $row) {
            if (!is_array($row) || !is_string($row['reference_code'] ?? null)) {
                throw new FactusInvalidResponseException('Factus no identificó la referencia de un registro.');
            }
            if ($row['reference_code'] !== $reference) {
                continue;
            }
            $number = $this->number($row['number'] ?? null);
            if ($number !== null && in_array($row['status'] ?? null, [1, '1', true], true)) {
                $detail = $this->client->requestJson('GET', '/v2/bills/' . rawurlencode($number));
                return $this->result($detail, $reference, $number);
            }
            return $this->result(['data' => $row], $reference, null, true);
        }
        $pagination = $data['pagination'] ?? $response['pagination'] ?? null;
        if ($pagination !== null && !in_array($pagination['last_page'] ?? null, [1, '1'], true)) {
            throw new FactusInvalidResponseException('Factus no confirmó que el resultado por referencia esté completo.');
        }
        return null;
    }

    public function download(string $number, string $format): string
    {
        if ($this->number($number) === null || !in_array($format, ['pdf', 'xml'], true)) {
            throw new \DomainException('Documento solicitado inválido.');
        }
        $response = $this->client->requestJson('GET', '/v2/bills/' . rawurlencode($number) . '/download-' . $format);
        $encoded = $response['data'][$format . '_base_64_encoded'] ?? null;
        $bytes = is_string($encoded) ? base64_decode($encoded, true) : false;
        if ($bytes === false || ($format === 'pdf' ? !str_starts_with($bytes, '%PDF-') : !str_starts_with(ltrim($bytes), '<'))) {
            throw new FactusInvalidResponseException('Factus aún no entregó el documento solicitado.');
        }
        return $bytes;
    }

    private function result(array $response, string $reference, ?string $validatedNumber = null, bool $listed = false): array
    {
        $bill = $response['data']['bill'] ?? $response['data'] ?? null;
        if (!is_array($bill) || ($bill['reference_code'] ?? null) !== $reference) {
            throw new FactusInvalidResponseException('La respuesta no identifica la referencia de la factura.');
        }
        $number = $this->number($bill['number'] ?? null);
        if ($validatedNumber !== null && $number !== $validatedNumber) {
            throw new FactusInvalidResponseException('Factus devolvió un número distinto al consultado.');
        }
        $cufe = $bill['cufe'] ?? null;
        $cufe = is_string($cufe) && preg_match('/\A[a-fA-F0-9]{96}\z/', $cufe) ? $cufe : null;
        $flag = $bill['is_validated'] ?? $bill['status'] ?? null;
        $confirmed = in_array($flag, [true, 1, '1'], true)
            || ($flag === null && $validatedNumber !== null && $validatedNumber === $number);
        $validated = $number !== null && $cufe !== null && $confirmed;
        $rejected = in_array($bill['status'] ?? null, ['rejected', 'Rechazada'], true);
        $state = match (true) {
            $validated => 'validada',
            $rejected => 'rechazada',
            $confirmed || $validatedNumber !== null => 'incierta',
            $listed || $number !== null || in_array($flag, [false, 0, '0'], true) => 'pendiente',
            default => 'incierta',
        };
        $id = $bill['id'] ?? null;
        return [
            'estado' => $state,
            'externo_id' => is_scalar($id) ? $this->number((string)$id) : null,
            'numero' => $number,
            'cufe' => $cufe,
            'pdf' => $validated ? '/v2/bills/' . rawurlencode($number) . '/download-pdf' : null,
            'xml' => $validated ? '/v2/bills/' . rawurlencode($number) . '/download-xml' : null,
            'error' => $rejected ? 'Factus informó un rechazo.' : null,
            'emitida_at' => $this->date($bill['created_at'] ?? null),
            'validada_at' => $validated ? $this->date($bill['validated_at'] ?? null) : null,
        ];
    }

    private function date(mixed $value): ?string
    {
        if (!is_string($value)) {
            return null;
        }
        foreach (['d-m-Y h:i:s A', 'Y-m-d H:i:s', DATE_ATOM] as $format) {
            $date = \DateTimeImmutable::createFromFormat($format, $value);
            if ($date !== false && $date->format($format) === $value) {
                return $date->setTimezone(new \DateTimeZone(date_default_timezone_get()))->format('Y-m-d H:i:s');
            }
        }
        return null;
    }

    private function number(mixed $value): ?string
    {
        return is_string($value) && preg_match('/\A[A-Za-z0-9_-]{1,100}\z/', $value) ? $value : null;
    }
}
