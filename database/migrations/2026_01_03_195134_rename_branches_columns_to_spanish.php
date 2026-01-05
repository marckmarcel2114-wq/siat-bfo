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
        Schema::table('sucursales', function (Blueprint $table) {
            $table->renameColumn('name', 'nombre');
            $table->renameColumn('code', 'codigo');
            $table->renameColumn('address', 'direccion');
            $table->renameColumn('phones', 'telefonos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sucursales', function (Blueprint $table) {
            $table->renameColumn('nombre', 'name');
            $table->renameColumn('codigo', 'code');
            $table->renameColumn('direccion', 'address');
            $table->renameColumn('telefonos', 'phones');
        });
    }
};
