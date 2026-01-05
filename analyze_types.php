<?php

use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Buffer output
ob_start();

echo "--- asset_types (12 rows) ---\n";
try {
    $t1 = DB::table('asset_types')->orderBy('id')->get();
    foreach ($t1 as $r) {
        echo "ID: {$r->id} | Name: " . ($r->name ?? $r->nombre ?? 'N/A') . "\n";
    }
} catch (\Exception $e) { echo "Error reading asset_types: ".$e->getMessage(); }

echo "\n--- tipos_activo (9 rows) ---\n";
try {
    $t2 = DB::table('tipos_activo')->orderBy('id')->get();
    foreach ($t2 as $r) {
        echo "ID: {$r->id} | Name: " . ($r->name ?? $r->nombre ?? 'N/A') . "\n";
    }
} catch (\Exception $e) { echo "Error reading tipos_activo: ".$e->getMessage(); }

echo "\n--- Activos Usage ---\n";
try {
    $col = Illuminate\Support\Facades\Schema::hasColumn('activos', 'tipo_activo_id') ? 'tipo_activo_id' : 'asset_type_id';
    echo "Using FK column: $col\n";
    $usage = DB::table('activos')->select($col, DB::raw('count(*) as count'))->groupBy($col)->get();

    foreach ($usage as $u) {
        $id = $u->$col;
        echo "ID $id used by {$u->count} assets.\n";
    }
} catch (\Exception $e) { echo "Error reading assets: ".$e->getMessage(); }

$output = ob_get_clean();
file_put_contents('types_analysis.txt', $output);
echo "Analysis written to types_analysis.txt\n";
