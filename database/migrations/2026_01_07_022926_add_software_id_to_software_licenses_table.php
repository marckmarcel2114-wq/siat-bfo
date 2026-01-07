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
        Schema::table('software_licenses', function (Blueprint $table) {
            $table->foreignId('software_id')->nullable()->after('nombre')->constrained('software')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('software_licenses', function (Blueprint $table) {
             $table->dropForeign(['software_id']);
             $table->dropColumn('software_id');
        });
    }
};
