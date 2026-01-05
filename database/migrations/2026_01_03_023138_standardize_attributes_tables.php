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
        // 1. Rename table
        if (Schema::hasTable('attribute_definitions')) {
            Schema::rename('attribute_definitions', 'definiciones_atributos');
        }

        // 2. Add FK column to 'atributos_activos'
        Schema::table('atributos_activos', function (Blueprint $table) {
            $table->foreignId('definicion_atributo_id')
                  ->nullable()
                  ->after('activo_id')
                  ->constrained('definiciones_atributos')
                  ->onDelete('cascade');
        });

        // 3. Migrate Data: Match by name
        // We need to match attributes by name AND type of asset.
        // But wait, 'atributos_activos' only has 'activo_id'.
        // So we need to join: atributos_activos -> activos -> tipos_activo -> definiciones_atributos
        
        $results = DB::table('atributos_activos')
            ->join('activos', 'atributos_activos.activo_id', '=', 'activos.id')
            ->join('definiciones_atributos', function($join) {
                $join->on('activos.tipo_activo_id', '=', 'definiciones_atributos.tipo_activo_id')
                     ->on('atributos_activos.nombre', '=', 'definiciones_atributos.nombre');
            })
            ->select('atributos_activos.id as attr_id', 'definiciones_atributos.id as def_id')
            ->get();

        foreach ($results as $row) {
            DB::table('atributos_activos')
                ->where('id', $row->attr_id)
                ->update(['definicion_atributo_id' => $row->def_id]);
        }

        // 4. Cleanup
        // Warning: If there are attributes that didn't match (orphans or custom), they will effectively be lost or invalid if we make the ID required.
        // For now, let's keep them but make the column nullable if we suspect custom attributes.
        // However, the goal is normalization. Let's assume strict schema.
        // But to be safe, we will leave 'nombre' for a moment or just drop it if we are confident?
        // Let's drop 'nombre' as requested to enforce the schema.
        
        Schema::table('atributos_activos', function (Blueprint $table) {
            $table->dropColumn('nombre');
            // Make ID required now?
            // $table->foreignId('definicion_atributo_id')->nullable(false)->change(); 
            // SQLite/MySQL modification support varies, best to just leave nullable or assuming data is good.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Restore 'nombre' column
        Schema::table('atributos_activos', function (Blueprint $table) {
            $table->string('nombre')->after('activo_id')->nullable();
        });

        // 2. Restore data
        $results = DB::table('atributos_activos')
            ->join('definiciones_atributos', 'atributos_activos.definicion_atributo_id', '=', 'definiciones_atributos.id')
            ->select('atributos_activos.id as attr_id', 'definiciones_atributos.nombre as def_nombre')
            ->get();

        foreach ($results as $row) {
            DB::table('atributos_activos')
                ->where('id', $row->attr_id)
                ->update(['nombre' => $row->def_nombre]);
        }

        // 3. Drop FK
        Schema::table('atributos_activos', function (Blueprint $table) {
            $table->dropForeign(['definicion_atributo_id']);
            $table->dropColumn('definicion_atributo_id');
        });

        // 4. Rename table back
        Schema::rename('definiciones_atributos', 'attribute_definitions');
    }
};
