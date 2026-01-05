<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$fkName = 'attribute_definitions_tipo_activo_id_foreign';
$table = 'definiciones_atributos';

echo "Attempting to drop FK: $fkName on $table\n";

try {
    Schema::table($table, function (Blueprint $table) use ($fkName) {
        $table->dropForeign($fkName);
    });
    echo "Success: FK dropped.\n";
} catch (\Exception $e) {
    echo "Laravel Schema Error: " . $e->getMessage() . "\n";
    echo "Trying raw SQL...\n";
    try {
        DB::statement("ALTER TABLE $table DROP FOREIGN KEY $fkName");
        echo "Success: FK dropped via Raw SQL.\n";
    } catch (\Exception $e2) {
        echo "Raw SQL Error: " . $e2->getMessage() . "\n";
    }
}
