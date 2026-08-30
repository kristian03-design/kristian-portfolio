<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Services\AuditLogger;

echo "1. Testing Auth::validate():\n";
$valid = Auth::validate(['email' => 'hkristianlloyd2@gmail.com', 'password' => 'admin123']);
echo "   Auth::validate: " . ($valid ? 'VALID' : 'INVALID') . "\n";

echo "2. Testing User::where('email')->update():\n";
$user = User::where('email', 'hkristianlloyd2@gmail.com')->first();
$user->update([
    'otp_code' => 123456,
    'otp_expires_at' => now()->addMinutes(5),
]);
echo "   Updated user OTP code: " . $user->otp_code . "\n";

echo "3. Testing AuditLogger::logLoginActivity():\n";
try {
    AuditLogger::logLoginActivity(true, 'hkristianlloyd2@gmail.com', $user->id, 'login');
    echo "   AuditLogger::logLoginActivity SUCCESS!\n";
} catch (Throwable $e) {
    echo "   AuditLogger ERROR: " . $e->getMessage() . "\n";
}

echo "4. Testing Mail configuration:\n";
try {
    $transport = \Illuminate\Support\Facades\Mail::mailer()->getSymfonyTransport();
    echo "   Mail transport initialized successfully!\n";
} catch (Throwable $e) {
    echo "   Mail initialization ERROR: " . $e->getMessage() . "\n";
}
