<?php

use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Find IDs dynamically to avoid encoding hardcoding issues in SQL if possible
$defs = DB::table('definiciones_atributos')
          ->where('nombre', 'like', 'Procesador%')
          ->orWhere('nombre', 'like', 'Generac%') // Flexible match
          ->get();

foreach ($defs as $def) {
    echo "\nDefinition: {$def->nombre} (ID: {$def->id})\n";
    $opts = DB::table('opciones_atributos')
              ->where('definicion_atributo_id', $def->id)
              ->pluck('nombre');
    
    foreach ($opts as $opt) {
        echo "- $opt\n";
    }
}
