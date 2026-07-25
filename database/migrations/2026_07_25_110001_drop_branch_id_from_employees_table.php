<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * The `companies` table (with its own parent_id hierarchy and
     * company_type of Company/Branch/Warehouse/Factory/Office) now
     * supersedes the standalone `branches` table, so employees.branch_id
     * is dropped ahead of removing `branches` itself.
     */
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropColumn('branch_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->foreignId('branch_id')
                ->nullable()
                ->after('employee_code')
                ->constrained()
                ->restrictOnDelete();
        });
    }
};
