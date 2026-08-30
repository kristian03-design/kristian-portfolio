<?php
echo "Waiting 50 seconds for Vercel to deploy...\n";
sleep(50);

function testUrl($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $html = curl_exec($ch);
    curl_close($ch);
    return $html;
}

echo "Checking live site...\n";

// Check home page
$html = testUrl('https://kldc.vercel.app/');

// More specific checks - looking for actual rendered data strings
$hasProject = str_contains($html, 'BTECH Admissions Office System');
$hasSkillsSection = str_contains($html, 'skill-chip') || str_contains($html, 'skill-card') || str_contains($html, 'skill-name');
$hasCert = str_contains($html, 'IN2ITion') || str_contains($html, 'Digital Armor');
$missingMsg = str_contains($html, 'Skills will appear here');
$emptyProjectMsg = str_contains($html, 'Projects will appear here') || str_contains($html, 'no projects');

echo "Project (BTECH): " . ($hasProject ? "✓ VISIBLE" : "✗ MISSING") . "\n";
echo "Skills rendered: " . ($hasSkillsSection ? "✓ VISIBLE" : "✗ MISSING") . "\n";
echo "Skills placeholder: " . ($missingMsg ? "YES (still empty)" : "NO (good)") . "\n";
echo "Certifications: " . ($hasCert ? "✓ VISIBLE" : "✗ MISSING") . "\n";

// Check login POST
echo "\nChecking GET /login...\n";
$loginHtml = testUrl('https://kldc.vercel.app/login');
preg_match('/name="_token"\s+value="([^"]+)"/', $loginHtml, $m);
$token = $m[1] ?? null;
echo "CSRF token: " . ($token ? "✓ present" : "✗ MISSING") . "\n";

if ($token) {
    $cookies = [];
    preg_match_all('/Set-Cookie:\s*([^\r\n]+)/i', '', $matches);
    
    $ch = curl_init('https://kldc.vercel.app/login');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $res = curl_exec($ch);
    $headerLen = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $headers = substr($res, 0, $headerLen);
    preg_match_all('/Set-Cookie:\s*([^;]+)/i', $headers, $cks);
    curl_close($ch);
    
    $ch2 = curl_init('https://kldc.vercel.app/login');
    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch2, CURLOPT_HEADER, true);
    curl_setopt($ch2, CURLOPT_POST, true);
    curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch2, CURLOPT_POSTFIELDS, http_build_query([
        '_token' => $token,
        'email'  => 'hkristianlloyd2@gmail.com',
        'password' => 'admin123',
    ]));
    curl_setopt($ch2, CURLOPT_COOKIE, implode('; ', $cks[1] ?? []));
    curl_setopt($ch2, CURLOPT_FOLLOWLOCATION, false);
    $res2 = curl_exec($ch2);
    $code2 = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
    $h2 = substr($res2, 0, curl_getinfo($ch2, CURLINFO_HEADER_SIZE));
    preg_match('/Location:\s*([^\r\n]+)/i', $h2, $loc);
    curl_close($ch2);
    
    echo "POST /login status: {$code2} " . ($code2 == 302 ? "✓ REDIRECT to " . ($loc[1] ?? '?') : "✗ FAILED") . "\n";
}
