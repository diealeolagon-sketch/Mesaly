<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\Factus\AuthenticatedFactusClient;
use App\Services\Factus\ClockInterface;
use App\Services\Factus\FactusAuthService;
use App\Services\Factus\FactusClientInterface;
use App\Services\Factus\FactusConfig;
use App\Services\Factus\FactusInvalidResponseException;
use App\Services\Factus\FactusNumberingRangeService;
use App\Services\Factus\FactusServerException;
use App\Services\Factus\FactusToken;
use App\Services\Factus\TokenStoreInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class FactusNumberingRangeServiceTest extends TestCase
{
    public function testListsInvoiceRangesAcrossPagesUsingTheAccessToken(): void
    {
        $client = $this->createMock(FactusClientInterface::class);
        $page = 0;
        $client->expects(self::exactly(2))->method('requestJson')
            ->willReturnCallback(function (string $method, string $path, array $body, string $token) use (&$page): array {
                ++$page;
                self::assertSame('GET', $method);
                self::assertSame('/v2/numbering-ranges?filter%5Bdocument%5D=21&page=' . $page, $path);
                self::assertSame([], $body);
                self::assertSame('test-access-token', $token);
                return [
                    'data' => [[
                        'id' => $page, 'document' => 'Factura electrónica de venta',
                        'current' => $page, 'to' => 1, 'is_active' => '1', 'is_expired' => false,
                        'technical_key' => 'test-technical-key',
                    ]],
                    'pagination' => ['current_page' => $page, 'last_page' => 2],
                ];
            });

        $ranges = $this->service($client)->listInvoiceRanges();

        self::assertSame([1, 2], array_column($ranges, 'id'));
        self::assertSame([true, false], array_column($ranges, 'is_available'));
        self::assertArrayNotHasKey('technical_key', $ranges[0]);
        self::assertNull($ranges[0]['resolution_number']);
    }

    public function testAcceptsAnEmptyList(): void
    {
        $client = $this->createMock(FactusClientInterface::class);
        $client->method('requestJson')->willReturn([
            'data' => ['data' => [], 'pagination' => ['current_page' => 1, 'last_page' => 1]],
        ]);

        self::assertSame([], $this->service($client)->listInvoiceRanges());
    }

    #[DataProvider('invalidResponses')]
    public function testRejectsInvalidResponses(array $response): void
    {
        $client = $this->createMock(FactusClientInterface::class);
        $client->method('requestJson')->willReturn($response);

        $this->expectException(FactusInvalidResponseException::class);
        $this->service($client)->listInvoiceRanges();
    }

    public static function invalidResponses(): iterable
    {
        yield 'missing list' => [[]];
        yield 'missing ID' => [['data' => [['prefix' => 'SETP']]]];
        yield 'invalid status' => [['data' => [['id' => 1, 'is_active' => 'unknown']]]];
        yield 'wrong page' => [['data' => [], 'pagination' => ['current_page' => 2, 'last_page' => 2]]];
    }

    public function testPropagatesFactusErrors(): void
    {
        $client = $this->createMock(FactusClientInterface::class);
        $error = new FactusServerException('Factus no está disponible.', 503);
        $client->method('requestJson')->willThrowException($error);

        $this->expectExceptionObject($error);
        $this->service($client)->listInvoiceRanges();
    }

    private function service(FactusClientInterface $client): FactusNumberingRangeService
    {
        $clock = $this->createStub(ClockInterface::class);
        $clock->method('now')->willReturn(1_800_000_000);
        $store = $this->createStub(TokenStoreInterface::class);
        $store->method('load')->willReturn(new FactusToken('test-access-token', '', 'Bearer', 1_800_003_600));
        $config = new FactusConfig('sandbox', 'https://api-sandbox.factus.com.co', '', '', '', '', '');
        $auth = new FactusAuthService($config, $client, $store, $clock, '');
        return new FactusNumberingRangeService(new AuthenticatedFactusClient($client, $auth));
    }
}
