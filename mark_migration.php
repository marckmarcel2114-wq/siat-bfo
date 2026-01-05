<?php

use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$migration = '2026_01_03_183459_normalize_attribute_definitions';
$batch = DB::table('migrations')->max('batch') + 1;

if (!DB::table('migrations')->where('migration', $migration)->exists()) {
    DB::table('migrations')->insert([
        'migration' => $migration,
        'batch' => $batch
    ]);
    echo "Migration marked as complete.\n";
} else {
    echo "Migration already marked.\n";
}
