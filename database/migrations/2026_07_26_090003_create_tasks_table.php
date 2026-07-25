<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('task_group_id')
                ->nullable()
                ->constrained('task_groups')
                ->nullOnDelete();

            $table->string('name', 200);
            $table->string('code', 20)->nullable();
            $table->text('description')->nullable();

            $table->decimal('cost', 10, 2)->default(0);

            // External dependency — which government department (if any)
            // this task's completion relies on.
            $table->foreignId('government_department_id')
                ->nullable()
                ->constrained('government_departments')
                ->nullOnDelete();

            // Position within its task_group when the group runs as a bundle.
            $table->unsignedInteger('sequence')->default(0);

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index('task_group_id');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
