<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Ubicacion;
use App\Models\City;

$path = base_path('referencia/agencias.txt');
$content = file_get_contents($path);
$lines = explode("\n", $content);

$txtNames = [];
foreach ($lines as $line) {
    if (str_starts_with($line, 'Sucursal ') || str_starts_with($line, 'Agencia ') || str_starts_with($line, 'Oficina Externa ')) {
        $txtNames[] = trim($line);
    }
}

$dbNames = Ubicacion::pluck('nombre')->toArray();

echo "--- Name Comparison ---\n";
$missing = array_diff($txtNames, $dbNames);
if (empty($missing)) {
    echo "All names from txt are in DB.\n";
} else {
    echo "Missing from DB:\n";
    foreach ($missing as $m) echo " - $m\n";
}

$extra = array_diff($dbNames, $txtNames);
echo "\nExtra in DB (not in txt main lines):\n";
foreach ($extra as $e) echo " + $e\n";
