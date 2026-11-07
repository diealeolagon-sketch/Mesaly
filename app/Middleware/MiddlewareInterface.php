<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;

interface MiddlewareInterface
{
    /**
     * Procesa la petición antes de llegar al controlador.
     * Retorna Response si interrumpe el flujo, o null si continúa.
     */
    public function handle(Request $request, array $params = []): ?Response;
}
