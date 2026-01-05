<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Ubicacion;

ob_start();
echo "Searching for ATM/Cajero names...\n";
$matches = Ubicacion::where('nombre', 'like', '%ATM%')
    ->orWhere('nombre', 'like', '%Cajero%')
    ->with('tipo')
    ->get();

if ($matches->isEmpty()) {
    echo "No records found with ATM or Cajero in name.\n";
} else {
    foreach ($matches as $loc) {
        $typeName = $loc->tipo ? $loc->tipo->nombre : 'NULL';
        echo "{$loc->id} | {$typeName} | {$loc->nombre}\n";
    }
}
$output = ob_get_clean();
file_put_contents('atm_search_results.txt', $output);
echo "Done\n";
