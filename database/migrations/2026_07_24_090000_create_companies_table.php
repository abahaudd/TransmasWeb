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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();

            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('companies')
                ->nullOnDelete();

            $table->string('company_type', 30)->default('Company'); // Company, Branch, Warehouse, Factory, Office

            $table->string('company_code', 20)->nullable();
            $table->string('legal_name', 200);
            $table->string('trade_name', 200)->nullable();
            $table->string('display_name', 200)->nullable();

            $table->string('status', 20)->default('Active'); // Active, Inactive

            $table->string('email', 200)->nullable();
            $table->string('website', 200)->nullable();

            $table->foreignId('tax_country_id')
                ->nullable()
                ->constrained('countries')
                ->nullOnDelete();

            // Currencies and languages are not yet modelled as their own tables;
            // kept as plain nullable references until those modules exist.
            $table->unsignedBigInteger('currency_id')->nullable();
            $table->unsignedBigInteger('language_id')->nullable();
            $table->string('timezone', 100)->nullable();

            $table->date('incorporation_date')->nullable();
            $table->date('financial_year_start')->nullable();

            $table->string('logo')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index('company_code');
            $table->index('legal_name');
            $table->index('status');
            $table->index('company_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
