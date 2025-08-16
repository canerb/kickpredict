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
            
            // Core prediction fields - indexed for quick queries
            $table->string('predicted_winner')->nullable()->index(); // home, draw, away
            $table->decimal('home_win_probability', 5, 4)->nullable();
            $table->decimal('draw_probability', 5, 4)->nullable();
            $table->decimal('away_win_probability', 5, 4)->nullable();
            
            // Popular betting markets - indexed for quick access
            $table->decimal('over_25_probability', 5, 4)->nullable()->index();
            $table->decimal('btts_probability', 5, 4)->nullable()->index(); // both teams to score
            $table->decimal('safe_bet_probability', 5, 4)->nullable()->index();
            $table->decimal('safe_bet_odds', 6, 2)->nullable();
            
            // Match characteristics for filtering
            $table->string('first_half_winner')->nullable();
            $table->decimal('total_goals_predicted', 4, 2)->nullable();
            $table->boolean('has_safe_combination')->default(false)->index();
            $table->boolean('has_moderate_combination')->default(false)->index();
            $table->boolean('has_high_risk_combination')->default(false)->index();
            $table->boolean('high_scoring_predicted')->default(false)->index(); // >2.5 goals likely
            $table->boolean('low_scoring_predicted')->default(false)->index();  // <1.5 goals likely

            // Performance Analytics
            $table->boolean('prediction_verified')->default(false)->index();
            $table->string('actual_result')->nullable(); // home, draw, away
            $table->integer('actual_home_goals')->nullable();
            $table->integer('actual_away_goals')->nullable();
            $table->boolean('prediction_correct')->nullable()->index();
            $table->decimal('prediction_accuracy_score', 5, 4)->nullable();
            $table->timestamp('result_verified_at')->nullable();
            $table->decimal('value_bet_rating', 5, 4)->nullable(); // 0-1 scale for value betting
            $table->decimal('market_odds_home', 6, 2)->nullable();
            $table->decimal('market_odds_draw', 6, 2)->nullable();
            $table->decimal('market_odds_away', 6, 2)->nullable();
            $table->decimal('market_odds_over_25', 6, 2)->nullable();
            $table->decimal('market_odds_btts_yes', 6, 2)->nullable();

            // Statistical Insights
            $table->decimal('home_team_form_score', 5, 4)->nullable(); // 0-1 scale
            $table->decimal('away_team_form_score', 5, 4)->nullable();
            $table->integer('home_team_last_5_wins')->nullable();
            $table->integer('away_team_last_5_wins')->nullable();
            $table->decimal('h2h_home_advantage', 5, 4)->nullable(); // Historical H2H advantage
            $table->boolean('high_value_bet')->default(false)->index();
            $table->boolean('underdog_opportunity')->default(false)->index();
            $table->string('betting_trend')->nullable(); // 'trending_up', 'trending_down', 'stable'
            
            // Accuracy details
            $table->json('accuracy_details')->nullable();
            
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
