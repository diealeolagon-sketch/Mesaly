<?php

declare(strict_types=1);

namespace App\Core;

use Dotenv\Dotenv;
use Throwable;

class App
{
    private Router $router;
    private Request $request;

    public function __construct()
    {
        // 1. Cargar variables de entorno
        $dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
        $dotenv->safeLoad();

        // 2. Zona horaria obligatoria
        date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'America/Bogota');

        // 3. Manejo de errores global
        $this->registerErrorHandlers();

        // 4. Iniciar sesión segura
        Session::start();

        // 5. Inicializar request y router
        $this->request = new Request();
        $this->router = new Router();
    }

    private function registerErrorHandlers(): void
    {
        $debug = filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN);

        error_reporting(E_ALL);
        ini_set('display_errors', $debug ? '1' : '0');

        set_error_handler(function (int $errno, string $errstr, string $errfile, int $errline) {
            if (!(error_reporting() & $errno)) {
                return false;
            }
            throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
        });

        set_exception_handler(function (Throwable $e) use ($debug) {
            Logger::error("Excepción no capturada: " . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            http_response_code(500);

            if ($debug) {
                echo "<h1>Error 500 — Excepción en modo depuración</h1>";
                echo "<p><strong>Mensaje:</strong> " . e($e->getMessage()) . "</p>";
                echo "<p><strong>Archivo:</strong> " . e($e->getFile()) . " (línea " . (int)$e->getLine() . ")</p>";
                echo "<pre>" . e($e->getTraceAsString()) . "</pre>";
            } else {
                try {
                    echo View::render('errors/500', ['title' => 'Error interno del servidor'], 'error');
                } catch (Throwable) {
                    echo "<h1>500 — Error interno del servidor</h1><p>Por favor contacte al administrador.</p>";
                }
            }
            exit;
        });
    }

    public function getRouter(): Router
    {
        return $this->router;
    }

    public function run(): void
    {
        // Cabeceras globales de seguridad obligatorias (Sección 9.8 y Auditoría 01)
        $nonce = View::getCspNonce();
        header("Content-Security-Policy: default-src 'self'; script-src 'self' 'nonce-{$nonce}'; style-src 'self' 'nonce-{$nonce}'; img-src 'self' data:; font-src 'self'; connect-src 'self'; object-src 'none'; base-uri 'self'; form-action 'self'; frame-ancestors 'none';");
        header("X-Content-Type-Options: nosniff");
        header("X-Frame-Options: DENY");
        header("Referrer-Policy: strict-origin-when-cross-origin");
        header("Permissions-Policy: camera=(), microphone=(), geolocation=()");

        // Cargar rutas
        $router = $this->router;
        require dirname(__DIR__, 2) . '/config/routes.php';

        // Despachar
        $response = $this->router->dispatch($this->request);
        $response->send();
    }
}
