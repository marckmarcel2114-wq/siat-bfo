<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Ubicacion;
use App\Models\City;
use App\Models\TipoSucursal;

$atmId = TipoSucursal::where('nombre', 'ATM')->value('id');
echo "Total ATMs: " . Ubicacion::where('tipo_sucursal_id', $atmId)->count() . "\n";
foreach (City::all() as $c) {
    $count = Ubicacion::where('ciudad_id', $c->id)->where('tipo_sucursal_id', $atmId)->count();
    $branches = Ubicacion::where('ciudad_id', $c->id)->where('tipo_sucursal_id', '!=', $atmId)->count();
    if ($count > 0 || $branches > 0) {
        echo "- {$c->nombre}: $count ATMs, $branches Branches\n";
    }
}
