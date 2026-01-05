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
        // 1. Tipos de Ubicación (sucursal, agencia, paf, etc.)
        Schema::create('tipos_ubicacion', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50)->unique(); // sucursal, agencia, paf, atm, cpd
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // 2. Proveedores (Hardware, Mantenimiento)
        Schema::create('proveedores', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150);
            $table->string('nit', 20)->nullable();
            $table->string('direccion', 255)->nullable();
            $table->string('telefono', 30)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('tipo_proveedor', 50)->nullable(); // Hardware, Mantenimiento
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // 3. Estados de Usuario (activo, baja, libre)
        Schema::create('estados_usuario', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 30)->unique();
            $table->timestamps();
        });

        // 4. Tipos de Activo (Laptop, UPS, Switch...)
        Schema::create('tipos_activo', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100)->unique();
            $table->timestamps();
        });

        // 5. Marcas (Lenovo, Dell...)
        Schema::create('marcas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100)->unique();
            $table->timestamps();
        });

        // 6. Modelos (OptiPlex 3000...)
        Schema::create('modelos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marca_id')->constrained('marcas')->onDelete('cascade');
            $table->foreignId('tipo_activo_id')->constrained('tipos_activo')->onDelete('cascade');
            $table->string('nombre', 150);
            $table->timestamps();
        });

        // 7. Estados de Activo (activo, mantenimiento, baja)
        Schema::create('estados_activo', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50)->unique();
            $table->timestamps();
        });

        // 8. Niveles de Criticidad (alta, media, baja)
        Schema::create('niveles_criticidad', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 20)->unique();
            $table->tinyInteger('nivel_numerico'); // 1, 2, 3
            $table->timestamps();
        });

        // 9. Propietarios (BANCO FORTALEZA, DATEC)
        Schema::create('propietarios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100)->unique();
            $table->timestamps();
        });
        
        // Seeding initial data for definitions
        DB::table('tipos_ubicacion')->insert([
           ['nombre' => 'sucursal', 'activo' => true],
           ['nombre' => 'agencia', 'activo' => true],
           ['nombre' => 'paf', 'activo' => true],
           ['nombre' => 'atm', 'activo' => true],
           ['nombre' => 'cpd', 'activo' => true],
        ]);

        DB::table('estados_usuario')->insert([
            ['nombre' => 'activo'],
            ['nombre' => 'libre'],
            ['nombre' => 'baja'],
        ]);
        
        DB::table('estados_activo')->insert([
            ['nombre' => 'activo'],
            ['nombre' => 'en mantenimiento'],
            ['nombre' => 'dado de baja'],
            ['nombre' => 'obsoleto'],
        ]);

        DB::table('niveles_criticidad')->insert([
            ['nombre' => 'baja', 'nivel_numerico' => 1],
            ['nombre' => 'media', 'nivel_numerico' => 2],
            ['nombre' => 'alta', 'nivel_numerico' => 3],
        ]);

        DB::table('propietarios')->insert([
            ['nombre' => 'BANCO FORTALEZA'],
            ['nombre' => 'OUTSOURCING DATEC'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('propietarios');
        Schema::dropIfExists('niveles_criticidad');
        Schema::dropIfExists('estados_activo');
        Schema::dropIfExists('modelos');
        Schema::dropIfExists('marcas');
        Schema::dropIfExists('tipos_activo');
        Schema::dropIfExists('estados_usuario');
        Schema::dropIfExists('proveedores');
        Schema::dropIfExists('tipos_ubicacion');
    }
};
