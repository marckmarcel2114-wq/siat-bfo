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
        Schema::table('historial_cambios', function (Blueprint $table) {
            $table->string('campo')->nullable();
            $table->text('valor_anterior')->nullable();
            $table->text('valor_nuevo')->nullable();
            // 'detalles' modification removed to avoid doctrine/dbal dependency issues
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('historial_cambios', function (Blueprint $table) {
            $table->dropColumn(['campo', 'valor_anterior', 'valor_nuevo']);
            $table->string('detalles')->nullable(false)->change(); // Revert logic potentially
        });
    }
};
