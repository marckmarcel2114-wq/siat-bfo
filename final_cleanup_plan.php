<?php

use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// 1. Tables check
$tables = DB::select('SHOW TABLES');
// Flatten
$tableNames = array_map(function($t){ return array_values((array)$t)[0]; }, $tables);

$softTables = array_filter($tableNames, fn($n) => str_contains($n, 'soft') || str_contains($n, 'lic') || str_contains($n, 'app'));

echo "Software Tables Found: " . implode(', ', $softTables) . "\n";

// 2. Non-Hardware Attributes
$nonHard = DB::table('definiciones_atributos')
    ->where('category', '!=', 'hardware')
    ->get();

echo "Non-Hardware Definitions Count: " . $nonHard->count() . "\n";

// 3. Duplicate Hardware Check
// Looking for similar names like 'Memoria', 'Disco', 'Procesador'
$hardware = DB::table('definiciones_atributos')
    ->where('category', 'hardware')
    ->get();

$names = $hardware->pluck('nombre')->toArray();
sort($names);
echo "Hardware Attributes:\n";
foreach ($names as $n) {
    echo "- $n\n";
}
