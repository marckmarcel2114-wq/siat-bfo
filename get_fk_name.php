<?php

use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$fk = DB::selectOne("SELECT CONSTRAINT_NAME 
                   FROM information_schema.KEY_COLUMN_USAGE 
                   WHERE TABLE_NAME = 'definiciones_atributos' 
                   AND COLUMN_NAME = 'tipo_activo_id'
                   AND REFERENCED_TABLE_NAME IS NOT NULL");

if ($fk) {
    echo $fk->CONSTRAINT_NAME;
} else {
    echo "FK_NOT_FOUND";
}
