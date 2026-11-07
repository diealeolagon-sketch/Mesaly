<?php

declare(strict_types=1);

namespace App\Core;

class View
{
    private static ?string $cspNonce = null;

    public static function getCspNonce(): string
    {
        if (self::$cspNonce === null) {
            self::$cspNonce = base64_encode(random_bytes(16));
        }
        return self::$cspNonce;
    }

    public static function render(string $template, array $data = [], ?string $layout = 'main'): string
    {
        $viewsPath = dirname(__DIR__) . '/Views/';
        $templatePath = $viewsPath . ltrim($template, '/') . '.php';

        if (!file_exists($templatePath)) {
            throw new \RuntimeException("La vista no existe: {$templatePath}");
        }

        $data['cspNonce'] = self::getCspNonce();
        $data['user'] = Session::get('user');
        $data['flash_success'] = Session::flash('success');
        $data['flash_error'] = Session::flash('error');

        extract($data);

        ob_start();
        require $templatePath;
        $content = ob_get_clean();

        if ($layout === null) {
            return $content;
        }

        $layoutPath = $viewsPath . 'layouts/' . $layout . '.php';
        if (!file_exists($layoutPath)) {
            throw new \RuntimeException("El layout no existe: {$layoutPath}");
        }

        ob_start();
        require $layoutPath;
        return ob_get_clean();
    }
}
