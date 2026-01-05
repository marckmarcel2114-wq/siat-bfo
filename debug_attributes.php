<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Activo;

$asset = Activo::find(29);
if (!$asset) {
    echo "Asset 29 not found\n";
    exit;
}

echo "Asset: " . $asset->codigo_activo . "\n";
echo "Type ID: " . $asset->tipo_activo_id . "\n";
echo "Attributes:\n";

foreach ($asset->atributos as $attr) {
    echo "ID: {$attr->id} | Def ID: [{$attr->definicion_atributo_id}] | Value: {$attr->valor}\n";
}

$mapped = $asset->atributos->pluck('valor', 'definicion_atributo_id')->toArray();
echo "\nMapped Array (what should be in form):\n";
print_r($mapped);
