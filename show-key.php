<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = \App\Models\User::first();
$key = \Illuminate\Support\Facades\Crypt::decryptString($user->external_api_key_encrypted);
echo $key;
