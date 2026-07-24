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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->unsignedBigInteger('address_id')->nullable();
            $table->string('phone_main', 50)->nullable();
            $table->string('phone_secondary', 50)->nullable();
            $table->string('email', 50)->nullable();
            $table->string('website', 255)->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Foreign keys
            $table->foreign('address_id')
                  ->references('id')
                  ->on('addresses')
                  ->onDelete('set null');

            $table->foreign('parent_id')
                  ->references('id')
                  ->on('customers')
                  ->onDelete('set null');

            // Indexes
            $table->index('address_id');
            $table->index('parent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
