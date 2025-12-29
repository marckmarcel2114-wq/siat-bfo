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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            
            // Normalized Type
            $table->foreignId('asset_type_id')->constrained()->cascadeOnDelete();
            
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('code_internal')->nullable(); // Activo Fijo / Internal ID
            
            $table->date('purchase_date')->nullable();
            $table->date('warranty_expiry_date')->nullable();
            
            $table->foreignId('location_id')->nullable()->constrained('branches')->nullOnDelete();
            
            $table->string('network_point')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('mac_address')->nullable();
            
            $table->string('status')->default('free'); // free, assigned, maintenance, broken...
            
            $table->json('specs')->nullable(); // CPU, RAM, HDD, OS, Antivirus, etc.
            $table->text('notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
