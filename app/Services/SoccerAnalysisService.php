<?php

namespace App\Services;

use App\Models\League;
use App\Models\Prediction;
use App\Models\SoccerMatch;
use App\Models\Team;
use Carbon\Carbon;
use OpenAI\Laravel\Facades\OpenAI;

class SoccerAnalysisService
{
    public function analyzeNextGameweek(League $league): array
    {
        // Increase execution time limit for this intensive operation
        set_time_limit(600); // 10 minutes
        
        try {
            // Get matches that don't have predictions yet
            $matches = SoccerMatch::where('league_id', $league->id)
                ->whereDoesntHave('prediction')
                ->get();
            
            if ($matches->isEmpty()) {
                throw new \Exception('No matches found without predictions for ' . $league->name . '. Please add matches first.');
            }
            
            \Log::info('Found matches without predictions', [
                'league' => $league->name,
                'matches_count' => $matches->count()
            ]);
            
            // Step 2: Generate predictions for each match
            $storedMatches = [];
            foreach ($matches as $index => $match) {
                \Log::info('Generating prediction for match', [
                    'match' => ($index + 1) . '/' . $matches->count(),
                    'teams' => $match->homeTeam->name . ' vs ' . $match->awayTeam->name
                ]);
                
                $matchData = [
                    'home_team' => ['name' => $match->homeTeam->name, 'city' => $match->homeTeam->city],
                    'away_team' => ['name' => $match->awayTeam->name, 'city' => $match->awayTeam->city],
                    'match_date' => $match->match_date,
                    'venue' => $match->venue ?? 'TBD'
                ];
                
                $prediction = $this->generateMatchPrediction($matchData, $league);
                
                // Store prediction
                $this->storePrediction($match, $prediction);
                $storedMatches[] = $match;
            }
            
            return [
                'season' => 'Current',
                'current_gameday' => 'Manual',
                'matches_count' => count($storedMatches),
                'matches' => $storedMatches
            ];
            
        } catch (\Exception $e) {
            \Log::error('Error in analyzeNextGameweek', [
                'error' => $e->getMessage(),
                'league' => $league->name
            ]);
            throw $e;
        }
    }

    private function storePrediction(SoccerMatch $match, array $predictionData): void
    {
        $prediction = new Prediction();
        $prediction->match_id = $match->id;
        $prediction->prediction_data = $predictionData;
        $prediction->analysis = $predictionData['analysis'] ?? '';
        $prediction->confidence_score = $predictionData['confidence_score'] ?? 0.5;
        $prediction->predicted_at = now();
        $prediction->model_version = 'gpt-4o-mini';
        $prediction->save();

        // Update the match to mark prediction as generated
        $match->update([
            'prediction_generated' => true
        ]);
    }

