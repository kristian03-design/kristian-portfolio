<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Project;

foreach (Project::all() as $project) {
    if ($project->image_path && str_starts_with($project->image_path, '/media/') && str_ends_with($project->image_path, '.webp')) {
        $oldPath = $project->image_path;
        $newPath = preg_replace('/\.webp$/i', '.png', $oldPath);
        $project->image_path = $newPath;
        $project->save();
        echo "Restored DB image path for {$project->title}: {$oldPath} -> {$newPath}\n";
    }
}

\Illuminate\Support\Facades\Cache::flush();
echo "Cache cleared.\n";
