<?php

use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$names = DB::table('activos')
    ->join('modelos', 'activos.modelo_id', '=', 'modelos.id')
    ->where('activos.tipo_activo_id', 1)
    ->distinct()
    ->pluck('modelos.nombre')
    ->toArray();

file_put_contents('models_list.txt', implode("\n", $names));
echo "Written " . count($names) . " names to models_list.txt";
