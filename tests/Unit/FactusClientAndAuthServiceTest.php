<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\Factus\AuthenticatedFactusClient;
use App\Services\Factus\ClockInterface;
use App\Services\Factus\EncryptedFileTokenStore;
use App\Services\Factus\FactusAuthService;
use App\Services\Factus\FactusAuthenticationException;
use App\Services\Factus\FactusClient;
use App\Services\Factus\FactusClientInterface;
use App\Services\Factus\FactusConfig;
use App\Services\Factus\FactusConnectionException;
use App\Services\Factus\FactusInvalidResponseException;
use App\Services\Factus\FactusRateLimitException;
use App\Services\Factus\FactusServerException;
use App\Services\Factus\FactusTimeoutException;
use App\Services\Factus\FactusToken;
use App\Services\Factus\FactusValidationException;
use App\Services\Factus\Http\HttpRequest;
use App\Services\Factus\Http\HttpResponse;
use App\Services\Factus\Http\HttpTransportInterface;
use App\Services\Factus\TokenStoreInterface;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class FactusClientAndAuthServiceTest extends TestCase
{
    private array $temporaryFiles = [];

    protected function tearDown(): void
    {
        foreach ($this->temporaryFiles as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        parent::tearDown();
    }

    public function testAutenticaConPasswordYReutilizaTokenVigente(): void
    {
        $transport = new QueueTransport([
            new HttpResponse(200, [], json_encode([
                'token_type' => 'Bearer',
                'expires_in' => 3600,
                'access_token' => 'access-secret',
                'refresh_token' => 'refresh-secret',
            ], JSON_THROW_ON_ERROR)),
        ]);
        $clock = new MutableClock(1_700_000_000);
        $store = new MemoryTokenStore();
        $service = $this->authService($transport, $store, $clock);

        $this->assertSame('access-secret', $service->getAccessToken());
        $this->assertSame('access-secret', $service->getAccessToken());
        $this->assertCount(1, $transport->requests);

        parse_str((string)$transport->requests[0]->body, $body);
        $this->assertSame('password', $body['grant_type']);
        $this->assertSame('client-id', $body['client_id']);
        $this->assertSame('client-secret', $body['client_secret']);
        $this->assertSame('sandbox@example.test', $body['username']);
        $this->assertSame('password-secret', $body['password']);
    }

    public function testRenuevaAntesDelVencimientoConRefreshToken(): void
    {
        $clock = new MutableClock(1_700_000_000);
        $store = new MemoryTokenStore(new FactusToken('old-access', 'old-refresh', 'Bearer', $clock->now() + 60));
        $transport = new QueueTransport([
            new HttpResponse(200, [], json_encode([
                'expires_in' => 1800,
                'access_token' => 'new-access',
                'refresh_token' => 'new-refresh',
            ], JSON_THROW_ON_ERROR)),
        ]);

        $service = $this->authService($transport, $store, $clock);
        $this->assertSame('new-access', $service->getAccessToken());
        parse_str((string)$transport->requests[0]->body, $body);
        $this->assertSame('refresh_token', $body['grant_type']);
        $this->assertSame('old-refresh', $body['refresh_token']);
        $this->assertSame('new-refresh', $store->token?->refreshToken);
    }

    public function testAlmacenCifraTokenEnDisco(): void
    {
        $path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'mesaly-factus-' . bin2hex(random_bytes(6)) . '.enc';
        $this->temporaryFiles[] = $path;
        $key = 'base64:' . base64_encode(random_bytes(32));
        $store = new EncryptedFileTokenStore($path, $key);
        $token = new FactusToken('access-not-plain', 'refresh-not-plain', 'Bearer', 1_800_000_000);

        $store->save($token);
        $raw = file_get_contents($path);
        $this->assertIsString($raw);
        $this->assertStringNotContainsString('access-not-plain', $raw);
        $this->assertStringNotContainsString('refresh-not-plain', $raw);
        $this->assertSame('access-not-plain', $store->load()?->accessToken);
    }

    public function testMapeaErroresHttpSinExponerSecretos(): void
    {
        $cases = [
            [401, FactusAuthenticationException::class, []],
            [422, FactusValidationException::class, []],
            [429, FactusRateLimitException::class, ['retry-after' => '15']],
            [500, FactusServerException::class, []],
            [503, FactusServerException::class, []],
        ];

        foreach ($cases as [$status, $expected, $headers]) {
            $transport = new QueueTransport([
                new HttpResponse($status, $headers, json_encode([
                    'message' => 'fallo',
                    'access_token' => 'must-not-leak',
                    'nested' => ['client_secret' => 'must-not-leak-either'],
                ], JSON_THROW_ON_ERROR)),
            ]);
            $client = new FactusClient($this->config(), $transport);
            try {
                $client->requestJson('GET', '/v2/companies', [], 'authorization-secret');
                $this->fail('Se esperaba una excepción Factus.');
            } catch (\Throwable $e) {
                $this->assertInstanceOf($expected, $e);
                $serialized = json_encode($e instanceof \App\Services\Factus\FactusException ? $e->details : [], JSON_THROW_ON_ERROR);
                $this->assertStringNotContainsString('must-not-leak', $serialized);
                if ($e instanceof FactusRateLimitException) {
                    $this->assertSame(15, $e->retryAfter);
                }
            }
        }
    }

    public function testRechazaRespuestaNoJson(): void
    {
        $client = new FactusClient($this->config(), new QueueTransport([
            new HttpResponse(200, [], '<html>error</html>'),
        ]));
        $this->expectException(FactusInvalidResponseException::class);
        $client->requestJson('GET', '/v2/companies');
    }

    public function testPropagaTimeoutYErrorDeConexionControlados(): void
    {
        foreach ([
            new FactusTimeoutException('timeout'),
            new FactusConnectionException('connection'),
        ] as $exception) {
            $client = new FactusClient($this->config(), new QueueTransport([$exception]));
            try {
                $client->requestJson('GET', '/v2/companies');
                $this->fail('Se esperaba un error de transporte.');
            } catch (\Throwable $e) {
                $this->assertSame($exception, $e);
            }
        }
    }

    public function testClienteAutenticadoRenuevaYReintentaUnaSolaVezTras401(): void
    {
        $clock = new MutableClock(1_700_000_000);
        $store = new MemoryTokenStore(new FactusToken('old-access', 'refresh', 'Bearer', $clock->now() + 3600));
        $scripted = new ScriptedFactusClient([
            new FactusAuthenticationException('401', 401),
            ['access_token' => 'fresh-access', 'refresh_token' => 'fresh-refresh', 'expires_in' => 3600],
            ['data' => ['ok' => true]],
        ]);
        $auth = new FactusAuthService($this->config(), $scripted, $store, $clock, $this->lockPath());
        $client = new AuthenticatedFactusClient($scripted, $auth);

        $this->assertSame(['data' => ['ok' => true]], $client->requestJson('GET', '/v2/companies'));
        $this->assertSame(['old-access', 'fresh-access'], $scripted->jsonTokens);
    }

    public function testBloqueaAmbienteProduccion(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new FactusConfig(
            'production',
            'https://api.factus.com.co',
            '', '', '', '', '',
        );
    }

    private function authService(QueueTransport $transport, MemoryTokenStore $store, MutableClock $clock): FactusAuthService
    {
        return new FactusAuthService(
            $this->config(),
            new FactusClient($this->config(), $transport),
            $store,
            $clock,
            $this->lockPath(),
        );
    }

    private function config(): FactusConfig
    {
        return new FactusConfig(
            'sandbox',
            'https://api-sandbox.factus.com.co',
            'client-id',
            'client-secret',
            'sandbox@example.test',
            'password-secret',
            'base64:' . base64_encode(str_repeat('k', 32)),
            2,
            5,
            120,
            sys_get_temp_dir() . '/unused-factus-token.enc',
            $this->lockPath(),
        );
    }

    private function lockPath(): string
    {
        $path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'mesaly-factus-test.lock';
        $this->temporaryFiles[] = $path;
        return $path;
    }
}

final class QueueTransport implements HttpTransportInterface
{
    public array $requests = [];

    public function __construct(private array $queue)
    {
    }

    public function send(HttpRequest $request): HttpResponse
    {
        $this->requests[] = $request;
        $next = array_shift($this->queue);
        if ($next instanceof \Throwable) {
            throw $next;
        }
        if (!$next instanceof HttpResponse) {
            throw new \RuntimeException('No hay respuesta HTTP simulada.');
        }
        return $next;
    }
}

final class MemoryTokenStore implements TokenStoreInterface
{
    public function __construct(public ?FactusToken $token = null)
    {
    }

    public function load(): ?FactusToken
    {
        return $this->token;
    }

    public function save(FactusToken $token): void
    {
        $this->token = $token;
    }

    public function clear(): void
    {
        $this->token = null;
    }
}

final class MutableClock implements ClockInterface
{
    public function __construct(public int $timestamp)
    {
    }

    public function now(): int
    {
        return $this->timestamp;
    }
}

final class ScriptedFactusClient implements FactusClientInterface
{
    public array $jsonTokens = [];

    public function __construct(private array $queue)
    {
    }

    public function requestJson(string $method, string $path, array $data = [], ?string $accessToken = null): array
    {
        $this->jsonTokens[] = $accessToken;
        return $this->next();
    }

    public function requestForm(string $method, string $path, array $data, ?string $accessToken = null): array
    {
        return $this->next();
    }

    private function next(): array
    {
        $next = array_shift($this->queue);
        if ($next instanceof \Throwable) {
            throw $next;
        }
        if (!is_array($next)) {
            throw new \RuntimeException('No hay respuesta Factus simulada.');
        }
        return $next;
    }
}
