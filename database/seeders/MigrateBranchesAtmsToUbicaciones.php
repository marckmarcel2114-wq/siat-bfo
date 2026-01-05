<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Branch;
use App\Models\Atm;

class MigrateBranchesAtmsToUbicaciones extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get IDs for location types (Case sensitive match)
        $sucursalId = DB::table('tipos_ubicacion')->where('nombre', 'Sucursal')->value('id');
        $agenciaId = DB::table('tipos_ubicacion')->where('nombre', 'Agencia')->value('id');
        $atmId = DB::table('tipos_ubicacion')->where('nombre', 'ATM')->value('id');
        $cpdId = DB::table('tipos_ubicacion')->where('nombre', 'CPD')->value('id');

        // Migrate Branches
        // Check if Branch model exists and tables have data
        if (DB::table('branches')->exists()) {
             $branches = DB::table('branches')
                ->join('cities', 'branches.city_id', '=', 'cities.id')
                ->leftJoin('branch_types', 'branches.branch_type_id', '=', 'branch_types.id')
                ->select('branches.*', 'branch_types.name as type_name')
                ->get();

             foreach ($branches as $branch) {
                $typeId = $agenciaId; // Default
                if (stripos($branch->type_name, 'Sucursal') !== false) {
                    $typeId = $sucursalId;
                }
                
                // Map 'code' if exists, otherwise generate or leave null
                // Assuming 'code' column exists from previous implementation
                $code = $branch->code ?? null;

                DB::table('ubicaciones')->updateOrInsert(
                    ['nombre' => $branch->name, 'ciudad_id' => $branch->city_id],
                    [
                        'tipo_ubicacion_id' => $typeId,
                        'codigo_ubicacion' => $code,
                        'created_at' => $branch->created_at,
                        'updated_at' => $branch->updated_at,
                    ]
                );
             }
             $this->command->info('Branches migrated to Ubicaciones.');
        }

        // Migrate ATMs
        if (DB::table('atms')->exists()) {
            $atms = DB::table('atms')->get();
            foreach ($atms as $atm) {
                DB::table('ubicaciones')->updateOrInsert(
                    ['nombre' => $atm->name, 'ciudad_id' => $atm->city_id],
                    [
                        'tipo_ubicacion_id' => $atmId,
                        'codigo_ubicacion' => $atm->code ?? null,
                        'created_at' => $atm->created_at,
                        'updated_at' => $atm->updated_at,
                    ]
                );
            }
            $this->command->info('ATMs migrated to Ubicaciones.');
        }
    }
}
