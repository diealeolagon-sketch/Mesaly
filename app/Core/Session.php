<?php

declare(strict_types=1);

namespace App\Core;

class Session
{
    private static bool $started = false;

    public static function start(): void
    {
        if (self::$started || session_status() === PHP_SESSION_ACTIVE) {
            self::$started = true;
            self::checkTimeout();
            return;
        }

        $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
            || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');

        $lifetime = (int)($_ENV['SESSION_LIFETIME'] ?? 7200);

        session_set_cookie_params([
            'lifetime' => $lifetime,
            'path' => '/',
            'domain' => '',
            'secure' => $isHttps,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        session_name('mesaly_sess');
        session_start();
        self::$started = true;

        self::checkTimeout();
    }

    private static function checkTimeout(): void
    {
        $now = time();
        $lifetime = (int)($_ENV['SESSION_LIFETIME'] ?? 7200);

        if (isset($_SESSION['last_activity']) && ($now - $_SESSION['last_activity'] > $lifetime)) {
            self::destroy();
            self::start();
            return;
        }

        $_SESSION['last_activity'] = $now;
    }

    public static function regenerate(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }

    public static function set(string $key, mixed $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        self::start();
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void
    {
        self::start();
        unset($_SESSION[$key]);
    }

    public static function flash(string $key, mixed $value = null): mixed
    {
        self::start();
        if ($value !== null) {
            $_SESSION['_flash'][$key] = $value;
            return null;
        }

        $val = $_SESSION['_flash'][$key] ?? null;
        unset($_SESSION['_flash'][$key]);
        return $val;
    }

    public static function setTableForRestaurant(string $slug, int $mesaNumero): void
    {
        self::start();
        $tableLifetime = (int)($_ENV['TABLE_SESSION_LIFETIME'] ?? 14400); // 4 hours
        $_SESSION['mesas'][$slug] = [
            'numero' => $mesaNumero,
            'expires_at' => time() + $tableLifetime,
        ];
    }

    public static function getTableForRestaurant(string $slug): ?int
    {
        self::start();
        if (empty($_SESSION['mesas'][$slug])) {
            return null;
        }

        $data = $_SESSION['mesas'][$slug];
        if (time() > $data['expires_at']) {
            unset($_SESSION['mesas'][$slug]);
            return null;
        }

        return (int)$data['numero'];
    }

    public static function clearTableForRestaurant(string $slug): void
    {
        self::start();
        unset($_SESSION['mesas'][$slug]);
    }

    public static function destroy(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(
                    session_name(),
                    '',
                    time() - 42000,
                    $params["path"],
                    $params["domain"],
                    $params["secure"],
                    $params["httponly"]
                );
            }
            session_destroy();
            self::$started = false;
        }
    }
}
