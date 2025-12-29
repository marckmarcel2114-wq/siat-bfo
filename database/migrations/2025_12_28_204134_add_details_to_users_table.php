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
            // Add new columns instead of renaming to avoid dependency issues or data loss risks during dev
            $table->string('first_name')->nullable()->after('id');
            $table->string('last_name')->nullable()->after('first_name');
            
            
            $table->string('role')->default('user')->after('password'); 
            // SQLite restriction: cannot add foreign key in ALTER TABLE. We use index only here.
            $table->foreignId('city_id')->nullable()->index()->after('role');
            $table->string('position')->nullable()->after('city_id'); 
            $table->string('ad_guid')->nullable()->after('position'); 
            $table->string('phone')->nullable()->after('ad_guid');
            $table->boolean('is_active')->default(true)->after('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['city_id']);
            $table->dropColumn(['first_name', 'last_name', 'role', 'city_id', 'position', 'ad_guid', 'phone', 'is_active']);
        });
    }
};
