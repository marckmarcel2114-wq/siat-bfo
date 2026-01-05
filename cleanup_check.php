<?php

use App\Models\DefinicionAtributo;
use App\Models\AtributoActivo;
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- ANALYSIS START ---\n";

// 1. Orphan Definitions (No Type linked via Pivot)
// Note: Some definitions might be 'global' conceptually but if they are not linked to any type, they appear nowhere in UI.
$orphanDefs = DefinicionAtributo::doesntHave('tiposActivo')->get();
echo "Orphan Definitions (No Type linked): " . $orphanDefs->count() . "\n";
foreach ($orphanDefs as $def) {
    echo " - [{$def->id}] {$def->nombre}\n";
}

// 2. Orphan Attributes (Values with no parent Definition)
$orphanValuesDef = AtributoActivo::leftJoin('definiciones_atributos', 'atributos_activos.definicion_atributo_id', '=', 'definiciones_atributos.id')
    ->whereNull('definiciones_atributos.id')
    ->count();
echo "Orphan Values (Invalid Definition ID): " . $orphanValuesDef . "\n";

// 3. Orphan Attributes (Values with no parent Asset)
$orphanValuesAsset = AtributoActivo::leftJoin('activos', 'atributos_activos.activo_id', '=', 'activos.id')
    ->whereNull('activos.id')
    ->count();
echo "Orphan Values (Invalid Asset ID): " . $orphanValuesAsset . "\n";

// 4. Empty Values (Just cleanup)
$emptyValues = AtributoActivo::where('valor', '')->orWhereNull('valor')->count();
echo "Empty Attribute Values: " . $emptyValues . "\n";

echo "--- ANALYSIS END ---\n";
