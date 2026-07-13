<?php
$file = __DIR__ . '/../public/images/btech-admissions.webp';
if (file_exists($file)) {
    echo "Size: " . filesize($file) . "\n";
    if (function_exists('imagecreatefromwebp')) {
        $img = @imagecreatefromwebp($file);
        if ($img) {
            echo "Valid WebP image! Width: " . imagesx($img) . " Height: " . imagesy($img) . "\n";
        } else {
            echo "Corrupted WebP image or GD doesn't support WebP!\n";
        }
    } else {
        echo "imagecreatefromwebp function not available.\n";
    }
} else {
    echo "File does not exist!\n";
}
