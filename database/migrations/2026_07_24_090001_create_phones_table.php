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
        Schema::create('phones', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')
                ->constrained('companies')
                ->cascadeOnDelete();

            $table->string('phone_type', 20); // Office, Mobile, WhatsApp, Fax, TollFree, Emergency

            $table->string('country_code', 10)->nullable();
            $table->string('phone_number', 30);
            $table->string('extension', 10)->nullable();
            $table->string('contact_name', 100)->nullable();

            $table->boolean('is_primary')->default(false);

            $table->string('remarks', 255)->nullable();

            $table->timestamps();

            $table->index('phone_type');
            $table->index('is_primary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phones');
    }
};
