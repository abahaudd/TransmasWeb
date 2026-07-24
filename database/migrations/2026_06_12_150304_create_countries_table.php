<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name');                 // e.g. "United States"
            $table->string('country_code', 2);         // ISO 3166-1 alpha-2, e.g. "US"
            $table->string('country_code_alpha3', 3);  // ISO 3166-1 alpha-3, e.g. "USA"
            $table->string('location_title')->nullable();    // e.g. "City"
            $table->string('territory_title')->nullable();   // e.g. "State, Province, Emirate ..."
            $table->string('postal_code_title')->nullable(); // e.g. "Zip Code, Pin code"
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
