<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('designations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('department_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('title', 150);
            $table->string('code', 20)->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index('department_id');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('designations');
    }
};
