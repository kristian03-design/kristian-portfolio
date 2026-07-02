<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Project;
use App\Models\Skill;
use App\Models\Experience;
use App\Models\Certification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class PortfolioController extends Controller
{
    public function index()
    {
        $projects = $this->safeCollection('projects', fn () => Project::orderBy('order', 'asc')->get());
        $skills = $this->safeCollection('skills', fn () => Skill::orderBy('proficiency_level', 'desc')->get());
        $experiences = $this->safeCollection('experiences', fn () => Experience::orderBy('start_date', 'desc')->get());
        $certifications = $this->safeCollection('certifications', fn () => Certification::orderBy('issue_date', 'desc')->get());

        $services = [
            [
                'num' => '01',
                'title' => 'Web Development',
                'body' => 'Full-stack Laravel applications with clean MVC, secure auth, and optimised DB queries.',
                'icon' => '<rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/>'
            ],
            [
                'num' => '02',
                'title' => 'Mobile Apps',
                'body' => 'Cross-platform iOS & Android apps with Flutter - state management, Firebase, offline-first.',
                'icon' => '<rect x="5" y="2" width="14" height="20" rx="2"/><line x1="12" y1="18" x2="12.01" y2="18"/>'
            ],
            [
                'num' => '03',
                'title' => 'REST API Design',
                'body' => 'Versioned, documented APIs with proper auth guards, rate limiting, and response schemas.',
                'icon' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>'
            ],
            [
                'num' => '04',
                'title' => 'Database Design',
                'body' => 'Normalised schemas, index tuning, migration management in MySQL & PostgreSQL.',
                'icon' => '<ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/>'
            ]
        ];

        return response()
            ->view('portfolio', compact('projects', 'skills', 'experiences', 'certifications', 'services'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0')
            ->header('CDN-Cache-Control', 'no-store');
    }

    public function projects()
    {
        $projects = $this->safeCollection('projects', fn () => Project::orderBy('order', 'asc')->get());

        return response()
            ->view('projects', compact('projects'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0')
            ->header('CDN-Cache-Control', 'no-store');
    }

    public function data()
    {
        $data = Cache::remember('portfolio.public.api', now()->addMinutes(10), fn () => [
            'projects' => $this->safeCollection('projects', fn () => Project::orderBy('order', 'asc')->get()),
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
            fn () => $this->safeCollection('projects', fn () => Project::orderBy('order', 'asc')->get())
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
