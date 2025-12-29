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
        Schema::create('asset_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Responsible person
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->dateTime('assigned_at');
            $table->dateTime('returned_at')->nullable();
            
            $table->string('act_document_path')->nullable(); // PDF Path for delivery act
            $table->string('return_document_path')->nullable(); // PDF Path for return act
            
            $table->json('details')->nullable(); // Peripherals included etc.
            $table->text('notes')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_assignments');
    }
};
