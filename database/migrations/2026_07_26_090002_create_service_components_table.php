<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A reusable component of tasks (e.g. "Document Collection"). A service's
     * workflow can reference a whole component as one step, or reference an
     * individual task directly — see create_service_workflow_steps_table.
     */
    public function up(): void
    {
        Schema::create('service_components', function (Blueprint $table) {
            $table->id();

            $table->string('name', 150);
            $table->string('code', 20)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_components');
    }
};