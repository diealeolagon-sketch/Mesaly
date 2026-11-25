<?php

declare(strict_types=1);

namespace App\Services\Factus;

use App\Services\Factus\Http\HttpRequest;
use App\Services\Factus\Http\HttpResponse;
use App\Services\Factus\Http\HttpTransportInterface;
use JsonException;

final class FactusClient implements FactusClientInterface
{
    public function __construct(
        private readonly FactusConfig $config,
        private readonly HttpTransportInterface $transport,
    ) {
    }

    public function requestJson(string $method, string $path, array $data = [], ?string $accessToken = null): array
    {
        $headers = ['Accept: application/json', 'Content-Type: application/json'];
        $this->appendAuthorization($headers, $accessToken);
        $body = $data === [] ? null : json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);

        return $this->send(new HttpRequest($method, $this->url($path), $headers, $body));
    }

    public function requestForm(string $method, string $path, array $data, ?string $accessToken = null): array
    {
        $headers = ['Accept: application/json', 'Content-Type: application/x-www-form-urlencoded'];
        $this->appendAuthorization($headers, $accessToken);
        return $this->send(new HttpRequest(
            $method,
            $this->url($path),
            $headers,
            http_build_query($data, '', '&', PHP_QUERY_RFC3986),
        ));
    }

    private function send(HttpRequest $request): array
    {
        $response = $this->transport->send($request);
        $decoded = $this->decode($response);

        if ($response->statusCode >= 200 && $response->statusCode < 300) {
            return $decoded;
        }

        $details = SensitiveDataRedactor::redact($decoded);
        $status = $response->statusCode;

        if ($status === 401) {
            throw new FactusAuthenticationException('Factus rechazó la autenticación.', $status, $details);
        }
        if ($status === 422) {
            throw new FactusValidationException('Factus rechazó los datos enviados.', $status, $details);
        }
        if ($status === 429) {
            $retryAfter = $this->positiveInt($response->header('retry-after'));
            throw new FactusRateLimitException('Se alcanzó el límite de solicitudes de Factus.', $status, $details, $retryAfter);
        }
        if ($status >= 500) {
            throw new FactusServerException('Factus no está disponible temporalmente.', $status, $details);
        }

        throw new FactusException('Factus rechazó la solicitud.', $status, $details);
    }

    private function decode(HttpResponse $response): array
    {
        if (trim($response->body) === '') {
            return [];
        }

        try {
            $decoded = json_decode($response->body, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new FactusInvalidResponseException(
                'Factus devolvió una respuesta que no es JSON válido.',
                $response->statusCode,
                [],
                null,
                $e,
            );
        }

        if (!is_array($decoded)) {
            throw new FactusInvalidResponseException('Factus devolvió una estructura JSON no válida.', $response->statusCode);
        }

        return $decoded;
    }

    private function url(string $path): string
    {
        if ($path === '' || $path[0] !== '/' || str_contains($path, '://')) {
            throw new FactusConfigurationException('La ruta solicitada a Factus no es válida.');
        }
        return $this->config->baseUrl . $path;
    }

    private function appendAuthorization(array &$headers, ?string $accessToken): void
    {
        if ($accessToken !== null && $accessToken !== '') {
            $headers[] = 'Authorization: Bearer ' . $accessToken;
        }
    }

    private function positiveInt(?string $value): ?int
    {
        if ($value === null || !ctype_digit($value)) {
            return null;
        }
        return max(0, (int)$value);
    }
}
