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
        Schema::create('software_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            
            $table->string('action'); // install, update, remove
            $table->string('software_name');
            $table->string('version')->nullable();
            
            $table->dateTime('performed_at');
            $table->foreignId('performed_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('software_logs');
    }
};
