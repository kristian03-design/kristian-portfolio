<?php
$url = "https://kldc.vercel.app/media/uploads/projects/1783962514_courtconnect.png";
echo "Fetching: $url\n";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

if ($response === false) {
    echo "Error: " . curl_error($ch) . "\n";
} else {
    echo "HTTP Status Code: $httpCode\n";
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $headers = substr($response, 0, $headerSize);
    $body = substr($response, $headerSize);
    echo "Headers:\n$headers\n";
    echo "Body Length: " . strlen($body) . " bytes\n";
}

curl_close($ch);
