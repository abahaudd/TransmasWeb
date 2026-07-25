<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Superseded by `companies` (self-referencing parent_id, company_type
     * of Company/Branch/Warehouse/Factory/Office, plus working hours /
     * weekends columns added alongside this migration).
     */
    public function up(): void
    {
        Schema::dropIfExists('branches');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('address_id')->nullable()->constrained('addresses')->nullOnDelete();
            $table->string('phone');
            $table->string('email')->nullable();
            $table->time('start_work_hour');
            $table->time('end_work_hour');
            $table->string('weekends');
            $table->boolean('is_active')->default(1);
            $table->string('created_by');
            $table->string('updated_by');
            $table->string('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
};
