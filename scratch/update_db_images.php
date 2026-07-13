<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Project;

$projects = Project::all();
foreach ($projects as $project) {
    $updated = false;

    // Update image_path
    if ($project->image_path && preg_match('/\.png$/i', $project->image_path)) {
        $project->image_path = preg_replace('/\.png$/i', '.webp', $project->image_path);
        $updated = true;
    }

    // Update gallery JSON
    if ($project->gallery) {
        $gallery = $project->gallery;
        foreach (['desktop', 'tablet', 'mobile'] as $key) {
            if (isset($gallery[$key]) && preg_match('/\.png$/i', $gallery[$key])) {
                $gallery[$key] = preg_replace('/\.png$/i', '.webp', $gallery[$key]);
                $updated = true;
            }
        }
        $project->gallery = $gallery;
    }

    if ($updated) {
        $project->save();
        echo "Updated DB paths for project: {$project->title}\n";
    }
}

\Illuminate\Support\Facades\Cache::flush();
echo "Cache flushed successfully.\n";
