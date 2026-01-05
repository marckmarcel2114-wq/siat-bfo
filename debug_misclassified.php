<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Ubicacion;
use App\Models\TipoSucursal;

$agenciaId = TipoSucursal::where('nombre', 'Agencia')->value('id');
$atmId = TipoSucursal::where('nombre', 'ATM')->value('id');

ob_start();
echo "Checking for misclassified ATMs marked as Agencias...\n";
$misclassified = Ubicacion::where('tipo_sucursal_id', $agenciaId)
    ->where(function($q) {
        $q->where('nombre', 'like', '%ATM%')
          ->orWhere('nombre', 'like', '%Cajero%');
    })->get();

foreach ($misclassified as $loc) {
    echo "- [{$loc->nombre}] (ID: {$loc->id}) should probably be ATM.\n";
}

echo "\nChecking for locations that DON'T have Agencia/Sucursal in name but ARE marked as Agencia...\n";
$others = Ubicacion::where('tipo_sucursal_id', $agenciaId)
    ->where('nombre', 'not like', '%Agencia%')
    ->where('nombre', 'not like', '%Sucursal%')
    ->get();

foreach ($others as $loc) {
    echo "- [{$loc->nombre}] (ID: {$loc->id}) is marked as Agencia but name lacks keyword.\n";
}

echo "\nTotal current ATMs: " . Ubicacion::where('tipo_sucursal_id', $atmId)->count() . "\n";
echo "Total current Agencias: " . Ubicacion::where('tipo_sucursal_id', $agenciaId)->count() . "\n";
$output = ob_get_clean();
file_put_contents('misclassified_log.txt', $output);
echo "Done\n";
