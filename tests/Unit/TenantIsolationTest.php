<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Model;
use App\Core\TenantContext;
use App\Core\TenantModel;
use PDO;
use PHPUnit\Framework\TestCase;

class DummyTenantItemModel extends TenantModel
{
    protected string $table = 'categorias';
    protected bool $usesSoftDeletes = true;
}

class TenantIsolationTest extends TestCase
{
    private static PDO $pdo;

    public static function setUpBeforeClass(): void
    {
        $dotenv = \Dotenv\Dotenv::createImmutable(dirname(__DIR__, 2));
        $dotenv->safeLoad();

        self::$pdo = Model::getPdo();

        // Limpiar restaurantes y categorías de prueba
        self::$pdo->exec("DELETE FROM `categorias` WHERE `nombre` LIKE 'TEST_TENANT_%'");
        self::$pdo->exec("DELETE FROM `restaurantes` WHERE `slug` IN ('tenant-uno', 'tenant-dos')");

        // Crear dos restaurantes de prueba
        $stmt = self::$pdo->prepare("INSERT INTO `restaurantes` (`id`, `nombre`, `slug`, `whatsapp`, `siguiente_consecutivo`) 
            VALUES (101, 'Restaurante Uno', 'tenant-uno', '573001111111', 1),
                   (102, 'Restaurante Dos', 'tenant-dos', '573002222222', 1)
            ON DUPLICATE KEY UPDATE `nombre` = VALUES(`nombre`)");
        $stmt->execute();
    }

    public static function tearDownAfterClass(): void
    {
        self::$pdo->exec("DELETE FROM `categorias` WHERE `nombre` LIKE 'TEST_TENANT_%'");
        self::$pdo->exec("DELETE FROM `restaurantes` WHERE `slug` IN ('tenant-uno', 'tenant-dos')");
        TenantContext::clear();
    }

    protected function tearDown(): void
    {
        TenantContext::clear();
    }

    public function testTenantCannotSeeAnotherTenantsRecords(): void
    {
        $model = new DummyTenantItemModel();

        // 1. Actuar como Tenant 101 y crear categoría
        TenantContext::setTenantId(101);
        $catId101 = $model->create([
            'nombre' => 'TEST_TENANT_CAT_1',
            'orden' => 1,
            'activa' => 1,
        ]);

        $this->assertGreaterThan(0, $catId101);

        // Verificar que Tenant 101 sí la ve
        $item = $model->find($catId101);
        $this->assertNotNull($item);
        $this->assertEquals('TEST_TENANT_CAT_1', $item['nombre']);
        $this->assertEquals(101, (int)$item['restaurante_id']);

        // 2. Cambiar contexto a Tenant 102 (Aislamiento / Anti-IDOR)
        TenantContext::setTenantId(102);

        // Tenant 102 intenta buscar por ID el recurso del Tenant 101
        $idorAttempt = $model->find($catId101);
        $this->assertNull($idorAttempt, 'Vulnerabilidad IDOR detectada: Tenant 102 pudo leer datos del Tenant 101');

        // Tenant 102 lista todas las categorías: no debe incluir la del Tenant 101
        $all102 = $model->all();
        $ids = array_column($all102, 'id');
        $this->assertNotContains($catId101, $ids, 'Fuga multitenant: el listado contiene registros de otro restaurante');

        // 3. Tenant 102 intenta modificar el recurso del Tenant 101
        $updateResult = $model->update($catId101, ['nombre' => 'HACKED_NAME']);
        
        // Verificar en BD que el nombre original sigue intacto
        TenantContext::setTenantId(101);
        $itemOriginal = $model->find($catId101);
        $this->assertEquals('TEST_TENANT_CAT_1', $itemOriginal['nombre'], 'Vulnerabilidad IDOR: Tenant 102 modificó datos de Tenant 101');

        // 4. Tenant 102 intenta borrar el recurso de Tenant 101
        TenantContext::setTenantId(102);
        $model->delete($catId101);

        // Verificar que sigue existiendo para Tenant 101
        TenantContext::setTenantId(101);
        $itemAunExiste = $model->find($catId101);
        $this->assertNotNull($itemAunExiste, 'Vulnerabilidad IDOR: Tenant 102 eliminó datos de Tenant 101');
    }

    public function testTenantContextPreventsUnauthenticatedModelOperations(): void
    {
        TenantContext::clear();
        $model = new DummyTenantItemModel();

        $this->expectException(\RuntimeException::class);
        $model->all();
    }
}
