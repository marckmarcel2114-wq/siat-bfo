<?php

use Illuminate\Support\Facades\Schema;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$cols = Schema::getColumnListing('historial_cambios');
$needed = ['campo', 'valor_anterior', 'valor_nuevo'];
$found = array_intersect($needed, $cols);

if (count($found) === 3) {
    echo "COLUMNS EXIST";
} else {
    echo "MISSING: " . implode(', ', array_diff($needed, $cols));
}
