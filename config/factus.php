<?php

declare(strict_types=1);

return [
    'environment' => $_ENV['FACTUS_ENV'] ?? 'sandbox',
    'base_url' => $_ENV['FACTUS_BASE_URL'] ?? 'https://api-sandbox.factus.com.co',
    'client_id' => $_ENV['FACTUS_CLIENT_ID'] ?? '',
    'client_secret' => $_ENV['FACTUS_CLIENT_SECRET'] ?? '',
    'username' => $_ENV['FACTUS_USERNAME'] ?? '',
    'password' => $_ENV['FACTUS_PASSWORD'] ?? '',
    'token_encryption_key' => $_ENV['FACTUS_TOKEN_ENCRYPTION_KEY'] ?? '',
    'connect_timeout' => (int)($_ENV['FACTUS_CONNECT_TIMEOUT'] ?? 5),
    'timeout' => (int)($_ENV['FACTUS_TIMEOUT'] ?? 30),
    'token_leeway' => (int)($_ENV['FACTUS_TOKEN_LEEWAY'] ?? 120),
    'token_cache_path' => dirname(__DIR__) . '/storage/cache/factus-token.enc',
    'auth_lock_path' => dirname(__DIR__) . '/storage/cache/factus-auth.lock',
];
