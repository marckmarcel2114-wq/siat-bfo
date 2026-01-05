<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Ubicacion;
use App\Models\City;
use App\Models\TipoSucursal;

$atmTypeId = TipoSucursal::where('nombre', 'ATM')->value('id');

echo "Final ATM distribution:\n";
$cities = City::all();
foreach ($cities as $city) {
    $count = Ubicacion::where('ciudad_id', $city->id)->where('tipo_sucursal_id', $atmTypeId)->count();
    if ($count > 0) {
        echo "- {$city->nombre}: $count ATMs\n";
    }
}

echo "Total ATMs in DB: " . Ubicacion::where('tipo_sucursal_id', $atmTypeId)->count() . "\n";
