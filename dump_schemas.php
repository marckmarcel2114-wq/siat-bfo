<?php

use Illuminate\Support\Facades\Schema;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tables = ['activos', 'historial_cambios', 'historial_laboral'];

foreach ($tables as $t) {
    if (Schema::hasTable($t)) {
        echo "\nTable: $t\n";
        print_r(Schema::getColumnListing($t));
    } else {
        echo "\nTable $t not found.\n";
    }
}
