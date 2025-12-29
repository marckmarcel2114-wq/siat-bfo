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
        Schema::create('procurements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requester_id')->constrained('users');
            $table->foreignId('city_id')->constrained(); // Associated city for the purchase
            
            $table->string('status')->default('draft'); // draft, submitted, authorized, completed, rejected
            
            $table->json('items'); // List of items requested
            $table->text('justification')->nullable();
            
            $table->string('authorization_document_path')->nullable(); // Signed PDF
            $table->dateTime('authorized_at')->nullable();
            $table->foreignId('authorized_by')->nullable()->constrained('users');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procurements');
    }
};
