<?php

use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$names = DB::table('definiciones_atributos')
    ->where('category', 'hardware')
    ->pluck('nombre')
    ->toArray();

sort($names);

file_put_contents('hardware_list.txt', implode("\n", $names));
echo "Listed " . count($names) . " hardware attributes.";
