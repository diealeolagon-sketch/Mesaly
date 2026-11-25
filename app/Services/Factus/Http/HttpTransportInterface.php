<?php

declare(strict_types=1);

namespace App\Services\Factus\Http;

interface HttpTransportInterface
{
    public function send(HttpRequest $request): HttpResponse;
}
