<?php

declare(strict_types=1);

namespace App\Core;

class Csrf
{
    private const SESSION_KEY = '_csrf_token';

    public static function getToken(): string
    {
        Session::start();
        $token = Session::get(self::SESSION_KEY);
        if (!$token || !is_string($token)) {
            $token = bin2hex(random_bytes(32));
            Session::set(self::SESSION_KEY, $token);
        }
        return $token;
    }

    public static function validate(?string $token): bool
    {
        Session::start();
        $stored = Session::get(self::SESSION_KEY);
        if (!is_string($stored) || !is_string($token) || empty($token)) {
            return false;
        }
        return hash_equals($stored, $token);
    }

    public static function regenerate(): string
    {
        Session::start();
        $token = bin2hex(random_bytes(32));
        Session::set(self::SESSION_KEY, $token);
        return $token;
    }
}
