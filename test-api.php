<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = \App\Models\User::first();
$apiService = new \App\Services\ExternalApiService($user);

echo "API Enabled: " . ($apiService->isEnabled() ? 'Yes' : 'No') . "\n";
echo "API URL: " . $user->external_api_url . "\n";

// Use reflection to get the actual processed URL
$reflection = new ReflectionClass($apiService);
$apiUrlProperty = $reflection->getProperty('apiUrl');
$apiUrlProperty->setAccessible(true);
$processedUrl = $apiUrlProperty->getValue($apiService);
echo "Processed API URL: " . $processedUrl . "\n";
echo "Full endpoint would be: " . $processedUrl . "/api/orders\n\n";

$lead = \App\Models\ProductLead::find(2);
if ($lead) {
    echo "Lead found:\n";
    echo "- Name: {$lead->name}\n";
    echo "- Phone: {$lead->phone}\n";
    echo "- Product: " . ($lead->product ? $lead->product->name : 'N/A') . "\n";
    
    echo "\nTesting API connection (GET /api/orders)...\n";
    $testResult = $apiService->testConnection();
    echo "Test Result: " . json_encode($testResult, JSON_PRETTY_PRINT) . "\n";
    
    echo "\nAttempting to create order (POST /api/orders)...\n";
    $orderData = [
        'customer_name' => $lead->name,
        'customer_phone' => $lead->phone,
        'product_id' => $lead->product_id,
        'product_name' => $lead->product->name ?? 'N/A',
        'product_price' => $lead->product->price ?? 0,
        'note' => $lead->note ?? '',
        'language' => $lead->language,
        'source' => 'landing_page',
        'lead_id' => $lead->id,
        'created_at' => $lead->created_at->toIso8601String(),
    ];
    
    echo "Order Data: " . json_encode($orderData, JSON_PRETTY_PRINT) . "\n\n";
    
    $result = $apiService->createOrder($orderData);
    echo "API Response: " . json_encode($result, JSON_PRETTY_PRINT) . "\n";
}
