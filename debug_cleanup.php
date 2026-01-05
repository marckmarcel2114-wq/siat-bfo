<?php

use App\Models\DefinicionAtributo;
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$orphan = DefinicionAtributo::doesntHave('tiposActivo')->first();

if ($orphan) {
    echo "Found Orphan: [{$orphan->id}] {$orphan->nombre}\n";
    try {
        $orphan->delete();
        echo "Deleted successfully.\n";
    } catch (\Exception $e) {
        echo "Delete Failed: " . $e->getMessage() . "\n";
    }
} else {
    echo "No orphans found via Eloquent.\n";
    
    // Check raw
    $rawOrphanId = DB::table('definiciones_atributos')
        ->whereNotExists(function ($query) {
            $query->select(DB::raw(1))
                  ->from('definicion_atributo_tipo_activo')
                  ->whereColumn('definicion_atributo_tipo_activo.definicion_atributo_id', 'definiciones_atributos.id');
        })
        ->value('id');
        
    if ($rawOrphanId) {
        echo "Found Raw Orphan ID: $rawOrphanId\n";
    } else {
        echo "No orphans found via Raw SQL either.\n";
    }
}
