<?php

namespace App\Observers;

use App\Models\Activo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AssetObserver
{
    /**
     * Handle the Activo "updated" event.
     */
    public function updated(Activo $activo): void
    {
        $userId = Auth::id() ?? 1; // Fallback to 1 if system/cli
        
        foreach ($activo->getDirty() as $field => $newValue) {
            // Skip timestamp updates
            if (in_array($field, ['updated_at', 'created_at'])) continue;

            $oldValue = $activo->getOriginal($field);

            // Log change
            DB::table('historial_cambios')->insert([
                'activo_id' => $activo->id,
                'responsable_id' => $userId,
                'action' => 'updated', // Keeping English 'action' as per schema
                'campo' => $field,
                'valor_anterior' => (string) $oldValue,
                'valor_nuevo' => (string) $newValue,
                'detalles' => "Cambio en $field", 
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Handle the Activo "created" event.
     */
    public function created(Activo $activo): void
    {
        $userId = Auth::id() ?? 1;

        DB::table('historial_cambios')->insert([
            'activo_id' => $activo->id,
            'responsable_id' => $userId,
            'action' => 'created',
            'campo' => 'all',
            'valor_anterior' => null,
            'valor_nuevo' => 'Activo Creado',
            'detalles' => 'Creación inicial',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
