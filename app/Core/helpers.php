<?php

declare(strict_types=1);

if (!function_exists('e')) {
    /**
     * Escape HTML output securely.
     */
    function e(?string $value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('csrf_token')) {
    /**
     * Get or generate current CSRF token.
     */
    function csrf_token(): string
    {
        return \App\Core\Csrf::getToken();
    }
}

if (!function_exists('csrf_field')) {
    /**
     * Render hidden CSRF token input field.
     */
    function csrf_field(): string
    {
        return '<input type="hidden" name="_csrf_token" value="' . e(csrf_token()) . '">';
    }
}

if (!function_exists('app_url')) {
    /**
     * Build absolute or relative application URL.
     */
    function app_url(string $path = ''): string
    {
        $base = rtrim($_ENV['APP_URL'] ?? 'http://localhost/Mesaly/public', '/');
        $path = ltrim($path, '/');
        return $path !== '' ? $base . '/' . $path : $base;
    }
}

if (!function_exists('format_cop')) {
    /**
     * Format Colombian Peso (COP) without decimals.
     * Example: $ 12.500
     */
    function format_cop(int|float $amount): string
    {
        return '$ ' . number_format((float)$amount, 0, ',', '.');
    }
}
