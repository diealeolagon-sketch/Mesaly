<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Logger;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\TenantContext;
use App\Models\FacturaElectronica;
use App\Models\Pedido;
use App\Services\Factus\FactusBillingAccount;
use App\Services\Factus\FactusException;
use App\Services\Factus\FactusInvoiceService;
use App\Services\Factus\InvoiceDraft;
use Closure;
use DomainException;
use Throwable;

final class FacturacionController extends Controller
{
    private const BASE = '/admin/facturacion';
    private readonly Closure $connect;

    public function __construct(
        private readonly FacturaElectronica $invoices = new FacturaElectronica(),
        ?Closure $connect = null,
    ) {
        $this->connect = $connect ?? FactusBillingAccount::connect(...);
    }

    public function index(Request $request): Response
    {
        return $this->handle(function (int $tenant) use ($request): Response {
            $filters = [];
            foreach (['q', 'estado', 'desde', 'hasta'] as $key) {
                $value = $request->query($key, '');
                $filters[$key] = is_string($value) ? mb_substr(trim($value), 0, 100) : '';
            }
            foreach (['desde', 'hasta'] as $key) {
                if (!InvoiceDraft::validDate($filters[$key])) {
                    $filters[$key] = '';
                }
            }
            $page = $request->query('page', 1);
            return $this->view('index', $this->invoices->listing($tenant, $filters, max(1, is_scalar($page) ? (int)$page : 1))
                + ['filters' => $filters]);
        });
    }

    public function settings(Request $request): Response
    {
        return $this->handle(fn (int $tenant): Response => $this->view('config', [
            'settings' => $this->invoices->settings($tenant),
            'products' => $this->invoices->products($tenant),
            'globalAllowed' => FactusBillingAccount::globalAllowed($tenant),
        ]));
    }

    public function save(Request $request): Response
    {
        return $this->handle(function (int $tenant) use ($request): Response {
            $input = $request->post();
            if (($input['settings_complete'] ?? null) !== '1') {
                throw new DomainException('El formulario de configuración se recibió incompleto.');
            }
            $current = $this->invoices->settings($tenant);
            $enabled = ($input['enabled'] ?? null) === '1';
            $range = InvoiceDraft::field($input, 'range', 18);
            $prefix = InvoiceDraft::field($input, 'prefix', 16);
            $fiscal = [
                'nit' => InvoiceDraft::field($input, 'nit', 20),
                'razon_social' => InvoiceDraft::field($input, 'razon_social', 255),
            ];
            if (($range !== '' && (!ctype_digit($range) || (int)$range < 1))
                || ($prefix !== '' && !ctype_alnum($prefix))
                || ($enabled && ($range === '' || $prefix === '' || !ctype_digit($fiscal['nit']) || $fiscal['razon_social'] === ''))) {
                throw new DomainException('Completa el rango, prefijo, NIT sin dígito de verificación y razón social del emisor.');
            }
            $taxes = [];
            $allowed = array_fill_keys(array_column($this->invoices->products($tenant), 'id'), true) + ['envio' => true];
            $submitted = $input['taxes'] ?? [];
            if (!is_array($submitted) || array_diff_key($submitted, $allowed) !== []) {
                throw new DomainException('La configuración tributaria contiene productos inválidos.');
            }
            foreach ($submitted as $code => $tax) {
                if (!is_array($tax)) {
                    throw new DomainException('La configuración tributaria es inválida.');
                }
                if (($tax['code'] ?? '') !== '' || ($tax['rate'] ?? '') !== '') {
                    $taxes[$code] = InvoiceDraft::tax($tax);
                }
            }
            $credentials = [];
            foreach (FactusBillingAccount::CREDENTIALS as $key) {
                $value = $input[$key] ?? '';
                if (!is_string($value) || strlen($value) > 2048 || preg_match('/[\x00-\x1F\x7F]/', $value)) {
                    throw new DomainException('Hay una credencial inválida.');
                }
                if (trim($value) !== '') {
                    $credentials[$key] = $value;
                }
            }
            $global = ($input['global'] ?? null) === '1';
            if (($global && (!FactusBillingAccount::globalAllowed($tenant) || $credentials !== []))
                || ($credentials !== [] && count($credentials) !== 4)) {
                throw new DomainException('Selecciona una fuente de credenciales válida o completa las cuatro credenciales.');
            }
            $cipher = $credentials === [] ? null : FactusBillingAccount::encrypt($tenant, $credentials);
            if ($enabled && $cipher === null && empty($current['credenciales']) && !FactusBillingAccount::globalAllowed($tenant)) {
                throw new DomainException('Configura las cuatro credenciales Sandbox del restaurante.');
            }
            $this->invoices->saveSettings($tenant, (int)Auth::id(), [
                'habilitada' => (int)$enabled, 'rango_id' => (int)$range, 'prefijo' => $prefix,
                'fiscal' => $fiscal, 'impuestos' => $taxes, 'credenciales' => $cipher,
            ], $global);
            Session::flash('success', 'Configuración de facturación guardada.');
            return $this->redirect(app_url(self::BASE . '/configuracion'));
        }, self::BASE . '/configuracion');
    }

