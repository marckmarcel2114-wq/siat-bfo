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
        Schema::create('software_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('software_id')->constrained('software')->onDelete('cascade');
            $table->string('version');
            $table->date('fecha_lanzamiento')->nullable();
            $table->date('eol_date')->nullable(); // End of Life
            $table->text('descripcion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('software_versions');
    }
};
