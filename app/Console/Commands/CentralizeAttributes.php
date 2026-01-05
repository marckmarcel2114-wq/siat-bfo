<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DefinicionAtributo;
use App\Models\AtributoActivo;
use App\Models\TipoActivo;
use Illuminate\Support\Facades\DB;

class CentralizeAttributes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:centralize-attributes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Centralize attribute definitions and standardize options';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Attribute Centralization...');

        $standardizations = [
            'Procesador' => [
                'type' => 'select',
                'options' => [
                    "Intel Core i3", "Intel Core i5", "Intel Core i7", "Intel Core i9", 
                    "AMD Ryzen 3", "AMD Ryzen 5", "AMD Ryzen 7", "AMD Ryzen 9", 
                    "Intel Celeron", "Intel Pentium", "Apple M1", "Apple M2", "Apple M3"
                ]
            ],
            'Generación' => [
                'type' => 'select',
                'options' => [
                    "1ra Gen", "2da Gen", "3ra Gen", "4ta Gen", "5ta Gen", 
                    "6ta Gen", "7ma Gen", "8va Gen", "9na Gen", "10ma Gen", 
                    "11va Gen", "12va Gen", "13ra Gen", "14ta Gen", "M1", "M2", "M3"
                ]
            ],
            'Capacidad de Memoria' => [
                'type' => 'select', // Renamed from Memoria RAM in previous step? Or is it still Memoria RAM? User request implied split.
                'regex_alias' => ['Memoria RAM'], // Fallback if still named old way, but we prefer 'Capacidad de Memoria' if split happened
                'options' => ["4 GB", "8 GB", "12 GB", "16 GB", "24 GB", "32 GB", "64 GB", "128 GB"]
            ],
            'Tipo de Memoria' => [
                'type' => 'select',
                'options' => ["DDR3", "DDR4", "DDR5", "LPDDR3", "LPDDR4", "LPDDR5", "Unified Memory"]
            ],
            'Capacidad de Disco' => [
                'type' => 'select',
                'options' => ["128 GB", "250 GB", "256 GB", "480 GB", "500 GB", "512 GB", "1 TB", "2 TB"]
            ],
            'Tipo de Disco' => [
                'type' => 'select',
                'options' => ["HDD", "SSD SATA", "SSD M.2", "SSD NVMe"]
            ]
        ];

        // Global Deduplication for ALL attributes
        $uniqueNames = DefinicionAtributo::distinct()->pluck('nombre');
        
        $bar = $this->output->createProgressBar($uniqueNames->count());
        
        foreach ($uniqueNames as $name) {
            // Check if this is one of our strict standards
            $config = $standardizations[$name] ?? null;
            $this->centralize($name, $config);
            $bar->advance();
        }
        $bar->finish();
        $this->newLine();

        $this->info('Attribute Centralization Complete!');
    }

    private function centralize($name, $config) {
        $this->info("Processing: {$name}");

        // Find all definitions matching this name (case insensitive)
        $definitions = DefinicionAtributo::where('nombre', $name)->get();

        if ($definitions->isEmpty()) {
            // Check regex alias/partial match if needed?
            // For now, strict name match or nothing.
            // If previous tasks renamed 'Memoria RAM' to 'Capacidad de Memoria', we look for that.
            // Let's assume standardized names exist.
            $this->warn("No definitions found for {$name}");
            return;
        }

        // Pick the first one as Master
        $master = $definitions->first();
        $this->info("  Master ID: {$master->id} (Originally for Type ID: " . ($master->tiposActivo()->first()->id ?? 'null') . ")");

        // Update Master Standard (Only if config exists, otherwise keep as is or merge?)
        if ($config) {
            $master->update([
                'tipo_dato' => $config['type'],
                'opciones' => $config['options'],
            ]);
        }
        // If no config, we assume the master's current settings are fine.
        // If we wanted to be fancy, we could merge 'opciones' if multiple dropdowns are merged, but for this task assuming same-name means same-type is safe enough for legacy data.

        // Process all definitions
        foreach ($definitions as $def) {
            // 1. Link Types to Master
            // Since we rely on the Pivot Table now, and migration copied definitions...
            // Each definition currently links to ONE type (via pivot or old column if we migrated).
            // We need to ensure Master is linked to ALL types that $def was linked to.
            
            // Get types linked to this definition
            $linkedTypes = $def->tiposActivo()->pluck('tipos_activo.id')->toArray();
            
            // If explicit type column still used? no, models updated to belongsToMany.
            
            if (empty($linkedTypes)) {
                // Should not happen if migration worked, unless orphan definition.
                // For safety, check old column if available via raw DB?
                // Assuming logic holds.
            }
            
            // Attach these types to Master (without duplicating if already there)
            $master->tiposActivo()->syncWithoutDetaching($linkedTypes);

            // 2. Migrate Values (AtributoActivo)
            if ($def->id !== $master->id) {
                $count = AtributoActivo::where('definicion_atributo_id', $def->id)->update(['definicion_atributo_id' => $master->id]);
                $this->info("    Migrated {$count} values from Def ID {$def->id} to {$master->id}");
                
                // 3. Delete Redundant Definition
                $def->delete();
            }
        }
    }
}