    public function show(Request $request, array $params): Response
    {
        $id = (int)$params['id'];
        return $this->handle(function (int $tenant) use ($id): Response {
            $order = (new Pedido())->obtenerDetalleCompleto($id, $tenant);
            if ($order === null) {
                return (new Response())->setStatusCode(404);
            }
            $invoice = $this->invoices->invoice($tenant, $id);
            $settings = $this->invoices->settings($tenant);
            $preview = null;
            $blocked = '';
            if ($invoice === null) {
                try {
                    $preview = $this->preview($order, $settings);
                } catch (DomainException $error) {
                    $blocked = $error->getMessage();
                }
            }
            $events = $invoice === null ? [] : $this->invoices->events($tenant, (int)$invoice['id']);
            return $this->view('pedido', compact('order', 'invoice', 'preview', 'blocked', 'events'));
        });
    }

    public function review(Request $request, array $params): Response
    {
        $id = (int)$params['id'];
        return $this->handle(function (int $tenant) use ($request, $id): Response {
            if ($this->invoices->invoice($tenant, $id) !== null) {
                return $this->detailRedirect($id);
            }
            $order = (new Pedido())->obtenerDetalleCompleto($id, $tenant);
            $settings = $this->invoices->settings($tenant);
            $preview = $this->preview($order, $settings);
            $customer = InvoiceDraft::customer($request->post());
            $payment = InvoiceDraft::payment($request->post(), $preview['total']);
            $ticket = bin2hex(random_bytes(24));
            Session::set('facturacion_confirmacion', [
                'ticket' => $ticket, 'tenant' => $tenant, 'order' => $id, 'user' => Auth::id(),
                'fingerprint' => $this->fingerprint($preview, $settings),
                'customer' => $customer, 'payment' => $payment, 'expires' => time() + 900,
            ]);
            return $this->view('confirmar', compact('order', 'preview', 'customer', 'payment', 'ticket'));
        }, self::BASE . '/pedidos/' . $id);
    }

    public function issue(Request $request, array $params): Response
    {
        $id = (int)$params['id'];
        return $this->handle(function (int $tenant) use ($request, $id): Response {
            if ($this->invoices->invoice($tenant, $id) !== null) {
                return $this->detailRedirect($id);
            }
            $confirmation = Session::get('facturacion_confirmacion');
            if (!is_array($confirmation) || $confirmation['tenant'] !== $tenant || $confirmation['order'] !== $id
                || $confirmation['user'] !== Auth::id() || $confirmation['expires'] < time()
                || $confirmation['ticket'] !== $request->post('ticket') || $request->post('confirm') !== '1') {
                throw new DomainException('Vuelve a revisar y confirmar la factura.');
            }
            $order = (new Pedido())->obtenerDetalleCompleto($id, $tenant);
            $settings = $this->invoices->settings($tenant);
            $preview = $this->preview($order, $settings);
            if (!hash_equals($confirmation['fingerprint'], $this->fingerprint($preview, $settings))) {
                throw new DomainException('El pedido o la configuración fiscal cambió. Revisa la factura nuevamente.');
            }
            [$account, $client] = ($this->connect)($tenant, $settings);
            $service = new FactusInvoiceService($client);
            $selectedRange = $service->availableRange((int)$settings['rango_id'], $settings['prefijo']);
            $reference = 'MESALY-SB-' . $tenant . '-' . $id . '-' . bin2hex(random_bytes(12));
            $payload = [
                'reference_code' => $reference, 'document' => '01', 'operation_type' => '10',
                'numbering_range_id' => (int)$settings['rango_id'], 'send_email' => false,
                'customer' => $confirmation['customer'], 'payment_details' => [$confirmation['payment']],
                'items' => $preview['items'],
            ];
            $user = (int)Auth::id();
            $invoice = $this->invoices->reserve($tenant, $id, $user, $account, $reference, [
                'payload' => $payload, 'preview' => $preview, 'fiscal' => $settings['fiscal'], 'range' => $selectedRange,
            ]);
            if ($invoice !== null) {
                Session::remove('facturacion_confirmacion');
                $this->dispatchInvoice($tenant, $invoice, $user, 0, $service, $payload);
            }
            return $this->detailRedirect($id);
        }, self::BASE . '/pedidos/' . $id);
    }

