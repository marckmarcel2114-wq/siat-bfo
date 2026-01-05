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
        Schema::table('software_installations', function (Blueprint $table) {
            $table->foreignId('license_id')->nullable()->change();
            $table->foreignId('software_version_id')->nullable()->constrained('software_versions')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('software_installations', function (Blueprint $table) {
            //
        });
    }
};
