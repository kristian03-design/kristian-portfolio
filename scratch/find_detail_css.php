<?php
$lines = file(__DIR__ . '/../resources/css/portfolio.css');
foreach ($lines as $i => $line) {
    if (str_contains($line, 'featured-image-wrapper') || str_contains($line, 'carousel-slide')) {
        echo "Line " . ($i + 1) . ": " . trim($line) . "\n";
        for ($j = 1; $j <= 15; $j++) {
            if (isset($lines[$i + $j])) {
                echo "  " . ($i + $j + 1) . ": " . trim($lines[$i + $j]) . "\n";
            }
        }
        echo "-------------------\n";
    }
}
