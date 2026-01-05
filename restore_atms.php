<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\TipoSucursal;
use App\Models\Ubicacion;

$hasAtms = Schema::hasTable('atms');
echo "Legacy atms table: " . ($hasAtms ? "YES" : "NO") . "\n";

if ($hasAtms) {
    $atmTypeId = TipoSucursal::where('nombre', 'ATM')->value('id');
    $legacyAtms = DB::table('atms')->get();
    echo "Found " . count($legacyAtms) . " records in legacy atms table.\n";
    
    foreach ($legacyAtms as $latm) {
        // Try to find matching record in ubicaciones
        $match = Ubicacion::where('nombre', $latm->nombre)
            ->where('ciudad_id', $latm->city_id)
            ->first();
            
        if ($match) {
            if ($match->tipo_sucursal_id != $atmTypeId) {
                echo "Restoring [{$match->nombre}] to ATM (Type $atmTypeId)\n";
                $match->tipo_sucursal_id = $atmTypeId;
                $match->save();
            }
        } else {
            echo "No match found for legacy ATM: {$latm->nombre}\n";
        }
    }
} else {
    echo "Cannot restore types from legacy table. Need alternative heuristic.\n";
}
