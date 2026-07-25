<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Configurable replacement for the employment_status enum — new statuses
     * can be added by admins without a schema change. is_terminal marks
     * statuses that end active employment (Resigned, Terminated, ...) so
     * headcount queries can exclude them without hardcoding names.
     */
    public function up(): void
    {
        Schema::create('employment_statuses', function (Blueprint $table) {
            $table->id();

            $table->string('name', 50)->unique();
            $table->boolean('is_terminal')->default(false);
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employment_statuses');
    }
};
