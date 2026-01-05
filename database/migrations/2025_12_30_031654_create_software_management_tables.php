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
        // 1. Licenses Catalog
        Schema::create('software_licenses', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); // e.g. "Office 2021", "Windows 11 Pro"
            $table->string('key')->nullable(); // License Key
            $table->string('tipo')->default('OEM'); // OEM, Volume, Subscription, Free
            $table->integer('seats_total')->default(1);
            $table->integer('seats_used')->default(0);
            $table->date('fecha_expiracion')->nullable();
            $table->foreignId('proveedor_id')->nullable()->constrained('proveedores')->nullOnDelete();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });

        // 2. Installations on Assets
        Schema::create('software_installations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('license_id')->nullable()->constrained('software_licenses')->nullOnDelete();
            $table->foreignId('activo_id')->constrained('activos')->cascadeOnDelete();
            $table->date('fecha_instalacion');
            $table->foreignId('registrado_por')->constrained('users'); // Who installed it
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('software_installations');
        Schema::dropIfExists('software_licenses');
    }
};
