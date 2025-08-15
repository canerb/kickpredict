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
        Schema::create('predictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained('soccer_matches')->onDelete('cascade');
            $table->json('prediction_data'); // JSON structure for all predictions
            $table->text('analysis'); // AI analysis text
            $table->decimal('confidence_score', 3, 2)->nullable(); // 0.00 to 1.00
            $table->timestamp('predicted_at');
            $table->string('model_version')->default('gpt-4'); // track which AI model was used
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('predictions');
    }
};
