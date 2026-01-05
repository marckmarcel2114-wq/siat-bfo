<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Ubicacion;

$data = Ubicacion::all()->toArray();
file_put_contents('ubicaciones_backup.json', json_encode($data, JSON_PRETTY_PRINT));
echo "Backup created: ubicaciones_backup.json\n";
