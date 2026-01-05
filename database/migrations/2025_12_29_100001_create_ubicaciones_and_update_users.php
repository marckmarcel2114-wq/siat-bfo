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
        // 3. Ubicaciones (Sucursales, agencias, atm, etc.)
        Schema::create('ubicaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ciudad_id')->constrained('cities')->onDelete('cascade');
            $table->foreignId('tipo_ubicacion_id')->constrained('tipos_ubicacion');
            $table->string('nombre', 150);
            $table->string('codigo_ubicacion', 20)->nullable();
            $table->timestamps();
        });

        // Modificar usuarios
        Schema::table('users', function (Blueprint $table) {
            // Separar nombre/apellido si es necesario (asumiendo que 'name' tiene todo por ahora)
            if (!Schema::hasColumn('users', 'lastname')) {
                $table->string('lastname', 100)->nullable()->after('name');
            }
            if (!Schema::hasColumn('users', 'cargo')) {
                $table->string('cargo', 150)->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'area')) {
                $table->string('area', 100)->nullable()->after('cargo');
            }
            // estado_id relation
            if (!Schema::hasColumn('users', 'estado_id')) {
                $table->foreignId('estado_id')->nullable()->constrained('estados_usuario');
            }
            if (!Schema::hasColumn('users', 'es_admin_ciudad')) {
                $table->boolean('es_admin_ciudad')->default(false);
            }
            if (!Schema::hasColumn('users', 'es_supervisor_nacional')) {
                $table->boolean('es_supervisor_nacional')->default(false);
            }
            // ciudad_id already exists in previous migrations or original schema? 
            // In 0001_01_01_000000_create_users_table.php usually not, but let's check.
            // Assuming it might not exist or we want to ensure it links to 'cities'.
            if (!Schema::hasColumn('users', 'ciudad_id')) {
                 $table->foreignId('ciudad_id')->nullable()->constrained('cities');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['estado_id']);
            $table->dropColumn(['lastname', 'cargo', 'area', 'estado_id', 'es_admin_ciudad', 'es_supervisor_nacional']);
             // careful dropping ciudad_id if it existed before
        });
        Schema::dropIfExists('ubicaciones');
    }
};
