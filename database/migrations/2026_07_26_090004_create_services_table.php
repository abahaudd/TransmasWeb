<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();

            $table->foreignId('service_category_id')
                ->nullable()
                ->constrained('service_categories')
                ->nullOnDelete();

            $table->string('name', 200);
            $table->string('code', 30)->nullable()->unique();
            $table->text('description')->nullable();

            // Stored, admin-editable attributes (not auto-derived from the
            // workflow's task costs, though ServiceCatalogService can
            // compute the latter for comparison).
            $table->decimal('cost', 10, 2)->default(0);
            $table->decimal('price', 10, 2)->default(0);

            $table->string('status', 20)->default('Active'); // Active, Inactive

            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
