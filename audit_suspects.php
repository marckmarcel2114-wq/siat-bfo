<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$suspects = [
    'asset_types', 'tipos_activo', 'tipos_activos',
    'brands', 'marcas',
    'models', 'modelos',
    'owners', 'propietarios',
    'branches', 'sucursales',
    'cities', 'ciudades',
    'asset_assignments', 'asignaciones',
    'admin_tasks', 'tareas_admin',
    'user_job_histories', 'historial_laboral',
    'activos', 'assets'
];

$results = [];
foreach ($suspects as $table) {
    if (Schema::hasTable($table)) {
        $results[$table] = DB::table($table)->count();
    } else {
        $results[$table] = -1; // Not Found
    }
}
echo json_encode($results);
