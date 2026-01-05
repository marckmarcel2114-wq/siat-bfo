<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$table = 'definiciones_atributos';
$col = 'tipo_activo_id';

echo "Attempting to drop Column: $col on $table\n";

try {
    Schema::table($table, function (Blueprint $table) use ($col) {
        $table->dropColumn($col);
    });
    echo "Success: Column dropped (Schema).\n";
} catch (\Exception $e) {
    echo "Schema Error: " . $e->getMessage() . "\n";
    echo "Trying raw SQL...\n";
    try {
        DB::statement("ALTER TABLE $table DROP COLUMN $col");
        echo "Success: Column dropped via Raw SQL.\n";
    } catch (\Exception $e2) {
        echo "Raw SQL Error: " . $e2->getMessage() . "\n";
    }
}
