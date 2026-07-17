<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Project;
use App\Models\Skill;
use App\Models\Experience;
use App\Models\Message;
use App\Models\Certification;
use App\Models\PortfolioGallery;
use App\Services\ImageOptimizerService;
use App\Services\FileSecurityValidator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use App\Mail\MessageReplyMail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    private const SKILL_CATALOG = [
        'Frontend' => [
            'HTML',
            'CSS',
            'JavaScript',
            'TypeScript',
            'Tailwind CSS',
            'Bootstrap',
            'React',
            'Vue.js',
            'Vite',
        ],
        'Backend' => [
            'PHP',
            'Laravel',
            'MySQL',
            'PostgreSQL',
            'Supabase',
            'REST API',
            'Node.js',
            'Express.js',
        ],
        'Mobile' => [
            'Flutter',
            'Dart',
            'Firebase',
        ],
        'Tools' => [
            'Git',
            'GitHub',
            'Docker',
            'Figma',
            'Postman',
            'Vercel',
            'XAMPP',
        ],
    ];

    public function index()
    {
        $projects = Project::orderBy('order')->latest()->get();
        $skills = Skill::orderBy('category')->orderBy('name')->get();
        $experiences = Experience::orderByDesc('start_date')->get();
        $certifications = Certification::orderByDesc('issue_date')->get();
        $messages = Message::latest()->limit(25)->get();
        $unreadCount = Message::where('status', 'unread')->count();
        $latestMsg = $messages->first();
        $skillCatalog = self::SKILL_CATALOG;
        $galleryItems = PortfolioGallery::orderBy('display_order')->orderByDesc('created_at')->get();
        
        return view('admin.admin', compact('projects', 'skills', 'experiences', 'certifications', 'messages', 'skillCatalog', 'unreadCount', 'latestMsg', 'galleryItems'));
    }

    private function clearPortfolioCache(): void
    {
        foreach ([
            'portfolio.public.index',
            'portfolio.public.projects',
            'portfolio.public.api',
            'portfolio.public.api.projects',
            'portfolio.public.api.skills',
            'portfolio.public.api.experiences',
        ] as $key) {
            Cache::forget($key);
        }
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
        // 1. Validate file for security (whitelist, MIME check, getimagesize, PHP tag check)
        FileSecurityValidator::validate($file);

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

        // 2. Secure file naming using UUIDs
        $extension = strtolower($file->getClientOriginalExtension());
        $uuid = (string) Str::uuid();
        $path = trim($directory, '/') . '/' . $uuid . '.' . $extension;

        // 3. EXIF metadata stripping and optimization if GD is loaded
        $fileContent = $file->getContent();
        $mime = $file->getMimeType();

        if (str_starts_with($mime, 'image/') && extension_loaded('gd') && function_exists('imagecreatefromstring')) {
            $srcImage = @imagecreatefromstring($fileContent);
            if ($srcImage) {
                imagealphablending($srcImage, false);
                imagesavealpha($srcImage, true);

                ob_start();
                if ($mime === 'image/jpeg') {
                    imagejpeg($srcImage, null, 90);
                } elseif ($mime === 'image/png') {
                    imagepng($srcImage, null, 9);
                } elseif ($mime === 'image/webp') {
                    imagewebp($srcImage, null, 85);
                } elseif ($mime === 'image/gif') {
                    imagegif($srcImage);
                }
                $cleaned = ob_get_clean();
                imagedestroy($srcImage);

                if ($cleaned !== false && strlen($cleaned) > 0) {
                    $fileContent = $cleaned;
                }
            }
        }

        try {
            $disk = Storage::disk('supabase');
            $disk->put($path, $fileContent, [
                'visibility' => 'public',
                'ContentType' => $mime,
            ]);
        } catch (\Throwable $e) {
            Log::error('Supabase upload failed.', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'bucket' => config('filesystems.disks.supabase.bucket'),
                'endpoint' => config('filesystems.disks.supabase.endpoint'),
                'path' => $path,
            ]);

            throw ValidationException::withMessages([
                'image' => $this->friendlyUploadError($e),
            ]);
        }

        return '/media/' . ltrim($path, '/');
    }

    private function friendlyUploadError(\Throwable $e): string
    {
        $message = $e->getMessage();

        if (str_contains($message, 'NoSuchBucket') || str_contains($message, 'Bucket not found')) {
            return 'Supabase upload failed because the configured bucket was not found. Vercel must use the existing File_Images bucket.';
        }

        if (str_contains($message, 'InvalidAccessKeyId') || str_contains($message, 'SignatureDoesNotMatch')) {
            return 'Supabase upload failed because the storage access key or secret key is invalid.';
        }

        if (str_contains($message, 'AccessDenied')) {
            return 'Supabase upload failed because the storage credentials do not have permission to write to this bucket.';
        }

        return 'The file could not be uploaded to Supabase Storage. Please check the Supabase storage settings in Vercel.';
    }

    private function deleteUpload(?string $pathOrUrl): void
    {
        if (! $pathOrUrl) {
            return;
        }

        $disk = Storage::disk('supabase');
        $diskUrl = rtrim((string) config('filesystems.disks.supabase.url'), '/');
        $objectPath = null;
        $urlPath = parse_url($pathOrUrl, PHP_URL_PATH);

        if ($urlPath && str_starts_with($urlPath, '/media/')) {
            $objectPath = ltrim(substr($urlPath, strlen('/media/')), '/');
        } elseif ($diskUrl && str_starts_with($pathOrUrl, $diskUrl . '/')) {
            $objectPath = ltrim(substr($pathOrUrl, strlen($diskUrl)), '/');
        } elseif (! Str::startsWith($pathOrUrl, ['http://', 'https://'])) {
            $objectPath = ltrim($pathOrUrl, '/');
            $objectPath = str_starts_with($objectPath, 'media/') ? substr($objectPath, strlen('media/')) : $objectPath;
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
        $this->clearPortfolioCache();

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
        $this->clearPortfolioCache();

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
        $this->clearPortfolioCache();

        return redirect()->back()->with('success', 'Project deleted.');
    }

    public function updateProjectDetails(Request $request, string $id) {
        $project = Project::findOrFail($id);

        $request->validate([
            'slug'                 => 'nullable|string|max:255',
            'category'             => 'nullable|string|max:100',
            'status'               => 'nullable|string|max:100',
            'duration'             => 'nullable|string|max:100',
            'role'                 => 'nullable|string|max:100',
            'documentation_url'    => 'nullable|url|max:500',
            'video_demo_url'       => 'nullable|string|max:500',
            'documentation_status' => 'required|string|in:published,under_development',
            // JSON fields are sent as raw JSON strings from the form
            'metrics'              => 'nullable|string',
            'overview'             => 'nullable|string',
            'gallery'              => 'nullable|string',
            'features'             => 'nullable|string',
            'architecture'         => 'nullable|string',
            'challenges'           => 'nullable|string',
            'timeline'             => 'nullable|string',
            'performance'          => 'nullable|string',
            'security_details'     => 'nullable|string',
        ]);

        $jsonFields = ['metrics','overview','gallery','features','architecture','challenges','timeline','performance','security_details'];

        $data = $request->only(['slug','category','status','duration','role','documentation_url','video_demo_url','documentation_status']);

        // Decode each JSON string field; skip if invalid/empty
        foreach ($jsonFields as $field) {
            $raw = $request->input($field);
            if ($raw && $raw !== 'null') {
                $decoded = json_decode($raw, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $data[$field] = $decoded;
                }
            } else {
                $data[$field] = null;
            }
        }

        $project->update($data);
        $this->clearPortfolioCache();

        return redirect()->route('admin.dashboard', ['tab' => 'projects'])
                         ->with('success', 'Project details updated for "' . $project->title . '".');
    }

    public function storeSkill(Request $request) {
        $validated = $request->validate([
            'names' => 'nullable|array',
            'names.*' => 'required|string',
            'custom_skills' => 'nullable|string',
            'custom_category' => 'required_with:custom_skills|in:Frontend,Backend,Mobile,Tools',
            'proficiency_level' => 'required|integer|min:0|max:100',
        ]);

        $catalog = collect(self::SKILL_CATALOG)
            ->flatMap(fn (array $skills, string $category) => collect($skills)->mapWithKeys(fn (string $skill) => [$skill => $category]));

        $selectedSkills = collect($validated['names'] ?? [])
            ->map(fn (string $name) => trim($name))
            ->filter()
            ->values();

        $invalidSkills = $selectedSkills->reject(fn (string $name) => $catalog->has($name));

        if ($invalidSkills->isNotEmpty()) {
            throw ValidationException::withMessages([
                'names' => 'Please select skills from the approved list.',
            ]);
        }

        $customSkills = collect(explode(',', (string) ($validated['custom_skills'] ?? '')))
            ->map(fn (string $name) => trim($name))
            ->filter()
            ->unique(fn (string $name) => strtolower($name))
            ->values();

        $skillsToCreate = $selectedSkills
            ->map(fn (string $name) => [
                'name' => $name,
                'category' => $catalog[$name],
            ])
            ->merge($customSkills->map(fn (string $name) => [
                'name' => $name,
                'category' => $validated['custom_category'] ?? 'Tools',
            ]))
            ->unique(fn (array $skill) => strtolower($skill['name']))
            ->values();

        if ($skillsToCreate->isEmpty()) {
            throw ValidationException::withMessages([
                'names' => 'Select at least one skill or enter a custom skill.',
            ]);
        }

        $existingSkills = Skill::whereIn(
            \Illuminate\Support\Facades\DB::raw('lower(name)'),
            $skillsToCreate->pluck('name')->map(fn (string $name) => strtolower($name))
        )->pluck('name');

        if ($existingSkills->isNotEmpty()) {
            throw ValidationException::withMessages([
                'names' => 'One or more selected skills are already in your list.',
            ]);
        }

        $skillsToCreate->each(function (array $skill) use ($validated) {
            Skill::create([
                'name' => $skill['name'],
                'category' => $skill['category'],
                'proficiency_level' => $validated['proficiency_level'],
            ]);
        });

        $this->clearPortfolioCache();

        return redirect()->back()->with('success', $skillsToCreate->count() === 1 ? 'Skill created.' : 'Skills created.');
    }
    public function destroySkill(string $id) {
        Skill::findOrFail($id)->delete();
        $this->clearPortfolioCache();

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
        $this->clearPortfolioCache();

        return redirect()->back()->with('success', 'Experience created.');
    }
    public function destroyExperience(string $id) {
        Experience::findOrFail($id)->delete();
        $this->clearPortfolioCache();

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
        $this->clearPortfolioCache();

        return redirect()->back()->with('success', 'Certification created.');
    }

    public function destroyCertification(string $id) {
        $cert = Certification::findOrFail($id);
        $this->deleteUpload($cert->image_path);
        $cert->delete();
        $this->clearPortfolioCache();

        return redirect()->back()->with('success', 'Certification deleted.');
    }

    public function markMessageRead(string $id) {
        $msg = Message::findOrFail($id);
        $msg->update(['status' => 'read']);
        return redirect()->back()->with('success', 'Message marked as read.');
    }

    public function replyMessage(Request $request, string $id) {
        $msg = Message::findOrFail($id);

        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        try {
            Mail::to($msg->email)->send(new MessageReplyMail($validated['subject'], $validated['message'], $msg));
            
            // Mark the message as read after successful send
            $msg->update(['status' => 'read']);
            
            return redirect()->back()->with('success', 'Reply sent successfully.');
        } catch (\Throwable $e) {
            Log::error('Failed to send reply email: ' . $e->getMessage());
            return redirect()->back()->withErrors(['message' => 'Failed to send reply email: ' . $e->getMessage()]);
        }
    }

    public function storeGalleryItem(Request $request, ImageOptimizerService $imageService) {
        try {
            $validated = $request->validate([
                'title' => 'nullable|string|max:255',
                'short_description' => 'nullable|string',
                'category' => 'required|string|in:Sports,Music,Travel,Photography,Gaming,Workstation,Coffee,Learning,Events,Lifestyle,Nature,Other',
                'image' => 'required|image|max:10240',
                'display_order' => 'integer|min:0',
                'is_featured' => 'boolean',
                'is_published' => 'boolean',
            ]);

            $slug = Str::slug($validated['title'] ?? $validated['category'] ?? 'gallery');
            $imageUrls = $imageService->processAndUpload($request->file('image'), $slug);

            PortfolioGallery::create([
                'title' => $validated['title'] ?? null,
                'short_description' => $validated['short_description'] ?? null,
                'category' => $validated['category'],
                'image' => $imageUrls,
                'display_order' => $validated['display_order'] ?? 0,
                'is_featured' => $request->boolean('is_featured'),
                'is_published' => $request->boolean('is_published', true),
            ]);

            $this->clearPortfolioCache();

            return redirect()->back()->with('success', 'Gallery item uploaded successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error('Gallery item upload failed: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return redirect()->back()->withInput()->withErrors(['image' => 'Upload failed: ' . $e->getMessage()]);
        }
    }

    public function updateGalleryItem(Request $request, string $id, ImageOptimizerService $imageService) {
        try {
            $item = PortfolioGallery::findOrFail($id);
            
            $validated = $request->validate([
                'title' => 'nullable|string|max:255',
                'short_description' => 'nullable|string',
                'category' => 'required|string|in:Sports,Music,Travel,Photography,Gaming,Workstation,Coffee,Learning,Events,Lifestyle,Nature,Other',
                'image' => 'nullable|image|max:10240',
                'display_order' => 'integer|min:0',
                'is_featured' => 'boolean',
                'is_published' => 'boolean',
            ]);

            $updateData = [
                'title' => $validated['title'] ?? null,
                'short_description' => $validated['short_description'] ?? null,
                'category' => $validated['category'],
                'display_order' => $validated['display_order'] ?? 0,
                'is_featured' => $request->boolean('is_featured'),
                'is_published' => $request->boolean('is_published'),
            ];

            if ($request->hasFile('image')) {
                if ($item->image) {
                    $imageService->deleteImages($item->image);
                }
                
                $slug = Str::slug($validated['title'] ?? $validated['category'] ?? 'gallery');
                $updateData['image'] = $imageService->processAndUpload($request->file('image'), $slug);
            }

            $item->update($updateData);
            $this->clearPortfolioCache();

            return redirect()->back()->with('success', 'Gallery item updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error('Gallery item update failed: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return redirect()->back()->withInput()->withErrors(['image' => 'Update failed: ' . $e->getMessage()]);
        }
    }

    public function destroyGalleryItem(string $id, ImageOptimizerService $imageService) {
        try {
            $item = PortfolioGallery::findOrFail($id);
            
            if ($item->image) {
                $imageService->deleteImages($item->image);
            }

            $item->delete();
            $this->clearPortfolioCache();

            return redirect()->back()->with('success', 'Gallery item deleted successfully.');
        } catch (\Throwable $e) {
            Log::error('Gallery item deletion failed: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return redirect()->back()->withErrors(['image' => 'Deletion failed: ' . $e->getMessage()]);
        }
    }

    public function toggleGalleryItemPublished(string $id) {
        $item = PortfolioGallery::findOrFail($id);
        $item->is_published = !$item->is_published;
        $item->save();
        
        $this->clearPortfolioCache();

        return response()->json([
            'success' => true,
            'is_published' => $item->is_published,
            'message' => $item->is_published ? 'Gallery item published.' : 'Gallery item unpublished.'
        ]);
    }

    public function toggleGalleryItemFeatured(string $id) {
        $item = PortfolioGallery::findOrFail($id);
        $item->is_featured = !$item->is_featured;
        $item->save();
        
        $this->clearPortfolioCache();

        return response()->json([
            'success' => true,
            'is_featured' => $item->is_featured,
            'message' => $item->is_featured ? 'Gallery item featured.' : 'Gallery item unfeatured.'
        ]);
    }

    public function reorderGalleryItems(Request $request) {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:portfolio_gallery,id'
        ]);

        foreach ($request->input('ids') as $index => $id) {
            PortfolioGallery::where('id', $id)->update(['display_order' => $index + 1]);
        }

        $this->clearPortfolioCache();

        return response()->json([
            'success' => true,
            'message' => 'Gallery reordered successfully.'
        ]);
    }
}
