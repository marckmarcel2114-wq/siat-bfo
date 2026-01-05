<?php

use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$res = DB::select('SHOW CREATE TABLE definiciones_atributos');
// Result is array of objects, key is "Create Table" usually
$key = 'Create Table';
$sql = $res[0]->$key;

file_put_contents('schema_dump.txt', $sql);
echo "Schema dumped to schema_dump.txt";
