<?php

use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$items = DB::table('definiciones_atributos')
    ->whereNotNull('opciones')
    ->where('opciones', '!=', '[]')
    ->get(['id', 'nombre', 'opciones']);

foreach ($items as $item) {
    echo "ID: {$item->id} | Name: {$item->nombre}\n";
    // echo "Options: " . substr($item->opciones, 0, 100) . "...\n"; 
}

echo "Total: " . $items->count();
