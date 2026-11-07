<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Model;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Core\TenantContext;
use App\Core\Validator;
use App\Models\Auditoria;
use App\Models\Horario;
use App\Models\MetodoPagoNegocio;
use App\Models\Restaurante;
use App\Services\ImagenService;

class ConfigController extends Controller
{
    private Restaurante $restauranteModel;
    private MetodoPagoNegocio $metodoPagoModel;
    private Horario $horarioModel;
    private ImagenService $imagenService;

    public function __construct()
    {
        $this->restauranteModel = new Restaurante();
        $this->metodoPagoModel = new MetodoPagoNegocio();
        $this->horarioModel = new Horario();
        $this->imagenService = new ImagenService();
    }

    public function show(Request $request): Response
    {
        $tenantId = (int)TenantContext::getTenantId();
        $tenant = $this->restauranteModel->find($tenantId);
        $metodos = $this->metodoPagoModel->getMetodos();
        $horarios = $this->horarioModel->allByTenant();

        // Organizar horarios por día de la semana (0 a 6)
        $horariosPorDia = [];
        for ($i = 0; $i <= 6; $i++) {
            $horariosPorDia[$i] = [
                'abre' => '11:00',
                'cierra' => '22:00',
                'activo' => 0,
            ];
        }
        foreach ($horarios as $h) {
            $dia = (int)$h['dia_semana'];
            $horariosPorDia[$dia] = [
                'abre' => substr($h['abre'], 0, 5),
                'cierra' => substr($h['cierra'], 0, 5),
                'activo' => (int)$h['activo'],
            ];
        }

        return $this->render('admin/config/index', [
            'title' => 'Configuración del Negocio — ' . ($tenant['nombre'] ?? 'Mesaly'),
            'tenant' => $tenant,
            'metodos' => $metodos,
            'horarios' => $horariosPorDia,
        ], 'admin');
    }

