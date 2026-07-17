<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\FileSecurityValidator;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;

class UploadSecurityTest extends TestCase
{
    /**
     * Test that a valid image passes validation.
     */
    public function test_valid_image_passes(): void
    {
        $file = UploadedFile::fake()->image('avatar.jpg', 400, 400);

        try {
            FileSecurityValidator::validate($file, 'image');
            $this->assertTrue(true);
        } catch (ValidationException $e) {
            $this->fail('Valid image was rejected: ' . json_encode($e->errors()));
        }
    }

    /**
     * Test that a valid PDF passes validation as a document.
     */
    public function test_valid_pdf_passes(): void
    {
        $pdfContent = "%PDF-1.5\n%\n1 0 obj\n<<\n/Type /Catalog\n>>\nendobj\n%%EOF";
        $file = UploadedFile::fake()->createWithContent('document.pdf', $pdfContent);

        try {
            FileSecurityValidator::validate($file, 'document');
            $this->assertTrue(true);
        } catch (ValidationException $e) {
            $this->fail('Valid PDF document was rejected: ' . json_encode($e->errors()));
        }
    }

    /**
     * Test that a dangerous extension is blocked.
     */
    public function test_dangerous_extension_blocked(): void
    {
        $file = UploadedFile::fake()->create('malicious.php', 10, 'text/x-php');

        $this->expectException(ValidationException::class);
        FileSecurityValidator::validate($file);
    }

    /**
     * Test that a MIME mismatch (e.g. php file renamed to .png) is blocked.
     */
    public function test_mime_mismatch_blocked(): void
    {
        $file = UploadedFile::fake()->create('spoofed.png', 10, 'text/x-php');

        $this->expectException(ValidationException::class);
        FileSecurityValidator::validate($file);
    }

    /**
     * Test that PHP script content inside a valid image is blocked.
     */
    public function test_php_script_content_blocked(): void
    {
        $content = "<?php echo 'shell'; ?>";
        $file = UploadedFile::fake()->createWithContent('shell.jpg', $content);

        $this->expectException(ValidationException::class);
        FileSecurityValidator::validate($file);
    }

    /**
     * Test that oversized image is blocked (limit 5MB).
     */
    public function test_oversized_image_blocked(): void
    {
        // 6MB image
        $file = UploadedFile::fake()->create('huge.jpg', 6 * 1024, 'image/jpeg');

        $this->expectException(ValidationException::class);
        FileSecurityValidator::validate($file, 'image');
    }

    /**
     * Test that path traversal is blocked on the media route.
     */
    public function test_path_traversal_is_blocked(): void
    {
        $response = $this->get('/media/..%2F..%2F.env');
        $response->assertStatus(400);
    }
}
