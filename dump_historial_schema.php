<?php

use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$res = DB::select("SHOW CREATE TABLE historial_cambios");
// Key is usually 'Create Table'
$key = 'Create Table';
$sql = $res[0]->$key;

file_put_contents('historial_schema_dump.txt', $sql);
echo "Dumped schema to historial_schema_dump.txt";
