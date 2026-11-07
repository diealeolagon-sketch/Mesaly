<?php

declare(strict_types=1);

return [
    'name' => $_ENV['APP_NAME'] ?? 'Mesaly',
    'env' => $_ENV['APP_ENV'] ?? 'production',
    'debug' => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN),
    'url' => rtrim($_ENV['APP_URL'] ?? 'http://localhost/Mesaly/public', '/'),
    'timezone' => $_ENV['APP_TIMEZONE'] ?? 'America/Bogota',
    'support_whatsapp' => $_ENV['SUPPORT_WHATSAPP'] ?? '573001234567',
    'subscription' => [
        'alert_days' => (int)($_ENV['SUBSCRIPTION_ALERT_DAYS'] ?? 5),
        'grace_days' => (int)($_ENV['SUBSCRIPTION_GRACE_DAYS'] ?? 3),
    ],
    'session' => [
        'lifetime' => (int)($_ENV['SESSION_LIFETIME'] ?? 7200), // 2 hours
        'table_lifetime' => (int)($_ENV['TABLE_SESSION_LIFETIME'] ?? 14400), // 4 hours
    ],
];
