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
        // --- GESTION DE TAREAS (Supervisor Nacional -> Admin Ciudad) ---

        // 32. Estados Tarea
        Schema::create('estados_tarea', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 30)->unique(); // asignada, en progreso, completada, vencida
            $table->timestamps();
        });

        // 33. Tareas Supervisor
        Schema::create('tareas_supervisor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supervisor_id')->constrained('users'); // Usuario rol supervisor
            $table->string('titulo', 200);
            $table->text('descripcion')->nullable();
            $table->dateTime('fecha_asignacion');
            $table->date('fecha_limite');
            $table->foreignId('estado_tarea_id')->constrained('estados_tarea');
            $table->timestamps();
        });

        // Helper table for Ejecuciones Status corresponding to "pendiente, ejecutada, verificada"
        Schema::create('estados_ejecucion', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 30)->unique();
            $table->timestamps();
        });

        // 34. Ejecuciones de Tarea (Instancia por ciudad/ubicación)
        Schema::create('ejecuciones_tarea', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tarea_id')->constrained('tareas_supervisor')->onDelete('cascade');
            $table->foreignId('admin_ciudad_id')->constrained('users');
            $table->foreignId('ubicacion_id')->constrained('ubicaciones');
            $table->date('fecha_ejecucion')->nullable();
            $table->text('observaciones')->nullable();
            $table->string('acta_ejecucion_path', 255)->nullable();
            $table->foreignId('estado_ejecucion_id')->constrained('estados_ejecucion');
            $table->timestamps();
        });


        // --- COMPRAS Y SOLICITUDES ---

        // 35. Estados Solicitud
        Schema::create('estados_solicitud', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 30)->unique(); // pendiente, aprobado, rechazado, comprado
            $table->timestamps();
        });

        // 36. Solicitudes de Compra
        Schema::create('solicitudes_compra', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ciudad_id')->constrained('cities');
            $table->foreignId('ubicacion_id')->constrained('ubicaciones');
            $table->text('descripcion');
            $table->text('justificacion')->nullable();
            $table->foreignId('estado_solicitud_id')->constrained('estados_solicitud');
            $table->string('pdf_formulario_path', 255)->nullable();
            $table->dateTime('fecha_solicitud');
            $table->dateTime('fecha_aprobacion')->nullable();
            $table->foreignId('solicitado_por_id')->constrained('users');
            $table->foreignId('aprobado_por_id')->nullable()->constrained('users');
            $table->foreignId('comprado_por_id')->nullable()->constrained('users');
            $table->foreignId('proveedor_propuesto_id')->nullable()->constrained('proveedores');
            $table->timestamps();
        });

        // --- SEEDERS ---
        DB::table('estados_tarea')->insert([
            ['nombre' => 'asignada'],
            ['nombre' => 'en progreso'],
            ['nombre' => 'completada'],
            ['nombre' => 'vencida'],
        ]);

        DB::table('estados_ejecucion')->insert([
            ['nombre' => 'pendiente'],
            ['nombre' => 'ejecutada'], // Subida por admin ciudad
            ['nombre' => 'verificada'], // Aprobada por supervisor
        ]);

        DB::table('estados_solicitud')->insert([
            ['nombre' => 'pendiente'],
            ['nombre' => 'aprobado'],
            ['nombre' => 'rechazado'],
            ['nombre' => 'comprado'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitudes_compra');
        Schema::dropIfExists('estados_solicitud');
        
        Schema::dropIfExists('ejecuciones_tarea');
        Schema::dropIfExists('estados_ejecucion');
        Schema::dropIfExists('tareas_supervisor');
        Schema::dropIfExists('estados_tarea');
    }
};