    private function generateMatchPrediction(array $matchData, League $league): array
    {
        $prompt = $this->buildPredictionPrompt($matchData, $league->name);
        $schema = $this->getPredictionSchema();

        $response = OpenAI::responses()->create([
            'model' => 'gpt-4o-mini',
            'input' => [
                ['role' => 'system', 'content' => 'You are an expert soccer analyst. Use web search to find current team form, head-to-head records, and other relevant data to make detailed predictions for this specific match.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            'tools' => [
                ['type' => 'web_search_preview'],
            ],
            'tool_choice' => 'auto',
            'text' => [
                'format' => [
                    'type'   => 'json_schema',
                    'name'   => 'match_prediction',
                    'schema' => $schema,
                ],
            ],
            'max_output_tokens' => 1500,
        ]);

        $text = '';
        foreach ($response->output as $out) {
            if (property_exists($out, 'content')) {
                foreach ($out->content as $c) {
                    if ($c->type === 'output_text') {
                        $text .= $c->text;
                    }
                }
            } elseif (property_exists($out, 'text')) {
                $text .= $out->text;
            }
        }

        if ($text === '') {
            throw new \Exception('Empty response from OpenAI for prediction');
        }

        $text = preg_replace('/[\x00-\x1F\x7F]/', '', $text);
        $predictionData = json_decode($text, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            \Log::error('JSON parsing error for prediction', [
                'error' => json_last_error_msg(),
                'response_content' => substr($text, 0, 500)
            ]);
            throw new \Exception('Failed to parse prediction JSON: ' . json_last_error_msg());
        }

        return $predictionData;
    }

    private function buildPredictionPrompt(array $matchData, string $leagueName): string
    {
        $homeTeam = $matchData['home_team']['name'];
        $awayTeam = $matchData['away_team']['name'];
        $matchDate = Carbon::parse($matchData['match_date'])->format('Y-m-d');

        return "
        **IMPORTANT: Use web search for detailed analysis of this specific match.**

        Analyze: {$homeTeam} vs {$awayTeam} on {$matchDate}

        **Search for current data:**
        - Recent form of both teams (last 5 matches)
        - Head-to-head record
        - Current league positions
        - Injury reports and player availability
        - Team news and motivation factors

        **Provide detailed predictions for ALL of the following:**
        
        **IMPORTANT: All probabilities must be decimal values between 0.0 and 1.0 (NOT percentages). For example, use 0.75 instead of 75%.**
        
        1. **Match Result**: Winner, probabilities for home/draw/away (as decimals 0.0-1.0)
        
        2. **Double Chance**: Home or Draw, Away or Draw, Home or Away probabilities (as decimals 0.0-1.0)
        
        3. **Handicaps**: Predictions for ±1, ±1.5 goals for both teams (as decimals 0.0-1.0)
        
        4. **Over/Under Goals**: Multiple thresholds (0.5, 1.5, 2.5, 3.5) with probabilities (as decimals 0.0-1.0)
        
        5. **First Half Predictions**:
           - Half-time result (winner)
           - First half over/under 0.5 and 1.5 goals (as decimals 0.0-1.0)
           - Both teams to score in first half (as decimals 0.0-1.0)
        
        6. **Second Half Predictions**:
           - Second half over/under 0.5 and 1.5 goals (as decimals 0.0-1.0)
           - Both teams to score in second half (as decimals 0.0-1.0)
        
        7. **Both Teams to Score**: Full match prediction and probability (as decimals 0.0-1.0)
        
        8. **First Goal**: Which team scores first (including no goals scenario) (as decimals 0.0-1.0)
        
        9. **Advanced Goal Statistics**: 
            - **Exact Score**: Most likely final score (e.g., 2-1, 1-0) with probability
            - **Clean Sheets**: Probability of home/away team keeping clean sheet
            - **Goal Ranges**: 0-1 goals, 2 to 3 goals, 4+ goals probabilities
            - **Extended Over/Under**: Over 3.5, 4.5 and Under 1.5 goals
        
        10. **Advanced Betting Markets**:
            - **Draw No Bet**: Home or Away (no draw) probabilities
            - **Asian Handicaps**: ±0.5 goal handicaps for both teams
            - **Winning Margins**: 1 goal, 2 goals, 3+ goals difference probabilities
        
        11. **Statistical Insights**:
            - **Team Form**: Recent performance scores (0.0-1.0) for both teams
            - **Last 5 Matches**: Number of wins in last 5 for each team
            - **Head-to-Head**: Historical advantage for home team
            - **Value Betting**: High value bet opportunities and underdog chances
            - **Betting Trends**: Trending up/down/stable based on recent form
        
        12. **Risk Combinations**: Create three betting combinations:
            - **Safe**: High probability (0.7+), low odds, conservative picks
            - **Moderate**: Medium probability (0.4-0.7), balanced risk/reward
            - **High Risk**: Lower probability (0.15-0.4), high potential return
        
        13. **Comprehensive Analysis**: Detailed reasoning for all predictions
        
        14. **Overall Confidence Score**: 0.0 to 1.0

        **CRITICAL**: Use decimal probabilities (0.0-1.0) NOT percentages. Use real current data to make accurate predictions for ALL categories above.
        ";
    }

    private function getPredictionSchema(): array
    {
        return [
            'type' => 'object',
            'additionalProperties' => false,
            'properties' => [
                'match_result' => [
                    'type' => 'object',
                    'additionalProperties' => false,
                    'properties' => [
                        'winner' => ['type' => 'string', 'enum' => ['home', 'draw', 'away']],
                        'home_win_probability' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1],
                        'draw_probability' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1],
                        'away_win_probability' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1],
                        'confidence' => ['type' => 'string', 'enum' => ['low', 'medium', 'high']]
                    ],
                    'required' => ['winner', 'home_win_probability', 'draw_probability', 'away_win_probability', 'confidence']
                ],
                'double_chance' => [
                    'type' => 'object',
                    'additionalProperties' => false,
                    'properties' => [
                        'home_or_draw' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1],
                        'away_or_draw' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1],
                        'home_or_away' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1]
                    ],
                    'required' => ['home_or_draw', 'away_or_draw', 'home_or_away']
                ],
                'handicaps' => [
                    'type' => 'object',
                    'additionalProperties' => false,
                    'properties' => [
                        'home_minus_1' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1],
                        'home_minus_1_5' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1],
                        'home_plus_1' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1],
                        'home_plus_1_5' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1],
                        'away_minus_1' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1],
                        'away_minus_1_5' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1],
                        'away_plus_1' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1],
                        'away_plus_1_5' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1]
                    ],
                    'required' => ['home_minus_1', 'home_minus_1_5', 'home_plus_1', 'home_plus_1_5', 'away_minus_1', 'away_minus_1_5', 'away_plus_1', 'away_plus_1_5']
                ],
                'over_under' => [
                    'type' => 'object',
                    'additionalProperties' => false,
                    'properties' => [
                        'over_0_5' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1],
                        'under_0_5' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1],
                        'over_1_5' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1],
                        'under_1_5' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1],
                        'over_2_5' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1],
                        'under_2_5' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1],
                        'over_3_5' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1],
                        'under_3_5' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1],
                        'total_goals_prediction' => ['type' => 'number', 'minimum' => 0]
                    ],
                    'required' => ['over_0_5', 'under_0_5', 'over_1_5', 'under_1_5', 'over_2_5', 'under_2_5', 'over_3_5', 'under_3_5', 'total_goals_prediction']
                ],
                'first_half' => [
                    'type' => 'object',
                    'additionalProperties' => false,
                    'properties' => [
                        'result' => [
                            'type' => 'object',
                            'additionalProperties' => false,
                            'properties' => [
                                'winner' => ['type' => 'string', 'enum' => ['home', 'draw', 'away']],
                                'home_win_probability' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1],
                                'draw_probability' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1],
                                'away_win_probability' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1]
                            ],
                            'required' => ['winner', 'home_win_probability', 'draw_probability', 'away_win_probability']
                        ],
                        'over_0_5' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1],
                        'under_0_5' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1],
                        'over_1_5' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1],
                        'under_1_5' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1],
                        'both_teams_to_score' => [
                            'type' => 'object',
                            'additionalProperties' => false,
                            'properties' => [
                                'prediction' => ['type' => 'boolean'],
                                'probability' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1]
                            ],
                            'required' => ['prediction', 'probability']
                        ],
                        'total_goals_prediction' => ['type' => 'number', 'minimum' => 0]
                    ],
                    'required' => ['result', 'over_0_5', 'under_0_5', 'over_1_5', 'under_1_5', 'both_teams_to_score', 'total_goals_prediction']
                ],
                'second_half' => [
                    'type' => 'object',
                    'additionalProperties' => false,
                    'properties' => [
                        'over_0_5' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1],
                        'under_0_5' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1],
                        'over_1_5' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1],
                        'under_1_5' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1],
                        'both_teams_to_score' => [
                            'type' => 'object',
                            'additionalProperties' => false,
                            'properties' => [
                                'prediction' => ['type' => 'boolean'],
                                'probability' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1]
                            ],
                            'required' => ['prediction', 'probability']
                        ],
                        'total_goals_prediction' => ['type' => 'number', 'minimum' => 0]
                    ],
                    'required' => ['over_0_5', 'under_0_5', 'over_1_5', 'under_1_5', 'both_teams_to_score', 'total_goals_prediction']
                ],
                'both_teams_to_score' => [
                    'type' => 'object',
                    'additionalProperties' => false,
                    'properties' => [
                        'prediction' => ['type' => 'boolean'],
                        'probability' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1]
                    ],
                    'required' => ['prediction', 'probability']
                ],
                'first_goal' => [
                    'type' => 'object',
                    'additionalProperties' => false,
                    'properties' => [
                        'home_team' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1],
                        'away_team' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1],
                        'no_goals' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1]
                    ],
                    'required' => ['home_team', 'away_team', 'no_goals']
                ],
                'advanced_goal_statistics' => [
                    'type' => 'object',
                    'additionalProperties' => false,
                    'properties' => [
                        'exact_score_prediction' => ['type' => 'string'],
                        'exact_score_probability' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1],
                        'clean_sheet_home_probability' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1],
                        'clean_sheet_away_probability' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1],
                        'over_35_probability' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1],
                        'over_45_probability' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1],
                        'under_15_probability' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1],
                        'goal_range_0_1_probability' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1],
                        'goal_range_2_3_probability' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1],
                        'goal_range_4_plus_probability' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1]
                    ],
                    'required' => ['exact_score_prediction', 'exact_score_probability', 'clean_sheet_home_probability', 'clean_sheet_away_probability', 'over_35_probability', 'over_45_probability', 'under_15_probability', 'goal_range_0_1_probability', 'goal_range_2_3_probability', 'goal_range_4_plus_probability']
                ],
                'advanced_betting_markets' => [
                    'type' => 'object',
                    'additionalProperties' => false,
                    'properties' => [
                        'draw_no_bet_home_probability' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1],
                        'draw_no_bet_away_probability' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1],
                        'asian_handicap_home_minus_05_probability' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1],
                        'asian_handicap_home_plus_05_probability' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1],
                        'asian_handicap_away_minus_05_probability' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1],
                        'asian_handicap_away_plus_05_probability' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1],
                        'winning_margin_1_goal_probability' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1],
                        'winning_margin_2_goals_probability' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1],
                        'winning_margin_3_plus_probability' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1]
                    ],
                    'required' => ['draw_no_bet_home_probability', 'draw_no_bet_away_probability', 'asian_handicap_home_minus_05_probability', 'asian_handicap_home_plus_05_probability', 'asian_handicap_away_minus_05_probability', 'asian_handicap_away_plus_05_probability', 'winning_margin_1_goal_probability', 'winning_margin_2_goals_probability', 'winning_margin_3_plus_probability']
                ],
                'statistical_insights' => [
                    'type' => 'object',
                    'additionalProperties' => false,
                    'properties' => [
                        'home_team_form_score' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1],
                        'away_team_form_score' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1],
                        'home_team_last_5_wins' => ['type' => 'integer', 'minimum' => 0, 'maximum' => 5],
                        'away_team_last_5_wins' => ['type' => 'integer', 'minimum' => 0, 'maximum' => 5],
                        'h2h_home_advantage' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1],
                        'high_value_bet' => ['type' => 'boolean'],
                        'underdog_opportunity' => ['type' => 'boolean'],
                        'betting_trend' => ['type' => 'string', 'enum' => ['trending_up', 'trending_down', 'stable']]
                    ],
                    'required' => ['home_team_form_score', 'away_team_form_score', 'home_team_last_5_wins', 'away_team_last_5_wins', 'h2h_home_advantage', 'high_value_bet', 'underdog_opportunity', 'betting_trend']
                ],

                'risk_combinations' => [
                    'type' => 'object',
                    'additionalProperties' => false,
                    'properties' => [
                        'safe' => [
                            'type' => 'object',
                            'additionalProperties' => false,
                            'properties' => [
                                'predictions' => ['type' => 'array', 'items' => ['type' => 'string']],
                                'combined_probability' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1],
                                'expected_odds' => ['type' => 'number', 'minimum' => 1],
                                'description' => ['type' => 'string']
                            ],
                            'required' => ['predictions', 'combined_probability', 'expected_odds', 'description']
                        ],
                        'moderate' => [
                            'type' => 'object',
                            'additionalProperties' => false,
                            'properties' => [
                                'predictions' => ['type' => 'array', 'items' => ['type' => 'string']],
                                'combined_probability' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1],
                                'expected_odds' => ['type' => 'number', 'minimum' => 1],
                                'description' => ['type' => 'string']
                            ],
                            'required' => ['predictions', 'combined_probability', 'expected_odds', 'description']
                        ],
                        'high_risk' => [
                            'type' => 'object',
                            'additionalProperties' => false,
                            'properties' => [
                                'predictions' => ['type' => 'array', 'items' => ['type' => 'string']],
                                'combined_probability' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1],
                                'expected_odds' => ['type' => 'number', 'minimum' => 1],
                                'description' => ['type' => 'string']
                            ],
                            'required' => ['predictions', 'combined_probability', 'expected_odds', 'description']
                        ]
                    ],
                    'required' => ['safe', 'moderate', 'high_risk']
                ],
                'analysis' => ['type' => 'string'],
                'confidence_score' => ['type' => 'number', 'minimum' => 0, 'maximum' => 1]
            ],
            'required' => [
                'match_result', 'double_chance', 'handicaps', 'over_under', 'first_half', 'second_half', 
                'both_teams_to_score', 'first_goal', 'advanced_goal_statistics', 'advanced_betting_markets', 
                'statistical_insights', 'risk_combinations', 'analysis', 'confidence_score'
            ]
        ];
    }
} 