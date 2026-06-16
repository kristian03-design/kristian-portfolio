<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Project;
use App\Models\Skill;
use App\Models\Experience;
use App\Models\Message;
use App\Models\Certification;
use Illuminate\Support\Facades\File;

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

        $file = $request->file('image');
        $filename = time() . '_' . $file->getClientOriginalName();
        File::ensureDirectoryExists(public_path('uploads/projects'));
        $file->move(public_path('uploads/projects'), $filename);

        return '/uploads/projects/' . $filename;
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
            if ($project->image_path && file_exists(public_path($project->image_path))) {
                @unlink(public_path($project->image_path));
            }

            $validated['image_path'] = $imagePath;
        }

        $project->update($validated);
        return redirect()->back()->with('success', 'Project updated.');
    }

    public function destroyProject(string $id) {
        $project = Project::findOrFail($id);
        if ($project->image_path && file_exists(public_path($project->image_path))) {
            @unlink(public_path($project->image_path));
        }
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
            $file = $request->file('certificate_image');
            $filename = time() . '_' . $file->getClientOriginalName();
            File::ensureDirectoryExists(public_path('uploads/certifications'));
            $file->move(public_path('uploads/certifications'), $filename);
            $validated['image_path'] = '/uploads/certifications/' . $filename;
        }

        Certification::create($validated);
        return redirect()->back()->with('success', 'Certification created.');
    }

    public function destroyCertification(string $id) {
        $cert = Certification::findOrFail($id);
        if ($cert->image_path && file_exists(public_path($cert->image_path))) {
            @unlink(public_path($cert->image_path));
        }
        $cert->delete();
        return redirect()->back()->with('success', 'Certification deleted.');
    }

    public function markMessageRead(string $id) {
        $msg = Message::findOrFail($id);
        $msg->update(['status' => 'read']);
        return redirect()->back()->with('success', 'Message marked as read.');
    }
}
