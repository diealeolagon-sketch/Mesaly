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
     * Build application URL (root-relative on web requests for robust CSP/vhost compatibility).
     */
    function app_url(string $path = ''): string
    {
        $path = '/' . ltrim($path, '/');

        // Si se ejecuta en un servidor web (Apache, Nginx, built-in)
        if (!empty($_SERVER['HTTP_HOST'])) {
            $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
            $baseDir = rtrim(dirname($scriptName), '/');
            if ($baseDir === '.' || $baseDir === '/' || $baseDir === '\\') {
                $baseDir = '';
            }
            return $path === '/' ? ($baseDir !== '' ? $baseDir : '/') : $baseDir . $path;
        }

        // Entorno CLI o fallback
        $base = rtrim($_ENV['APP_URL'] ?? 'http://localhost/Mesaly/public', '/');
        return $path === '/' ? $base : $base . $path;
    }
}

if (!function_exists('asset_url')) {
    /**
     * Build asset URL with automatic filemtime cache-busting.
     */
    function asset_url(string $path): string
    {
        $cleanPath = '/' . ltrim($path, '/');
        $fullPath = dirname(__DIR__, 2) . '/public' . $cleanPath;
        $url = app_url($cleanPath);
        if (file_exists($fullPath)) {
            $mtime = filemtime($fullPath);
            return $url . (str_contains($url, '?') ? '&' : '?') . 'v=' . $mtime;
        }
        return $url;
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

if (!function_exists('icon')) {
    /**
     * Render consistent, lightweight SVG icon with 1.75px stroke.
     * Complies with strict CSP: no inline style attribute, uses class .icon-svg.
     */
    function icon(string $name, int $size = 18, string $class = ''): string
    {
        $stroke = '1.75';
        $classes = trim('icon-svg ' . $class);
        $attrs = 'width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="' . $stroke . '" stroke-linecap="round" stroke-linejoin="round" class="' . e($classes) . '" aria-hidden="true"';

        return match ($name) {
            'dashboard' => '<svg ' . $attrs . '><rect width="7" height="9" x="3" y="3" rx="1.5"/><rect width="7" height="5" x="14" y="3" rx="1.5"/><rect width="7" height="9" x="14" y="12" rx="1.5"/><rect width="7" height="5" x="3" y="16" rx="1.5"/></svg>',
            'catalog' => '<svg ' . $attrs . '><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/><path d="M6 6h10"/><path d="M6 10h10"/></svg>',
            'modifiers' => '<svg ' . $attrs . '><line x1="4" x2="20" y1="21" y2="21"/><line x1="4" x2="20" y1="3" y2="3"/><line x1="4" x2="20" y1="12" y2="12"/><circle cx="8" cy="12" r="2.5"/><circle cx="16" cy="3" r="2.5"/><circle cx="12" cy="21" r="2.5"/></svg>',
            'zap' => '<svg ' . $attrs . '><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>',
            'settings' => '<svg ' . $attrs . '><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>',
            'eye' => '<svg ' . $attrs . '><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>',
            'plus' => '<svg ' . $attrs . '><path d="M5 12h14"/><path d="M12 5v14"/></svg>',
            'minus' => '<svg ' . $attrs . '><path d="M5 12h14"/></svg>',
            'edit' => '<svg ' . $attrs . '><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg>',
            'trash' => '<svg ' . $attrs . '><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>',
            'image' => '<svg ' . $attrs . '><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>',
            'check' => '<svg ' . $attrs . '><polyline points="20 6 9 17 4 12"/></svg>',
            'x' => '<svg ' . $attrs . '><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>',
            'logout' => '<svg ' . $attrs . '><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>',
            'upload' => '<svg ' . $attrs . '><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" x2="12" y1="3" y2="15"/></svg>',
            'store' => '<svg ' . $attrs . '><path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7"/><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/><path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4"/><path d="M2 7h20"/></svg>',
            'folder' => '<svg ' . $attrs . '><path d="M4 20h16a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.93a2 2 0 0 1-1.66-.9l-.82-1.2A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13c0 1.1.9 2 2 2Z"/></svg>',
            'star' => '<svg ' . $attrs . '><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>',
            'clock' => '<svg ' . $attrs . '><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>',
            'phone' => '<svg ' . $attrs . '><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>',
            'palette' => '<svg ' . $attrs . '><circle cx="13.5" cy="6.5" r=".5" fill="currentColor"/><circle cx="17.5" cy="10.5" r=".5" fill="currentColor"/><circle cx="8.5" cy="7.5" r=".5" fill="currentColor"/><circle cx="6.5" cy="12.5" r=".5" fill="currentColor"/><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.926 0 1.648-.746 1.648-1.688 0-.437-.18-.835-.437-1.125-.29-.289-.438-.652-.438-1.125a1.64 1.64 0 0 1 1.668-1.668h1.996c3.051 0 5.563-2.512 5.563-5.563C22 6.5 17.5 2 12 2Z"/></svg>',
            'truck' => '<svg ' . $attrs . '><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><path d="M15 18H9"/><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/></svg>',
            'arrow-left' => '<svg ' . $attrs . '><path d="m12 19-7-7 7-7"/><path d="M19 12H5"/></svg>',
            'arrow-right' => '<svg ' . $attrs . '><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>',
            'external-link' => '<svg ' . $attrs . '><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" x2="21" y1="14" y2="3"/></svg>',
            'cutlery' => '<svg ' . $attrs . '><path d="M18 2v20"/><path d="M18 7a4 4 0 0 1-4-4V2"/><path d="M6 2v7a3 3 0 0 0 3 3v10"/><path d="M10 2v5"/><path d="M6 2v5"/></svg>',
            'motorcycle' => '<svg ' . $attrs . '><circle cx="5" cy="17" r="3"/><circle cx="19" cy="17" r="3"/><path d="M9 17h6"/><path d="m12 14 3-6 4 1"/><path d="m5 17 5-5h3"/><path d="M15 8h2"/></svg>',
            'shopping-bag' => '<svg ' . $attrs . '><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>',
            'cart' => '<svg ' . $attrs . '><circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/></svg>',
            'cash' => '<svg ' . $attrs . '><rect width="20" height="12" x="2" y="6" rx="2"/><circle cx="12" cy="12" r="2"/><path d="M6 12h.01M18 12h.01"/></svg>',
            'smartphone' => '<svg ' . $attrs . '><rect width="14" height="20" x="5" y="2" rx="2" ry="2"/><path d="M12 18h.01"/></svg>',
            'credit-card' => '<svg ' . $attrs . '><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/></svg>',
            'search' => '<svg ' . $attrs . '><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>',
            'whatsapp' => '<svg ' . $attrs . '><path d="M3 21l1.65-3.8a9 9 0 1 1 3.4 2.9L3 21"/><path d="M9 10a.5.5 0 0 0 1 0V9a.5.5 0 0 0-1 0v1a5 5 0 0 0 5 5h1a.5.5 0 0 0 0-1h-1a.5.5 0 0 0 0 1"/></svg>',
            'lock' => '<svg ' . $attrs . '><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>',
            'mail' => '<svg ' . $attrs . '><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>',
            'tag' => '<svg ' . $attrs . '><path d="M12 2H2v10l9.29 9.29c.94.94 2.48.94 3.42 0l6.58-6.58c.94-.94.94-2.48 0-3.42L12 2Z"/><circle cx="7" cy="7" r=".5" fill="currentColor"/></svg>',
            'sparkles' => '<svg ' . $attrs . '><path d="m12 3-1.9 5.8a2 2 0 0 1-1.3 1.3L3 12l5.8 1.9a2 2 0 0 1 1.3 1.3L12 21l1.9-5.8a2 2 0 0 1 1.3-1.3L21 12l-5.8-1.9a2 2 0 0 1-1.3-1.3Z"/></svg>',
            'alert' => '<svg ' . $attrs . '><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg>',
            'bell' => '<svg ' . $attrs . '><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>',
            'more' => '<svg ' . $attrs . '><circle cx="12" cy="12" r="1.5"/><circle cx="12" cy="5" r="1.5"/><circle cx="12" cy="19" r="1.5"/></svg>',
            'more-vertical' => '<svg ' . $attrs . '><circle cx="12" cy="12" r="1.5"/><circle cx="12" cy="5" r="1.5"/><circle cx="12" cy="19" r="1.5"/></svg>',
            'chart' => '<svg ' . $attrs . '><line x1="18" x2="18" y1="20" y2="10"/><line x1="12" x2="12" y1="20" y2="4"/><line x1="6" x2="6" y1="20" y2="14"/></svg>',
            'copy' => '<svg ' . $attrs . '><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>',
            'coin' => '<svg ' . $attrs . '><circle cx="12" cy="12" r="9"/><path d="M12 6v12M15 9.5A2.5 2.5 0 0 0 12.5 7h-1A2.5 2.5 0 0 0 9 9.5 2.5 2.5 0 0 0 11.5 12h1a2.5 2.5 0 0 1 2.5 2.5 2.5 2.5 0 0 1-2.5 2.5h-1A2.5 2.5 0 0 1 9 14.5"/></svg>',
            'download' => '<svg ' . $attrs . '><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>',
            'calendar' => '<svg ' . $attrs . '><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>',
            'printer' => '<svg ' . $attrs . '><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>',
            'trending-up' => '<svg ' . $attrs . '><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>',
            'trending-down' => '<svg ' . $attrs . '><polyline points="23 18 13.5 8.5 8.5 13.5 1 6"/><polyline points="17 18 23 18 23 12"/></svg>',
            'list' => '<svg ' . $attrs . '><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>',
            'receipt' => '<svg ' . $attrs . '><path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1-2-1Z"/><path d="M16 8H8"/><path d="M16 12H8"/><path d="M14 16H8"/></svg>',
            'menu' => '<svg ' . $attrs . '><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>',
            default => '<svg ' . $attrs . '><circle cx="12" cy="12" r="10"/></svg>',
        };
    }
}

if (!function_exists('support_whatsapp_number')) {
    /**
     * Get official Mesaly support WhatsApp number.
     */
    function support_whatsapp_number(): string
    {
        $num = preg_replace('/\D/', '', (string)($_ENV['SUPPORT_WHATSAPP'] ?? '573248670360'));
        return !empty($num) ? $num : '573248670360';
    }
}

if (!function_exists('support_whatsapp_url')) {
    /**
     * Build direct WhatsApp support link with prefilled message.
     */
    function support_whatsapp_url(string $message = 'Hola equipo de soporte de Mesaly, necesito asistencia con mi restaurante.'): string
    {
        $number = support_whatsapp_number();
        return 'https://wa.me/' . $number . '?text=' . rawurlencode($message);
    }
}

