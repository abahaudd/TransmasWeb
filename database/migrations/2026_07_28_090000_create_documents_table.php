<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Catalog of document types (e.g. "Passport Copy", "Trade License") that
     * services can require and that companies/customers/employees can hold
     * instances of — see service_documents, company_documents, employee_documents.
     */
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();

            $table->string('group', 50)->nullable();
            $table->string('name', 200);
            $table->string('issuing_authority', 150)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();

            $table->index('group');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
