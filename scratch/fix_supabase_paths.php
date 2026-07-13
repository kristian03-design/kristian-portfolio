<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Project;
use Illuminate\Support\Facades\Storage;

$projects = Project::all();
$disk = Storage::disk('supabase');

foreach ($projects as $project) {
    $updated = false;

    // Fix image_path if it starts with /media/
    if ($project->image_path && str_starts_with($project->image_path, '/media/')) {
        $pathWithoutMedia = preg_replace('/^\/media\//', '', $project->image_path);
        
        // If it ends in .webp, let's check if the original (.png, .jpg, .jpeg) exists on Supabase
        if (str_ends_with(strtolower($pathWithoutMedia), '.webp')) {
            $base = preg_replace('/\.webp$/i', '', $pathWithoutMedia);
            
            foreach (['png', 'jpg', 'jpeg'] as $ext) {
                $testPath = "$base.$ext";
                if ($disk->exists($testPath)) {
                    $project->image_path = "/media/$testPath";
                    $updated = true;
                    echo "Restored Supabase path to /media/$testPath for project: {$project->title}\n";
                    break;
                }
            }
        }
    }

    if ($updated) {
        $project->save();
    }
}

\Illuminate\Support\Facades\Cache::flush();
echo "Cache flushed.\n";
