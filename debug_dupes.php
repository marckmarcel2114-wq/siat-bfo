<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Ubicacion;

$name = 'Sucursal La Paz';
$all = Ubicacion::where('nombre', 'like', "%$name%")->get();
echo "Found " . $all->count() . " matches for [$name]\n";
foreach ($all as $loc) {
    echo "ID: {$loc->id} | Name: {$loc->nombre} | City ID: {$loc->ciudad_id} | Type ID: {$loc->tipo_sucursal_id}\n";
}
