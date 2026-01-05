<?php

use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$defs = DB::table('definiciones_atributos')->count();
$pivots = DB::table('definicion_atributo_tipo_activo')->count();

echo "Definitions: $defs\n";
echo "Pivots: $pivots\n";

if ($pivots < $defs) {
    echo "Backfill required.\n";
} else {
    echo "Counts look plausible.\n";
}
