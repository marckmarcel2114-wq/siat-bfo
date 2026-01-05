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
        Schema::table('activos', function (Blueprint $table) {
            $table->string('mac_ethernet', 17)->nullable()->after('numero_serie');
            $table->string('mac_wifi', 17)->nullable()->after('mac_ethernet');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activos', function (Blueprint $table) {
            $table->dropColumn(['mac_ethernet', 'mac_wifi']);
        });
    }
};
