<?php

use App\Models\DefinicionAtributo;
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$definitions = DefinicionAtributo::pluck('nombre')->toArray();
$counts = array_count_values($definitions);
$duplicates = array_filter($counts, fn($count) => $count > 1);

echo "Total Definitions: " . count($definitions) . "\n";
echo "Unique Names: " . count($counts) . "\n";

if (empty($duplicates)) {
    echo "No duplicates found. All attribute names are unique.\n";
} else {
    echo "Duplicates found:\n";
    print_r($duplicates);
}

// Check specific technical headers
echo "\n--- Technical Attribs Check ---\n";
foreach (['Procesador', 'Memoria RAM', 'Capacidad de Memoria', 'Disco Duro', 'Capacidad de Disco'] as $key) {
    echo "$key: " . ($counts[$key] ?? 0) . " occurrences.\n";
}