    public function reconcile(Request $request, array $params): Response
    {
        $id = (int)$params['id'];
        return $this->handle(function (int $tenant) use ($id): Response {
            $invoice = $this->invoices->invoice($tenant, $id);
            if ($invoice === null) {
                return $this->detailRedirect($id);
            }
            if ($invoice['estado'] === 'emitiendo' && !$invoice['puede_revisar']) {
                throw new DomainException('La emisión está en curso. Espera antes de consultar nuevamente.');
            }
            $result = $this->serviceForInvoice($tenant, $invoice)->find($invoice['referencia']);
            if ($invoice['estado'] === 'validada') {
                $confirmed = ($result['estado'] ?? null) === 'validada'
                    && ($result['numero'] ?? null) === $invoice['numero']
                    && ($result['cufe'] ?? null) === $invoice['cufe'];
                $this->invoices->event($tenant, (int)$invoice['id'], (int)Auth::id(), 'consulta_estado',
                    $confirmed ? 'Factus confirmó la validación.' : 'La consulta no confirmó el estado registrado.');
                Session::flash($confirmed ? 'success' : 'error',
                    $confirmed ? 'Factus confirmó la validación.' : 'Factus no confirmó el estado registrado.');
                return $this->detailRedirect($id);
            }
            if ($result === null && $invoice['estado'] === 'rechazada') {
                Session::flash('error', 'La referencia rechazada aún no figura en el listado de Factus.');
                return $this->detailRedirect($id);
            }
            $this->invoices->record($tenant, (int)$invoice['id'], (int)Auth::id(), (int)$invoice['version'], $result ?? [
                'estado' => 'incierta', 'error' => 'Factus aún no lista esta referencia. Consulta nuevamente más tarde.',
            ]);
            return $this->detailRedirect($id);
        }, self::BASE . '/pedidos/' . $id);
    }

    public function retry(Request $request, array $params): Response
    {
        $id = (int)$params['id'];
        return $this->handle(function (int $tenant) use ($request, $id): Response {
            $invoice = $this->invoices->invoice($tenant, $id);
            if ($invoice === null || $invoice['estado'] !== 'incierta' || $invoice['numero'] !== null
                || $request->post('confirm') !== '1' || !$invoice['puede_revisar']) {
                throw new DomainException('La factura todavía no se puede reintentar. Revisa su estado.');
            }
            if (empty($this->invoices->settings($tenant)['habilitada'])) {
                throw new DomainException('La emisión está deshabilitada para el restaurante.');
            }
            $service = $this->serviceForInvoice($tenant, $invoice);
            $result = $service->find($invoice['referencia']);
            $user = (int)Auth::id();
            $invoiceId = (int)$invoice['id'];
            $version = (int)$invoice['version'];
            if ($result !== null) {
                $this->invoices->record($tenant, $invoiceId, $user, $version, $result);
                return $this->detailRedirect($id);
            }
            $service->availableRange((int)$invoice['rango_id'], $invoice['snapshot']['range']['prefix']);
            if ($this->invoices->claimRetry($tenant, $invoiceId, $user, $version)) {
                $this->dispatchInvoice($tenant, $invoiceId, $user, $version + 1, $service, $invoice['snapshot']['payload']);
            }
            return $this->detailRedirect($id);
        }, self::BASE . '/pedidos/' . $id);
    }

