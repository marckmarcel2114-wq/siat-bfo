<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Software;
use App\Models\SoftwareVersion;

class MigrateSoftwareSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Starting Software Migration...');

        // 1. Identify Software Attributes
        $softDefinitions = \App\Models\DefinicionAtributo::whereIn('nombre', ['Sistema Operativo', 'Software', 'Antivirus', 'Office'])
            ->pluck('id');

        if ($softDefinitions->isEmpty()) {
            $this->command->warn('No software attribute definitions found.');
            // Fallback: Check if we have any attributes at all?
        }

        // 2. Get Unique Values
        $values = \App\Models\AtributoActivo::whereIn('definicion_atributo_id', $softDefinitions)
            ->distinct() // Eloquent distinct() might need specific column if not pluck
            ->pluck('valor')
            ->unique();

        $count = 0;
        foreach ($values as $val) {
            if (empty($val) || $val == '---' || $val == 'N/A') continue;

            try {
                // Check if exists
                $software = Software::firstOrCreate(
                    ['nombre' => $val],
                    ['tipo' => 'Aplicación', 'fabricante' => 'Desconocido']
                );
                
                // Create a default version
                SoftwareVersion::firstOrCreate(
                    ['software_id' => $software->id, 'version' => 'Base'],
                    ['descripcion' => 'Migrated from legacy attributes']
                );
                $count++;
            } catch (\Exception $e) {
                $this->command->error("Failed to migrate: $val. Error: " . $e->getMessage());
            }
        }

        $this->command->info("Migrated $count software titles from attributes.");
        
        // 3. Migrate Licenses
        $licenses = \App\Models\SoftwareLicense::all();
        foreach($licenses as $lic) {
            try {
                 $soft = Software::firstOrCreate(
                    ['nombre' => $lic->nombre],
                    ['tipo' => $lic->tipo, 'fabricante' => 'Desconocido']
                );
                
                // Create version for license
                 SoftwareVersion::firstOrCreate(
                    ['software_id' => $soft->id, 'version' => 'Base'],
                    ['descripcion' => 'Migrated from license']
                );
            } catch (\Exception $e) {
                 $this->command->error("Failed to migrate license: {$lic->nombre}. Error: " . $e->getMessage());
            }
        }
        $this->command->info("Ensured consistency with " . $licenses->count() . " licenses.");
    }
}
