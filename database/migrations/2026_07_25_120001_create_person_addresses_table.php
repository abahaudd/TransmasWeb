<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * A person (e.g. an employee) can have more than one address on file —
     * current, permanent, mailing, etc. — hence a dedicated child table
     * rather than the single address_id column on `persons`.
     */
    public function up(): void
    {
        Schema::create('person_addresses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('person_id')
                ->constrained('persons')
                ->cascadeOnDelete();

            $table->string('address_type', 30); // Current, Permanent, Mailing, Other

            $table->string('address', 255);
            $table->string('location', 255)->nullable();
            $table->string('territory', 255)->nullable();
            $table->string('postal_code', 50)->nullable();

            $table->foreignId('country_id')
                ->nullable()
                ->constrained('countries')
                ->nullOnDelete();

            $table->boolean('is_primary')->default(false);
            $table->string('remarks', 255)->nullable();

            $table->timestamps();

            $table->index('person_id');
            $table->index('address_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('person_addresses');
    }
};
