<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Activo;
use App\Models\TipoActivo;
use App\Models\Marca;
use App\Models\Modelo;
use App\Models\Ubicacion;
use App\Models\EstadoActivo;
use App\Models\NivelCriticidad;
use App\Models\Propietario;
use App\Models\Asignacion;
use Illuminate\Support\Str;

class TestHistorySeeder extends Seeder
{
    /**
     * Creates Assets and Assigns them to users.
     */
    public function run(): void
    {
        // Ensure we have dependencies
        $locations = Ubicacion::take(10)->get(); // Get some locations
        $users = User::all();
        $dell = Marca::where('nombre', 'Dell')->first();
        $laptop = TipoActivo::where('nombre', 'Laptop')->first();
        $lat5420 = Modelo::where('nombre', 'Latitude 5420')->first();
        $operativo = EstadoActivo::where('nombre', 'Disponible')->first();
        $asignado = EstadoActivo::where('nombre', 'Asignado')->first();
        $criticidad = NivelCriticidad::where('nombre', 'Media')->first();
        $prop = Propietario::first();

        if ($users->count() == 0 || $locations->count() == 0) {
            // Need users and locations defined first
            return; 
        }

        // Create 20 Assets
        for ($i = 1; $i <= 20; $i++) {
            $loc = $locations->random();
            
            $activo = Activo::create([
                'codigo_activo' => 'BFO-LPT-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'numero_serie' => Str::upper(Str::random(10)),
                'tipo_activo_id' => $laptop->id,
                'marca_id' => $dell->id,
                'modelo_id' => $lat5420->id,
                'estado_activo_id' => $operativo->id,
                'criticidad_id' => $criticidad->id,
                'propietario_id' => $prop->id,
                'ubicacion_id' => $loc->id,
                'fecha_adquisicion' => now()->subMonths(rand(1, 24)),
                'valor_adquisicion' => rand(800, 1500) * 6.96, // USD to BOB approx
                'vida_util_anios' => 4
            ]);

            // Randomly Assign 50% of them
            if (rand(0, 1) === 1) {
                $user = $users->random();
                
                // Update Asset State
                $activo->update(['estado_activo_id' => $asignado->id]);
                
                // Create Assignment
                Asignacion::create([
                    'activo_id' => $activo->id,
                    'usuario_id' => $user->id,
                    'fecha_asignacion' => now()->subDays(rand(1, 30)),
                    'es_actual' => true,
                    // 'ubicacion_id' => $loc->id // If assignments tracked location historically
                ]);
            }
        }
    }
}
