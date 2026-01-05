<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AtributoActivo;
use App\Models\DefinicionAtributo;
use Illuminate\Support\Facades\DB;

class NormalizeAssets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:normalize-assets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Limpia y simplifica los atributos de los activos, especialmente procesadores.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Iniciando normalización de atributos...");

        $this->normalizeProcessors();
        $this->splitRamAndStorage();
        $this->deduplicateAttributes();
        $this->trimAllAttributes();

        $this->info("Proceso completado con éxito.");
    }

    private function splitRamAndStorage()
    {
        $this->info("Separando capacidades y tipos de RAM/Disco...");

        // 1. Process RAM
        $ramAttrs = AtributoActivo::whereHas('definicion', fn($q) => $q->where('nombre', 'Memoria RAM'))->get();
        foreach ($ramAttrs as $attr) {
            $valor = $attr->valor; 
            if (preg_match('/(\d+\s*(GB|MB|TB))\s*(.*)/i', $valor, $matches)) {
                $capacidad = trim($matches[1]);
                $tipo = trim($matches[3]);

                $this->createOrUpdateAttribute($attr->activo_id, 'Capacidad de Memoria', $capacidad);
                if ($tipo) {
                    $this->createOrUpdateAttribute($attr->activo_id, 'Tipo de Memoria', $tipo);
                }
                $attr->delete();
            }
        }

        // 2. Process Storage
        $storageAttrs = AtributoActivo::whereHas('definicion', fn($q) => $q->where('nombre', 'Almacenamiento'))->get();
        foreach ($storageAttrs as $attr) {
            $valor = $attr->valor; 
            if (preg_match('/(\d+\s*(GB|MB|TB))\s*(.*)/i', $valor, $matches)) {
                $capacidad = trim($matches[1]);
                $tipo = trim($matches[3]);

                $this->createOrUpdateAttribute($attr->activo_id, 'Capacidad de Disco', $capacidad);
                if ($tipo) {
                    $this->createOrUpdateAttribute($attr->activo_id, 'Tipo de Disco', $tipo);
                }
                $attr->delete();
            }
        }
    }

    private function createOrUpdateAttribute($activoId, $nombre, $valor)
    {
        // Find if definition exists or create one based on first asset's type
        $activo = \App\Models\Activo::find($activoId);
        if (!$activo) return;

        $def = DefinicionAtributo::where('nombre', $nombre)
            ->where('tipo_activo_id', $activo->tipo_activo_id)
            ->first();

        if (!$def) {
            $def = DefinicionAtributo::create([
                'tipo_activo_id' => $activo->tipo_activo_id,
                'nombre' => $nombre,
                'category' => 'hardware',
                // 'slug' if needed, but not in fillable? assuming not needed strictly or handled by boot
                'tipo_dato' => 'text', 
                'orden' => 10
            ]);
        }

        AtributoActivo::updateOrCreate(
            ['activo_id' => $activoId, 'definicion_atributo_id' => $def->id],
            ['valor' => $valor]
        );
    }

    private function normalizeProcessors()
    {
        $this->info("Simplicando nombres de procesadores...");
        
        $atributos = AtributoActivo::whereHas('definicion', fn($q) => $q->where('nombre', 'Procesador'))->get();
        $count = 0;

        foreach ($atributos as $attr) {
            $original = $attr->valor;
            $new = $original;

            // 1. Core i3/i5/i7/i9
            if (preg_match('/(Core|i[3579])/i', $new, $matches)) {
                if (preg_match('/i[3579]/i', $new, $subMatches)) {
                    $new = "Core " . strtoupper($subMatches[0]);
                }
            }
            
            // 2. Ryzen
            if (preg_match('/Ryzen\s*([3579])/i', $new, $matches)) {
                $new = "Ryzen " . $matches[1];
            }

            // 3. Celeron / Pentium
            if (stripos($new, 'Celeron') !== false) $new = "Celeron";
            if (stripos($new, 'Pentium') !== false) $new = "Pentium";
            if (stripos($new, 'Xeon') !== false) $new = "Xeon";

            if ($new !== $original) {
                $attr->valor = $new;
                $attr->save();
                $count++;
            }
        }

        $this->line("Procesadores actualizados: $count");
    }

    private function deduplicateAttributes()
    {
        $this->info("Eliminando duplicados redundantes...");
        
        // Find assets with more than one instance of the same attribute definition
        $duplicates = AtributoActivo::select('activo_id', 'definicion_atributo_id', DB::raw('COUNT(*) as total'))
            ->groupBy('activo_id', 'definicion_atributo_id')
            ->having('total', '>', 1)
            ->get();

        $removed = 0;
        foreach ($duplicates as $dup) {
            // Keep the one with more content or the latest one
            $items = AtributoActivo::where('activo_id', $dup->activo_id)
                ->where('definicion_atributo_id', $dup->definicion_atributo_id)
                ->orderByDesc('id')
                ->get();

            $keep = $items->first();
            foreach ($items as $index => $item) {
                if ($index === 0) continue; // Keep the first
                $item->delete();
                $removed++;
            }
        }

        $this->line("Atributos duplicados eliminados: $removed");
        
        // Also remove empty ones
        $emptyCount = AtributoActivo::where('valor', '')->orWhereNull('valor')->delete();
        if ($emptyCount > 0) {
            $this->line("Atributos vacíos eliminados: $emptyCount");
        }
    }

    private function trimAllAttributes()
    {
        $this->info("Limpiando espacios en blanco adicionales...");
        $count = 0;
        
        AtributoActivo::all()->each(function($a) use (&$count) {
            $trimmed = trim(preg_replace('/\s+/', ' ', $a->valor));
            if ($a->valor !== $trimmed) {
                $a->valor = $trimmed;
                $a->save();
                $count++;
            }
        });

        $this->line("Registros con espacios corregidos: $count");
    }
}
