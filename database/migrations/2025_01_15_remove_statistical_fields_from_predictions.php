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
            $table->dropColumn([
                // Advanced Goal Statistics (keeping basic ones)
                'exact_score_prediction',
                'exact_score_probability',
                'clean_sheet_home_probability',
                'clean_sheet_away_probability',
                'over_35_probability',
                'over_45_probability',
                'under_15_probability',
                'goal_range_0_1_probability',
                'goal_range_2_3_probability',
                'goal_range_4_plus_probability',
                
                // Advanced Betting Markets
                'draw_no_bet_home_probability',
                'draw_no_bet_away_probability',
                'asian_handicap_home_minus_05_probability',
                'asian_handicap_home_plus_05_probability',
                'asian_handicap_away_minus_05_probability',
                'asian_handicap_away_plus_05_probability',
                'winning_margin_1_goal_probability',
                'winning_margin_2_goals_probability',
                'winning_margin_3_plus_probability',
                
                // Statistical Insights
                'home_team_form_score',
                'away_team_form_score',
                'home_team_last_5_wins',
                'away_team_last_5_wins',
                'h2h_home_advantage',
                'high_value_bet',
                'underdog_opportunity',
                'betting_trend',
                
                // Market Odds (not used in current app)
                'market_odds_home',
                'market_odds_draw',
                'market_odds_away',
                'market_odds_over_25',
                'market_odds_btts_yes',
                
                // Value betting fields
                'value_bet_rating',
                
                // Scoring characteristics (redundant)
                'high_scoring_predicted',
                'low_scoring_predicted',
                'total_goals_predicted',
                
                // Risk combinations (not used without statistics)
                'has_safe_combination',
                'has_moderate_combination',
                'has_high_risk_combination',
                
                // Additional fields
                'safe_bet_probability',
                'safe_bet_odds',
                'first_half_winner',
                'prediction_accuracy_score'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('predictions', function (Blueprint $table) {
            // Advanced Goal Statistics
            $table->string('exact_score_prediction')->nullable();
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
            
            // Statistical Insights
            $table->decimal('home_team_form_score', 5, 4)->nullable();
            $table->decimal('away_team_form_score', 5, 4)->nullable();
            $table->integer('home_team_last_5_wins')->nullable();
            $table->integer('away_team_last_5_wins')->nullable();
            $table->decimal('h2h_home_advantage', 5, 4)->nullable();
            $table->boolean('high_value_bet')->default(false)->index();
            $table->boolean('underdog_opportunity')->default(false)->index();
            $table->string('betting_trend')->nullable();
            
            // Market Odds
            $table->decimal('market_odds_home', 6, 2)->nullable();
            $table->decimal('market_odds_draw', 6, 2)->nullable();
            $table->decimal('market_odds_away', 6, 2)->nullable();
            $table->decimal('market_odds_over_25', 6, 2)->nullable();
            $table->decimal('market_odds_btts_yes', 6, 2)->nullable();
            
            // Value betting fields
            $table->decimal('value_bet_rating', 5, 4)->nullable();
            
            // Scoring characteristics
            $table->boolean('high_scoring_predicted')->default(false)->index();
            $table->boolean('low_scoring_predicted')->default(false)->index();
            $table->decimal('total_goals_predicted', 4, 2)->nullable();
            
            // Risk combinations
            $table->boolean('has_safe_combination')->default(false)->index();
            $table->boolean('has_moderate_combination')->default(false)->index();
            $table->boolean('has_high_risk_combination')->default(false)->index();
            
            // Additional fields
            $table->decimal('safe_bet_probability', 5, 4)->nullable()->index();
            $table->decimal('safe_bet_odds', 6, 2)->nullable();
            $table->string('first_half_winner')->nullable();
            $table->decimal('prediction_accuracy_score', 5, 4)->nullable();
        });
    }
}; 