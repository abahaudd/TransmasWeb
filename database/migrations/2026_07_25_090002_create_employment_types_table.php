<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Configurable replacement for the employment_type enum — new types can
     * be added by admins without a schema change.
     */
    public function up(): void
    {
        Schema::create('employment_types', function (Blueprint $table) {
            $table->id();

            $table->string('name', 50)->unique();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employment_types');
    }
};
