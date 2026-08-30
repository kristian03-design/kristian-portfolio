<?php
// Check what the live site actually renders in the skills section

$ch = curl_init('https://kldc.vercel.app/');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$html = curl_exec($ch);
curl_close($ch);

// Check for actual skill content markers from the blade template
$checks = [
    'sk-icon-cell (skill wrapper)'   => str_contains($html, 'sk-icon-cell'),
    'sk-icon-name (skill label)'     => str_contains($html, 'sk-icon-name'),
    'sk-group (category group)'      => str_contains($html, 'sk-group'),
    'sk-icon-grid'                   => str_contains($html, 'sk-icon-grid'),
    'Empty state message'            => str_contains($html, 'Skills will appear here'),
    'Project BTECH'                  => str_contains($html, 'BTECH Admissions Office System'),
    'Cert IN2ITion'                  => str_contains($html, 'IN2ITion'),
    'Cert Digital Armor'             => str_contains($html, 'Digital Armor'),
];

echo "=== LIVE VERCEL HTML CHECKS ===\n";
foreach ($checks as $label => $result) {
    echo ($result ? "✓" : "✗") . " {$label}\n";
}

// Extract the skills section from the HTML for debugging
$start = strpos($html, 'id="skills"');
if ($start !== false) {
    $end = strpos($html, '</section>', $start);
    $skillsHtml = substr($html, $start, $end - $start + 10);
    echo "\n=== Skills section HTML (first 1000 chars) ===\n";
    echo substr($skillsHtml, 0, 1000) . "\n";
}

// Also check the cache situation on Vercel - does it vary per request?
echo "\n=== POST /login CSRF test (proper cookie passing) ===\n";

// Step 1: GET to get cookies + token
$ch1 = curl_init('https://kldc.vercel.app/login');
curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch1, CURLOPT_HEADER, true);
curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch1, CURLOPT_FOLLOWLOCATION, false);
$res1 = curl_exec($ch1);
$h1len = curl_getinfo($ch1, CURLINFO_HEADER_SIZE);
$headers1 = substr($res1, 0, $h1len);
$body1 = substr($res1, $h1len);
curl_close($ch1);

// Extract cookies
preg_match_all('/Set-Cookie:\s*([^=]+=[^;]+)/i', $headers1, $ckMatches);
$cookies = $ckMatches[1] ?? [];
echo "Cookies from GET /login: " . count($cookies) . "\n";

// Extract CSRF token
preg_match('/name="_token"\s+value="([^"]+)"/', $body1, $tokenMatch);
$token = $tokenMatch[1] ?? null;
echo "CSRF token: " . ($token ? substr($token, 0, 10) . '...' : 'NOT FOUND') . "\n";

if ($token && !empty($cookies)) {
    // Step 2: POST with cookies
    $ch2 = curl_init('https://kldc.vercel.app/login');
    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch2, CURLOPT_HEADER, true);
    curl_setopt($ch2, CURLOPT_POST, true);
    curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch2, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch2, CURLOPT_POSTFIELDS, http_build_query([
        '_token'   => $token,
        'email'    => 'hkristianlloyd2@gmail.com',
        'password' => 'admin123',
    ]));
    curl_setopt($ch2, CURLOPT_COOKIE, implode('; ', $cookies));
    $res2 = curl_exec($ch2);
    $code2 = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
    $h2len = curl_getinfo($ch2, CURLINFO_HEADER_SIZE);
    $headers2 = substr($res2, 0, $h2len);
    $body2 = substr($res2, $h2len);
    preg_match('/Location:\s*([^\r\n]+)/i', $headers2, $loc);
    curl_close($ch2);

    echo "POST /login status: {$code2}\n";
    if ($code2 === 302) {
        echo "✓ Login redirected to: " . trim($loc[1] ?? 'unknown') . "\n";
    } elseif ($code2 === 419) {
        echo "✗ 419 CSRF mismatch still\n";
    } elseif ($code2 === 500) {
        $excerpt = substr(strip_tags($body2), 0, 400);
        echo "✗ 500 Error: {$excerpt}\n";
    } else {
        echo "Response body excerpt: " . substr(strip_tags($body2), 0, 300) . "\n";
    }
}
