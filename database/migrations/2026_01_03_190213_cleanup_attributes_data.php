<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Remove non-hardware categories (software, security)
        $categoriesToRemove = ['software', 'security', 'software_license', 'network_config'];
        
        $ids = DB::table('definiciones_atributos')
            ->whereIn('category', $categoriesToRemove)
            ->pluck('id');

        if ($ids->isNotEmpty()) {
            DB::table('atributos_activos')->whereIn('definicion_atributo_id', $ids)->delete();
            DB::table('definicion_atributo_tipo_activo')->whereIn('definicion_atributo_id', $ids)->delete();
            DB::table('definiciones_atributos')->whereIn('id', $ids)->delete();
        }

        // 2. Remove specific "Software" items that might differ in naming
        $namesToRemove = [
            'Versión Office', 
            'Versión Sistema Operativo', 
            'Sistema Operativo', 
            'Antivirus', 
            'Ofimática',
            'Soft. Correo (Outlook)',
            'Soft. Navegador (Edge)',
            'Soft. Antivirus (Kaspersky)',
            'Soft. Protector Pantalla (AD)',
            'Soft. Compresor (Winrar)',
            'Soft. Visor (Adobe Reader)',
            'Soft. Word',
            'Soft. Excel',
            'Soft. PowerPoint', 
            'Carpetas Compartidas Cerradas',
            'NetBank Instalado',
            'Ficha Técnica Actualizada',
            'Form. Aceptación Responsabilidad',
            'Formulario de Excepción',
            'BIOS Acceso Bloqueado',
            'Antivirus (Kaspersky)',
            'USB Bloqueado',
            'Admin. Tareas Bloqueado',
            'OSC Inventory Instalado',
            'Precinto de Seguridad',
            'Motivo Excepción'
        ];

        $idsSoft = DB::table('definiciones_atributos')
            ->whereIn('nombre', $namesToRemove)
            ->pluck('id');

        if ($idsSoft->isNotEmpty()) {
            DB::table('atributos_activos')->whereIn('definicion_atributo_id', $idsSoft)->delete();
            DB::table('definicion_atributo_tipo_activo')->whereIn('definicion_atributo_id', $idsSoft)->delete();
            DB::table('definiciones_atributos')->whereIn('id', $idsSoft)->delete();
        }

        // 3. Merge Duplicates (Old naming -> New Standard naming)
        // Master <- Slave
        $merges = [
            'Capacidad de Memoria' => ['Memoria RAM'],
            'Tipo de Memoria' => ['Tipo Memoria RAM'],
            'Capacidad de Disco' => ['Disco Duro', 'Almacenamiento'],
            'Tipo de Disco' => ['Tipo Disco'],
        ];

        foreach ($merges as $masterName => $slaves) {
            $master = DB::table('definiciones_atributos')->where('nombre', $masterName)->first();
            
            if (!$master) {
                // If master doesn't exist, try to find one of the slaves and promote it?
                // Or skip. Assuming master exists from CentralizeAttributes.
                continue;
            }

            foreach ($slaves as $slaveName) {
                $slave = DB::table('definiciones_atributos')->where('nombre', $slaveName)->first();
                if (!$slave) continue;
                if ($slave->id == $master->id) continue;

                // Move Pivot entries
                // Logic: link master to types that slave was linked to (ignore duplicates)
                $slaveTypes = DB::table('definicion_atributo_tipo_activo')
                    ->where('definicion_atributo_id', $slave->id)
                    ->pluck('tipo_activo_id');

                foreach ($slaveTypes as $typeId) {
                    $exists = DB::table('definicion_atributo_tipo_activo')
                        ->where('definicion_atributo_id', $master->id)
                        ->where('tipo_activo_id', $typeId)
                        ->exists();
                    
                    if (!$exists) {
                        DB::table('definicion_atributo_tipo_activo')->insert([
                            'definicion_atributo_id' => $master->id,
                            'tipo_activo_id' => $typeId
                        ]);
                    }
                }

                // Move Values (AtributoActivo)
                DB::table('atributos_activos')
                    ->where('definicion_atributo_id', $slave->id)
                    ->update(['definicion_atributo_id' => $master->id]);

                // Delete Slave
                DB::table('definicion_atributo_tipo_activo')->where('definicion_atributo_id', $slave->id)->delete();
                DB::table('definiciones_atributos')->where('id', $slave->id)->delete();
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Irreversible cleanup
    }
};
