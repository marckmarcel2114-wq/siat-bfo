<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Ubicacion;

$atmsLinked = Ubicacion::whereNotNull('padre_id')->count();
echo "ATMs vinculados a una agencia: $atmsLinked\n";

$samples = Ubicacion::whereNotNull('padre_id')->with('padre')->limit(5)->get();
foreach ($samples as $s) {
    echo "ATM: {$s->nombre} -> Padre: {$s->padre->nombre}\n";
}
