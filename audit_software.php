<?php

use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// 1. List all tables finding matches for 'soft', 'app', 'lic'
echo "--- Potential Software Tables ---\n";
$tables = DB::select('SHOW TABLES');
// Key is usually Tables_in_dbName
$dbName = DB::connection()->getDatabaseName();
$key = "Tables_in_" . $dbName;

foreach ($tables as $t) {
    $name = $t->$key;
    if (str_contains($name, 'soft') || str_contains($name, 'app') || str_contains($name, 'lic') || str_contains($name, 'prog')) {
        echo "- $name\n";
    }
}

// 2. Count Categories in definiciones_atributos
echo "\n--- Attribute Categories ---\n";
$cats = DB::table('definiciones_atributos')
    ->select('category', DB::raw('count(*) as count'))
    ->groupBy('category')
    ->get();

foreach ($cats as $c) {
    echo "{$c->category}: {$c->count}\n";
}

// 3. List actual Definition names for non-hardware
echo "\n--- Non-Hardware Definitions ---\n";
$bad = DB::table('definiciones_atributos')
    ->where('category', '!=', 'hardware')
    ->get();

foreach ($bad as $b) {
    echo "ID {$b->id}: {$b->nombre} ({$b->category})\n";
}
