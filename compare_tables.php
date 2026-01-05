<?php

use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$t1 = DB::table('asset_types')->orderBy('id')->get();
$t2 = DB::table('tipos_activo')->orderBy('id')->get();

echo "asset_types count: " . $t1->count() . "\n";
echo "tipos_activo count: " . $t2->count() . "\n";

$diff = $t1->count() !== $t2->count();

foreach ($t1 as $i => $row1) {
    if (!isset($t2[$i])) {
        echo "Row $i missing in tipos_activo\n";
        continue;
    }
    $row2 = $t2[$i];
    // Compare name (assuming 'name' or 'nombre')
    $n1 = $row1->name ?? $row1->nombre ?? 'N/A';
    $n2 = $row2->name ?? $row2->nombre ?? 'N/A';
    
    if ($n1 !== $n2) {
        echo "Mismatch at ID {$row1->id}: '$n1' vs '$n2'\n";
        $diff = true;
    }
}

if (!$diff) {
    echo "Tables are effectively IDENTICAL in content.\n";
} else {
    echo "Tables differ.\n";
}
