<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Model;
use App\Core\Session;
use PHPUnit\Framework\TestCase;

class AuthAndSecurityTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        $dotenv = \Dotenv\Dotenv::createImmutable(dirname(__DIR__, 2));
        $dotenv->safeLoad();
    }

    public function testPasswordHashingStrong(): void
    {
        $password = 'ClaveSegura123*';
        $hash = Auth::hashPassword($password);

        $this->assertNotEmpty($hash);
        $this->assertTrue(password_verify($password, $hash));
        $this->assertFalse(password_verify('ClaveEquivocada', $hash));
    }

    public function testCsrfValidation(): void
    {
        $token = Csrf::getToken();
        $this->assertNotEmpty($token);
        $this->assertTrue(Csrf::validate($token));
        $this->assertFalse(Csrf::validate('token_invalido'));
        $this->assertFalse(Csrf::validate(''));
    }

    public function testSessionTableExpiration(): void
    {
        $slug = 'restaurante-test';

        // Fijar mesa
        Session::setTableForRestaurant($slug, 7);
        $this->assertEquals(7, Session::getTableForRestaurant($slug));

        // Simular expiración de sesión de mesa (3-4 horas)
        $_SESSION['mesas'][$slug]['expires_at'] = time() - 10;

        $this->assertNull(Session::getTableForRestaurant($slug), 'La mesa en sesión debió expirar después de su tiempo límite.');
    }

    public function testRateLimitingOnLogin(): void
    {
        $testIp = '192.168.1.99';
        $testEmail = 'rata_test@ejemplo.com';

        // Limpiar intentos previos
        $pdo = Model::getPdo();
        $pdo->prepare("DELETE FROM `intentos_login` WHERE `ip` = :ip")->execute(['ip' => $testIp]);

        $this->assertFalse(Auth::isRateLimited($testIp, $testEmail));

        // Registrar 5 intentos fallidos
        for ($i = 0; $i < 5; $i++) {
            Auth::recordLoginAttempt($testIp, $testEmail, false);
        }

        // El sexto intento debe ser bloqueado por rate limit
        $this->assertTrue(Auth::isRateLimited($testIp, $testEmail));

        // Cleanup
        $pdo->prepare("DELETE FROM `intentos_login` WHERE `ip` = :ip")->execute(['ip' => $testIp]);
    }
}
