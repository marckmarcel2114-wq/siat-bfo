<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

foreach (\App\Models\City::all() as $c) {
    echo "ID: {$c->id} | Name: [{$c->nombre}]\n";
}
