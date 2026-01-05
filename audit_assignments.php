<?php

use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$assignments = DB::table('definicion_atributo_tipo_activo')
    ->join('tipos_activo', 'definicion_atributo_tipo_activo.tipo_activo_id', '=', 'tipos_activo.id')
    ->join('definiciones_atributos', 'definicion_atributo_tipo_activo.definicion_atributo_id', '=', 'definiciones_atributos.id')
    ->orderBy('tipos_activo.nombre')
    ->select('tipos_activo.nombre as tipo', 'definiciones_atributos.nombre as def', 'definiciones_atributos.category')
    ->get();

$grouped = $assignments->groupBy('tipo');

foreach ($grouped as $tipo => $items) {
    echo "Tipo: $tipo\n";
    foreach ($items as $item) {
        echo "  - {$item->def} ({$item->category})\n";
    }
    echo "\n";
}
