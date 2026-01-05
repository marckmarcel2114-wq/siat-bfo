<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Ubicacion;
use App\Models\TipoSucursal;

$sucursalId = TipoSucursal::where('nombre', 'Sucursal')->value('id');
$agenciaId = TipoSucursal::where('nombre', 'Agencia')->value('id');
$atmId = TipoSucursal::where('nombre', 'ATM')->value('id');
$externaId = TipoSucursal::where('nombre', 'like', '%Externa%')->value('id') ?? 4;

echo "IDs: Sucursal=$sucursalId, Agencia=$agenciaId, ATM=$atmId, Externa=$externaId\n";

$all = Ubicacion::all();
echo "Found " . count($all) . " locations.\n";

foreach ($all as $loc) {
    $currId = $loc->tipo_sucursal_id;
    $targetId = $currId;
    $name = $loc->nombre;
    
    if (stripos($name, 'Sucursal') !== false) {
        $targetId = $sucursalId;
    } elseif (stripos($name, 'Agencia') !== false) {
        $targetId = $agenciaId;
    } elseif (stripos($name, 'Oficina Externa') !== false) {
        $targetId = $externaId;
    } elseif (stripos($name, 'ATM') !== false || stripos($name, 'Cajero') !== false) {
        $targetId = $atmId;
    }

    if ($targetId && $targetId != $currId) {
        echo "Updating [{$loc->nombre}]: Type $currId -> $targetId\n";
        $loc->tipo_sucursal_id = $targetId;
        $loc->save();
    }
}
echo "Fix complete.\n";
