<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;

class RateLimitMiddleware implements MiddlewareInterface
{
    private int $maxAttempts;
    private int $decaySeconds;

    public function __construct(int $maxAttempts = 10, int $decaySeconds = 600)
    {
        $this->maxAttempts = $maxAttempts;
        $this->decaySeconds = $decaySeconds;
    }

    public function handle(Request $request, array $params = []): ?Response
    {
        $ip = $request->ip();
        $cacheFile = dirname(__DIR__, 2) . '/storage/cache/ratelimit_' . md5($ip . '_' . $request->getUri()) . '.json';

        $data = ['count' => 0, 'first_attempt' => time()];
        if (file_exists($cacheFile)) {
            $raw = @file_get_contents($cacheFile);
            $parsed = json_decode((string)$raw, true);
            if (is_array($parsed)) {
                $data = $parsed;
            }
        }

        $now = time();
        if ($now - $data['first_attempt'] > $this->decaySeconds) {
            $data = ['count' => 1, 'first_attempt' => $now];
        } else {
            $data['count']++;
        }

        @file_put_contents($cacheFile, json_encode($data), LOCK_EX);

        if ($data['count'] > $this->maxAttempts) {
            $response = new Response();
            $response->setStatusCode(429);
            $response->header('Retry-After', (string)($this->decaySeconds - ($now - $data['first_attempt'])));

            if ($request->isAjax()) {
                return $response->json([
                    'success' => false,
                    'message' => 'Demasiadas peticiones. Por favor intente más tarde.',
                ], 429);
            }

            return $response->setContent('<h1>429 — Demasiadas peticiones. Por favor intente más tarde.</h1>');
        }

        return null;
    }
}
