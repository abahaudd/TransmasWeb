<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * "Task Groups" renamed to "Service Components" throughout the app.
     */
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['task_group_id']);
        });

        Schema::rename('task_groups', 'service_components');

        Schema::table('tasks', function (Blueprint $table) {
            $table->renameColumn('task_group_id', 'service_component_id');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->foreign('service_component_id')
                ->references('id')
                ->on('service_components')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['service_component_id']);
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->renameColumn('service_component_id', 'task_group_id');
        });

        Schema::rename('service_components', 'task_groups');

        Schema::table('tasks', function (Blueprint $table) {
            $table->foreign('task_group_id')
                ->references('id')
                ->on('task_groups')
                ->nullOnDelete();
        });
    }
};
