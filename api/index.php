<?php

if (isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL'])) {
    $storagePath = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'laravel-storage';

    $_ENV['LARAVEL_STORAGE_PATH'] = $storagePath;
    $_SERVER['LARAVEL_STORAGE_PATH'] = $storagePath;
    $_ENV['VIEW_COMPILED_PATH'] = $storagePath . '/framework/views';
    $_SERVER['VIEW_COMPILED_PATH'] = $storagePath . '/framework/views';
    $_ENV['LOG_CHANNEL'] = $_ENV['LOG_CHANNEL'] ?? 'stderr';
    $_SERVER['LOG_CHANNEL'] = $_SERVER['LOG_CHANNEL'] ?? 'stderr';

    foreach ([
        $storagePath . '/app',
        $storagePath . '/framework/cache/data',
        $storagePath . '/framework/sessions',
        $storagePath . '/framework/views',
        $storagePath . '/logs',
    ] as $path) {
        if (! is_dir($path)) {
            mkdir($path, 0777, true);
        }
    }
}

require __DIR__ . '/../public/index.php';
