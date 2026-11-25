<?php

declare(strict_types=1);

namespace App\Services\Factus\Http;

use App\Services\Factus\FactusConnectionException;
use App\Services\Factus\FactusTimeoutException;

final class CurlHttpTransport implements HttpTransportInterface
{
    public function __construct(
        private readonly int $connectTimeout,
        private readonly int $timeout,
        private readonly int $maxResponseBytes = 5_242_880,
    ) {
        if (!extension_loaded('curl')) {
            throw new FactusConnectionException('La extensión cURL de PHP no está disponible.');
        }
    }

    public function send(HttpRequest $request): HttpResponse
    {
        $curl = curl_init($request->url);
        if ($curl === false) {
            throw new FactusConnectionException('No fue posible inicializar la conexión con Factus.');
        }

        $responseHeaders = [];
        $responseBody = '';
        $tooLarge = false;

        curl_setopt_array($curl, [
            CURLOPT_CUSTOMREQUEST => strtoupper($request->method),
            CURLOPT_HTTPHEADER => $request->headers,
            CURLOPT_CONNECTTIMEOUT => $this->connectTimeout,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_RETURNTRANSFER => false,
            CURLOPT_HEADERFUNCTION => static function ($handle, string $line) use (&$responseHeaders): int {
                $length = strlen($line);
                $parts = explode(':', $line, 2);
                if (count($parts) === 2) {
                    $responseHeaders[strtolower(trim($parts[0]))] = trim($parts[1]);
                }
                return $length;
            },
            CURLOPT_WRITEFUNCTION => function ($handle, string $chunk) use (&$responseBody, &$tooLarge): int {
                if (strlen($responseBody) + strlen($chunk) > $this->maxResponseBytes) {
                    $tooLarge = true;
                    return 0;
                }
                $responseBody .= $chunk;
                return strlen($chunk);
            },
        ]);

        if ($request->body !== null) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $request->body);
        }

        $ok = curl_exec($curl);
        $errno = curl_errno($curl);
        $statusCode = (int)curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        curl_close($curl);

        if ($tooLarge) {
            throw new FactusConnectionException('La respuesta de Factus excede el tamaño permitido.');
        }

        if ($ok === false || $errno !== 0) {
            if ($errno === CURLE_OPERATION_TIMEDOUT) {
                throw new FactusTimeoutException('Factus no respondió dentro del tiempo permitido.');
            }
            throw new FactusConnectionException('No fue posible conectar con Factus.');
        }

        return new HttpResponse($statusCode, $responseHeaders, $responseBody);
    }
}
