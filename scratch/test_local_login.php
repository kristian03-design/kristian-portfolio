<?php
// Test POST /login locally

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = Illuminate\Http\Request::create('/login', 'POST', [
    'email' => 'hkristianlloyd2@gmail.com',
    'password' => 'admin123',
]);

// Set session
$session = $app->make('session')->driver();
$request->setLaravelSession($session);

try {
    $response = $kernel->handle($request);
    echo "Local POST /login status: " . $response->getStatusCode() . "\n";
    echo "Headers: \n";
    foreach ($response->headers->all() as $name => $values) {
        echo "  $name: " . implode(', ', $values) . "\n";
    }
    if ($response->getStatusCode() >= 400) {
        echo "Content: " . substr($response->getContent(), 0, 500) . "\n";
    }
} catch (Throwable $e) {
    echo "Exception during local POST /login: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
