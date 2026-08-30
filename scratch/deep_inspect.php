<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Project;
use App\Models\Skill;
use App\Models\Certification;
use App\Models\Experience;
use Illuminate\Support\Facades\DB;

echo "=== DATABASE CONNECTION ===\n";
try {
    $conn = DB::connection()->getPDO();
    echo "Driver: " . $conn->getAttribute(PDO::ATTR_DRIVER_NAME) . "\n";
    $ver = DB::selectOne('SELECT version()');
    echo "Version: " . ($ver->version ?? $ver->{'version()'}) . "\n";
} catch (Throwable $e) {
    echo "DB ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== PROJECTS RAW QUERY ===\n";
try {
    // Run the exact query the controller runs
    $projects = Project::visible()->orderBy('order', 'asc')->get();
    echo "Count: " . $projects->count() . "\n";
    foreach ($projects as $p) {
        echo "  id={$p->id} title='{$p->title}' status='" . json_encode($p->status) . "' order={$p->order}\n";
    }
} catch (Throwable $e) {
    echo "PROJECT QUERY ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}

echo "\n=== PROJECTS RAW SQL (no scope) ===\n";
try {
    $raw = DB::table('projects')->get();
    echo "Raw count: " . $raw->count() . "\n";
    foreach ($raw as $p) {
        echo "  id={$p->id} title='{$p->title}' status='" . json_encode($p->status) . "'\n";
    }
} catch (Throwable $e) {
    echo "RAW PROJECT ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== CERTIFICATIONS ===\n";
try {
    $certs = Certification::orderBy('issue_date', 'desc')->get();
    echo "Count: " . $certs->count() . "\n";
    foreach ($certs as $c) {
        echo "  id={$c->id} title='{$c->title}'\n";
    }
} catch (Throwable $e) {
    echo "CERT ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}

echo "\n=== SKILLS ===\n";
try {
    $skills = Skill::orderBy('proficiency_level', 'desc')->get();
    echo "Count: " . $skills->count() . "\n";
} catch (Throwable $e) {
    echo "SKILL ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== login_activities TABLE ===\n";
try {
    $cols = DB::select("SELECT column_name, data_type FROM information_schema.columns WHERE table_name = 'login_activities' ORDER BY ordinal_position");
    foreach ($cols as $col) {
        echo "  {$col->column_name}: {$col->data_type}\n";
    }
} catch (Throwable $e) {
    echo "TABLE CHECK ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== TEST INSERT login_activities ===\n";
try {
    \App\Models\LoginActivity::create([
        'user_id' => null,
        'email' => 'test@test.com',
        'ip_address' => '127.0.0.1',
        'user_agent' => 'test',
        'browser' => 'test',
        'operating_system' => 'test',
        'device' => 'Desktop',
        'success' => true,
        'failure_reason' => null,
        'type' => 'login',
    ]);
    echo "LoginActivity INSERT: OK\n";
} catch (Throwable $e) {
    echo "LoginActivity INSERT ERROR: " . $e->getMessage() . "\n";
}
