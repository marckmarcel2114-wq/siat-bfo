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
        Schema::create('branch_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Sucursal, Agencia, ATM, Oficina Externa
            $table->timestamps();
        });

        Schema::create('asset_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Laptop, Desktop, Server, Router, Switch
            $table->timestamps();
        });

        Schema::create('maintenance_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Preventivo, Correctivo
            $table->timestamps();
        });

        Schema::create('task_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Prueba Corte, Inventario
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_types');
        Schema::dropIfExists('maintenance_types');
        Schema::dropIfExists('asset_types');
        Schema::dropIfExists('branch_types');
    }
};
