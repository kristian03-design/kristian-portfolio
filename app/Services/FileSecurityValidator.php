<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class FileSecurityValidator
{
    /**
     * Validate an uploaded file for security.
     *
     * @param UploadedFile $file
     * @param string $type 'image'|'document'|'auto'
     * @return void
     * @throws ValidationException
     */
    public static function validate(UploadedFile $file, string $type = 'auto'): void
    {
        $filename = $file->getClientOriginalName();
        $ip = request()->ip();
        $userId = Auth::id() ?: 'guest';
        $timestamp = now()->toIso8601String();

        // 1. Basic Laravel File Check
        if (!$file->isValid()) {
            self::logAndThrow(
                $filename,
                'Invalid file upload state.',
                compact('ip', 'userId', 'timestamp')
            );
        }

        // 2. Get client extension & verify whitelist
        $extension = strtolower($file->getClientOriginalExtension());
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'pdf'];
        if (!in_array($extension, $allowedExtensions, true)) {
            self::logAndThrow(
                $filename,
                "Forbidden file extension: {$extension}",
                compact('ip', 'userId', 'timestamp'),
                'The uploaded file extension is not allowed.'
            );
        }

        // 3. True MIME type detection using server-side Fileinfo
        $filePath = $file->getPathname();
        $finfo = @finfo_open(FILEINFO_MIME_TYPE);
        $detectedMime = $finfo ? @finfo_file($finfo, $filePath) : null;
        if ($finfo) {
            @finfo_close($finfo);
        }

        if (!$detectedMime) {
            $detectedMime = @mime_content_type($filePath) ?: $file->getMimeType();
        }

        // Verify MIME type is in allowed whitelist
        $allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'application/pdf'];
        if (!in_array($detectedMime, $allowedMimes, true)) {
            self::logAndThrow(
                $filename,
                "Forbidden detected MIME type: {$detectedMime}",
                compact('ip', 'userId', 'timestamp'),
                'The uploaded file type is not allowed.'
            );
        }

        // 4. Verify Extension matches MIME type
        $mismatch = false;
        if ($detectedMime === 'application/pdf' && $extension !== 'pdf') {
            $mismatch = true;
        } elseif ($detectedMime === 'image/jpeg' && !in_array($extension, ['jpg', 'jpeg'], true)) {
            $mismatch = true;
        } elseif ($detectedMime === 'image/png' && $extension !== 'png') {
            $mismatch = true;
        } elseif ($detectedMime === 'image/webp' && $extension !== 'webp') {
            $mismatch = true;
        } elseif ($detectedMime === 'image/gif' && $extension !== 'gif') {
            $mismatch = true;
        }

        if ($mismatch) {
            self::logAndThrow(
                $filename,
                "MIME type mismatch: extension .{$extension} does not match detected MIME type {$detectedMime}",
                compact('ip', 'userId', 'timestamp'),
                'File extension does not match the actual file type.'
            );
        }

        // Determine specific limit
        $isImage = str_starts_with($detectedMime, 'image/');
        $isPdf = $detectedMime === 'application/pdf';

        if ($type === 'image' && !$isImage) {
            self::logAndThrow(
                $filename,
                "Expected image, got {$detectedMime}",
                compact('ip', 'userId', 'timestamp'),
                'The uploaded file is not a valid image.'
            );
        }

        if ($type === 'document' && !$isPdf) {
            self::logAndThrow(
                $filename,
                "Expected document, got {$detectedMime}",
                compact('ip', 'userId', 'timestamp'),
                'The uploaded file is not a valid PDF document.'
            );
        }

        // 5. Size limits
        $fileSize = $file->getSize();
        $maxSize = $isPdf ? 10 * 1024 * 1024 : 5 * 1024 * 1024; // 10MB for doc, 5MB for image
        if ($fileSize > $maxSize) {
            $maxMb = $isPdf ? 10 : 5;
            self::logAndThrow(
                $filename,
                "File size {$fileSize} bytes exceeds limit of {$maxSize} bytes",
                compact('ip', 'userId', 'timestamp'),
                "The file is too large. Maximum size allowed is {$maxMb}MB."
            );
        }

        // 6. Real Image verification
        if ($isImage) {
            // Check image structure via getimagesize
            $imageInfo = @getimagesize($filePath);
            if ($imageInfo === false) {
                self::logAndThrow(
                    $filename,
                    'Image validation failed via getimagesize(). File is corrupted or spoofed.',
                    compact('ip', 'userId', 'timestamp'),
                    'The uploaded image is invalid or corrupted.'
                );
            }
        }

        // 7. Malicious Signature Scan (PHP script tags)
        $content = @file_get_contents($filePath);
        if ($content !== false) {
            $signatures = ['<?php', '<?=', '<script language="php"'];
            foreach ($signatures as $sig) {
                if (stripos($content, $sig) !== false) {
                    self::logAndThrow(
                        $filename,
                        "Executable PHP signature detected: '{$sig}'",
                        compact('ip', 'userId', 'timestamp'),
                        'Upload blocked: The file contains suspicious signatures.'
                    );
                }
            }
        }
    }

    private static function logAndThrow(string $filename, string $reason, array $context, string $userMessage = 'Invalid file upload.'): void
    {
        Log::warning("File Upload Rejected: {$reason}", array_merge($context, [
            'filename' => $filename,
        ]));

        throw ValidationException::withMessages([
            'image' => [$userMessage],
            'certificate_image' => [$userMessage],
        ]);
    }
}
