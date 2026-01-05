<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Ubicacion;

$locsWithAssets = Ubicacion::has('activos')->withCount('activos')->get();
$map = [];
foreach ($locsWithAssets as $l) {
    $map[$l->id] = [
        'name' => $l->nombre,
        'assets_count' => $l->activos_count,
        'type' => $l->tipo_sucursal_id,
        'city' => $l->ciudad_id
    ];
}
file_put_contents('locs_with_assets.json', json_encode($map, JSON_PRETTY_PRINT));
echo "Found " . count($map) . " locations with assets.\n";
foreach ($map as $id => $data) {
    echo " - ID $id: {$data['name']} ({$data['assets_count']} assets)\n";
}
