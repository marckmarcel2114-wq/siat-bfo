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
        // 15. Puntos de Red (Infraestructura física)
        Schema::create('puntos_red', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ubicacion_id')->constrained('ubicaciones');
            $table->string('patch_panel', 20);
            $table->string('roseta', 20);
            $table->string('switch', 50)->nullable();
            $table->string('descripcion', 255)->nullable();
            $table->timestamps();

            // Índice único recomendado por el usuario
            $table->unique(['patch_panel', 'roseta', 'ubicacion_id'], 'puntos_red_unique_idx');
        });

        // 16. Resultados Certificación (Catálogo)
        Schema::create('resultados_certificacion', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 20)->unique(); // aprobado, rechazado
            $table->timestamps();
        });

        // 17. Certificaciones Red (Historial de pruebas)
        Schema::create('certificaciones_red', function (Blueprint $table) {
            $table->id();
            $table->foreignId('punto_red_id')->constrained('puntos_red')->onDelete('cascade');
            $table->string('tipo_certificacion', 20); // Cat 5, Cat 6
            $table->foreignId('resultado_id')->constrained('resultados_certificacion');
            $table->dateTime('fecha_certificacion');
            $table->string('tecnico_responsable', 150);
            $table->string('informe_certificacion_path', 255)->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });

        // 18. Asignaciones de Red (Configuración lógica por activo)
        Schema::create('asignaciones_red', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activo_id')->constrained('activos')->onDelete('cascade');
            $table->foreignId('punto_red_id')->nullable()->constrained('puntos_red');
            $table->string('ip_address', 15)->nullable();
            $table->string('mac_ethernet', 17)->nullable();
            $table->string('mac_wifi', 17)->nullable();
            $table->string('numero_interno', 20)->nullable();
            $table->dateTime('fecha_asignacion');
            $table->dateTime('fecha_baja')->nullable();
            $table->boolean('es_actual')->default(true);
            $table->timestamps();
        });

        // 19. Asignaciones (Custodia del activo - Actas)
        Schema::create('asignaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activo_id')->constrained('activos')->onDelete('cascade');
            $table->foreignId('usuario_id')->nullable()->constrained('users'); // NULL = libre
            $table->dateTime('fecha_asignacion');
            $table->dateTime('fecha_devolucion')->nullable();
            $table->string('acta_entrega_path', 255)->nullable();
            $table->string('acta_devolucion_path', 255)->nullable();
            $table->boolean('es_actual')->default(true);
            $table->timestamps();
        });

        // Seed initial data for certification results
        DB::table('resultados_certificacion')->insert([
            ['nombre' => 'aprobado'],
            ['nombre' => 'rechazado'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asignaciones');
        Schema::dropIfExists('asignaciones_red');
        Schema::dropIfExists('certificaciones_red');
        Schema::dropIfExists('resultados_certificacion');
        Schema::dropIfExists('puntos_red');
    }
};
