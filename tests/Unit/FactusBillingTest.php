<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\Factus\AuthenticatedFactusClient;
use App\Services\Factus\ClockInterface;
use App\Services\Factus\FactusAuthenticationException;
use App\Services\Factus\FactusAuthService;
use App\Services\Factus\FactusBillingAccount;
use App\Services\Factus\FactusClientInterface;
use App\Services\Factus\FactusConfig;
use App\Services\Factus\FactusInvoiceService;
use App\Services\Factus\FactusToken;
use App\Services\Factus\InvoiceDraft;
use App\Services\Factus\TokenStoreInterface;
use DomainException;
use PHPUnit\Framework\TestCase;

final class FactusBillingTest extends TestCase
{
    public function testPreviewUsesHistoricalTotalsIncludingModifiersAndShipping(): void
    {
        $preview = InvoiceDraft::totals($this->order(), [
            22 => ['code' => '04', 'rate' => '8'],
            'envio' => ['code' => 'excluded', 'rate' => '0'],
        ]);
        self::assertSame('12800.00', $preview['total']);
        self::assertSame('800.00', $preview['tax']);
        self::assertSame('10000.00', $preview['items'][0]['price']);
        self::assertSame('P-22', $preview['items'][0]['code_reference']);
        self::assertTrue($preview['items'][1]['taxes'][0]['is_excluded']);
    }

    public function testMissingTaxConfigurationBlocksPreview(): void
    {
        $this->expectException(DomainException::class);
        InvoiceDraft::totals($this->order(), []);
    }

    public function testValidatesBuyerAndRequiresDueDateForCredit(): void
    {
        $buyer = InvoiceDraft::customer([
            'buyer_type' => 'identified', 'document' => '31', 'organization' => '1',
            'identification' => '123456789', 'name' => 'Comprador de prueba',
        ]);
        self::assertSame('Comprador de prueba', $buyer['company']);
        self::assertArrayNotHasKey('names', $buyer);
        self::assertSame('22222222222', InvoiceDraft::customer(['buyer_type' => 'final'])['identification']);
        $this->expectException(DomainException::class);
        InvoiceDraft::payment(['payment_form' => '2', 'payment_method' => '10'], '12800.00');
    }

    public function testSuccessfulHttpResponseDoesNotImplyValidation(): void
    {
        $client = $this->createMock(FactusClientInterface::class);
        $payload = ['reference_code' => 'MESALY-test'];
        $client->expects(self::once())->method('requestJson')
            ->with('POST', '/v2/bills/validate', $payload, 'test-access')
            ->willReturn(['status' => 'Created', 'data' => [
                'reference_code' => 'MESALY-test', 'number' => 'SETP1', 'is_validated' => false,
            ]]);
        $result = $this->service($client)->issue($payload);
        self::assertSame('pendiente', $result['estado']);
        self::assertNull($result['pdf']);
    }

    public function testIssueDoesNotAutomaticallyResendOnUnauthorized(): void
    {
        $client = $this->createMock(FactusClientInterface::class);
        $client->expects(self::never())->method('requestForm');
        $client->expects(self::once())->method('requestJson')
            ->willThrowException(new FactusAuthenticationException('Credencial no válida.', 401));
        $this->expectException(FactusAuthenticationException::class);
        $this->service($client)->issue(['reference_code' => 'MESALY-test']);
    }

    public function testReconciliationUsesExactReferenceAndFetchesValidatedDetail(): void
    {
        $client = $this->createMock(FactusClientInterface::class);
        $cufe = str_repeat('a', 96);
        $client->expects(self::exactly(2))->method('requestJson')
            ->willReturnCallback(static function (string $method, string $path) use ($cufe): array {
                self::assertSame('GET', $method);
                if ($path === '/v2/bills?filter%5Breference_code%5D=MESALY-test') {
                    return ['data' => ['data' => [
                        ['reference_code' => 'MESALY-test-other', 'number' => 'SETP2', 'status' => 1],
                        ['reference_code' => 'MESALY-test', 'number' => 'SETP1', 'status' => 1],
                    ]]];
                }
                self::assertSame('/v2/bills/SETP1', $path);
                return ['data' => ['bill' => [
                    'reference_code' => 'MESALY-test', 'number' => 'SETP1', 'is_validated' => true, 'cufe' => $cufe,
                ]]];
            });
        $result = $this->service($client)->find('MESALY-test');
        self::assertSame('validada', $result['estado']);
        self::assertSame($cufe, $result['cufe']);
        self::assertSame('/v2/bills/SETP1/download-xml', $result['xml']);
    }

    public function testDownloadsOfficialPdfBytes(): void
    {
        $client = $this->createMock(FactusClientInterface::class);
        $client->expects(self::once())->method('requestJson')->with('GET', '/v2/bills/SETP1/download-pdf')
            ->willReturn(['data' => ['pdf_base_64_encoded' => base64_encode('%PDF-1.7 test')]]);
        self::assertSame('%PDF-1.7 test', $this->service($client)->download('SETP1', 'pdf'));
    }

    public function testEncryptedCredentialsAreBoundToTheirTenant(): void
    {
        $key = $_ENV['FACTUS_TOKEN_ENCRYPTION_KEY'] ?? null;
        $_ENV['FACTUS_TOKEN_ENCRYPTION_KEY'] = 'base64:' . base64_encode(random_bytes(32));
        try {
            $credentials = [
                'username' => 'test@example.test', 'password' => 'mock-password',
                'client_id' => 'mock-client', 'client_secret' => 'mock-secret',
            ];
            $cipher = FactusBillingAccount::encrypt(1, $credentials);
            $config = ['ambiente' => 'sandbox', 'credenciales' => $cipher];
            self::assertInstanceOf(AuthenticatedFactusClient::class, FactusBillingAccount::connect(1, $config)[1]);
            $this->expectException(DomainException::class);
            FactusBillingAccount::connect(2, $config);
        } finally {
            if ($key === null) {
                unset($_ENV['FACTUS_TOKEN_ENCRYPTION_KEY']);
            } else {
                $_ENV['FACTUS_TOKEN_ENCRYPTION_KEY'] = $key;
            }
        }
    }

    private function order(): array
    {
        return ['items' => [[
            'producto_id' => 22, 'cantidad' => 1, 'total_linea' => 10800,
            'precio_unitario' => 10000, 'nombre_producto' => 'Producto de prueba',
        ]], 'costo_envio' => 2000, 'subtotal' => 10800, 'total' => 12800];
    }

    private function service(FactusClientInterface $client): FactusInvoiceService
    {
        $clock = $this->createStub(ClockInterface::class);
        $clock->method('now')->willReturn(1000);
        $store = $this->createStub(TokenStoreInterface::class);
        $store->method('load')->willReturn(new FactusToken('test-access', '', 'Bearer', 5000));
        $config = new FactusConfig('sandbox', 'https://api-sandbox.factus.com.co', '', '', '', '', '');
        return new FactusInvoiceService(new AuthenticatedFactusClient(
            $client, new FactusAuthService($config, $client, $store, $clock, ''),
        ));
    }
}
