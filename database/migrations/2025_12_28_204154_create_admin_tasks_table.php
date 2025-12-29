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
        Schema::create('admin_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            
            $table->foreignId('created_by')->constrained('users'); // National Supervisor
            
            // Assignment targets
            $table->foreignId('assigned_city_id')->nullable()->constrained('cities');
            $table->foreignId('assigned_user_id')->nullable()->constrained('users');
            
            // Normalized Type
            $table->foreignId('task_type_id')->constrained()->cascadeOnDelete();
            
            $table->date('due_date')->nullable();
            
            $table->dateTime('completed_at')->nullable();
            $table->foreignId('completed_by')->nullable()->constrained('users');
            
            $table->string('proof_document_path')->nullable(); // Uploaded act/proof
            $table->string('status')->default('pending'); // pending, completed, verified
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_tasks');
    }
};
