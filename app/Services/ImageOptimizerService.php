<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ImageOptimizerService
{
    /**
     * Process, optimize and resize an image into Thumbnail, Medium, and Original sizes.
     * Output format: WebP.
     *
     * @param UploadedFile $file
     * @param string $slug
     * @return array Array of paths: [thumbnail, medium, original]
     * @throws ValidationException
     */
    public function processAndUpload(UploadedFile $file, string $slug): array
    {
        // 1. Validate File Size (over 10MB)
        if ($file->getSize() > 10 * 1024 * 1024) {
            throw ValidationException::withMessages([
                'image' => 'The image size cannot exceed 10MB.',
            ]);
        }

        // 2. Validate format
        $mime = $file->getMimeType();
        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        if (!in_array($mime, $allowedMimes)) {
            throw ValidationException::withMessages([
                'image' => 'Unsupported image format. Allowed formats: JPEG, PNG, WebP, GIF.',
            ]);
        }

        // Load image resource
        $imageContent = $file->getContent();
        $srcImage = @imagecreatefromstring($imageContent);
        if (!$srcImage) {
            throw ValidationException::withMessages([
                'image' => 'Failed to process the uploaded image. The file might be corrupted.',
            ]);
        }

        $width = imagesx($srcImage);
        $height = imagesy($srcImage);

        // 3. Reject very small images
        if ($width < 300 || $height < 300) {
            imagedestroy($srcImage);
            throw ValidationException::withMessages([
                'image' => 'The image is too small. Please upload an image with a minimum size of 300x300 pixels.',
            ]);
        }

        // Enable alpha blending and save alpha for transparency (if PNG/WebP)
        imagealphablending($srcImage, false);
        imagesavealpha($srcImage, true);

        // Generate sizes
        $timestamp = now()->timestamp;
        
        // Target paths
        $thumbnailPath = "uploads/gallery/{$timestamp}_{$slug}_thumb.webp";
        $mediumPath = "uploads/gallery/{$timestamp}_{$slug}_medium.webp";
        $originalPath = "uploads/gallery/{$timestamp}_{$slug}_original.webp";

        // Create Thumbnail: 400x400 px, cropped automatically from center
        $thumbImage = $this->cropToSquare($srcImage, $width, $height, 400);
        $this->uploadToSupabase($thumbImage, $thumbnailPath);
        imagedestroy($thumbImage);

        // Create Medium: 800px width (aspect-ratio scaled)
        $mediumImage = $this->resizeToWidth($srcImage, $width, $height, 800);
        $this->uploadToSupabase($mediumImage, $mediumPath);
        imagedestroy($mediumImage);

        // Create Original: Max 1600px width/height (aspect-ratio scaled if it exceeds, otherwise original)
        $originalImage = $this->resizeToMax($srcImage, $width, $height, 1600);
        $this->uploadToSupabase($originalImage, $originalPath);
        imagedestroy($originalImage);

        imagedestroy($srcImage);

        return [
            'thumbnail' => '/media/' . $thumbnailPath,
            'medium' => '/media/' . $mediumPath,
            'original' => '/media/' . $originalPath,
        ];
    }

    /**
     * Delete all uploaded image sizes from Supabase Storage.
     *
     * @param array $imageUrls
     * @return void
     */
    public function deleteImages(array $imageUrls): void
    {
        foreach ($imageUrls as $url) {
            if (empty($url)) {
                continue;
            }

            $objectPath = null;
            $urlPath = parse_url($url, PHP_URL_PATH);

            if ($urlPath && str_starts_with($urlPath, '/media/')) {
                $objectPath = ltrim(substr($urlPath, strlen('/media/')), '/');
            }

            if ($objectPath) {
                try {
                    Storage::disk('supabase')->delete($objectPath);
                } catch (\Throwable $e) {
                    Log::error('Failed to delete gallery image from Supabase.', [
                        'url' => $url,
                        'message' => $e->getMessage()
                    ]);
                }
            }
        }
    }

    private function cropToSquare($src, $w, $h, $size)
    {
        $dst = imagecreatetruecolor($size, $size);
        imagealphablending($dst, false);
        imagesavealpha($dst, true);

        $cropSize = min($w, $h);
        $srcX = (int)(($w - $cropSize) / 2);
        $srcY = (int)(($h - $cropSize) / 2);

        imagecopyresampled($dst, $src, 0, 0, $srcX, $srcY, $size, $size, $cropSize, $cropSize);
        return $dst;
    }

    private function resizeToWidth($src, $w, $h, $targetWidth)
    {
        if ($w <= $targetWidth) {
            $targetWidth = $w;
            $targetHeight = $h;
        } else {
            $ratio = $targetWidth / $w;
            $targetHeight = (int)($h * $ratio);
        }

        $dst = imagecreatetruecolor($targetWidth, $targetHeight);
        imagealphablending($dst, false);
        imagesavealpha($dst, true);

        imagecopyresampled($dst, $src, 0, 0, 0, 0, $targetWidth, $targetHeight, $w, $h);
        return $dst;
    }

    private function resizeToMax($src, $w, $h, $maxDim)
    {
        if ($w <= $maxDim && $h <= $maxDim) {
            $targetWidth = $w;
            $targetHeight = $h;
        } else {
            if ($w > $h) {
                $targetWidth = $maxDim;
                $ratio = $maxDim / $w;
                $targetHeight = (int)($h * $ratio);
            } else {
                $targetHeight = $maxDim;
                $ratio = $maxDim / $h;
                $targetWidth = (int)($w * $ratio);
            }
        }

        $dst = imagecreatetruecolor($targetWidth, $targetHeight);
        imagealphablending($dst, false);
        imagesavealpha($dst, true);

        imagecopyresampled($dst, $src, 0, 0, 0, 0, $targetWidth, $targetHeight, $w, $h);
        return $dst;
    }

    private function uploadToSupabase($imageResource, string $path): void
    {
        // Output WebP to temporary stream
        $tempStream = fopen('php://temp', 'r+');
        imagewebp($imageResource, $tempStream, 85); // 85% quality WebP
        rewind($tempStream);
        $content = stream_get_contents($tempStream);
        fclose($tempStream);

        // Upload using storage facade
        try {
            $disk = Storage::disk('supabase');
            $disk->put($path, $content, [
                'visibility' => 'public',
                'ContentType' => 'image/webp',
            ]);
        } catch (\Throwable $e) {
            Log::error('Supabase gallery image upload failed', [
                'path' => $path,
                'message' => $e->getMessage()
            ]);
            throw ValidationException::withMessages([
                'image' => 'The image could not be uploaded to Supabase Storage. Check configuration.',
            ]);
        }
    }
}
