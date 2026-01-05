<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Ubicacion;
use App\Models\TipoSucursal;
use App\Models\City;

ob_start();
$atmTypeId = TipoSucursal::where('nombre', 'ATM')->value('id');
echo "ATM Type ID: $atmTypeId\n";
echo "Total Ubicaciones: " . Ubicacion::count() . "\n";

$types = TipoSucursal::all();
foreach ($types as $t) {
    echo "Type: {$t->nombre} (ID: {$t->id}) - Count: " . Ubicacion::where('tipo_sucursal_id', $t->id)->count() . "\n";
}

$cities = ['La Paz', 'Cochabamba', 'Santa Cruz', 'Oruro', 'Potosí', 'Sucre', 'Tarija', 'Beni', 'Pando'];
foreach ($cities as $cityName) {
    $city = City::where('nombre', 'like', "$cityName%")->first();
    if ($city) {
        echo "\nLocations in {$city->nombre} (ID: {$city->id}):\n";
        $locs = Ubicacion::where('ciudad_id', $city->id)->with('tipo')->get();
        foreach ($locs as $loc) {
            $typeName = ($loc->tipo) ? $loc->tipo->nombre : 'NULL';
            echo "- {$loc->nombre} (Type ID: {$loc->tipo_sucursal_id}, Type Name: $typeName)\n";
        }
    }
}

$output = ob_get_clean();
file_put_contents('counts_log.txt', $output);
echo "Done\n";
