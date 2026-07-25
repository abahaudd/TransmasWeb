<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('person_id')
                ->constrained('persons')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('employee_code', 50);

            $table->foreignId('branch_id')
                ->nullable()
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('department_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('designation_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            // employment_type / employment_status are configurable lookup
            // tables rather than enums — see create_employment_types_table
            // and create_employment_statuses_table.
            $table->foreignId('employment_type_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('employment_status_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->boolean('is_manager')->default(false);

            $table->foreignId('reporting_to_id')
                ->nullable()
                ->constrained('employees')
                ->nullOnDelete();

            $table->date('joining_date');

            $table->date('confirmation_date')->nullable();

            $table->date('end_date')->nullable();

            $table->string('termination_reason', 100)->nullable();

            $table->text('remarks')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->unique(['company_id', 'employee_code']);

            $table->index(['company_id', 'employment_status_id']);
            $table->index('joining_date');
            $table->index('reporting_to_id');
            $table->index('is_manager');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
