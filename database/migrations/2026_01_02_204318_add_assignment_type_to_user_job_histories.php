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
        Schema::table('user_job_histories', function (Blueprint $table) {
            $table->string('assignment_type')->default('permanent')->after('branch_id'); // permanent, temporary
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_job_histories', function (Blueprint $table) {
            $table->dropColumn('assignment_type');
        });
    }
};
