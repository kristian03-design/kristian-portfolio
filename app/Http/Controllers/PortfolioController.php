<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Project;
use App\Models\Skill;
use App\Models\Experience;
use App\Models\Certification;
use App\Models\PortfolioGallery;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class PortfolioController extends Controller
{
    public function index()
    {
        $cachedData = Cache::remember('portfolio.public.index', now()->addMinutes(10), function() {
            $projects = $this->safeCollection('projects', fn () => Project::visible()->orderBy('order', 'asc')->get());
            $skills = $this->safeCollection('skills', fn () => Skill::orderBy('proficiency_level', 'desc')->get());
            $experiences = $this->safeCollection('experiences', fn () => Experience::orderBy('start_date', 'desc')->get());
            $certifications = $this->safeCollection('certifications', fn () => Certification::orderBy('issue_date', 'desc')->get());
            $galleryItems = $this->safeCollection('gallery', fn () => PortfolioGallery::where('is_published', true)->orderByDesc('is_featured')->orderBy('display_order', 'asc')->get());
            return compact('projects', 'skills', 'experiences', 'certifications', 'galleryItems');
        });

        $projects = $cachedData['projects'];
        $skills = $cachedData['skills'];
        $experiences = $cachedData['experiences'];
        $certifications = $cachedData['certifications'];
        $galleryItems = $cachedData['galleryItems'] ?? collect();

        $services = [
            [
                'num' => '01',
                'title' => 'Web Development',
                'body' => 'Full-stack Laravel applications with clean MVC, secure auth, and optimised DB queries.',
                'icon' => 'monitor'
            ],
            [
                'num' => '02',
                'title' => 'Mobile Apps',
                'body' => 'Cross-platform iOS & Android apps with Flutter - state management, Firebase, offline-first.',
                'icon' => 'smartphone'
            ],
            [
                'num' => '03',
                'title' => 'REST API Design',
                'body' => 'Versioned, documented APIs with proper auth guards, rate limiting, and response schemas.',
                'icon' => 'code-2'
            ],
            [
                'num' => '04',
                'title' => 'Database Design',
                'body' => 'Normalised schemas, index tuning, migration management in MySQL & PostgreSQL.',
                'icon' => 'database'
            ]
        ];

        return response()
            ->view('portfolio', compact('projects', 'skills', 'experiences', 'certifications', 'services', 'galleryItems'))
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate');
    }

    public function projects()
    {
        $projects = Cache::remember('portfolio.public.projects', now()->addMinutes(10), function() {
            return $this->safeCollection('projects', fn () => Project::visible()->orderBy('order', 'asc')->get());
        });

        return response()
            ->view('projects', compact('projects'))
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate');
    }

    public function data()
    {
        $data = Cache::remember('portfolio.public.api', now()->addMinutes(10), fn () => [
            'projects' => $this->safeCollection('projects', fn () => Project::visible()->orderBy('order', 'asc')->get()),
            'skills' => $this->safeCollection('skills', fn () => Skill::orderBy('category')->orderByDesc('proficiency_level')->orderBy('name')->get()),
            'experiences' => $this->safeCollection('experiences', fn () => Experience::orderBy('start_date', 'desc')->get()),
        ]);

        $projects = $data['projects'];
        $skills = $data['skills'];
        $experiences = $data['experiences'];

        return response()->json([
            'counts' => [
                'projects' => $projects->count(),
                'skills' => $skills->count(),
                'experiences' => $experiences->count(),
            ],
            'projects' => $projects,
            'skills' => $skills,
            'experiences' => $experiences,
        ]);
    }

    public function projectData()
    {
        return response()->json(Cache::remember(
            'portfolio.public.api.projects',
            now()->addMinutes(10),
            fn () => $this->safeCollection('projects', fn () => Project::visible()->orderBy('order', 'asc')->get())
        ));
    }

    public function skillData()
    {
        return response()->json(Cache::remember(
            'portfolio.public.api.skills',
            now()->addMinutes(10),
            fn () => $this->safeCollection('skills', fn () => Skill::orderBy('category')->orderByDesc('proficiency_level')->orderBy('name')->get())
        ));
    }

    public function experienceData()
    {
        return response()->json(Cache::remember(
            'portfolio.public.api.experiences',
            now()->addMinutes(10),
            fn () => $this->safeCollection('experiences', fn () => Experience::orderBy('start_date', 'desc')->get())
        ));
    }

    public function media(string $path)
    {
        $path = ltrim($path, '/');

        try {
            $disk = Storage::disk('supabase');

            if (! $disk->exists($path)) {
                abort(404);
            }

            $stream = $disk->readStream($path);
            $mimeType = $disk->mimeType($path) ?: 'application/octet-stream';

            return response()->stream(function () use ($stream) {
                fpassthru($stream);

                if (is_resource($stream)) {
                    fclose($stream);
                }
            }, 200, [
                'Content-Type' => $mimeType,
                'Cache-Control' => 'public, max-age=3600, s-maxage=86400, stale-while-revalidate=604800',
                'CDN-Cache-Control' => 'public, max-age=86400, stale-while-revalidate=604800',
            ]);
        } catch (Throwable $e) {
            Log::error('Supabase media stream failed', [
                'path' => $path,
                'exception' => $e,
            ]);

            abort(404);
        }
    }

    public function viewResume()
    {
        $filePath = public_path('resume/HERNANDEZ_KRISTIAN_RESUME_2025.pdf');
        if (!file_exists($filePath)) {
            return response()->view('resume.error', [], 404);
        }
        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="HERNANDEZ_KRISTIAN_RESUME_2025.pdf"'
        ]);
    }

    public function downloadResume()
    {
        $filePath = public_path('resume/HERNANDEZ_KRISTIAN_RESUME_2025.pdf');
        if (!file_exists($filePath)) {
            return redirect()->route('resume.view');
        }
        return response()->download($filePath, 'HERNANDEZ_KRISTIAN_RESUME_2025.pdf');
    }

    public function show(Project $project)
    {
        if ($project->status === 'Draft') {
            abort(404);
        }

        if (($project->documentation_status ?? 'under_development') === 'under_development') {
            return redirect()->route('projects.coming-soon', $project->slug);
        }

        $allProjects = Cache::remember('portfolio.public.projects', now()->addMinutes(10), function() {
            return $this->safeCollection('projects', fn () => Project::visible()->orderBy('order', 'asc')->get());
        });

        $relatedProjects = $allProjects->where('id', '!=', $project->id)->take(4)->values();

        return response()
            ->view('projects.show', compact('project', 'relatedProjects'))
            ->header('Cache-Control', 'public, max-age=60, stale-while-revalidate=600')
            ->header('CDN-Cache-Control', 'public, max-age=600, stale-while-revalidate=3600');
    }

    public function comingSoon(Project $project)
    {
        if ($project->status === 'Draft') {
            abort(404);
        }

        if (($project->documentation_status ?? 'under_development') === 'published') {
            return redirect()->route('projects.show', $project->slug);
        }

        $allProjects = Cache::remember('portfolio.public.projects', now()->addMinutes(10), function() {
            return $this->safeCollection('projects', fn () => Project::visible()->orderBy('order', 'asc')->get());
        });

        $currentIndex = $allProjects->pluck('id')->search($project->id);
        
        $prevProject = $currentIndex > 0 ? $allProjects->get($currentIndex - 1) : null;
        $nextProject = $currentIndex !== -1 && $currentIndex < $allProjects->count() - 1 
            ? $allProjects->get($currentIndex + 1) 
            : null;

        return response()
            ->view('projects.coming-soon', compact('project', 'prevProject', 'nextProject'))
            ->header('Cache-Control', 'public, max-age=60, stale-while-revalidate=600')
            ->header('CDN-Cache-Control', 'public, max-age=600, stale-while-revalidate=3600');
    }

    private function safeCollection(string $label, callable $query)
    {
        try {
            return $query();
        } catch (Throwable $e) {
            Log::error("Portfolio {$label} query failed", ['exception' => $e]);

            return collect();
        }
    }
}
