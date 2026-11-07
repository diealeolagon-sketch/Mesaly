<?php

declare(strict_types=1);

namespace App\Core;

class Logger
{
    private static ?string $logFile = null;

    private static function getLogFile(): string
    {
        if (self::$logFile === null) {
            $dir = dirname(__DIR__, 2) . '/storage/logs';
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            self::$logFile = $dir . '/app-' . date('Y-m-d') . '.log';
        }
        return self::$logFile;
    }

    public static function log(string $level, string $message, array $context = []): void
    {
        $date = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? ' ' . json_encode($context, JSON_UNESCAPED_UNICODE) : '';
        $line = "[{$date}] [{$level}] {$message}{$contextStr}" . PHP_EOL;
        @file_put_contents(self::getLogFile(), $line, FILE_APPEND | LOCK_EX);
    }

    public static function info(string $message, array $context = []): void
    {
        self::log('INFO', $message, $context);
    }

    public static function warning(string $message, array $context = []): void
    {
        self::log('WARNING', $message, $context);
    }

    public static function error(string $message, array $context = []): void
    {
        self::log('ERROR', $message, $context);
    }
}
