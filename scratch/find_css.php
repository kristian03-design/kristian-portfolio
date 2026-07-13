<?php
$lines = file(__DIR__ . '/../resources/css/portfolio.css');
foreach ($lines as $i => $line) {
    if (str_contains($line, '.proj-image') || str_contains($line, '.proj-thumb') || str_contains($line, 'proj-card-thumb-link')) {
        echo "Line " . ($i + 1) . ": " . trim($line) . "\n";
        // print next 10 lines
        for ($j = 1; $j <= 15; $j++) {
            if (isset($lines[$i + $j])) {
                echo "  " . ($i + $j + 1) . ": " . trim($lines[$i + $j]) . "\n";
            }
        }
        echo "-------------------\n";
    }
}
