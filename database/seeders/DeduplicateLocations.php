<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Ubicacion;
use App\Models\Activo;

class DeduplicateLocations extends Seeder
{
    public function run(): void
    {
        // 1. Find duplicates grouped by Name + City
        $groups = DB::table('ubicaciones')
            ->select('nombre', 'ciudad_id', DB::raw('count(*) as total'))
            ->groupBy('nombre', 'ciudad_id')
            ->having('total', '>', 1)
            ->get();

        $this->command->info("Found {$groups->count()} groups of duplicates.");

        foreach ($groups as $group) {
            // Get all IDs for this group
            $ids = DB::table('ubicaciones')
                ->where('nombre', $group->nombre)
                ->where('ciudad_id', $group->ciudad_id)
                ->orderBy('id')
                ->pluck('id')
                ->toArray();

            // Keep the first one (master)
            $masterId = array_shift($ids); // Remove first ID from list to delete
            $idsToDelete = $ids;

            if (empty($idsToDelete)) continue;

            $this->command->warn("Merging " . count($idsToDelete) . " duplicates into ID $masterId for {$group->nombre}");

            // 2. Reassign FKs
            // Activos
            DB::table('activos')
                ->whereIn('ubicacion_id', $idsToDelete)
                ->update(['ubicacion_id' => $masterId]);

            // Puntos de Red (if exists)
            if (DB::getSchemaBuilder()->hasTable('puntos_red')) {
                 // Check if column exists
                 if (DB::getSchemaBuilder()->hasColumn('puntos_red', 'ubicacion_id')) {
                    DB::table('puntos_red')
                        ->whereIn('ubicacion_id', $idsToDelete)
                        ->update(['ubicacion_id' => $masterId]);
                 }
            }

            // Users? (if they have location)
            if (DB::getSchemaBuilder()->hasColumn('users', 'ubicacion_id')) {
                 DB::table('users')
                    ->whereIn('ubicacion_id', $idsToDelete)
                    ->update(['ubicacion_id' => $masterId]);
            }

            // 3. Delete duplicates
            DB::table('ubicaciones')->whereIn('id', $idsToDelete)->delete();
        }

        $this->command->info("Deduplication complete.");
    }
}
