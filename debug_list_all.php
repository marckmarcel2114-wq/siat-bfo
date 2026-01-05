<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Ubicacion;

ob_start();
echo "ID | Type | Name | Code\n";
echo "--------------------------\n";
$locs = Ubicacion::with('tipo')->get();
foreach ($locs as $loc) {
    $typeName = $loc->tipo ? $loc->tipo->nombre : 'NULL';
    echo "{$loc->id} | {$typeName} | {$loc->nombre} | {$loc->codigo_ubicacion}\n";
}
$output = ob_get_clean();
file_put_contents('locations_full_list.txt', $output);
echo "Done\n";
