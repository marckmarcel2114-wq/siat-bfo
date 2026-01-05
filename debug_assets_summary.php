<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Ubicacion;
use App\Models\Activo;
use App\Models\TipoActivo;

$atmAssetsTypeId = TipoActivo::where('nombre', 'like', '%ATM%')->orWhere('nombre', 'like', '%Cajero%')->pluck('id');

ob_start();
echo "Location Summary:\n";
$locs = Ubicacion::withCount('activos')->with('tipo')->get();
foreach ($locs as $loc) {
    echo "ID: {$loc->id} | Type: {$loc->tipo->nombre} | Name: {$loc->nombre} | Assets: {$loc->activos_count}\n";
    
    // Check if it has ATM assets
    if ($atmAssetsTypeId->isNotEmpty()) {
        $atmCount = Activo::where('ubicacion_id', $loc->id)->whereIn('tipo_activo_id', $atmAssetsTypeId)->count();
        if ($atmCount > 0) {
            echo "   -> Has $atmCount ATM machine assets.\n";
        }
    }
}

echo "\nTotal Assets: " . Activo::count() . "\n";
$output = ob_get_clean();
file_put_contents('asset_summary_log.txt', $output);
echo "Done\n";
