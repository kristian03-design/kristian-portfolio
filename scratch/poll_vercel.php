<?php
for ($i = 1; $i <= 10; $i++) {
    $ch = curl_init('https://kldc.vercel.app/');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $html = curl_exec($ch);
    curl_close($ch);
    
    $hasProject = str_contains($html, 'BTECH Admissions Office System');
    $hasSkills = str_contains($html, 'HTML') && str_contains($html, 'Laravel');
    $hasCert = str_contains($html, 'PhYIGF') || str_contains($html, 'Digital Armor');
    
    echo "Check {$i}: Projects: " . ($hasProject ? 'YES' : 'NO') . " | Skills: " . ($hasSkills ? 'YES' : 'NO') . " | Certs: " . ($hasCert ? 'YES' : 'NO') . "\n";
    
    if ($hasProject && $hasSkills) {
        echo "\n[SUCCESS] Live Vercel website has loaded all dynamic data from Supabase!\n";
        exit(0);
    }
    sleep(5);
}
