<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Project;

foreach (Project::all() as $project) {
    echo "ID: {$project->id}\n";
    echo "Title: {$project->title}\n";
    echo "Image Path: {$project->image_path}\n";
    echo "Image exists: " . (file_exists(public_path(ltrim($project->image_path, '/'))) ? 'YES' : 'NO') . " (" . public_path(ltrim($project->image_path, '/')) . ")\n";
    echo "Gallery: " . json_encode($project->gallery) . "\n\n";
}
