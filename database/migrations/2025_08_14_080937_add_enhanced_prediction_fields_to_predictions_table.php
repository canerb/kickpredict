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
        Schema::table('predictions', function (Blueprint $table) {
            // Add indexed columns for quick queries
            $table->string('predicted_winner')->nullable()->index(); // home, draw, away
            $table->decimal('home_win_probability', 5, 4)->nullable();
            $table->decimal('draw_probability', 5, 4)->nullable();
            $table->decimal('away_win_probability', 5, 4)->nullable();
            
            // Quick access columns for popular betting markets
            $table->decimal('over_25_probability', 5, 4)->nullable()->index();
            $table->decimal('btts_probability', 5, 4)->nullable()->index(); // both teams to score
            $table->decimal('safe_bet_probability', 5, 4)->nullable()->index();
            $table->decimal('safe_bet_odds', 6, 2)->nullable();
            
            // First half winner for quick filtering
            $table->string('first_half_winner')->nullable();
            
            // Total goals prediction for sorting
            $table->decimal('total_goals_predicted', 4, 2)->nullable();
            
            // Risk level indicators
            $table->boolean('has_safe_combination')->default(false)->index();
            $table->boolean('has_moderate_combination')->default(false)->index();
            $table->boolean('has_high_risk_combination')->default(false)->index();
            
            // Match characteristics for filtering
            $table->boolean('high_scoring_predicted')->default(false)->index(); // >2.5 goals likely
            $table->boolean('low_scoring_predicted')->default(false)->index();  // <1.5 goals likely

            // Advanced Goal Statistics
            $table->string('exact_score_prediction')->nullable(); // e.g., "2-1", "1-0"
            $table->decimal('exact_score_probability', 5, 4)->nullable();
            $table->decimal('clean_sheet_home_probability', 5, 4)->nullable();
            $table->decimal('clean_sheet_away_probability', 5, 4)->nullable();
            $table->decimal('over_35_probability', 5, 4)->nullable();
            $table->decimal('over_45_probability', 5, 4)->nullable();
            $table->decimal('under_15_probability', 5, 4)->nullable();
            $table->decimal('goal_range_0_1_probability', 5, 4)->nullable();
            $table->decimal('goal_range_2_3_probability', 5, 4)->nullable();
            $table->decimal('goal_range_4_plus_probability', 5, 4)->nullable();

            // Advanced Betting Markets
            $table->decimal('draw_no_bet_home_probability', 5, 4)->nullable();
            $table->decimal('draw_no_bet_away_probability', 5, 4)->nullable();
            $table->decimal('asian_handicap_home_minus_05_probability', 5, 4)->nullable();
            $table->decimal('asian_handicap_home_plus_05_probability', 5, 4)->nullable();
            $table->decimal('asian_handicap_away_minus_05_probability', 5, 4)->nullable();
            $table->decimal('asian_handicap_away_plus_05_probability', 5, 4)->nullable();
            $table->decimal('winning_margin_1_goal_probability', 5, 4)->nullable();
            $table->decimal('winning_margin_2_goals_probability', 5, 4)->nullable();
            $table->decimal('winning_margin_3_plus_probability', 5, 4)->nullable();

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
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('predictions', function (Blueprint $table) {
            // Remove all the new columns
            $table->dropColumn([
                'predicted_winner', 'home_win_probability', 'draw_probability', 'away_win_probability',
                'over_25_probability', 'btts_probability', 'safe_bet_probability', 'safe_bet_odds',
                'first_half_winner', 'total_goals_predicted', 'has_safe_combination', 'has_moderate_combination',
                'has_high_risk_combination', 'high_scoring_predicted', 'low_scoring_predicted',
                'exact_score_prediction', 'exact_score_probability', 'clean_sheet_home_probability',
                'clean_sheet_away_probability', 'over_35_probability', 'over_45_probability',
                'under_15_probability', 'goal_range_0_1_probability', 'goal_range_2_3_probability',
                'goal_range_4_plus_probability', 'draw_no_bet_home_probability', 'draw_no_bet_away_probability',
                'asian_handicap_home_minus_05_probability', 'asian_handicap_home_plus_05_probability',
                'asian_handicap_away_minus_05_probability', 'asian_handicap_away_plus_05_probability',
                'winning_margin_1_goal_probability', 'winning_margin_2_goals_probability',
                'winning_margin_3_plus_probability', 'prediction_verified', 'actual_result',
                'actual_home_goals', 'actual_away_goals', 'prediction_correct', 'prediction_accuracy_score',
                'result_verified_at', 'value_bet_rating', 'market_odds_home', 'market_odds_draw',
                'market_odds_away', 'market_odds_over_25', 'market_odds_btts_yes', 'home_team_form_score',
                'away_team_form_score', 'home_team_last_5_wins', 'away_team_last_5_wins',
                'h2h_home_advantage', 'high_value_bet', 'underdog_opportunity', 'betting_trend'
            ]);
        });
    }
};
