<?php
// Test all Vercel routes

function testUrl($url, $post = null, $cookies = []) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    if ($post) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
    }
    
    if (!empty($cookies)) {
        curl_setopt($ch, CURLOPT_COOKIE, implode('; ', $cookies));
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $headers = substr($response, 0, $headerSize);
    $body = substr($response, $headerSize);
    
    // Extract cookies
    preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $headers, $matches);
    $newCookies = $matches[1] ?? [];
    
    curl_close($ch);
    
    return [
        'code' => $httpCode,
        'headers' => $headers,
        'body' => $body,
        'cookies' => $newCookies
    ];
}

echo "1. Testing GET https://kldc.vercel.app/\n";
$home = testUrl('https://kldc.vercel.app/');
echo "   Status: {$home['code']}\n";
echo "   Body length: " . strlen($home['body']) . " bytes\n";
if ($home['code'] >= 400) {
    echo "   Error excerpt: " . substr(strip_tags($home['body']), 0, 300) . "\n";
}

echo "\n2. Testing GET https://kldc.vercel.app/login\n";
$loginGet = testUrl('https://kldc.vercel.app/login');
echo "   Status: {$loginGet['code']}\n";
echo "   Cookies received: " . count($loginGet['cookies']) . "\n";
if ($loginGet['code'] >= 400) {
    echo "   Error excerpt: " . substr(strip_tags($loginGet['body']), 0, 300) . "\n";
}

// Extract CSRF token from login form
preg_match('/name="_token"\s+value="([^"]+)"/', $loginGet['body'], $tokenMatch);
$token = $tokenMatch[1] ?? null;
echo "   CSRF Token: " . ($token ? substr($token, 0, 10) . '...' : 'NOT FOUND') . "\n";

if ($token) {
    echo "\n3. Testing POST https://kldc.vercel.app/login\n";
    $postData = [
        '_token' => $token,
        'email' => 'hkristianlloyd2@gmail.com',
        'password' => 'admin123'
    ];
    $loginPost = testUrl('https://kldc.vercel.app/login', $postData, $loginGet['cookies']);
    echo "   POST Status: {$loginPost['code']}\n";
    if ($loginPost['code'] >= 400) {
        echo "   POST Error excerpt: " . substr(strip_tags($loginPost['body']), 0, 300) . "\n";
    } elseif ($loginPost['code'] == 302) {
        preg_match('/Location:\s*([^\r\n]+)/i', $loginPost['headers'], $loc);
        echo "   Redirected to: " . ($loc[1] ?? 'unknown') . "\n";
    }
}
