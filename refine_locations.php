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

$trueAgencias = [
    '16 de Julio', 'Río Seco', 'Cruce Villa Adela', 'Villa Dolores', 
    'Aeropuerto', 'Viru Viru', 'Genex', 'Mercado Campesino', 
    'Mercado Bolívar', 'Av. del Ejército', 'Quillacollo', 'Sacaba', 
    '14 de Septiembre', 'Montero', 'Villazón', 'Bermejo'
];

ob_start();
echo "Refining categorization v2...\n";

foreach (Ubicacion::all() as $loc) {
    $name = $loc->nombre;
    $targetId = $loc->tipo_sucursal_id;

    // Default heuristics
    if (stripos($name, 'Sucursal') !== false) {
        $targetId = $sucursalId;
    } elseif (stripos($name, 'Oficina Externa') !== false) {
        $targetId = $externaId;
    } elseif (stripos($name, 'ATM') !== false || stripos($name, 'Cajero') !== false) {
        $targetId = $atmId;
    } elseif (stripos($name, 'Agencia') !== false) {
        // Check if it's a "True Agencia" or should be an ATM
        $isTrue = false;
        foreach ($trueAgencias as $ta) {
            if (stripos($name, $ta) !== false) {
                $isTrue = true;
                break;
            }
        }
        $targetId = $isTrue ? $agenciaId : $atmId;
    }

    if ($targetId != $loc->tipo_sucursal_id) {
        echo "Updating [{$loc->nombre}]: Type {$loc->tipo_sucursal_id} -> $targetId\n";
        $loc->tipo_sucursal_id = $targetId;
        $loc->save();
    }
}

$output = ob_get_clean();
file_put_contents('refine_log.txt', $output);
echo "Done\n";
