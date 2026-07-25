<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A service's configurable, ordered workflow. Each row is one step,
     * pointing at either a Task or a TaskGroup (polymorphic — see the
     * 'task'/'task_group' morph map registered in AppServiceProvider), so
     * the same task or group can be reused across many services, each
     * with its own sequence.
     */
    public function up(): void
    {
        Schema::create('service_workflow_steps', function (Blueprint $table) {
            $table->id();

            $table->foreignId('service_id')
                ->constrained('services')
                ->cascadeOnDelete();

            $table->string('step_type', 20);
            $table->unsignedBigInteger('step_id');

            $table->unsignedInteger('sequence')->default(0);

            $table->timestamps();

            $table->index(['service_id', 'sequence']);
            $table->index(['step_type', 'step_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_workflow_steps');
    }
};
