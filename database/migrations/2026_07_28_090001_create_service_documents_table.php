<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Which documents a service requires — attaches the documents catalog
     * to a service, with per-service mandatory/optional flag and notes.
     */
    public function up(): void
    {
        Schema::create('service_documents', function (Blueprint $table) {
            $table->id();

            $table->foreignId('service_id')
                ->constrained('services')
                ->cascadeOnDelete();

            $table->foreignId('document_id')
                ->constrained('documents')
                ->cascadeOnDelete();

            $table->boolean('is_mandatory')->default(true);
            $table->text('remarks')->nullable();
            $table->unsignedInteger('sequence')->default(0);

            $table->timestamps();

            $table->unique(['service_id', 'document_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_documents');
    }
};
