<?php

declare(strict_types=1);

namespace App\Services\Factus;

final class FactusAuthService
{
    public function __construct(
        private readonly FactusConfig $config,
        private readonly FactusClientInterface $client,
        private readonly TokenStoreInterface $tokenStore,
        private readonly ClockInterface $clock,
        private readonly string $lockPath,
    ) {
    }

    public function getAccessToken(bool $forceRenewal = false): string
    {
        $cached = $this->tokenStore->load();
        if (!$forceRenewal && $cached?->isUsable($this->clock->now(), $this->config->tokenLeeway)) {
            return $cached->accessToken;
        }

        return $this->withExclusiveLock(function () use ($forceRenewal): string {
            $current = $this->tokenStore->load();
            if (!$forceRenewal && $current?->isUsable($this->clock->now(), $this->config->tokenLeeway)) {
                return $current->accessToken;
            }

            if ($current !== null && $current->refreshToken !== '') {
                try {
                    return $this->refresh($current)->accessToken;
                } catch (FactusAuthenticationException|FactusValidationException $e) {
                    $this->tokenStore->clear();
                } catch (FactusException $e) {
                    if (!in_array($e->httpStatus, [400, 401, 422], true)) {
                        throw $e;
                    }
                    $this->tokenStore->clear();
                }
            }

            return $this->authenticate()->accessToken;
        });
    }

    public function clearToken(): void
    {
        $this->tokenStore->clear();
    }

    public function authenticateWithCredentials(): FactusToken
    {
        return $this->withExclusiveLock(fn (): FactusToken => $this->authenticate());
    }

    private function authenticate(): FactusToken
    {
        $this->config->assertCredentialsConfigured();
        $response = $this->client->requestForm('POST', '/oauth/token', [
            'grant_type' => 'password',
            'client_id' => $this->config->clientId,
            'client_secret' => $this->config->clientSecret,
            'username' => $this->config->username,
            'password' => $this->config->password,
        ]);
        return $this->storeResponse($response, null);
    }

    private function refresh(FactusToken $current): FactusToken
    {
        $this->config->assertCredentialsConfigured();
        $response = $this->client->requestForm('POST', '/oauth/token', [
            'grant_type' => 'refresh_token',
            'client_id' => $this->config->clientId,
            'client_secret' => $this->config->clientSecret,
            'refresh_token' => $current->refreshToken,
        ], $current->accessToken);
        return $this->storeResponse($response, $current->refreshToken);
    }

    private function storeResponse(array $response, ?string $previousRefreshToken): FactusToken
    {
        $accessToken = $response['access_token'] ?? null;
        $expiresIn = $response['expires_in'] ?? null;
        if (is_string($expiresIn) && ctype_digit($expiresIn)) {
            $expiresIn = (int)$expiresIn;
        }
        $now = $this->clock->now();
        if (!is_string($accessToken) || trim($accessToken) === '' || !is_int($expiresIn)
            || $expiresIn < 1 || $expiresIn > PHP_INT_MAX - $now) {
            throw new FactusInvalidResponseException('Factus no devolvió un token de acceso con vigencia válida.');
        }

        $refreshToken = is_string($response['refresh_token'] ?? null)
            ? $response['refresh_token']
            : ($previousRefreshToken ?? '');
        $token = new FactusToken(
            $accessToken,
            $refreshToken,
            is_string($response['token_type'] ?? null) ? $response['token_type'] : 'Bearer',
            $now + $expiresIn,
            $expiresIn,
        );
        $this->tokenStore->save($token);
        return $token;
    }

    private function withExclusiveLock(callable $callback): mixed
    {
        $directory = dirname($this->lockPath);
        if (!is_dir($directory) && !mkdir($directory, 0700, true) && !is_dir($directory)) {
            throw new FactusConfigurationException('No fue posible crear el directorio de bloqueo de Factus.');
        }
        $handle = @fopen($this->lockPath, 'c+b');
        if ($handle === false) {
            throw new FactusConfigurationException('No fue posible abrir el bloqueo de autenticación Factus.');
        }
        try {
            if (!flock($handle, LOCK_EX)) {
                throw new FactusConfigurationException('No fue posible bloquear la autenticación Factus.');
            }
            return $callback();
        } finally {
            flock($handle, LOCK_UN);
            fclose($handle);
        }
    }
}
