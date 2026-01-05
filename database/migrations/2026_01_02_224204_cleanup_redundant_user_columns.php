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
        Schema::table('users', function (Blueprint $table) {
            // Drop redundant/incorrect columns as per normalization request
            
            // Drop foreign keys first if they exist
            // Note: If using SQLite, dropForeign might be ignored or fail if not supported.
            // But screenshot suggests MySQL/MariaDB.
            if (DB::getDriverName() !== 'sqlite') {
                $table->dropForeign(['ciudad_id']);
                $table->dropForeign(['estado_id']);
            }

            $table->dropColumn([
                'lastname', 
                'cargo', 
                'area', 
                'ciudad_id', 
                'estado_id', 
                'es_admin_ciudad', 
                'es_supervisor_nacional'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('lastname', 100)->nullable();
            $table->string('cargo', 150)->nullable();
            $table->string('area', 100)->nullable();
            $table->foreignId('ciudad_id')->nullable();
            $table->foreignId('estado_id')->nullable();
            $table->boolean('es_admin_ciudad')->default(false);
            $table->boolean('es_supervisor_nacional')->default(false);
        });
    }
};
