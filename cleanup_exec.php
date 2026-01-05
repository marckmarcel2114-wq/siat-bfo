<?php

use App\Models\DefinicionAtributo;
use App\Models\AtributoActivo;
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- CLEANUP START ---\n";

DB::transaction(function() {
    // 1. Delete Orphan Definitions (Not shared with any type)
    // Using raw query for certainty
    $countDefs = DB::delete("
        DELETE FROM definiciones_atributos 
        WHERE id NOT IN (SELECT definicion_atributo_id FROM definicion_atributo_tipo_activo)
    ");
    echo "Deleted {$countDefs} orphaned definitions.\n";

    // 2. Delete Orphan Values (Pointing to non-existent definitions)
    // Raw query for performance/safety if relationship is broken
    $countValuesDef = DB::delete("
        DELETE FROM atributos_activos 
        WHERE definicion_atributo_id NOT IN (SELECT id FROM definiciones_atributos)
    ");
    echo "Deleted {$countValuesDef} orphaned attribute values (Invalid Def ID).\n";

    // 3. Delete Orphan Values (Pointing to non-existent assets)
    $countValuesAsset = DB::delete("
        DELETE FROM atributos_activos 
        WHERE activo_id NOT IN (SELECT id FROM activos)
    ");
    echo "Deleted {$countValuesAsset} orphaned attribute values (Invalid Asset ID).\n";
});

echo "--- CLEANUP COMPLETE ---\n";