    private function dispatchInvoice(int $tenant, int $invoice, int $user, int $version, FactusInvoiceService $service, array $payload): void
    {
        try {
            $result = $service->issue($payload);
        } catch (FactusException $error) {
            $rejected = $error instanceof \App\Services\Factus\FactusValidationException;
            $result = [
                'estado' => $rejected ? 'rechazada' : 'incierta',
                'error' => $rejected ? 'Factus rechazó los datos de la solicitud.'
                    : 'La recepción no pudo confirmarse. Consulta el estado por referencia.',
            ];
            if (!$rejected) {
                try {
                    $result = $service->find($payload['reference_code']) ?? $result;
                } catch (FactusException) {
                    // La referencia persistida permite conciliar cuando Factus vuelva a responder.
                }
            }
        } catch (Throwable) {
            $result = ['estado' => 'incierta', 'error' => 'No se confirmó la emisión. Consulta el estado por referencia.'];
        }
        $this->invoices->record($tenant, $invoice, $user, $version, $result);
    }

    public function document(Request $request, array $params): Response
    {
        $id = (int)$params['id'];
        $format = $params['formato'];
        return $this->handle(function (int $tenant) use ($request, $id, $format): Response {
            $invoice = $this->invoices->invoice($tenant, $id);
            if (!in_array($format, ['pdf', 'xml'], true) || $invoice === null || $invoice['estado'] !== 'validada') {
                return (new Response())->setStatusCode(404);
            }
            $bytes = $this->serviceForInvoice($tenant, $invoice)->download($invoice['numero'], $format);
            $this->invoices->event($tenant, (int)$invoice['id'], (int)Auth::id(), 'descarga_' . $format);
            $disposition = $format === 'pdf' && $request->query('imprimir') === '1' ? 'inline' : 'attachment';
            return (new Response())->header('Content-Type', $format === 'pdf' ? 'application/pdf' : 'application/xml')
                ->header('Content-Disposition', $disposition . '; filename="' . $invoice['numero'] . '.' . $format . '"')
                ->header('Cache-Control', 'private, no-store')->header('X-Content-Type-Options', 'nosniff')->setContent($bytes);
        }, self::BASE . '/pedidos/' . $id);
    }

    private function preview(?array $order, ?array $settings): array
    {
        if (($order['estado'] ?? null) !== 'entregado') {
            throw new DomainException('Solo puedes facturar pedidos entregados.');
        }
        if (empty($settings['habilitada']) || ($settings['ambiente'] ?? null) !== 'sandbox') {
            throw new DomainException('Habilita y configura la facturación Sandbox del restaurante.');
        }
        return InvoiceDraft::totals($order, $settings['impuestos']);
    }

    private function fingerprint(array $preview, array $settings): string
    {
        return hash('sha256', json_encode([$preview, $settings], JSON_THROW_ON_ERROR));
    }

    private function serviceForInvoice(int $tenant, array $invoice): FactusInvoiceService
    {
        [$account, $client] = ($this->connect)($tenant, $this->invoices->settings($tenant) ?? []);
        if ($invoice['ambiente'] !== 'sandbox' || !hash_equals($invoice['cuenta'], $account)) {
            throw new DomainException('La consulta requiere la cuenta Sandbox con la que se emitió la factura.');
        }
        return new FactusInvoiceService($client);
    }

    private function detailRedirect(int $id): Response
    {
        return $this->redirect(app_url(self::BASE . '/pedidos/' . $id));
    }

    private function view(string $name, array $data): Response
    {
        return $this->render('admin/facturacion/' . $name, $data + [
            'title' => 'Facturación electrónica', 'tenant' => TenantContext::getTenant(),
        ], 'admin')->header('Cache-Control', 'private, no-store');
    }

    private function handle(callable $action, ?string $redirect = null): Response
    {
        $tenant = Auth::tenantId();
        if (!$tenant || Auth::role() !== Auth::ROLE_ADMIN_NEGOCIO || !Auth::id()) {
            return (new Response())->setStatusCode(403);
        }
        try {
            return $action($tenant);
        } catch (DomainException $error) {
            $message = $error->getMessage();
        } catch (FactusException $error) {
            $message = $error->httpStatus === 429 ? 'Factus limitó las solicitudes. Intenta más tarde.'
                : 'Factus Sandbox no pudo completar la consulta. Revisa la conexión y la cuenta configurada.';
        } catch (Throwable $error) {
            Logger::error('Error local de facturación electrónica.', ['restaurante_id' => $tenant, 'tipo' => get_class($error)]);
            $message = 'No se pudo completar la operación. Verifica que la migración de facturación esté aplicada.';
        }
        if ($redirect === null) {
            return $this->view('error', compact('message'))->setStatusCode(503);
        }
        Session::flash('error', $message);
        return $this->redirect(app_url($redirect));
    }
}
