<?php

$filePath = __DIR__ . '/../database/seeders/PortfolioRedesignSeeder.php';
$content = file_get_contents($filePath);

// Detect UTF-16
if (str_starts_with($content, "\xFF\xFE") || str_starts_with($content, "\xFE\xFF")) {
    echo "UTF-16 encoding detected. Converting to UTF-8...\n";
    $content = mb_convert_encoding($content, 'UTF-8', 'UTF-16');
}

// Replace png references with webp for mockups
$updatedContent = preg_replace('/\.png/i', '.webp', $content);

// Save back as UTF-8
file_put_contents($filePath, $updatedContent);
echo "PortfolioRedesignSeeder.php successfully updated and saved as UTF-8.\n";
