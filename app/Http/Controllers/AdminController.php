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
        $filename = now()->timestamp . '_' . Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $extension = $file->getClientOriginalExtension();
        $path = trim($directory, '/') . '/' . $filename . ($extension ? '.' . $extension : '');

        $disk = Storage::disk('supabase');
        $disk->put($path, file_get_contents($file->getRealPath()), [
            'visibility' => 'public',
            'ContentType' => $file->getMimeType(),
        ]);

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

        if ($imagePath = $this->storeProjectImage($request)) {
            $validated['image_path'] = $imagePath;
        }

        Project::create($validated);
        return redirect()->back()->with('success', 'Project created.');
    }

    public function updateProject(Request $request, string $id) {
        $project = Project::findOrFail($id);
        $validated = $this->validateProject($request);
        $validated['tech_stack'] = $this->resolveProjectTechStack($request);
        unset($validated['custom_tech_stack'], $validated['image']);

        if ($imagePath = $this->storeProjectImage($request)) {
            $this->deleteUpload($project->image_path);

            $validated['image_path'] = $imagePath;
        }

        $project->update($validated);
        return redirect()->back()->with('success', 'Project updated.');
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
