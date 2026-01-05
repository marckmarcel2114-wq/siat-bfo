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
        Schema::create('attribute_definitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tipo_activo_id')->constrained('tipos_activo')->onDelete('cascade');
            $table->string('nombre'); // e.g., "Procesador", "RAM"
            $table->string('tipo_dato')->default('text'); // text, number, select
            $table->json('opciones')->nullable(); // for selects
            $table->integer('orden')->default(0);
            $table->timestamps();
            
            // Unique name per type
            $table->unique(['tipo_activo_id', 'nombre']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_definitions');
    }
};
