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
    die("Error: No se encontró el archivo $path\n");
}

$sucursalId = TipoSucursal::where('nombre', 'Sucursal')->value('id');
$agenciaId = TipoSucursal::where('nombre', 'Agencia')->value('id');
$atmId = TipoSucursal::where('nombre', 'ATM')->value('id');
$externaId = TipoSucursal::where('nombre', 'like', '%Externa%')->value('id');

echo "--- Iniciando reconstrucción con Jerarquía (Agencia/Sucursal Padre) ---\n";

$content = File::get($path);
$lines = explode("\n", $content);

$currentCity = null;
$currentBranch = null;
$isStandaloneATM = false;

foreach ($lines as $line) {
    try {
        $line = trim($line);
        if (empty($line)) continue;

        if (str_starts_with($line, 'Sucursal ')) {
            $cityName = trim(str_replace('Sucursal ', '', $line));
            $currentCity = City::where('nombre', 'like', "%$cityName%")->first();
            $currentBranch = Ubicacion::updateOrCreate(
                ['nombre' => $line, 'ciudad_id' => $currentCity ? $currentCity->id : 1],
                ['tipo_sucursal_id' => $sucursalId]
            );
            echo " - [Sucursal] $line\n";
            continue;
        }

        if (str_starts_with($line, 'Agencia ')) {
            $currentBranch = Ubicacion::updateOrCreate(
                ['nombre' => $line, 'ciudad_id' => $currentCity ? $currentCity->id : 1],
                ['tipo_sucursal_id' => $agenciaId]
            );
            echo " - [Agencia] $line\n";
            continue;
        }

        if (str_starts_with($line, 'Oficina Externa ')) {
            $currentBranch = Ubicacion::updateOrCreate(
                ['nombre' => $line, 'ciudad_id' => $currentCity ? $currentCity->id : 1],
                ['tipo_sucursal_id' => $externaId]
            );
            echo " - [Externa] $line\n";
            continue;
        }

        if ($line === 'ATM') {
            $isStandaloneATM = true;
            $currentBranch = null;
            continue;
        }

        if (str_starts_with($line, 'ATM:')) {
            if ($currentCity) {
                $name = $currentBranch ? "ATM - {$currentBranch->nombre}" : "ATM Individual";
                $address = trim(str_replace('ATM:', '', $line));
                Ubicacion::updateOrCreate(
                    ['nombre' => $name, 'ciudad_id' => $currentCity->id],
                    [
                        'tipo_sucursal_id' => $atmId, 
                        'direccion' => $address,
                        'padre_id' => $currentBranch ? $currentBranch->id : null
                    ]
                );
                echo "   + [ATM vinculado] $name a " . ($currentBranch ? $currentBranch->nombre : 'Ninguna') . "\n";
            }
            continue;
        }

        if ($isStandaloneATM && $currentCity) {
            $name = "ATM $line";
            $currentBranch = Ubicacion::updateOrCreate(
                ['nombre' => $name, 'ciudad_id' => $currentCity->id],
                ['tipo_sucursal_id' => $atmId]
            );
            echo "   + [ATM independiente] $name\n";
            $isStandaloneATM = false;
            // Note: Standalone ATMs don't usually become "currentBranch" for others, 
            // but the logic allows it if there were sub-ATM details.
            continue;
        }

        if ($currentBranch) {
            if (str_starts_with($line, 'Teléfonos:')) {
                $currentBranch->telefonos = trim(str_replace('Teléfonos:', '', $line));
                $currentBranch->save();
            } elseif (empty($currentBranch->direccion)) {
                $currentBranch->direccion = $line;
                $currentBranch->save();
            }
        }
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
echo "Finalizado.\n";
