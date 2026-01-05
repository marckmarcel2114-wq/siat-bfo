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
        Schema::table('atms', function (Blueprint $table) {
            $table->renameColumn('name', 'nombre');
            $table->renameColumn('address', 'direccion');
            $table->renameColumn('phones', 'telefonos');
            $table->renameColumn('city_id', 'ciudad_id');
            // 'parent_id' refers to a Branch (sucursal), so renaming to sucursal_id is clearer
            $table->renameColumn('parent_id', 'sucursal_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('atms', function (Blueprint $table) {
            $table->renameColumn('nombre', 'name');
            $table->renameColumn('direccion', 'address');
            $table->renameColumn('telefonos', 'phones');
            $table->renameColumn('ciudad_id', 'city_id');
            $table->renameColumn('sucursal_id', 'parent_id');
        });
    }
};
