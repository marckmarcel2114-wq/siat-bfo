<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Activo;
use App\Models\Marca;
use App\Models\Modelo;
use App\Models\TipoActivo;
use App\Models\Ubicacion; // Assuming Ubicacion corresponds to 'Agencia /Oficina'
use App\Models\Ciudad; // Assuming we need this for Ubicacion
use App\Models\Propietario;
use App\Models\EstadoActivo; // To set default status
use App\Models\NivelCriticidad; // To set default criticality
use App\Models\AtributoActivo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportAssetsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:assets {file : Path to the CSV file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import assets from a CSV file (Reference: Verificación y Control de Computadoras BFO)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $file = $this->argument('file');

        if (!file_exists($file)) {
            $this->error("File not found: $file");
            return 1;
        }

        $this->info("Importing assets from: $file");

        $handle = fopen($file, 'r');
        $header = fgetcsv($handle, 1000, ';'); // Semi-colon separator based on previous view

        // Map Header Columns to Index
        $map = array_flip($header);
        
        // Helper to get value
        $val = function($row, $colName) use ($map) {
            return isset($map[$colName]) && isset($row[$map[$colName]]) ? trim($row[$map[$colName]]) : null;
        };

        // Pre-load or Defaults
        $defaultStatus = EstadoActivo::firstOrCreate(['nombre' => 'Operativo']);
        $defaultCriticidad = NivelCriticidad::firstOrCreate(['nombre' => 'Media'], ['nivel_numerico' => 5, 'color' => '#fbbf24']); // Default mid
        
        // Type Mapping
        $typeMapping = [
            'PORTÁTIL' => 'Laptop',
            'ESCRITORIO' => 'Desktop',
            // Add others if needed
        ];

        DB::beginTransaction();
        try {
            $count = 0;
            while (($row = fgetcsv($handle, 2000, ';')) !== false) {
                // Skip empty rows
                if (empty(implode('', $row))) continue;

                $code = $val($row, 'Codigo de Activo (Nombre de Equipo)');
                if (!$code) continue; // Skip if no code

                $this->info("Processing: $code");

                // 1. Resolve Type
                $rawType = $val($row, 'TIPO DE EQUIPO');
                $normalizedType = $typeMapping[strtoupper($rawType)] ?? $rawType;
                $tipo = TipoActivo::firstOrCreate(['nombre' => $normalizedType]);

                // 2. Resolve Brand
                $rawBrand = $val($row, 'MARCA');
                $marca = Marca::firstOrCreate(['nombre' => $rawBrand]);

                // 3. Resolve Model
                $rawModel = $val($row, 'MODELO');
                $modelo = Modelo::firstOrCreate(
                    ['nombre' => $rawModel, 'marca_id' => $marca->id],
                    ['marca_id' => $marca->id] // Should be redundant but safe
                );

                // 4. Resolve Location (Simple Logic: Create location by name, associate with dummy city if needed)
                // CSV has 'Ubicación del Equipo' (e.g., Gerencia 1er Piso) and 'Agencia /Oficina' (e.g., Sucursal Central)
                $agenciaName = $val($row, 'Agencia /Oficina');
                
                // Try to find existing location or create. 
                $defaultCity = Ciudad::firstOrCreate(['name' => 'Oficina Central'], ['country_id' => 1]); // Adjust as needed
                
                $ubicacion = Ubicacion::firstOrCreate(
                    ['nombre' => $agenciaName],
                    ['ciudad_id' => $defaultCity->id, 'direccion' => 'Importado']
                );


                // 5. Resolve Owner
                $rawOwner = $val($row, 'PROPIEDAD DE EQUIPO') ?? 'BANCO FORTALEZA';
                $propietario = Propietario::firstOrCreate(['nombre' => $rawOwner]);

                // 6. Create/Update Asset using updateOrCreate to prevent duplicates
                $asset = Activo::updateOrCreate(
                    ['codigo_activo' => $code], // Search keys
                    [
                        'numero_serie' => $val($row, 'SERIE') ?? 'S/N',
                        'tipo_activo_id' => $tipo->id,
                        'marca_id' => $marca->id,
                        'modelo_id' => $modelo->id,
                        // Only update status/owner if needed, but for import we force match
                        'estado_activo_id' => $defaultStatus->id, 
                        'criticidad_id' => $defaultCriticidad->id,
                        'ubicacion_id' => $ubicacion->id,
                        'propietario_id' => $propietario->id,
                        
                        // Network Fields (New)
                        'ip_address' => $val($row, 'IPAsignado'),
                        'mac_ethernet' => $val($row, 'MAC Ethernet '),
                        'mac_wifi' => $val($row, 'MAC  Wi-Fi'),
                    ]
                );

                // 7. Dynamic Attributes (EAV)
                $specs = [
                    'Procesador' => $val($row, 'PROCESADOR'),
                    'Memoria RAM' => $val($row, 'CAPACIDAD DE MEMORIA'),
                    'Disco Duro' => $val($row, 'CAPACIDAD DE DISCO'),
                    'Tipo Disco' => $val($row, 'TIPO DE DISCO'),
                    'Sistema Operativo' => $val($row, 'Sistema Operativo') . ' ' . $val($row, 'Version Sistema Operativo'),
                    'Generación' => $val($row, 'GENERACION '),
                ];

                foreach ($specs as $key => $value) {
                    if ($value) {
                         AtributoActivo::updateOrCreate(
                            ['activo_id' => $asset->id, 'nombre' => $key],
                            ['valor' => $value]
                        );
                    }
                }
                
                $count++;
            }
            DB::commit();
            $this->info("Imported $count assets successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Error importing: " . $e->getMessage());
            $this->error("Trace: " . $e->getTraceAsString());
            return 1;
        }

        return 0;
    }
}
