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
        Schema::create('sequence_number_formats', function (Blueprint $table) {
            $table->id();

            $table->string('category', 200)->unique(); // invoice, receipt, employee, ...
            $table->string('prefix', 200)->nullable();
            $table->string('separator', 50)->nullable();
            $table->unsignedInteger('incrementer')->default(0);
            $table->unsignedInteger('length')->nullable(); // zero-pad width of the numeric part

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sequence_number_formats');
    }
};
