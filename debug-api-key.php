<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = \App\Models\User::first();

echo "=== API Configuration Debug ===\n\n";
echo "Enabled: " . ($user->external_api_enabled ? 'YES' : 'NO') . "\n";
echo "URL: " . $user->external_api_url . "\n";

if ($user->external_api_key_encrypted) {
    try {
        $decryptedKey = \Illuminate\Support\Facades\Crypt::decryptString($user->external_api_key_encrypted);
        echo "API Key (first 10 chars): " . substr($decryptedKey, 0, 10) . "...\n";
        echo "API Key (last 10 chars): ..." . substr($decryptedKey, -10) . "\n";
        echo "API Key length: " . strlen($decryptedKey) . " characters\n";
        echo "Starts with 'capi_': " . (str_starts_with($decryptedKey, 'capi_') ? 'YES' : 'NO') . "\n\n";
        
        // Test the actual HTTP request
        echo "=== Testing HTTP Request ===\n";
        $url = rtrim($user->external_api_url, '/') . '/api/orders';
        echo "Endpoint: " . $url . "\n";
        echo "Header: Authorization: Bearer " . substr($decryptedKey, 0, 10) . "..." . substr($decryptedKey, -10) . "\n\n";
        
        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'Authorization' => 'Bearer ' . $decryptedKey,
            'Accept' => 'application/json',
        ])
        ->timeout(10)
        ->get($url);
        
        echo "Response Status: " . $response->status() . "\n";
        echo "Response Body: " . $response->body() . "\n";
        
    } catch (\Throwable $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
} else {
    echo "API Key: NOT SET\n";
}
