<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Ubicacion;
use App\Models\TipoSucursal;

$types = TipoSucursal::all();
echo "--- Location Audit ---\n";
foreach ($types as $type) {
    $count = Ubicacion::where('tipo_sucursal_id', $type->id)->count();
    echo "Type: {$type->nombre} (ID: {$type->id}) - Count: $count\n";
    
    // Sample names
    $samples = Ubicacion::where('tipo_sucursal_id', $type->id)->take(3)->pluck('nombre')->toArray();
    if (!empty($samples)) {
        echo "   Samples: " . implode(', ', $samples) . "\n";
    }
}

echo "\nTotal Locations: " . Ubicacion::count() . "\n";
