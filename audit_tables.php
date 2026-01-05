<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tables = DB::select('SHOW TABLES');
echo str_pad('Table Name', 40) . "Rows\n";
echo str_repeat('-', 50) . "\n";

foreach ($tables as $t) {
    $tableName = array_values((array)$t)[0];
    $count = DB::table($tableName)->count();
    echo str_pad($tableName, 40) . $count . "\n";
}
