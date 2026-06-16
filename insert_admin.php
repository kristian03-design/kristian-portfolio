<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

// Insert directly into database using Query Builder to bypass model mutators
$email = 'hkristianlloyd2@gmail.com';
$password = 'admin123';
$adminExists = DB::table('adminlist')->where('email', $email)->exists();

if ($adminExists) {
    echo "Admin account already exists.\n";
}
else {
    DB::table('adminlist')->insert([
        'full_name' => 'Admin',
        'email' => $email,
        'password' => Hash::make($password),
        'position' => 'Admin',
        'status' => 'Active',
        'created_at' => now(),
        'updated_at' => now()
    ]);

    echo "Admin account created successfully!\n";
}

echo "Email: {$email}\n";
echo "Password: {$password}\n";

// Verify it
$admin = DB::table('adminlist')->first();
echo "\nVerification:\n";
echo "Email in DB: " . $admin->email . "\n";
echo "Password works: " . (Hash::check($password, $admin->password) ? 'YES' : 'NO') . "\n";
