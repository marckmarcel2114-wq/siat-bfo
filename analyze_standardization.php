<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$pairs = [
    'asset_types' => 'tipos_activo',
    'brands' => 'marcas',
    'models' => 'modelos',
    'owners' => 'propietarios',
    'branches' => 'sucursales', // or maybe 'sedes'?
    'cities' => 'ciudades',
    'asset_assignments' => 'asignaciones',
    'admin_tasks' => 'tareas_admin',
    'user_job_histories' => 'historial_laboral',
    'assets' => 'activos', 
    'asset_status' => 'estados_activo',
    'criticality' => 'niveles_criticidad',
];

echo "--- ANALYSIS REPORT ---\n";

foreach ($pairs as $eng => $esp) {
    $engExists = Schema::hasTable($eng);
    $espExists = Schema::hasTable($esp);
    
    $engCount = $engExists ? DB::table($eng)->count() : 0;
    $espCount = $espExists ? DB::table($esp)->count() : 0;

    echo "Pair: [$eng] ($engCount) vs [$esp] ($espCount)\n";
    
    if ($engExists && !$espExists) {
        if ($engCount > 0) {
            echo "  Result: RENAME '$eng' TO '$esp'\n";
        } else {
            echo "  Result: DELETE '$eng' (Empty)\n";
        }
    } elseif ($engExists && $espExists) {
        if ($engCount == 0) {
             echo "  Result: DELETE '$eng' (Empty, Spanish exists)\n";
        } else {
             echo "  Result: MANUAL CHECK (Both exist and have data)\n";
        }
    } elseif (!$engExists && $espExists) {
        echo "  Result: OK (Already Spanish)\n";
    } else {
        echo "  Result: ??? (Neither exist)\n";
    }
    echo "-------------------------\n";
}
