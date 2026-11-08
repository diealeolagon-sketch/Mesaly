<?php

declare(strict_types=1);

namespace App\Core;

class Response
{
    private int $statusCode = 200;
    private array $headers = [];
    private string $content = '';

    public function setStatusCode(int $code): self
    {
        $this->statusCode = $code;
        return $this;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function header(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function json(array $data, int $code = 200): self
    {
        $this->setStatusCode($code);
        $this->header('Content-Type', 'application/json; charset=utf-8');
        $this->setContent(json_encode($data, JSON_UNESCAPED_UNICODE));
        return $this;
    }

    public function redirect(string $url, int $code = 302): self
    {
        $this->setStatusCode($code);
        $this->header('Location', $url);
        return $this;
    }

    public function send(): void
    {
        http_response_code($this->statusCode);
        foreach ($this->headers as $name => $value) {
            header("{$name}: {$value}");
        }
        echo $this->content;
        exit;
    }
}
