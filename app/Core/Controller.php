<?php

declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    protected function render(string $view, array $data = [], ?string $layout = 'main'): Response
    {
        $html = View::render($view, $data, $layout);
        $res = new Response();
        return $res->setContent($html);
    }

    protected function json(array $data, int $code = 200): Response
    {
        $res = new Response();
        return $res->json($data, $code);
    }

    protected function redirect(string $url, int $code = 302): Response
    {
        $res = new Response();
        return $res->redirect($url, $code);
    }

    protected function back(): Response
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? app_url('/');
        return $this->redirect($referer);
    }
}