    public function guardar(Request $request): Response
    {
        $tenantId = (int)TenantContext::getTenantId();
        $tenant = $this->restauranteModel->find($tenantId);

        $nombre = trim((string)$request->post('nombre'));
        $descripcion = trim((string)$request->post('descripcion'));
        $colorPrimario = trim((string)$request->post('color_primario'));
        $colorSecundario = trim((string)$request->post('color_secundario'));
        $colorFondo = trim((string)$request->post('color_fondo'));
        $colorTexto = trim((string)$request->post('color_texto'));
        $fuente = trim((string)$request->post('fuente', 'Inter'));
        $whatsapp = preg_replace('/\D/', '', (string)$request->post('whatsapp'));
        $recibiendoPedidos = (bool)$request->post('recibiendo_pedidos');
        $aceptaMesa = (bool)$request->post('acepta_mesa');
        $aceptaDomicilio = (bool)$request->post('acepta_domicilio');
        $aceptaRecoger = (bool)$request->post('acepta_recoger');
        $costoEnvio = max(0, (int)$request->post('costo_envio', 0));
        $pedidoMinimo = max(0, (int)$request->post('pedido_minimo', 0));
        $infoPagoTexto = trim((string)$request->post('info_pago_texto'));

        // Al menos una modalidad debe estar activa (Sección 6.3)
        if (!$aceptaMesa && !$aceptaDomicilio && !$aceptaRecoger) {
            Session::flash('error', 'Debes mantener activa al menos una modalidad de pedido (Mesa, Domicilio o Para recoger).');
            return $this->back();
        }

        $validator = Validator::make([
            'nombre' => $nombre,
            'color_primario' => $colorPrimario,
            'color_secundario' => $colorSecundario,
            'color_fondo' => $colorFondo,
            'color_texto' => $colorTexto,
            'whatsapp' => $whatsapp,
        ], [
            'nombre' => 'required|min:3|max:150',
            'color_primario' => 'required|hex_color',
            'color_secundario' => 'required|hex_color',
            'color_fondo' => 'required|hex_color',
            'color_texto' => 'required|hex_color',
            'whatsapp' => 'required|min:10|max:15',
        ]);

        if ($validator->fails()) {
            Session::flash('error', $validator->firstError());
            return $this->back();
        }

        // Subida de logo
        $logoPath = $tenant['logo_path'];
        $logoFile = $request->file('logo');
        if ($logoFile && !empty($logoFile['tmp_name'])) {
            try {
                $nuevoLogo = $this->imagenService->procesarYGuardar($logoFile, $tenantId, 500);
                $this->imagenService->eliminar($logoPath);
                $logoPath = $nuevoLogo;
            } catch (\Throwable $e) {
                Session::flash('error', "Error al procesar el logo: " . $e->getMessage());
                return $this->back();
            }
        }

        // Subida de banner
        $bannerPath = $tenant['banner_path'];
        $bannerFile = $request->file('banner');
        if ($bannerFile && !empty($bannerFile['tmp_name'])) {
            try {
                $nuevoBanner = $this->imagenService->procesarYGuardar($bannerFile, $tenantId, 1400);
                $this->imagenService->eliminar($bannerPath);
                $bannerPath = $nuevoBanner;
            } catch (\Throwable $e) {
                Session::flash('error', "Error al procesar el banner: " . $e->getMessage());
                return $this->back();
            }
        }

        $dataUpdate = [
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'color_primario' => $colorPrimario,
            'color_secundario' => $colorSecundario,
            'color_fondo' => $colorFondo,
            'color_texto' => $colorTexto,
            'fuente' => in_array($fuente, ['Inter', 'Poppins', 'Roboto', 'Outfit', 'Playfair', 'System'], true) ? $fuente : 'Inter',
            'whatsapp' => $whatsapp,
            'recibiendo_pedidos' => $recibiendoPedidos ? 1 : 0,
            'acepta_mesa' => $aceptaMesa ? 1 : 0,
            'acepta_domicilio' => $aceptaDomicilio ? 1 : 0,
            'acepta_recoger' => $aceptaRecoger ? 1 : 0,
            'costo_envio' => $costoEnvio,
            'pedido_minimo' => $pedidoMinimo,
            'info_pago_texto' => $infoPagoTexto,
            'logo_path' => $logoPath,
            'banner_path' => $bannerPath,
        ];

        Model::beginTransaction();
        try {
            $this->restauranteModel->update($tenantId, $dataUpdate);

            // Sincronizar métodos de pago informativos
            $metodosSeleccionados = (array)$request->post('metodos_pago', []);
            $this->metodoPagoModel->syncMetodos($metodosSeleccionados);

            // Sincronizar horarios
            $diasPost = (array)$request->post('dias', []);
            $horariosData = [];
            for ($d = 0; $d <= 6; $d++) {
                $infoDia = $diasPost[$d] ?? null;
                if ($infoDia) {
                    $horariosData[] = [
                        'dia_semana' => $d,
                        'abre' => !empty($infoDia['abre']) ? $infoDia['abre'] . ':00' : '11:00:00',
                        'cierra' => !empty($infoDia['cierra']) ? $infoDia['cierra'] . ':00' : '22:00:00',
                        'activo' => !empty($infoDia['activo']) ? 1 : 0,
                        'tramo' => 1,
                    ];
                }
            }
            $this->horarioModel->syncHorarios($horariosData);

            Auditoria::registrar('actualizar_configuracion', 'restaurantes', $tenantId, $tenant, $dataUpdate, $tenantId);
            Model::commit();

            // Actualizar contexto en memoria
            TenantContext::setTenant(array_merge($tenant, $dataUpdate));

            Session::flash('success', 'Configuración de marca y operación actualizada exitosamente.');
            return $this->redirect(app_url('/admin/configuracion'));

        } catch (\Throwable $e) {
            Model::rollBack();
            Session::flash('error', 'Error al guardar configuración: ' . $e->getMessage());
            return $this->back();
        }
    }
}
