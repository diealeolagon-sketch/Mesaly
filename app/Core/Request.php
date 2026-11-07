<?php

declare(strict_types=1);

namespace App\Core;

class Request
{
    private string $method;
    private string $uri;
    private array $get;
    private array $post;
    private array $json;
    private array $files;
    private array $headers;

    public function __construct()
    {
        $this->method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        
        // Simulación de PUT/PATCH/DELETE vía campo _method
        if ($this->method === 'POST' && isset($_POST['_method'])) {
            $override = strtoupper((string)$_POST['_method']);
            if (in_array($override, ['PUT', 'PATCH', 'DELETE'], true)) {
                $this->method = $override;
            }
        }

        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';

        // Normalizar URI eliminando el prefijo si corre en subcarpeta (ej. /Mesaly/public)
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $baseDir = dirname($scriptName);
        if ($baseDir !== '/' && $baseDir !== '\\' && str_starts_with($uri, $baseDir)) {
            $uri = substr($uri, strlen($baseDir));
        }

        $this->uri = '/' . trim($uri, '/');
        $this->get = $_GET;
        $this->post = $_POST;
        $this->files = $_FILES;

        // Headers
        $this->headers = [];
        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $name = str_replace('_', '-', strtolower(substr($key, 5)));
                $this->headers[$name] = $value;
            }
        }

        // Parse JSON si aplica
        $this->json = [];
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (str_contains(strtolower($contentType), 'application/json')) {
            $raw = file_get_contents('php://input');
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) {
                $this->json = $decoded;
            }
        }
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function query(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->get;
        }
        return $this->get[$key] ?? $default;
    }

    public function post(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->post;
        }
        return $this->post[$key] ?? $default;
    }

    public function json(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->json;
        }
        return $this->json[$key] ?? $default;
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $this->json[$key] ?? $this->post[$key] ?? $this->get[$key] ?? $default;
    }

    public function all(): array
    {
        return array_merge($this->get, $this->post, $this->json);
    }

    public function file(string $key): ?array
    {
        return $this->files[$key] ?? null;
    }

    public function header(string $key, ?string $default = null): ?string
    {
        $normalized = strtolower(str_replace('_', '-', $key));
        return $this->headers[$normalized] ?? $default;
    }

    public function ip(): string
    {
        if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            return $_SERVER['HTTP_CF_CONNECTING_IP'];
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $list = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($list[0]);
        }
        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }

    public function isAjax(): bool
    {
        return $this->header('x-requested-with') === 'XMLHttpRequest'
            || str_contains(strtolower($this->header('accept', '') ?? ''), 'application/json');
    }
}
