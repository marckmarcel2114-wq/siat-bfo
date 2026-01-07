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
            $table->string('scope')->default('CITY')->after('tipo'); // CITY, GLOBAL
            $table->foreignId('city_id')->nullable()->after('scope')->constrained('ciudades')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('software_licenses', function (Blueprint $table) {
            $table->dropForeign(['city_id']);
            $table->dropColumn(['scope', 'city_id']);
        });
    }

};
