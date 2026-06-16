<?php

if (isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL'])) {
    $uriPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

    if ($uriPath === '/up') {
        header('Content-Type: text/plain; charset=utf-8');
        echo 'OK';

        return;
    }

    $publicPath = realpath(__DIR__ . '/../public');
    $staticPath = realpath($publicPath . '/' . ltrim($uriPath, '/'));

    if (
        in_array($_SERVER['REQUEST_METHOD'] ?? 'GET', ['GET', 'HEAD'], true)
        && $publicPath !== false
        && $staticPath !== false
        && str_starts_with($staticPath, $publicPath . DIRECTORY_SEPARATOR)
        && is_file($staticPath)
    ) {
        $types = [
            'css' => 'text/css; charset=utf-8',
            'js' => 'application/javascript; charset=utf-8',
            'json' => 'application/json; charset=utf-8',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'ico' => 'image/x-icon',
            'pdf' => 'application/pdf',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
        ];
        $extension = strtolower(pathinfo($staticPath, PATHINFO_EXTENSION));

        header('Content-Type: ' . ($types[$extension] ?? 'application/octet-stream'));
        header('Content-Length: ' . filesize($staticPath));
        header('Cache-Control: public, max-age=31536000, s-maxage=31536000, immutable');
        header('CDN-Cache-Control: public, max-age=31536000, immutable');

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'HEAD') {
            readfile($staticPath);
        }

        return;
    }

    $storagePath = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'laravel-storage';

    $_ENV['LARAVEL_STORAGE_PATH'] = $storagePath;
    $_SERVER['LARAVEL_STORAGE_PATH'] = $storagePath;
    $_ENV['VIEW_COMPILED_PATH'] = $storagePath . '/framework/views';
    $_SERVER['VIEW_COMPILED_PATH'] = $storagePath . '/framework/views';
    $_ENV['APP_PACKAGES_CACHE'] = $storagePath . '/framework/cache/packages.php';
    $_SERVER['APP_PACKAGES_CACHE'] = $storagePath . '/framework/cache/packages.php';
    $_ENV['APP_SERVICES_CACHE'] = $storagePath . '/framework/cache/services.php';
    $_SERVER['APP_SERVICES_CACHE'] = $storagePath . '/framework/cache/services.php';
    $_ENV['APP_CONFIG_CACHE'] = $storagePath . '/framework/cache/config.php';
    $_SERVER['APP_CONFIG_CACHE'] = $storagePath . '/framework/cache/config.php';
    $_ENV['APP_ROUTES_CACHE'] = $storagePath . '/framework/cache/routes.php';
    $_SERVER['APP_ROUTES_CACHE'] = $storagePath . '/framework/cache/routes.php';
    $_ENV['APP_EVENTS_CACHE'] = $storagePath . '/framework/cache/events.php';
    $_SERVER['APP_EVENTS_CACHE'] = $storagePath . '/framework/cache/events.php';
    $_ENV['SESSION_DRIVER'] = 'cookie';
    $_SERVER['SESSION_DRIVER'] = 'cookie';
    $_ENV['CACHE_STORE'] = 'file';
    $_SERVER['CACHE_STORE'] = 'file';
    $_ENV['LOG_CHANNEL'] = 'stderr';
    $_SERVER['LOG_CHANNEL'] = 'stderr';
    $_ENV['APP_URL'] = 'https://' . ($_SERVER['HTTP_HOST'] ?? 'kristian-portfolio-two.vercel.app');
    $_SERVER['APP_URL'] = $_ENV['APP_URL'];
    $_SERVER['HTTPS'] = 'on';

    foreach ([
        $storagePath . '/app',
        $storagePath . '/framework/cache',
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

try {
    require __DIR__ . '/../public/index.php';
} catch (Throwable $e) {
    for ($i = 0, $throwable = $e; $throwable !== null; $i++, $throwable = $throwable->getPrevious()) {
        error_log(sprintf(
            'APP_BOOT_ERROR[%d] %s: %s in %s:%d',
            $i,
            get_class($throwable),
            $throwable->getMessage(),
            $throwable->getFile(),
            $throwable->getLine()
        ));
    }

    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Server error';
}
