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
        Schema::table('ubicaciones', function (Blueprint $table) {
            if (!Schema::hasColumn('ubicaciones', 'direccion')) {
                $table->string('direccion')->nullable()->after('nombre');
            }
            if (!Schema::hasColumn('ubicaciones', 'telefonos')) {
                $table->string('telefonos')->nullable()->after('direccion');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ubicaciones', function (Blueprint $table) {
            $table->dropColumn(['direccion', 'telefonos']);
        });
    }
};
