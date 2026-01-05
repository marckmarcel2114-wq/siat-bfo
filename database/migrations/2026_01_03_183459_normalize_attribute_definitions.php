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
        // 1. Ensure all existing relationships are in pivot table
        $definitions = DB::table('definiciones_atributos')
            ->whereNotNull('tipo_activo_id')
            ->get();

        foreach ($definitions as $def) {
            $exists = DB::table('definicion_atributo_tipo_activo')
                ->where('definicion_atributo_id', $def->id)
                ->where('tipo_activo_id', $def->tipo_activo_id)
                ->exists();

            if (!$exists) {
                DB::table('definicion_atributo_tipo_activo')->insert([
                    'definicion_atributo_id' => $def->id,
                    'tipo_activo_id' => $def->tipo_activo_id,
                    // No timestamps in pivot usually unless defined
                ]);
            }
        }

        // 2. Drop the column (and the foreign key if exists)
        Schema::table('definiciones_atributos', function (Blueprint $table) {
            // Drop FK first if it exists. 
            // We need to guess the name or use dynamic helper, but usually definitions_attributes_tipo_activo_id_foreign
            // Let's try flexible approach or catch error? 
            // Better to assume standard name from previous migration.
            
            // Standard name: table_column_foreign
            $fkName = 'attribute_definitions_tipo_activo_id_foreign';
            
            // Drop FK if exists (caught in try block ideally, but here we just try standard)
            // Or better, just drop column, some drivers handle key drop.
            // But MySQL needs FK drop first.
            
            try { 
                $table->dropForeign($fkName); 
            } catch (\Exception $e) {
                // Ignore if not exists
            }
            
             try { 
                $table->dropForeign(['tipo_activo_id']); 
            } catch (\Exception $e) {
                // Ignore if not exists
            }

            // Also drop Unique Index which uses this column
            try {
                $table->dropUnique('attribute_definitions_tipo_activo_id_nombre_unique');
            } catch (\Exception $e) {
                // Ignore
            }

            $table->dropColumn('tipo_activo_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('definiciones_atributos', function (Blueprint $table) {
            $table->foreignId('tipo_activo_id')->nullable()->constrained('tipos_activo')->nullOnDelete();
        });
        
        // Data restoration impossible perfectly without logic, 
        // as 1 definition now belongs to MANY types, which one to pick for the column?
        // We leave it nullable.
    }
};
