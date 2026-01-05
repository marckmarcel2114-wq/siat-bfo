<?php

use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Define IT attributes that should NOT belong to Furniture
$itAttributes = [
    'Procesador', 'Memoria RAM', 'Capacidad de Memoria', 
    'Disco Duro', 'Capacidad de Disco', 'Sistema Operativo',
    'Tarjeta Madre', 'Tarjeta de Video', 'Dirección IP', 'MAC Address'
];

// Define Furniture Types (or non-computing)
$furnitureTypes = ['Silla', 'Mesa', 'Escritorio', 'Estante', 'Mueble', 'Monitor', 'Teclado', 'Mouse']; 
// Monitor/Teclado/Mouse are IT peripherals but don't have Processor usually (smart monitors maybe, but standard no).

$suspicious = [];

foreach ($furnitureTypes as $typeName) {
    $type = DB::table('tipos_activo')->where('nombre', 'like', "%$typeName%")->first();
    if (!$type) continue;

    $assigned = DB::table('definicion_atributo_tipo_activo')
        ->join('definiciones_atributos', 'definicion_atributo_tipo_activo.definicion_atributo_id', '=', 'definiciones_atributos.id')
        ->where('tipo_activo_id', $type->id)
        ->whereIn('definiciones_atributos.nombre', $itAttributes)
        ->select('definiciones_atributos.nombre')
        ->get();

    if ($assigned->isNotEmpty()) {
        $suspicious[$type->nombre] = $assigned->pluck('nombre')->toArray();
    }
}

if (empty($suspicious)) {
    echo "No invalid assignments found (Furniture/Peripherals clean of Core IT specs).\n";
} else {
    echo "Found invalid assignments:\n";
    print_r($suspicious);
}
