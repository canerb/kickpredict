<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prediction extends Model
{
    use HasFactory;

    protected $fillable = [
        'match_id',
        'prediction_data',
        'analysis',
        'confidence_score',
        'predicted_at',
        'model_version',
        // Core prediction fields
        'predicted_winner',
        'home_win_probability',
        'draw_probability',
        'away_win_probability',
        'over_25_probability',
        'btts_probability',
        // Result verification fields
        'prediction_verified',
        'actual_result',
        'actual_home_goals',
        'actual_away_goals',
        'prediction_correct',
        'result_verified_at',
        'accuracy_details',
    ];

    protected $casts = [
        'prediction_data' => 'array',
        'predicted_at' => 'datetime',
        'result_verified_at' => 'datetime',
        'confidence_score' => 'decimal:2',
        'home_win_probability' => 'decimal:4',
        'draw_probability' => 'decimal:4',
        'away_win_probability' => 'decimal:4',
        'over_25_probability' => 'decimal:4',
        'btts_probability' => 'decimal:4',
        'prediction_verified' => 'boolean',
        'prediction_correct' => 'boolean',
        'accuracy_details' => 'array',
    ];

    protected static function booted()
    {
        static::saving(function ($prediction) {
            $prediction->populateIndexedFields();
        });
    }

    /**
     * Populate indexed fields from prediction_data JSON
     */
    public function populateIndexedFields(): void
    {
        if (!$this->prediction_data) return;

        $data = $this->prediction_data;

        // Match result fields - convert percentages to decimals if needed
        $this->predicted_winner = $data['match_result']['winner'] ?? null;
        $this->home_win_probability = $this->convertToDecimal($data['match_result']['home_win_probability'] ?? null);
        $this->draw_probability = $this->convertToDecimal($data['match_result']['draw_probability'] ?? null);
        $this->away_win_probability = $this->convertToDecimal($data['match_result']['away_win_probability'] ?? null);

        // Popular betting markets - convert percentages to decimals if needed
        $this->over_25_probability = $this->convertToDecimal($data['over_under']['over_2_5'] ?? null);
        $this->btts_probability = $this->convertToDecimal($data['both_teams_to_score']['probability'] ?? null);
    }

    /**
     * Convert percentage values to decimals if needed
     */
    private function convertToDecimal($value): ?float
    {
        if ($value === null) return null;
        
        // If value is greater than 1, assume it's a percentage and convert to decimal
        if ($value > 1) {
            return $value / 100;
        }
        
        return $value;
    }

    public function match(): BelongsTo
    {
        return $this->belongsTo(SoccerMatch::class, 'match_id');
    }

    // Match Result Accessors
    public function getWinnerPredictionAttribute(): ?string
    {
        return $this->prediction_data['match_result']['winner'] ?? null;
    }

    public function getMatchResultProbabilitiesAttribute(): ?array
    {
        return $this->prediction_data['match_result'] ?? null;
    }

    // Double Chance Accessors
    public function getDoubleChancePredictionsAttribute(): ?array
    {
        return $this->prediction_data['double_chance'] ?? null;
    }

    public function getHomeOrDrawProbabilityAttribute(): ?float
    {
        return $this->prediction_data['double_chance']['home_or_draw'] ?? null;
    }

    public function getAwayOrDrawProbabilityAttribute(): ?float
    {
        return $this->prediction_data['double_chance']['away_or_draw'] ?? null;
    }

    public function getHomeOrAwayProbabilityAttribute(): ?float
    {
        return $this->prediction_data['double_chance']['home_or_away'] ?? null;
    }

    // Handicap Accessors
    public function getHandicapPredictionsAttribute(): ?array
    {
        return $this->prediction_data['handicaps'] ?? null;
    }

    // Over/Under Accessors
    public function getOverUnderPredictionAttribute(): ?array
    {
        return $this->prediction_data['over_under'] ?? null;
    }

    public function getOver25ProbabilityAttribute(): ?float
    {
        return $this->prediction_data['over_under']['over_2_5'] ?? null;
    }

    public function getUnder25ProbabilityAttribute(): ?float
    {
        return $this->prediction_data['over_under']['under_2_5'] ?? null;
    }

    public function getTotalGoalsPredictionAttribute(): ?float
    {
        return $this->prediction_data['over_under']['total_goals_prediction'] ?? null;
    }

    // First Half Accessors
    public function getFirstHalfPredictionsAttribute(): ?array
    {
        return $this->prediction_data['first_half'] ?? null;
    }

    public function getFirstHalfWinnerAttribute(): ?string
    {
        return $this->prediction_data['first_half']['result']['winner'] ?? null;
    }

    public function getFirstHalfBothTeamsToScoreAttribute(): ?bool
    {
        return $this->prediction_data['first_half']['both_teams_to_score']['prediction'] ?? null;
    }

    public function getFirstHalfBothTeamsToScoreProbabilityAttribute(): ?float
    {
        return $this->prediction_data['first_half']['both_teams_to_score']['probability'] ?? null;
    }

    // Second Half Accessors
    public function getSecondHalfPredictionsAttribute(): ?array
    {
        return $this->prediction_data['second_half'] ?? null;
    }

    public function getSecondHalfBothTeamsToScoreAttribute(): ?bool
    {
        return $this->prediction_data['second_half']['both_teams_to_score']['prediction'] ?? null;
    }

    public function getSecondHalfBothTeamsToScoreProbabilityAttribute(): ?float
    {
        return $this->prediction_data['second_half']['both_teams_to_score']['probability'] ?? null;
    }

    // Both Teams To Score (Full Match)
    public function getBothTeamsToScorePredictionAttribute(): ?bool
    {
        return $this->prediction_data['both_teams_to_score']['prediction'] ?? null;
    }

    public function getBothTeamsToScoreProbabilityAttribute(): ?float
    {
        return $this->prediction_data['both_teams_to_score']['probability'] ?? null;
    }

    // First Goal Accessors
    public function getFirstGoalPredictionsAttribute(): ?array
    {
        return $this->prediction_data['first_goal'] ?? null;
    }

    public function getFirstGoalHomeProbabilityAttribute(): ?float
    {
        return $this->prediction_data['first_goal']['home_team'] ?? null;
    }

    public function getFirstGoalAwayProbabilityAttribute(): ?float
    {
        return $this->prediction_data['first_goal']['away_team'] ?? null;
    }

    public function getNoGoalsProbabilityAttribute(): ?float
    {
        return $this->prediction_data['first_goal']['no_goals'] ?? null;
    }

    // Goal Scorers Accessors
    public function getGoalScorerPredictionsAttribute(): ?array
    {
        return $this->prediction_data['goal_scorers'] ?? null;
    }

    public function getHomeLikelyScorersAttribute(): ?array
    {
        return $this->prediction_data['goal_scorers']['home_team_likely_scorers'] ?? null;
    }

    public function getAwayLikelyScorersAttribute(): ?array
    {
        return $this->prediction_data['goal_scorers']['away_team_likely_scorers'] ?? null;
    }

    // Risk Combinations Accessors
    public function getRiskCombinationsAttribute(): ?array
    {
        return $this->prediction_data['risk_combinations'] ?? null;
    }

    public function getSafeCombinationAttribute(): ?array
    {
        return $this->prediction_data['risk_combinations']['safe'] ?? null;
    }

    public function getModerateCombinationAttribute(): ?array
    {
        return $this->prediction_data['risk_combinations']['moderate'] ?? null;
    }

    public function getHighRiskCombinationAttribute(): ?array
    {
        return $this->prediction_data['risk_combinations']['high_risk'] ?? null;
    }

    // Analysis Accessor
    public function getAnalysisTextAttribute(): ?string
    {
        return $this->prediction_data['analysis'] ?? $this->analysis;
    }

    // Helper Methods
    public function getMostLikelyOutcome(): ?string
    {
        $probabilities = $this->getMatchResultProbabilitiesAttribute();
        if (!$probabilities) return null;

        $maxProb = max(
            $probabilities['home_win_probability'] ?? 0,
            $probabilities['draw_probability'] ?? 0,
            $probabilities['away_win_probability'] ?? 0
        );

        if ($maxProb === ($probabilities['home_win_probability'] ?? 0)) return 'home';
        if ($maxProb === ($probabilities['draw_probability'] ?? 0)) return 'draw';
        return 'away';
    }

    public function getBestSafeBet(): ?array
    {
        $safe = $this->getSafeCombinationAttribute();
        return $safe ? [
            'description' => $safe['description'],
            'probability' => $safe['combined_probability'],
            'expected_odds' => $safe['expected_odds']
        ] : null;
    }

    public function getHighestProbabilityBet(): ?array
    {
        $combinations = $this->getRiskCombinationsAttribute();
        if (!$combinations) return null;

        $highest = null;
        $highestProb = 0;

        foreach (['safe', 'moderate', 'high_risk'] as $type) {
            if (isset($combinations[$type]) && $combinations[$type]['combined_probability'] > $highestProb) {
                $highestProb = $combinations[$type]['combined_probability'];
                $highest = $combinations[$type];
                $highest['type'] = $type;
            }
        }

        return $highest;
    }
}
