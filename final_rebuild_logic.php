<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Ubicacion;
use App\Models\City;
use App\Models\TipoSucursal;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

$path = base_path('referencia/agencias.txt');
if (!File::exists($path)) {
    die("Error: No se encontró el archivo $path\n");
}

$sucursalId = TipoSucursal::where('nombre', 'Sucursal')->value('id');
$agenciaId = TipoSucursal::where('nombre', 'Agencia')->value('id');
$atmId = TipoSucursal::where('nombre', 'ATM')->value('id');
$externaId = TipoSucursal::where('nombre', 'like', '%Externa%')->value('id');

$preservedIds = [610, 611, 612, 613];

echo "--- Iniciando reconstrucción final v3 ---\n";

$content = File::get($path);
$lines = explode("\n", $content);

$currentCity = null;
$currentBranchName = null;
$isStandaloneATM = false;

foreach ($lines as $line) {
    try {
        $line = trim($line);
        if (empty($line)) continue;

        if (str_starts_with($line, 'Sucursal ')) {
            $cityName = trim(str_replace('Sucursal ', '', $line));
            $currentCity = City::where('nombre', 'like', "%$cityName%")->first();
            Ubicacion::updateOrCreate(
                ['nombre' => $line, 'ciudad_id' => $currentCity ? $currentCity->id : 1],
                ['tipo_sucursal_id' => $sucursalId]
            );
            $currentBranchName = $line;
            echo " - [Sucursal] $line\n";
            continue;
        }

        if (str_starts_with($line, 'Agencia ')) {
            Ubicacion::updateOrCreate(
                ['nombre' => $line, 'ciudad_id' => $currentCity ? $currentCity->id : 1],
                ['tipo_sucursal_id' => $agenciaId]
            );
            $currentBranchName = $line;
            echo " - [Agencia] $line\n";
            continue;
        }

        if (str_starts_with($line, 'Oficina Externa ')) {
            Ubicacion::updateOrCreate(
                ['nombre' => $line, 'ciudad_id' => $currentCity ? $currentCity->id : 1],
                ['tipo_sucursal_id' => $externaId]
            );
            $currentBranchName = $line;
            echo " - [Externa] $line\n";
            continue;
        }

        if ($line === 'ATM') {
            $isStandaloneATM = true;
            $currentBranchName = null;
            continue;
        }

        if (str_starts_with($line, 'ATM:')) {
            if ($currentCity && $currentBranchName) {
                $name = "ATM - $currentBranchName";
                Ubicacion::updateOrCreate(
                    ['nombre' => $name, 'ciudad_id' => $currentCity->id],
                    ['tipo_sucursal_id' => $atmId, 'direccion' => trim(str_replace('ATM:', '', $line))]
                );
                echo "   + [ATM anidado] $name\n";
            }
            continue;
        }

        if ($isStandaloneATM && $currentCity) {
            $name = "ATM $line";
            Ubicacion::updateOrCreate(
                ['nombre' => $name, 'ciudad_id' => $currentCity->id],
                ['tipo_sucursal_id' => $atmId]
            );
            echo "   + [ATM independiente] $name\n";
            $isStandaloneATM = false;
            continue;
        }

        if ($currentBranchName) {
            $loc = Ubicacion::where('nombre', $currentBranchName)->where('ciudad_id', $currentCity->id)->first();
            if ($loc && empty($loc->direccion)) {
                $loc->direccion = $line;
                $loc->save();
            }
        }
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
echo "Finalizado.\n";
