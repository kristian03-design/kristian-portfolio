<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Project;
use App\Models\Certification;

$projects = Project::visible()->get();
echo "Visible Projects in DB: " . $projects->count() . "\n";
foreach ($projects as $p) {
    echo "  - Title: {$p->title}, Status: '" . $p->status . "', order: " . $p->order . "\n";
}

$certs = Certification::all();
echo "Certifications in DB: " . $certs->count() . "\n";
foreach ($certs as $c) {
    echo "  - Title: {$c->title}\n";
}
