<?php

declare(strict_types=1);

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, array|callable $handler, array $middlewares = []): void
    {
        $this->addRoute('GET', $path, $handler, $middlewares);
    }

    public function post(string $path, array|callable $handler, array $middlewares = []): void
    {
        $this->addRoute('POST', $path, $handler, $middlewares);
    }

    public function put(string $path, array|callable $handler, array $middlewares = []): void
    {
        $this->addRoute('PUT', $path, $handler, $middlewares);
    }

    public function delete(string $path, array|callable $handler, array $middlewares = []): void
    {
        $this->addRoute('DELETE', $path, $handler, $middlewares);
    }

    private function addRoute(string $method, string $path, array|callable $handler, array $middlewares = []): void
    {
        $path = '/' . trim($path, '/');
        if ($path === '//') {
            $path = '/';
        }

        // Convert {param} to regex named captures
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $path);
        $regex = '#^' . $pattern . '$#';

        // Clasificar para dar prioridad a rutas estáticas sobre rutas con comodín
        $isDynamic = str_contains($path, '{');

        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'regex' => $regex,
            'handler' => $handler,
            'middlewares' => $middlewares,
            'is_dynamic' => $isDynamic,
        ];
    }

    public function dispatch(Request $request): Response
    {
        $method = $request->getMethod();
        $uri = $request->getUri();

        // Ordenar rutas: estáticas primero, dinámicas después
        $sortedRoutes = $this->routes;
        usort($sortedRoutes, function ($a, $b) {
            if ($a['is_dynamic'] === $b['is_dynamic']) {
                return strlen($b['path']) <=> strlen($a['path']);
            }
            return $a['is_dynamic'] ? 1 : -1;
        });

        foreach ($sortedRoutes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            if (preg_match($route['regex'], $uri, $matches)) {
                $params = [];
                foreach ($matches as $key => $val) {
                    if (is_string($key)) {
                        $params[$key] = $val;
                    }
                }

                // Ejecutar middlewares
                foreach ($route['middlewares'] as $mw) {
                    $middlewareInstance = is_string($mw) ? new $mw() : $mw;
                    $response = $middlewareInstance->handle($request, $params);
                    if ($response instanceof Response) {
                        return $response;
                    }
                }

                // Ejecutar handler
                $handler = $route['handler'];
                if (is_callable($handler)) {
                    $result = call_user_func($handler, $request, $params);
                } elseif (is_array($handler) && count($handler) === 2) {
                    [$class, $action] = $handler;
                    $controller = new $class();
                    $result = call_user_func([$controller, $action], $request, $params);
                } else {
                    throw new \RuntimeException("Handler inválido en ruta.");
                }

                if ($result instanceof Response) {
                    return $result;
                }

                $response = new Response();
                return $response->setContent((string)$result);
            }
        }

        // 404 Not Found
        $response = new Response();
        $response->setStatusCode(404);
        try {
            $html = View::render('errors/404', ['title' => 'Página no encontrada'], 'error');
            return $response->setContent($html);
        } catch (\Throwable $e) {
            return $response->setContent('<h1>404 — Página no encontrada</h1>');
        }
    }
}
