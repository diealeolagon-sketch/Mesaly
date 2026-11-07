<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Model;
use App\Core\TenantContext;
use App\Models\Categoria;
use App\Models\GrupoModificador;
use App\Models\OpcionModificador;
use App\Models\Producto;
use PDO;
use PHPUnit\Framework\TestCase;

class CatalogAndTenantTest extends TestCase
{
    private static PDO $pdo;

    public static function setUpBeforeClass(): void
    {
        $dotenv = \Dotenv\Dotenv::createImmutable(dirname(__DIR__, 2));
        $dotenv->safeLoad();

        self::$pdo = Model::getPdo();
    }

    protected function tearDown(): void
    {
        TenantContext::clear();
    }

    public function testCategoryScopedByTenant(): void
    {
        $catModel = new Categoria();

        // 1. Tenant 1 crea categoría
        TenantContext::setTenantId(1);
        $catId1 = $catModel->create([
            'nombre' => 'Entradas Test T1',
            'orden' => 1,
            'activa' => 1,
        ]);

        $this->assertGreaterThan(0, $catId1);

        // 2. Tenant 2 no puede ver la categoría de Tenant 1
        TenantContext::setTenantId(2);
        $this->assertNull($catModel->find($catId1));

        $catsT2 = $catModel->all();
        $this->assertNotContains($catId1, array_column($catsT2, 'id'));

        // 3. Cleanup
        TenantContext::setTenantId(1);
        $catModel->delete($catId1);
    }

    public function testProductScopedByTenantAndSoftDelete(): void
    {
        $catModel = new Categoria();
        $prodModel = new Producto();

        TenantContext::setTenantId(1);
        $catId = $catModel->create([
            'nombre' => 'Cat Prod Test',
            'orden' => 1,
            'activa' => 1,
        ]);

        $prodId = $prodModel->create([
            'categoria_id' => $catId,
            'nombre' => 'Plato Prueba IDOR',
            'descripcion' => 'Descripción deliciosa',
            'precio' => 25000,
            'disponible' => 1,
        ]);

        $this->assertGreaterThan(0, $prodId);

        // Tenant 2 no puede acceder a este producto
        TenantContext::setTenantId(2);
        $this->assertNull($prodModel->find($prodId));
        $this->assertFalse($prodModel->update($prodId, ['nombre' => 'Hackeado']));

        // Tenant 1 borra el producto (soft delete)
        TenantContext::setTenantId(1);
        $prodModel->delete($prodId);

        // Ya no aparece en all() de Tenant 1
        $this->assertNull($prodModel->find($prodId));

        // Pero en la base de datos física el registro sigue existiendo con deleted_at (integridad histórica)
        $stmt = self::$pdo->prepare("SELECT deleted_at FROM `productos` WHERE `id` = :id");
        $stmt->execute(['id' => $prodId]);
        $deletedAt = $stmt->fetchColumn();
        $this->assertNotNull($deletedAt);

        // Cleanup
        $catModel->delete($catId);
    }

    public function testModifierGroupAndOptionsAntiIdor(): void
    {
        $grupoModel = new GrupoModificador();
        $opcionModel = new OpcionModificador();

        TenantContext::setTenantId(1);
        $grupoId = $grupoModel->create([
            'nombre' => 'Salsas Test',
            'tipo' => 'multiple',
            'obligatorio' => 0,
            'min_selecciones' => 0,
            'max_selecciones' => 2,
        ]);

        $opcionId = $opcionModel->create([
            'grupo_id' => $grupoId,
            'nombre' => 'Salsa BBQ Ahumada',
            'precio_extra' => 2000,
            'orden' => 1,
            'activa' => 1,
        ]);

        $this->assertGreaterThan(0, $opcionId);

        // Tenant 2 no puede buscar ni eliminar la opción de Tenant 1
        $this->assertNull($opcionModel->findScopedByTenant($opcionId, 2));
        $this->assertFalse($opcionModel->deleteScoped($opcionId, 2));

        // Tenant 1 sí puede
        $this->assertNotNull($opcionModel->findScopedByTenant($opcionId, 1));
        $this->assertTrue($opcionModel->deleteScoped($opcionId, 1));

        // Cleanup
        $grupoModel->delete($grupoId);
    }
}
