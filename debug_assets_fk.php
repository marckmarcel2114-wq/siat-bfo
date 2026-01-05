<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

$cols = Schema::getColumnListing('activos');
echo "Assets columns: " . implode(', ', $cols) . "\n";

$orphans = DB::table('activos')
    ->leftJoin('ubicaciones', 'activos.ubicacion_id', '=', 'ubicaciones.id')
    ->whereNull('ubicaciones.id')
    ->count();

echo "Orphan assets (invalid ubicacion_id): $orphans\n";

$sample = DB::table('activos')->take(5)->get();
foreach ($sample as $s) {
    echo "Asset ID: {$s->id} | Ubicacion ID: " . ($s->ubicacion_id ?? 'NULL') . "\n";
}
