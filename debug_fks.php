<?php

use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tables = ['definiciones_atributos', 'definicion_atributo_tipo_activo'];

foreach ($tables as $table) {
    try {
        $fks = DB::select("SELECT CONSTRAINT_NAME, TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME 
                           FROM information_schema.KEY_COLUMN_USAGE 
                           WHERE TABLE_NAME = ? AND REFERENCED_TABLE_NAME IS NOT NULL", [$table]);

        echo "--- FKs for '$table' ---\n";
        foreach ($fks as $fk) {
            echo "Constraint: {$fk->CONSTRAINT_NAME} | Col: {$fk->COLUMN_NAME} -> {$fk->REFERENCED_TABLE_NAME}.{$fk->REFERENCED_COLUMN_NAME}\n";
        }
    } catch (\Exception $e) {
        echo "Error checking '$table': " . $e->getMessage() . "\n";
    }
}
