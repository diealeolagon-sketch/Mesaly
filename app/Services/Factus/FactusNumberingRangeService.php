<?php

declare(strict_types=1);

namespace App\Services\Factus;

final class FactusNumberingRangeService
{
    private const FIELDS = [
        'id', 'document', 'document_name', 'prefix', 'resolution_number',
        'from', 'to', 'current', 'start_date', 'end_date', 'deleted_at',
    ];

    public function __construct(private readonly AuthenticatedFactusClient $client)
    {
    }

    /** @return list<array<string, int|string|bool|null>> */
    public function listInvoiceRanges(): array
    {
        $ranges = [];
        $page = 1;
        do {
            $query = http_build_query(['filter' => ['document' => '21'], 'page' => $page]);
            $response = $this->client->requestJson('GET', '/v2/numbering-ranges?' . $query);
            if (isset($response['data']['data'])) {
                $response = $response['data'];
            }
            $data = $response['data'] ?? null;
            if (!is_array($data) || !array_is_list($data)) {
                throw new FactusInvalidResponseException('Factus no devolvió una lista válida de rangos.');
            }

            foreach ($data as $row) {
                $ranges[] = $this->normalizeRange($row);
            }
            $lastPage = $this->lastPage($response, $page);
        } while (++$page <= $lastPage);

        return $ranges;
    }

    private function normalizeRange(mixed $row): array
    {
        $id = is_array($row) ? ($row['id'] ?? null) : null;
        if (!(is_int($id) && $id > 0) && !(is_string($id) && trim($id) !== '')) {
            throw new FactusInvalidResponseException('Factus devolvió un rango sin ID válido.');
        }

        $range = [];
        foreach (self::FIELDS as $field) {
            $value = $row[$field] ?? null;
            if ($value !== null && !is_string($value) && !is_int($value)) {
                throw new FactusInvalidResponseException('Factus devolvió un campo de rango inválido.');
            }
            $range[$field] = $value;
        }
        $range['is_active'] = $this->flag($row['is_active'] ?? null);
        $range['is_expired'] = $this->flag($row['is_expired'] ?? null);
        $range['is_external'] = $this->flag($row['is_external'] ?? null);
        $range['is_exhausted'] = is_numeric($range['current']) && is_numeric($range['to'])
            && $range['current'] > $range['to'];
        $range['is_available'] = $range['is_active'] === true && $range['is_expired'] === false
            && $range['deleted_at'] === null && $range['is_external'] !== true && !$range['is_exhausted'];
        return $range;
    }

    private function flag(mixed $value): ?bool
    {
        return match ($value) {
            true, 1, '1' => true,
            false, 0, '0' => false,
            null => null,
            default => throw new FactusInvalidResponseException('Factus devolvió un estado de rango inválido.'),
        };
    }

    private function lastPage(array $response, int $page): int
    {
        if (!array_key_exists('pagination', $response)) {
            return $page;
        }
        $pagination = $response['pagination'];
        if (!is_array($pagination)) {
            throw new FactusInvalidResponseException('Factus devolvió una paginación de rangos inválida.');
        }
        $current = filter_var($pagination['current_page'] ?? null, FILTER_VALIDATE_INT);
        $last = filter_var($pagination['last_page'] ?? null, FILTER_VALIDATE_INT);
        if ($current !== $page || $last === false || $last < $page) {
            throw new FactusInvalidResponseException('Factus devolvió una paginación de rangos inválida.');
        }
        return $last;
    }
}
