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
        Schema::create('maintenances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            
            // Normalized Type
            $table->foreignId('maintenance_type_id')->constrained()->cascadeOnDelete();
            
            $table->text('description');
            
            $table->string('technician_name')->nullable();
            // Or user_id if internal technician
            
            $table->date('performed_at');
            $table->string('report_document_path')->nullable(); // PDF
            
            $table->decimal('cost', 10, 2)->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenances');
    }
};
