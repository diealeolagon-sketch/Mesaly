<?php

declare(strict_types=1);

namespace App\Services\Factus;

final class AuthenticatedFactusClient
{
    public function __construct(
        private readonly FactusClientInterface $client,
        private readonly FactusAuthService $auth,
    ) {
    }

    public function requestJson(string $method, string $path, array $data = []): array
    {
        $token = $this->auth->getAccessToken();
        try {
            return $this->client->requestJson($method, $path, $data, $token);
        } catch (FactusAuthenticationException) {
            $freshToken = $this->auth->getAccessToken(true);
            return $this->client->requestJson($method, $path, $data, $freshToken);
        }
    }
}
