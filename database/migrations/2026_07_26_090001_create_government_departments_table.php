<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A task's external dependency — the government department (or other
     * external authority) a task's completion relies on, e.g. Dubai
     * Economy, GDRFA, Ministry of Labour, Municipality, Customs.
     */
    public function up(): void
    {
        Schema::create('government_departments', function (Blueprint $table) {
            $table->id();

            $table->string('name', 200);
            $table->string('code', 30)->nullable();
            $table->text('remarks')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('government_departments');
    }
};
