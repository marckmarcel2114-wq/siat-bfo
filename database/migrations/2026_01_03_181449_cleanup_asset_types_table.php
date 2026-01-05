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
        Schema::disableForeignKeyConstraints();

        // 1. Drop duplicate table
        Schema::dropIfExists('asset_types');

        // 2. Add missing Spanish types to 'tipos_activo'
        // Original asset_types IDs 10, 11, 12 were: Key/Mouse, Access Point, Otro
        // We will add them if they don't share the same name.
        
        $newTypes = [
            ['nombre' => 'Teclado/Mouse', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Punto de Acceso', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Otro', 'created_at' => now(), 'updated_at' => now()],
        ];

        foreach ($newTypes as $type) {
            // Avoid duplicates
            if (!DB::table('tipos_activo')->where('nombre', $type['nombre'])->exists()) {
                DB::table('tipos_activo')->insert($type);
            }
        }

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-create asset_types (simplified)
        /*
        Schema::create('asset_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
        */
        // Logic to restore data omitted for now as this is a cleanup
    }
};
