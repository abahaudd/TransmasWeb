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
        Schema::table('companies', function (Blueprint $table) {
            $table->time('start_work_hour')->nullable()->after('timezone');
            $table->time('end_work_hour')->nullable()->after('start_work_hour');
            $table->string('weekends', 100)->nullable()->after('end_work_hour');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['start_work_hour', 'end_work_hour', 'weekends']);
        });
    }
};
