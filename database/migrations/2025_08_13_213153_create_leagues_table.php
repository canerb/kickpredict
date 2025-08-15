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
        Schema::create('leagues', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Süper Lig", "Bundesliga"
            $table->string('country'); // e.g., "Turkey", "Germany"
            $table->string('country_code', 2); // e.g., "TR", "DE" for flag display
            $table->string('external_api_id')->nullable(); // for API integration
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leagues');
    }
};
