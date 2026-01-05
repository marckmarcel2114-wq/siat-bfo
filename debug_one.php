<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Ubicacion;

$loc = Ubicacion::where('nombre', 'like', '%Sucursal La Paz%')->first();
if ($loc) {
    echo "Found: {$loc->nombre}\n";
    echo "Type ID: {$loc->tipo_sucursal_id}\n";
    echo "Type Name: " . ($loc->tipo->nombre ?? 'NULL') . "\n";
} else {
    echo "Not found.\n";
}
