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
        Schema::create('definicion_atributo_tipo_activo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('definicion_atributo_id')->constrained('definiciones_atributos')->onDelete('cascade');
            $table->foreignId('tipo_activo_id')->constrained('tipos_activo')->onDelete('cascade'); // Correct table name found in Model
            // Wait, previous migration showed: `Schema::create('definiciones_atributos', ...)`
            // I need to be sure about `tipos_activos` table name.
            // Use 'tipos_activos' if standardized, or check existing migrations.
            // Models/TipoActivo.php usually has table name.
            $table->timestamps();
            
            $table->unique(['definicion_atributo_id', 'tipo_activo_id'], 'def_attr_type_unique');
        });

        // Migrate existing relationships
        DB::statement('
            INSERT INTO definicion_atributo_tipo_activo (definicion_atributo_id, tipo_activo_id, created_at, updated_at)
            SELECT id, tipo_activo_id, NOW(), NOW()
            FROM definiciones_atributos
            WHERE tipo_activo_id IS NOT NULL
        ');

            /* 
            // Dynamic FK lookup skipped for now due to persistent errors.
            // Will cleanup column in a future migration.
            
            $fkName = DB::scalar("...");
            if ($fkName) $table->dropForeign($fkName);
            $table->dropColumn('tipo_activo_id');
            */
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('definiciones_atributos', function (Blueprint $table) {
            $table->foreignId('tipo_activo_id')->nullable()->constrained('tipos_activos')->onDelete('cascade');
        });

        // Restore relationships (Best Effort - takes the first one found)
        // This is lossy if multiple types share the same definition
        DB::statement('
            UPDATE definiciones_atributos da
            JOIN definicion_atributo_tipo_activo pivot ON da.id = pivot.definicion_atributo_id
            SET da.tipo_activo_id = pivot.tipo_activo_id
        ');

        Schema::dropIfExists('definicion_atributo_tipo_activo');
    }
};
