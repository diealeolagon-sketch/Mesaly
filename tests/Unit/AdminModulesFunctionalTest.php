<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Controllers\Admin\CatalogoController;
use App\Controllers\Admin\ConfigController;
use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\ModificadorController;
use App\Core\Auth;
use App\Core\Model;
use App\Core\Request;
use App\Core\Session;
use App\Core\TenantContext;
use App\Models\Categoria;
use App\Models\GrupoModificador;
use App\Models\Mesa;
use App\Models\OpcionModificador;
use App\Models\Producto;
use App\Models\Restaurante;
use App\Models\Usuario;
use PDO;
use PHPUnit\Framework\TestCase;

class AdminModulesFunctionalTest extends TestCase
{
    private static PDO $pdo;
    private int $tenantId;
    private array $tenant;
    private CatalogoController $catalogoController;
    private ModificadorController $modificadorController;
    private ConfigController $configController;
    private DashboardController $dashboardController;

    public static function setUpBeforeClass(): void
    {
        $dotenv = \Dotenv\Dotenv::createImmutable(dirname(__DIR__, 2));
        $dotenv->safeLoad();

        self::$pdo = Model::getPdo();
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->tenant = (new Restaurante())->findBySlug('la-parrilla-paisa');
        $this->assertNotNull($this->tenant);
        $this->tenantId = (int)$this->tenant['id'];

        TenantContext::setTenant($this->tenant);

        // Login as admin_negocio
        $adminUser = (new Usuario())->findByEmail('restaurante@mesaly.co');
        $this->assertNotNull($adminUser);
        Auth::login($adminUser);

        $this->catalogoController = new CatalogoController();
        $this->modificadorController = new ModificadorController();
        $this->configController = new ConfigController();
        $this->dashboardController = new DashboardController();
    }

    protected function tearDown(): void
    {
        TenantContext::clear();
        Auth::logout();
        parent::tearDown();
    }

    public function testGestionCategoriasCrud(): void
    {
        $catModel = new Categoria();

        // 1. Crear categoría vía controlador
        $requestCrear = new Request('POST', '/admin/catalogo/categoria', [
            'nombre' => 'Parrilladas Especiales Test',
            'orden' => 10,
            'activa' => '1',
        ]);
        $resCrear = $this->catalogoController->guardarCategoria($requestCrear);
        $this->assertTrue($resCrear->isRedirection());

        $creada = self::$pdo->query("SELECT * FROM `categorias` WHERE `restaurante_id` = {$this->tenantId} AND `nombre` = 'Parrilladas Especiales Test'")->fetch(PDO::FETCH_ASSOC);
        $this->assertNotNull($creada);
        $catId = (int)$creada['id'];

        // 2. Editar categoría
        $requestEditar = new Request('POST', '/admin/catalogo/categoria', [
            'id' => $catId,
            'nombre' => 'Parrilladas Premium Editada',
            'orden' => 15,
            'activa' => '1',
        ]);
        $resEditar = $this->catalogoController->guardarCategoria($requestEditar);
        $this->assertTrue($resEditar->isRedirection());

        $editada = $catModel->find($catId);
        $this->assertSame('Parrilladas Premium Editada', $editada['nombre']);
        $this->assertSame(15, (int)$editada['orden']);

        // 3. Eliminar categoría (Soft delete)
        $resEliminar = $this->catalogoController->eliminarCategoria(new Request('POST', "/admin/catalogo/categoria/{$catId}/eliminar"), ['id' => (string)$catId]);
        $this->assertTrue($resEliminar->isRedirection());
        $this->assertNull($catModel->find($catId));

        // Cleanup físico
        self::$pdo->prepare("DELETE FROM `categorias` WHERE `id` = :id")->execute(['id' => $catId]);
    }

    public function testGestionProductosCrudYModificadores(): void
    {
        $catModel = new Categoria();
        $prodModel = new Producto();

        // Crear categoría base
        $catId = $catModel->create([
            'nombre' => 'Cortes Finos Test',
            'orden' => 1,
            'activa' => 1,
        ]);

        // 1. Crear producto con validación
        $requestCrear = new Request('POST', '/admin/catalogo/producto', [
            'categoria_id' => $catId,
            'nombre' => 'Bife de Chorizo Test',
            'descripcion' => 'Corte argentino de 400g madurado',
            'precio' => '48000',
            'disponible' => '1',
            'destacado' => '1',
            'orden' => 1,
        ]);
        $resCrear = $this->catalogoController->guardarProducto($requestCrear);
        $this->assertTrue($resCrear->isRedirection());

        $creado = self::$pdo->query("SELECT * FROM `productos` WHERE `restaurante_id` = {$this->tenantId} AND `nombre` = 'Bife de Chorizo Test'")->fetch(PDO::FETCH_ASSOC);
        $this->assertNotNull($creado);
        $prodId = (int)$creado['id'];
        $this->assertSame(48000, (int)$creado['precio']);
        $this->assertSame(1, (int)$creado['destacado']);

        // 2. 1-Click Toggle Disponibilidad (Agotados)
        $this->catalogoController->toggleAgotado(new Request('POST', "/admin/catalogo/producto/{$prodId}/toggle-agotado"), ['id' => (string)$prodId]);
        $prodAgotado = $prodModel->find($prodId);
        $this->assertSame(0, (int)$prodAgotado['disponible']);

        $this->catalogoController->toggleAgotado(new Request('POST', "/admin/catalogo/producto/{$prodId}/toggle-agotado"), ['id' => (string)$prodId]);
        $prodDisponible = $prodModel->find($prodId);
        $this->assertSame(1, (int)$prodDisponible['disponible']);

        // Cleanup
        $prodModel->delete($prodId);
        $catModel->delete($catId);
        self::$pdo->prepare("DELETE FROM `productos` WHERE `id` = :id")->execute(['id' => $prodId]);
        self::$pdo->prepare("DELETE FROM `categorias` WHERE `id` = :id")->execute(['id' => $catId]);
    }

