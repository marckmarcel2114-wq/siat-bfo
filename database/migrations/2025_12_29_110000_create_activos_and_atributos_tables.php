<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 13. Activos - Tabla Principal (CMDB Core)
        Schema::create('activos', function (Blueprint $table) {
            $table->id();
            
            // Relaciones con catálogos
            $table->foreignId('tipo_activo_id')->constrained('tipos_activo');
            $table->foreignId('modelo_id')->constrained('modelos');
            $table->foreignId('estado_activo_id')->constrained('estados_activo');
            $table->foreignId('criticidad_id')->constrained('niveles_criticidad');
            $table->foreignId('propietario_id')->constrained('propietarios');
            $table->foreignId('ubicacion_id')->constrained('ubicaciones');
            
            // Identificación
            $table->string('codigo_activo', 50)->unique(); // OR30540385
            $table->string('numero_serie', 100)->unique(); // PF4JSL2H
            
            // Datos Financieros (NIIF)
            $table->date('fecha_adquisicion')->nullable();
            $table->decimal('valor_adquisicion', 12, 2)->nullable();
            $table->decimal('valor_residual', 12, 2)->nullable();
            $table->integer('vida_util_anios')->nullable();
            
            // Documentación
            $table->string('ruta_ficha_tecnica', 255)->nullable();
            
            $table->timestamps();
        });

        // 14. Atributos Activos (EAV - Entity Attribute Value)
        Schema::create('atributos_activos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activo_id')->constrained('activos')->onDelete('cascade');
            $table->string('nombre', 100); // ram_gb, os_version, capacidad_kva
            $table->text('valor'); // 16, 24H2, 10
            $table->timestamps();
            
            // Índice para búsquedas rápidas de atributos
            $table->index(['activo_id', 'nombre']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('atributos_activos');
        Schema::dropIfExists('activos');
    }
};
