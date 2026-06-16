<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Project;
use App\Models\Skill;
use App\Models\Experience;
use App\Models\Message;
use App\Models\Certification;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    public function index()
    {
        $projects = Project::orderBy('order')->latest()->get();
        $skills = Skill::orderBy('category')->orderBy('name')->get();
        $experiences = Experience::orderByDesc('start_date')->get();
        $certifications = Certification::orderByDesc('issue_date')->get();
        $messages = Message::orderBy('created_at', 'desc')->get();
        
        return view('admin.admin', compact('projects', 'skills', 'experiences', 'certifications', 'messages'));
    }

    private function validateProject(Request $request): array
    {
        return $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'url' => 'nullable|url',
            'github_url' => 'nullable|url',
            'tech_stack' => 'nullable', // Can be array from checkboxes
            'custom_tech_stack' => 'nullable|string', // Custom comma-separated string
            'image' => 'nullable|image|max:4096',
        ]);
    }

    private function resolveProjectTechStack(Request $request): array
    {
        $techStack = [];

        // Gather from checkboxes (tech_stack)
        if (! empty($request->input('tech_stack'))) {
            $inputStack = $request->input('tech_stack');
            if (is_array($inputStack)) {
                $techStack = array_merge($techStack, $inputStack);
            } elseif (is_string($inputStack)) {
                $techStack = array_merge($techStack, explode(',', $inputStack));
            }
        }

        // Gather from custom text input (custom_tech_stack)
        if (! empty($request->input('custom_tech_stack'))) {
            $customStack = explode(',', $request->input('custom_tech_stack'));
            $techStack = array_merge($techStack, $customStack);
        }

        // Clean, remove duplicates, trim and store
        return array_values(array_filter(array_unique(array_map('trim', $techStack))));
    }

    private function storeProjectImage(Request $request): ?string
    {
        if (! $request->hasFile('image')) {
            return null;
        }

        return $this->storeUpload($request->file('image'), 'uploads/projects');
    }

    private function storeUpload(UploadedFile $file, string $directory): string
    {
        $required = [
            'SUPABASE_STORAGE_BUCKET' => config('filesystems.disks.supabase.bucket'),
            'SUPABASE_STORAGE_ACCESS_KEY_ID' => config('filesystems.disks.supabase.key'),
            'SUPABASE_STORAGE_SECRET_ACCESS_KEY' => config('filesystems.disks.supabase.secret'),
            'SUPABASE_STORAGE_ENDPOINT' => config('filesystems.disks.supabase.endpoint'),
        ];

        if (in_array(null, $required, true) || in_array('', $required, true)) {
            throw ValidationException::withMessages([
                'image' => 'Supabase file storage is not fully configured. Please add the storage bucket, endpoint, access key, and secret key in Vercel.',
            ]);
        }

        $filename = now()->timestamp . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $extension = $file->getClientOriginalExtension();
        $path = trim($directory, '/') . '/' . $filename . ($extension ? '.' . $extension : '');

        try {
            $disk = Storage::disk('supabase');
            $disk->put($path, file_get_contents($file->getRealPath()), [
                'visibility' => 'public',
                'ContentType' => $file->getMimeType(),
            ]);
        } catch (\Throwable $e) {
            report($e);

            throw ValidationException::withMessages([
                'image' => 'The file could not be uploaded to Supabase Storage. Please check the Supabase storage credentials in Vercel.',
            ]);
        }

        return $disk->url($path);
    }

    private function deleteUpload(?string $pathOrUrl): void
    {
        if (! $pathOrUrl) {
            return;
        }

        $disk = Storage::disk('supabase');
        $diskUrl = rtrim((string) config('filesystems.disks.supabase.url'), '/');
        $objectPath = null;

        if ($diskUrl && str_starts_with($pathOrUrl, $diskUrl . '/')) {
            $objectPath = ltrim(substr($pathOrUrl, strlen($diskUrl)), '/');
        } elseif (! Str::startsWith($pathOrUrl, ['http://', 'https://'])) {
            $objectPath = ltrim($pathOrUrl, '/');
        }

        if ($objectPath) {
            $disk->delete($objectPath);

            return;
        }

        if (file_exists(public_path($pathOrUrl))) {
            @unlink(public_path($pathOrUrl));
        }
    }

    public function storeProject(Request $request) {
        $validated = $this->validateProject($request);
        $validated['tech_stack'] = $this->resolveProjectTechStack($request);
        unset($validated['custom_tech_stack'], $validated['image']);
        $warning = null;

        try {
            if ($imagePath = $this->storeProjectImage($request)) {
                $validated['image_path'] = $imagePath;
            }
        } catch (ValidationException $e) {
            $warning = $e->errors()['image'][0] ?? 'The project was saved, but the preview image could not be uploaded.';
        }

        Project::create($validated);

        $redirect = redirect()->back()->with('success', 'Project created.');

        if ($warning) {
            $redirect->with('warning', $warning);
        }

        return $redirect;
    }

    public function updateProject(Request $request, string $id) {
        $project = Project::findOrFail($id);
        $validated = $this->validateProject($request);
        $validated['tech_stack'] = $this->resolveProjectTechStack($request);
        unset($validated['custom_tech_stack'], $validated['image']);
        $warning = null;

        try {
            if ($imagePath = $this->storeProjectImage($request)) {
                $this->deleteUpload($project->image_path);

                $validated['image_path'] = $imagePath;
            }
        } catch (ValidationException $e) {
            $warning = $e->errors()['image'][0] ?? 'The project was updated, but the preview image could not be uploaded.';
        }

        $project->update($validated);

        $redirect = redirect()->back()->with('success', 'Project updated.');

        if ($warning) {
            $redirect->with('warning', $warning);
        }

        return $redirect;
    }

    public function destroyProject(string $id) {
        $project = Project::findOrFail($id);
        $this->deleteUpload($project->image_path);
        $project->delete();
        return redirect()->back()->with('success', 'Project deleted.');
    }

    public function storeSkill(Request $request) {
        $validated = $request->validate(['name' => 'required|string', 'category' => 'required|string', 'proficiency_level' => 'required|integer']);
        Skill::create($validated);
        return redirect()->back()->with('success', 'Skill created.');
    }
    public function destroySkill(string $id) {
        Skill::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Skill deleted.');
    }

    public function storeExperience(Request $request) {
        $validated = $request->validate([
            'role' => 'required|string',
            'company' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'description' => 'nullable|string',
        ]);
        Experience::create($validated);
        return redirect()->back()->with('success', 'Experience created.');
    }
    public function destroyExperience(string $id) {
        Experience::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Experience deleted.');
    }

    public function storeCertification(Request $request) {
        $validated = $request->validate([
            'title' => 'required|string',
            'recipient_name' => 'nullable|string',
            'course_name' => 'nullable|string',
            'issuer' => 'required|string',
            'issue_date' => 'required|date',
            'expiration_date' => 'nullable|date',
            'credential_id' => 'nullable|string',
            'credential_url' => 'nullable|url',
            'description' => 'nullable|string',
            'certificate_image' => 'nullable|file|mimes:pdf,png,jpg,jpeg|max:4096',
        ]);

        if ($request->hasFile('certificate_image')) {
            $validated['image_path'] = $this->storeUpload($request->file('certificate_image'), 'uploads/certifications');
        }

        Certification::create($validated);
        return redirect()->back()->with('success', 'Certification created.');
    }

    public function destroyCertification(string $id) {
        $cert = Certification::findOrFail($id);
        $this->deleteUpload($cert->image_path);
        $cert->delete();
        return redirect()->back()->with('success', 'Certification deleted.');
    }

    public function markMessageRead(string $id) {
        $msg = Message::findOrFail($id);
        $msg->update(['status' => 'read']);
        return redirect()->back()->with('success', 'Message marked as read.');
    }
}
