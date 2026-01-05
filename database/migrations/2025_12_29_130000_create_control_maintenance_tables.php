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
        // --- CONTROLES Y CUMPLIMIENTO ---

        // 20. Tipos de Control (Seguridad, Físico)
        Schema::create('tipos_control', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50)->unique();
            $table->timestamps();
        });

        // 21. Controles (Reglas de cumplimiento)
        Schema::create('controles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tipo_activo_id')->nullable()->constrained('tipos_activo'); // NULL = para todos
            $table->foreignId('tipo_control_id')->constrained('tipos_control');
            $table->string('nombre', 100); // BIOS Bloqueado, etc.
            $table->boolean('obligatorio')->default(true);
            $table->timestamps();
        });

        // 22. Estados de Cumplimiento (SI, NO, PENDIENTE)
        Schema::create('estados_cumplimiento', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 20)->unique();
            $table->timestamps();
        });

        // 23. Cumplimientos (Verificación con evidencia)
        Schema::create('cumplimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activo_id')->constrained('activos')->onDelete('cascade');
            $table->foreignId('control_id')->constrained('controles');
            $table->foreignId('estado_id')->constrained('estados_cumplimiento');
            $table->dateTime('fecha_verificacion');
            $table->string('usuario_responsable', 150);
            $table->string('evidencia_path', 255)->nullable();
            $table->timestamps();
        });

        // --- ESTANDARES (Motor de reglas) ---

        // 24. Tipos de Estándar (Hardware, Sist. Operativo)
        Schema::create('tipos_estandar', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50)->unique();
            $table->timestamps();
        });

        // 25. Operadores lógicos
        Schema::create('operadores', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 10)->unique(); // >=, =, IN
            $table->timestamps();
        });

        // 26. Estándares (Reglas parametrizables)
        Schema::create('estandares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tipo_estandar_id')->constrained('tipos_estandar');
            $table->foreignId('tipo_activo_id')->nullable()->constrained('tipos_activo');
            $table->string('atributo_clave', 100); // os_version, ram_gb
            $table->foreignId('operador_id')->constrained('operadores');
            $table->string('valor_esperado', 100);
            $table->string('descripcion', 255)->nullable();
            $table->timestamps();
        });

        // --- MANTENIMIENTO ---

        // 27. Tipos de Mantenimiento
        Schema::create('tipos_mantenimiento', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 20)->unique(); // preventivo, correctivo
            $table->timestamps();
        });

        // 28. Estados de Mantenimiento
        Schema::create('estados_mantenimiento', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 20)->unique(); // programado, finalizado
            $table->timestamps();
        });

        // 29. Mantenimientos (Registro)
        Schema::create('mantenimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activo_id')->constrained('activos')->onDelete('cascade');
            $table->foreignId('proveedor_id')->nullable()->constrained('proveedores');
            $table->foreignId('tipo_mantenimiento_id')->constrained('tipos_mantenimiento');
            $table->foreignId('estado_mantenimiento_id')->constrained('estados_mantenimiento');
            $table->date('fecha_inicio');
            $table->decimal('costo_bs', 10, 2)->nullable();
            $table->string('hoja_trabajo', 20)->nullable();
            $table->string('nota_servicio_path', 255)->nullable();
            $table->timestamps();
        });

        // --- AUDITORIA Y FINANZAS ---

        // 30. Historial de Cambios (Bitácora)
        Schema::create('historial_cambios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activo_id')->constrained('activos')->onDelete('cascade');
            $table->string('campo_modificado', 100);
            $table->text('valor_anterior')->nullable();
            $table->text('valor_nuevo')->nullable();
            $table->dateTime('fecha');
            // usuario_responsable_id references users.id
            $table->foreignId('usuario_responsable_id')->constrained('users');
            $table->timestamps();
        });

        // 31. Depreciación (NIIF)
        Schema::create('depreciacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activo_id')->constrained('activos')->onDelete('cascade');
            $table->date('periodo'); // Último día del mes
            $table->decimal('valor_inicial', 12, 2);
            $table->decimal('depreciacion_mensual', 10, 2);
            $table->decimal('valor_neto', 12, 2);
            $table->timestamps();

            $table->unique(['activo_id', 'periodo']);
        });

        // --- SEEDERS for Definitions ---
        DB::table('tipos_control')->insert([
            ['nombre' => 'Seguridad'],
            ['nombre' => 'Físico'],
        ]);
        
        DB::table('estados_cumplimiento')->insert([
            ['nombre' => 'SI'],
            ['nombre' => 'NO'],
            ['nombre' => 'PENDIENTE'],
        ]);

        DB::table('tipos_estandar')->insert([
            ['nombre' => 'Hardware'],
            ['nombre' => 'Sistema Operativo'],
            ['nombre' => 'Software'],
        ]);

        DB::table('operadores')->insert([
            ['nombre' => '>='],
            ['nombre' => '='],
            ['nombre' => 'IN'],
            ['nombre' => '<'],
        ]);

        DB::table('tipos_mantenimiento')->insert([
            ['nombre' => 'preventivo'],
            ['nombre' => 'correctivo'],
        ]);

        DB::table('estados_mantenimiento')->insert([
            ['nombre' => 'programado'],
            ['nombre' => 'en curso'],
            ['nombre' => 'finalizado'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('depreciacion');
        Schema::dropIfExists('historial_cambios');
        Schema::dropIfExists('mantenimientos');
        Schema::dropIfExists('estados_mantenimiento');
        Schema::dropIfExists('tipos_mantenimiento');
        
        Schema::dropIfExists('estandares');
        Schema::dropIfExists('operadores');
        Schema::dropIfExists('tipos_estandar');
        
        Schema::dropIfExists('cumplimientos');
        Schema::dropIfExists('estados_cumplimiento');
        Schema::dropIfExists('controles');
        Schema::dropIfExists('tipos_control');
    }
};
