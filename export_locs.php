<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Ubicacion;

$locs = Ubicacion::with('tipo', 'ciudad')->get();
echo "ID|Type|City|Name|Address\n";
foreach ($locs as $l) {
    echo "{$l->id}|" . ($l->tipo->nombre ?? 'N/A') . "|" . ($l->ciudad->nombre ?? 'N/A') . "|{$l->nombre}|{$l->direccion}\n";
}
