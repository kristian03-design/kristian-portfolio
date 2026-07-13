<?php

$dir = __DIR__ . '/../public/images';
$files = scandir($dir);

foreach ($files as $file) {
    if ($file === '.' || $file === '..') continue;
    $filePath = "$dir/$file";
    if (is_dir($filePath)) continue;

    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    if (!in_array($ext, ['png', 'jpg', 'jpeg'])) continue;

    $name = pathinfo($file, PATHINFO_FILENAME);
    $destWebp = "$dir/$name.webp";

    if (file_exists($destWebp)) {
        echo "WebP version already exists for $file, skipping.\n";
        continue;
    }

    echo "Converting $file to WebP... ";
    
    // Load image
    if ($ext === 'png') {
        $img = @imagecreatefrompng($filePath);
    } else {
        $img = @imagecreatefromjpeg($filePath);
    }

    if (!$img) {
        echo "Failed to load image.\n";
        continue;
    }

    // Enable alpha blend/save for PNG transparency preservation in WebP
    if ($ext === 'png') {
        imagepalettetotruecolor($img);
        imagealphablending($img, true);
        imagesavealpha($img, true);
    }

    // Convert and save
    if (imagewebp($img, $destWebp, 82)) {
        $origSize = filesize($filePath);
        $webpSize = filesize($destWebp);
        $savings = round((1 - ($webpSize / $origSize)) * 100);
        echo "Success! Savings: {$savings}% (" . round($origSize / 1024) . "KB -> " . round($webpSize / 1024) . "KB)\n";
    } else {
        echo "Failed to save WebP.\n";
    }

    imagedestroy($img);
}

echo "Conversion process complete.\n";
