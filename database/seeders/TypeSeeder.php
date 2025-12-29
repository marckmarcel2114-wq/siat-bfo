<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Branch Types
        $branchTypes = ['Sucursal', 'Agencia', 'ATM', 'Oficina Externa', 'PAF'];
        foreach ($branchTypes as $type) {
            DB::table('branch_types')->insertOrIgnore(['name' => $type, 'created_at' => now(), 'updated_at' => now()]);
        }
        
        // Asset Types
        $assetTypes = [
            'Desktop', 'Laptop', 'Server', 'Router', 'Switch', 'Firewall', 'Printer', 'UPS', 'Monitor', 'Key/Mouse', 'Access Point', 'Otro'
        ];
        foreach ($assetTypes as $type) {
            DB::table('asset_types')->insertOrIgnore(['name' => $type, 'created_at' => now(), 'updated_at' => now()]);
        }
        
        // Maintenance Types
        $maintTypes = ['Preventivo', 'Correctivo', 'Instalación', 'Configuración'];
        foreach ($maintTypes as $type) {
            DB::table('maintenance_types')->insertOrIgnore(['name' => $type, 'created_at' => now(), 'updated_at' => now()]);
        }
        
        // Task Types
        $taskTypes = ['Prueba de Corte', 'Inventario', 'Actualización Software', 'Mantenimiento General', 'Auditoría'];
        foreach ($taskTypes as $type) {
            DB::table('task_types')->insertOrIgnore(['name' => $type, 'created_at' => now(), 'updated_at' => now()]);
        }
    }
}
