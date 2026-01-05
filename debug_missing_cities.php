<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Ubicacion;
use App\Models\City;

ob_start();
$targetCities = ['Oruro', 'Sucre', 'Tarija', 'Potosí'];
$cityIds = City::whereIn('nombre', $targetCities)->pluck('id', 'nombre');

echo "Detailed Location List for Target Cities:\n";
echo "------------------------------------------\n";

foreach ($cityIds as $name => $id) {
    echo "\nCity: $name (ID: $id)\n";
    $locs = Ubicacion::where('ciudad_id', $id)->with('tipo')->get();
    if ($locs->isEmpty()) {
        echo "   (No locations found)\n";
    } else {
        foreach ($locs as $loc) {
            echo "   - ID: {$loc->id} | Type: {$loc->tipo->nombre} | Name: {$loc->nombre} | Code: {$loc->codigo_ubicacion}\n";
        }
    }
}

$output = ob_get_clean();
file_put_contents('missing_cities_log.txt', $output);
echo "Done\n";
