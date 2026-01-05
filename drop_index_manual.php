<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$table = 'definiciones_atributos';
$index = 'attribute_definitions_tipo_activo_id_nombre_unique';

echo "Attempting to drop Index: $index on $table\n";

try {
    Schema::table($table, function (Blueprint $table) use ($index) {
        $table->dropUnique($index);
    });
    echo "Success: Index dropped (Schema).\n";
} catch (\Exception $e) {
    echo "Schema Error: " . $e->getMessage() . "\n";
    echo "Trying raw SQL...\n";
    try {
        DB::statement("ALTER TABLE $table DROP INDEX $index");
        echo "Success: Index dropped via Raw SQL.\n";
    } catch (\Exception $e2) {
        echo "Raw SQL Error: " . $e2->getMessage() . "\n";
    }
}