    public function testGestionGruposModificadoresYOpciones(): void
    {
        $grupoModel = new GrupoModificador();
        $opcionModel = new OpcionModificador();

        // 1. Crear grupo de modificadores
        $requestGrupo = new Request('POST', '/admin/modificadores/grupo', [
            'nombre' => 'Tipo de Pan Hamburguesa',
            'tipo' => 'unica',
            'obligatorio' => '1',
        ]);
        $resGrupo = $this->modificadorController->guardarGrupo($requestGrupo);
        $this->assertTrue($resGrupo->isRedirection());

        $grupo = self::$pdo->query("SELECT * FROM `grupos_modificadores` WHERE `restaurante_id` = {$this->tenantId} AND `nombre` = 'Tipo de Pan Hamburguesa'")->fetch(PDO::FETCH_ASSOC);
        $this->assertNotNull($grupo);
        $grupoId = (int)$grupo['id'];
        $this->assertSame('unica', $grupo['tipo']);
        $this->assertSame(1, (int)$grupo['obligatorio']);

        // 2. Crear opciones dentro del grupo
        $reqOpcion1 = new Request('POST', '/admin/modificadores/opcion', [
            'grupo_id' => $grupoId,
            'nombre' => 'Pan Brioche Artesanal',
            'precio_extra' => '0',
            'orden' => 1,
        ]);
        $resOpc1 = $this->modificadorController->guardarOpcion($reqOpcion1);
        $this->assertTrue($resOpc1->isRedirection());

        $reqOpcion2 = new Request('POST', '/admin/modificadores/opcion', [
            'grupo_id' => $grupoId,
            'nombre' => 'Pan Sin Gluten Especial',
            'precio_extra' => '3500',
            'orden' => 2,
        ]);
        $resOpc2 = $this->modificadorController->guardarOpcion($reqOpcion2);
        $this->assertTrue($resOpc2->isRedirection());

        // Verificar opciones en la base de datos
        $opciones = self::$pdo->query("SELECT * FROM `opciones_modificadores` WHERE `grupo_id` = {$grupoId} ORDER BY orden ASC")->fetchAll(PDO::FETCH_ASSOC);
        $this->assertCount(2, $opciones);
        $this->assertSame(3500, (int)$opciones[1]['precio_extra']);

        // Cleanup
        self::$pdo->prepare("DELETE FROM `opciones_modificadores` WHERE `grupo_id` = :id")->execute(['id' => $grupoId]);
        self::$pdo->prepare("DELETE FROM `grupos_modificadores` WHERE `id` = :id")->execute(['id' => $grupoId]);
    }

    public function testConfiguracionNegocioValidacionModalidades(): void
    {
        // Si intenta apagar todas las modalidades de entrega (mesa, domicilio, recoger), debe ser rechazado
        $requestInvalido = new Request('POST', '/admin/configuracion', [
            'nombre' => 'La Parrilla Paisa',
            'color_primario' => '#E85D04',
            'color_secundario' => '#1F2937',
            'color_fondo' => '#F8FAFC',
            'color_texto' => '#0F172A',
            'acepta_mesa' => '0',
            'acepta_domicilio' => '0',
            'acepta_recoger' => '0',
        ]);

        $response = $this->configController->guardar($requestInvalido);
        $this->assertTrue($response->isRedirection());
        $flashError = Session::flash('error');
        $this->assertNotNull($flashError);
        $this->assertStringContainsString('al menos una modalidad', $flashError);
    }

    public function testToggleRecibiendoPedidos(): void
    {
        $estadoOriginal = (int)$this->tenant['recibiendo_pedidos'];

        // 1. Alternar
        $resToggle = $this->dashboardController->toggleRecibiendoPedidos(new Request('POST', '/admin/toggle-recibiendo-pedidos'));
        $this->assertTrue($resToggle->isRedirection());

        $actualizado = (new Restaurante())->find($this->tenantId);
        $this->assertSame($estadoOriginal === 1 ? 0 : 1, (int)$actualizado['recibiendo_pedidos']);

        // 2. Restaurar al estado original
        $this->dashboardController->toggleRecibiendoPedidos(new Request('POST', '/admin/toggle-recibiendo-pedidos'));
        $restaurado = (new Restaurante())->find($this->tenantId);
        $this->assertSame($estadoOriginal, (int)$restaurado['recibiendo_pedidos']);
    }
}
