<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Ubicacion;
use App\Models\City;
use App\Models\TipoSucursal;
use Illuminate\Support\Facades\File;

$path = base_path('referencia/agencias.txt');
if (!File::exists($path)) {
    die("File not found: $path\n");
}

$atmTypeId = TipoSucursal::where('nombre', 'ATM')->value('id');
$content = File::get($path);
$lines = explode("\n", $content);

$currentCity = null;
$currentBranch = null;
$restoredCount = 0;

echo "Restoring ATMs from agencias.txt...\n";

foreach ($lines as $line) {
    $line = trim($line);
    if (empty($line)) continue;

    // Detect City / Sucursal Principal
    if (str_starts_with($line, 'Sucursal ')) {
        $cityName = trim(str_replace('Sucursal ', '', $line));
        $currentCity = City::where('nombre', 'like', "%$cityName%")->first();
        if (!$currentCity) {
            echo "Warning: City $cityName not found, skipping block.\n";
            $currentBranch = null;
            continue;
        }
        $currentBranch = $line;
        continue;
    }

    // Detect Agencies
    if (str_starts_with($line, 'Agencia ')) {
        $currentBranch = $line;
        continue;
    }

    // Detect standalone ATM block
    if ($line === 'ATM') {
        $currentBranch = null;
        $isStandaloneHeader = true;
        continue;
    }

    // Detect nested ATM
    if (str_starts_with($line, 'ATM:')) {
        if ($currentCity) {
            $name = $currentBranch ? "ATM - $currentBranch" : "ATM Desconocido";
            $address = trim(str_replace('ATM:', '', $line));
            
            Ubicacion::updateOrCreate(
                ['nombre' => $name, 'ciudad_id' => $currentCity->id],
                [
                    'tipo_sucursal_id' => $atmTypeId,
                    'direccion' => $address
                ]
            );
            echo "Restored nested ATM: $name in {$currentCity->nombre}\n";
            $restoredCount++;
        }
        continue;
    }

    // Handle standalone ATM name (after 'ATM' header)
    if (isset($isStandaloneHeader) && $isStandaloneHeader && $currentCity) {
        $name = "ATM $line";
        Ubicacion::updateOrCreate(
            ['nombre' => $name, 'ciudad_id' => $currentCity->id],
            [
                'tipo_sucursal_id' => $atmTypeId,
                'direccion' => 'Verificar en agencias.txt'
            ]
        );
        echo "Restored standalone ATM: $name in {$currentCity->nombre}\n";
        $restoredCount++;
        $isStandaloneHeader = false;
        continue;
    }
}

echo "Total ATMs restored/verified: $restoredCount\n";
echo "Done\n";
