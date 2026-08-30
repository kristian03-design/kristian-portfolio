<?php

ini_set('expose_php', '0');
header_remove('X-Powered-By');

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
            'webp' => 'image/webp',
            'pdf' => 'application/pdf',
            'txt' => 'text/plain; charset=utf-8',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
        ];
        $extension = strtolower(pathinfo($staticPath, PATHINFO_EXTENSION));

        header('Content-Type: ' . ($types[$extension] ?? 'application/octet-stream'));
        header('Content-Length: ' . filesize($staticPath));
        header('Cache-Control: public, max-age=31536000, s-maxage=31536000, immutable');
        header('CDN-Cache-Control: public, max-age=31536000, immutable');
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');
        header('Referrer-Policy: strict-origin-when-cross-origin');

        if (preg_match('#^/(uploads|media|storage)/#', $uriPath)) {
            header('X-Robots-Tag: noindex, nofollow, noarchive');
        }

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
    // --- App ---
    $_ENV['APP_KEY']   = $_ENV['APP_KEY']   ?? 'base64:qEFsC1p+ZNR6ahdY3WCncSeMV4EighpwVLh0HPkJMt0=';
    $_SERVER['APP_KEY'] = $_ENV['APP_KEY'];
    $_ENV['APP_ENV']   = 'production';
    $_SERVER['APP_ENV'] = 'production';
    $_ENV['APP_DEBUG'] = 'false';
    $_SERVER['APP_DEBUG'] = 'false';
    $_ENV['APP_URL']   = 'https://' . ($_SERVER['HTTP_HOST'] ?? 'kldc.vercel.app');
    $_SERVER['APP_URL'] = $_ENV['APP_URL'];
    $_SERVER['HTTPS']  = 'on';

    // --- Database (Supabase PostgreSQL pooler) ---
    $_ENV['DB_CONNECTION'] = 'pgsql';
    $_SERVER['DB_CONNECTION'] = 'pgsql';
    $_ENV['DB_HOST']       = 'aws-0-ap-southeast-2.pooler.supabase.com';
    $_SERVER['DB_HOST']    = $_ENV['DB_HOST'];
    $_ENV['DB_PORT']       = '6543';
    $_SERVER['DB_PORT']    = '6543';
    $_ENV['DB_DATABASE']   = 'postgres';
    $_SERVER['DB_DATABASE'] = 'postgres';
    $_ENV['DB_USERNAME']   = 'postgres.hyunfbzhturtcytvvkki';
    $_SERVER['DB_USERNAME'] = $_ENV['DB_USERNAME'];
    $_ENV['DB_PASSWORD']   = 'Ashedpowder03';
    $_SERVER['DB_PASSWORD'] = $_ENV['DB_PASSWORD'];
    $_ENV['DB_SSLMODE']    = 'require';
    $_SERVER['DB_SSLMODE'] = 'require';

    // --- Session ---
    $_ENV['SESSION_DRIVER']        = 'cookie';
    $_SERVER['SESSION_DRIVER']     = 'cookie';
    $_ENV['SESSION_SECURE_COOKIE'] = 'true';
    $_SERVER['SESSION_SECURE_COOKIE'] = 'true';
    $_ENV['SESSION_HTTP_ONLY']     = 'true';
    $_SERVER['SESSION_HTTP_ONLY']  = 'true';
    $_ENV['SESSION_SAME_SITE']     = 'lax';
    $_SERVER['SESSION_SAME_SITE']  = 'lax';

    // --- Cache / Logging ---
    $_ENV['CACHE_STORE']  = 'file';
    $_SERVER['CACHE_STORE'] = 'file';
    $_ENV['LOG_CHANNEL']  = 'stderr';
    $_SERVER['LOG_CHANNEL'] = 'stderr';

    // --- Mail (Gmail SMTP) ---
    $_ENV['MAIL_MAILER']       = $_ENV['MAIL_MAILER']       ?? 'smtp';
    $_SERVER['MAIL_MAILER']    = $_ENV['MAIL_MAILER'];
    $_ENV['MAIL_HOST']         = $_ENV['MAIL_HOST']         ?? 'smtp.gmail.com';
    $_SERVER['MAIL_HOST']      = $_ENV['MAIL_HOST'];
    $_ENV['MAIL_PORT']         = $_ENV['MAIL_PORT']         ?? '465';
    $_SERVER['MAIL_PORT']      = $_ENV['MAIL_PORT'];
    $_ENV['MAIL_USERNAME']     = $_ENV['MAIL_USERNAME']     ?? 'hkristianlloyd2@gmail.com';
    $_SERVER['MAIL_USERNAME']  = $_ENV['MAIL_USERNAME'];
    $_ENV['MAIL_PASSWORD']     = $_ENV['MAIL_PASSWORD']     ?? 'vdziuwvzejbgpaxh';
    $_SERVER['MAIL_PASSWORD']  = $_ENV['MAIL_PASSWORD'];
    $_ENV['MAIL_ENCRYPTION']   = $_ENV['MAIL_ENCRYPTION']   ?? 'ssl';
    $_SERVER['MAIL_ENCRYPTION'] = $_ENV['MAIL_ENCRYPTION'];
    $_ENV['MAIL_FROM_ADDRESS'] = $_ENV['MAIL_FROM_ADDRESS'] ?? 'hkristianlloyd2@gmail.com';
    $_SERVER['MAIL_FROM_ADDRESS'] = $_ENV['MAIL_FROM_ADDRESS'];
    $_ENV['MAIL_FROM_NAME']    = $_ENV['MAIL_FROM_NAME']    ?? 'Kristian Hernandez';
    $_SERVER['MAIL_FROM_NAME'] = $_ENV['MAIL_FROM_NAME'];

    // --- Admin ---
    $_ENV['ADMIN_EMAILS']              = $_ENV['ADMIN_EMAILS'] ?? $_SERVER['ADMIN_EMAILS'] ?? 'hkristianlloyd2@gmail.com';
    $_SERVER['ADMIN_EMAILS']           = $_ENV['ADMIN_EMAILS'];
    $_ENV['ADMIN_REGISTRATION_ENABLED'] = 'false';
    $_SERVER['ADMIN_REGISTRATION_ENABLED'] = 'false';

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
