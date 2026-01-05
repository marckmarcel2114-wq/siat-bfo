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
        Schema::create('opciones_atributos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('definicion_atributo_id')->constrained('definiciones_atributos')->cascadeOnDelete();
            $table->string('nombre');
            $table->timestamps();
        });

        // Migrate Data
        $definitions = DB::table('definiciones_atributos')
            ->whereNotNull('opciones')
            ->get();

        foreach ($definitions as $def) {
            if (empty($def->opciones)) continue;

            $json = $def->opciones;
            if (is_string($json)) {
                 $options = json_decode($json, true);
                 // Handle if decode fails or if it was already not json
                 if (!is_array($options)) {
                     // Try to see if it's a simple string or single value? 
                     // Assuming valid JSON array based on previous checks.
                     continue;
                 }
            } else {
                // Already array? (Unlikely with DB query unless cast)
                continue;
            }

            foreach ($options as $opt) {
                if (is_string($opt) && trim($opt) !== '') {
                    DB::table('opciones_atributos')->insert([
                        'definicion_atributo_id' => $def->id,
                        'nombre' => trim($opt),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
        
        // Optional: Nullify the old column to prevent confusion, or drop it later.
        // For safety, let's keep it for now, but logical switch happens in code.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opciones_atributos');
    }
};
