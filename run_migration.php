<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Artisan;

try {
    echo "Running migration...\n";
    Artisan::call('migrate', ['-v' => true]);
    echo Artisan::output();
} catch (\Exception $e) {
    echo "Migration Exception:\n";
    echo $e->getMessage();
    echo "\nTrace:\n";
    echo $e->getTraceAsString();
}
