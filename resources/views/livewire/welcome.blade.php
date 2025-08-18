<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Soccer AI Predictions</h1>
                    <p class="mt-2 text-gray-600">Professional match analysis and predictions</p>
                </div>
                <div class="mt-4 sm:mt-0 flex space-x-3">
                    <!-- League Selector -->
                    <select 
                        wire:model.live="selectedLeagueId"
                        class="bg-white border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                        @foreach($leagues as $league)
                            <option value="{{ $league->id }}">{{ $league->name }}</option>
                        @endforeach
                    </select>
                    
                    <!-- Gameweek Filter -->
                    @if(count($availableGameweeks) > 0)
                        <select 
                            wire:model.live="selectedGameweek"
                            class="bg-white border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                            @foreach($availableGameweeks as $gameweek)
                                <option value="{{ $gameweek }}">Gameweek {{ $gameweek }}</option>
                            @endforeach
                        </select>
                    @endif
                    
                    <!-- Generate Predictions Button (Admin Only) -->
                    @auth
                        @if(auth()->user()->is_admin)
                            <button 
                                wire:click="analyzeNextGameweek"
                                wire:loading.attr="disabled"
                                class="bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white font-semibold py-2 px-4 rounded-md transition duration-200"
                            >
                                <span wire:loading.remove>Generate Predictions</span>
                                <span wire:loading>Generating...</span>
                            </button>
                        @endif
                    @endauth
                </div>
            </div>
        </div>

        <!-- Matches Grid -->
        @if($matches->count() > 0)
            <div class="space-y-4">
                @foreach($matches as $match)
                    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                        <!-- Match Header -->
                        <div class="p-6 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-4">
                                        <!-- Home Team -->
                                        <div class="text-right flex-1">
                                            <div class="font-semibold text-lg text-gray-900">{{ $match->homeTeam->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $match->homeTeam->city }}</div>
                                        </div>
                                        
                                        <!-- VS -->
                                        <div class="flex flex-col items-center">
                                            <div class="text-2xl font-bold text-gray-400">VS</div>
                                            @if($match->gameweek)
                                                <div class="text-xs text-blue-600 font-medium">{{ $match->gameweek_label ?: 'Gameweek ' . $match->gameweek }}</div>
                                            @endif
                                            <div class="text-sm text-gray-500">{{ $match->match_date->format('M j, Y') }}</div>
                                            <div class="text-sm text-gray-500">{{ $match->match_date->format('H:i') }}</div>
                                        </div>
                                        
                                        <!-- Away Team -->
                                        <div class="text-left flex-1">
                                            <div class="font-semibold text-lg text-gray-900">{{ $match->awayTeam->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $match->awayTeam->city }}</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Expand Button -->
                                <div class="ml-6">
                                    <button 
                                        wire:click="toggleMatch({{ $match->id }})"
                                        class="text-gray-400 hover:text-gray-600 transition-colors"
                                    >
                                        <svg class="w-6 h-6 transform transition-transform {{ $this->isExpanded($match->id) ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Expanded Predictions -->
                        @if($this->isExpanded($match->id) && $match->prediction)
                            <div class="p-6 bg-gray-50">
                                @php $prediction = $match->prediction->prediction_data; @endphp
                                
                                <!-- Prediction Summary Header -->
                                <div class="bg-white rounded-lg p-4 shadow-sm mb-6">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900">Prediction Summary</h3>
                                            <p class="text-sm text-gray-600">AI Analysis Confidence: {{ $this->formatProbability($match->prediction->confidence_score) }}</p>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-2xl font-bold text-blue-600">{{ ucfirst($prediction['match_result']['winner']) }}</div>
                                            <div class="text-sm text-gray-500">Predicted Winner</div>
                                        </div>
                                    </div>
                                    
                                    <!-- Key Highlights -->
                                    @if(isset($prediction['statistical_insights']))
                                    <div class="mt-4 flex flex-wrap gap-2">
                                        @if($prediction['statistical_insights']['high_value_bet'])
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                💎 High Value Bet
                                            </span>
                                        @endif
                                        @if($prediction['statistical_insights']['underdog_opportunity'])
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                🎯 Underdog Opportunity
                                            </span>
                                        @endif
                                        @if(isset($prediction['advanced_goal_statistics']['exact_score_prediction']))
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                                🎲 Exact Score: {{ $prediction['advanced_goal_statistics']['exact_score_prediction'] }}
                                            </span>
                                        @endif
                                        @if($prediction['both_teams_to_score']['prediction'])
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                ⚽ Both Teams to Score
                                            </span>
                                        @endif
                                    </div>
                                    @endif
                                </div>
                                
                                <!-- Main Prediction Summary -->
                                <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-6">
                                    <!-- Match Result -->
                                    <div class="bg-white rounded-lg p-4 shadow-sm">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Match Result</h3>
                                        <div class="space-y-2">
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Winner:</span>
                                                <span class="font-semibold capitalize">{{ $prediction['match_result']['winner'] }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Home Win:</span>
                                                <span class="font-semibold">{{ $this->formatProbability($prediction['match_result']['home_win_probability']) }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Draw:</span>
                                                <span class="font-semibold">{{ $this->formatProbability($prediction['match_result']['draw_probability']) }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Away Win:</span>
                                                <span class="font-semibold">{{ $this->formatProbability($prediction['match_result']['away_win_probability']) }}</span>
                                            </div>
                                            <div class="mt-3">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $this->getConfidenceColor($prediction['match_result']['confidence']) }}">
                                                    {{ ucfirst($prediction['match_result']['confidence']) }} Confidence
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Advanced Goal Statistics -->
                                    @if(isset($prediction['advanced_goal_statistics']))
                                    <div class="bg-white rounded-lg p-4 shadow-sm">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Advanced Goals</h3>
                                        <div class="space-y-2">
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Exact Score:</span>
                                                <span class="font-semibold">{{ $prediction['advanced_goal_statistics']['exact_score_prediction'] }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Score Probability:</span>
                                                <span class="font-semibold">{{ $this->formatProbability($prediction['advanced_goal_statistics']['exact_score_probability']) }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Over 3.5:</span>
                                                <span class="font-semibold">{{ $this->formatProbability($prediction['advanced_goal_statistics']['over_35_probability']) }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Under 1.5:</span>
                                                <span class="font-semibold">{{ $this->formatProbability($prediction['advanced_goal_statistics']['under_15_probability']) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    <!-- Advanced Betting Markets -->
                                    @if(isset($prediction['advanced_betting_markets']))
                                    <div class="bg-white rounded-lg p-4 shadow-sm">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Advanced Markets</h3>
                                        <div class="space-y-2">
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Draw No Bet Home:</span>
                                                <span class="font-semibold">{{ $this->formatProbability($prediction['advanced_betting_markets']['draw_no_bet_home_probability']) }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Draw No Bet Away:</span>
                                                <span class="font-semibold">{{ $this->formatProbability($prediction['advanced_betting_markets']['draw_no_bet_away_probability']) }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">1 Goal Margin:</span>
                                                <span class="font-semibold">{{ $this->formatProbability($prediction['advanced_betting_markets']['winning_margin_1_goal_probability']) }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">2+ Goals Margin:</span>
                                                <span class="font-semibold">{{ $this->formatProbability($prediction['advanced_betting_markets']['winning_margin_2_goals_probability']) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    <!-- Statistical Insights -->
                                    @if(isset($prediction['statistical_insights']))
                                    <div class="bg-white rounded-lg p-4 shadow-sm">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Team Insights</h3>
                                        <div class="space-y-2">
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Home Form:</span>
                                                <span class="font-semibold">{{ $this->formatProbability($prediction['statistical_insights']['home_team_form_score']) }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Away Form:</span>
                                                <span class="font-semibold">{{ $this->formatProbability($prediction['statistical_insights']['away_team_form_score']) }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">H2H Advantage:</span>
                                                <span class="font-semibold">{{ $this->formatProbability($prediction['statistical_insights']['h2h_home_advantage']) }}</span>
                                            </div>
                                            <div class="mt-3">
                                                @if($prediction['statistical_insights']['high_value_bet'])
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        High Value Bet
                                                    </span>
                                                @endif
                                                @if($prediction['statistical_insights']['underdog_opportunity'])
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 ml-1">
                                                        Underdog Opportunity
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>

                                <!-- Detailed Predictions -->
                                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                                    <!-- Basic Predictions -->
                                    <div class="bg-white rounded-lg p-4 shadow-sm">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Basic Markets</h3>
                                        <div class="space-y-2">
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Over 2.5:</span>
                                                <span class="font-semibold">{{ $this->formatProbability($prediction['over_under']['over_2_5']) }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Under 2.5:</span>
                                                <span class="font-semibold">{{ $this->formatProbability($prediction['over_under']['under_2_5']) }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Both Teams Score:</span>
                                                <span class="font-semibold">{{ $this->formatProbability($prediction['both_teams_to_score']['probability']) }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Total Goals:</span>
                                                <span class="font-semibold">{{ $prediction['over_under']['total_goals_prediction'] }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Double Chance -->
                                    <div class="bg-white rounded-lg p-4 shadow-sm">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Double Chance</h3>
                                        <div class="space-y-2">
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Home or Draw:</span>
                                                <span class="font-semibold">{{ $this->formatProbability($prediction['double_chance']['home_or_draw']) }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Away or Draw:</span>
                                                <span class="font-semibold">{{ $this->formatProbability($prediction['double_chance']['away_or_draw']) }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Home or Away:</span>
                                                <span class="font-semibold">{{ $this->formatProbability($prediction['double_chance']['home_or_away']) }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- First Goal -->
                                    <div class="bg-white rounded-lg p-4 shadow-sm">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-3">First Goal</h3>
                                        <div class="space-y-2">
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">{{ $match->homeTeam->name }}:</span>
                                                <span class="font-semibold">{{ $this->formatProbability($prediction['first_goal']['home_team']) }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">{{ $match->awayTeam->name }}:</span>
                                                <span class="font-semibold">{{ $this->formatProbability($prediction['first_goal']['away_team']) }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">No Goals:</span>
                                                <span class="font-semibold">{{ $this->formatProbability($prediction['first_goal']['no_goals']) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Additional Statistics -->
                                @if(isset($prediction['advanced_goal_statistics']) || isset($prediction['advanced_betting_markets']))
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                                    <!-- Goal Ranges and Clean Sheets -->
                                    @if(isset($prediction['advanced_goal_statistics']))
                                    <div class="bg-white rounded-lg p-4 shadow-sm">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Goal Ranges & Clean Sheets</h3>
                                        <div class="space-y-2">
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">0-1 Goals:</span>
                                                <span class="font-semibold">{{ $this->formatProbability($prediction['advanced_goal_statistics']['goal_range_0_1_probability']) }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">2-3 Goals:</span>
                                                <span class="font-semibold">{{ $this->formatProbability($prediction['advanced_goal_statistics']['goal_range_2_3_probability']) }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">4+ Goals:</span>
                                                <span class="font-semibold">{{ $this->formatProbability($prediction['advanced_goal_statistics']['goal_range_4_plus_probability']) }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Home Clean Sheet:</span>
                                                <span class="font-semibold">{{ $this->formatProbability($prediction['advanced_goal_statistics']['clean_sheet_home_probability']) }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Away Clean Sheet:</span>
                                                <span class="font-semibold">{{ $this->formatProbability($prediction['advanced_goal_statistics']['clean_sheet_away_probability']) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    <!-- Asian Handicaps -->
                                    @if(isset($prediction['advanced_betting_markets']))
                                    <div class="bg-white rounded-lg p-4 shadow-sm">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Asian Handicaps</h3>
                                        <div class="space-y-2">
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Home -0.5:</span>
                                                <span class="font-semibold">{{ $this->formatProbability($prediction['advanced_betting_markets']['asian_handicap_home_minus_05_probability']) }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Home +0.5:</span>
                                                <span class="font-semibold">{{ $this->formatProbability($prediction['advanced_betting_markets']['asian_handicap_home_plus_05_probability']) }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Away -0.5:</span>
                                                <span class="font-semibold">{{ $this->formatProbability($prediction['advanced_betting_markets']['asian_handicap_away_minus_05_probability']) }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Away +0.5:</span>
                                                <span class="font-semibold">{{ $this->formatProbability($prediction['advanced_betting_markets']['asian_handicap_away_plus_05_probability']) }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">3+ Goals Margin:</span>
                                                <span class="font-semibold">{{ $this->formatProbability($prediction['advanced_betting_markets']['winning_margin_3_plus_probability']) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                @endif

                                <!-- Half Time Predictions -->
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                                    <!-- First Half -->
                                    <div class="bg-white rounded-lg p-4 shadow-sm">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-3">First Half</h3>
                                        <div class="space-y-2">
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Winner:</span>
                                                <span class="font-semibold capitalize">{{ $prediction['first_half']['result']['winner'] }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Over 0.5 Goals:</span>
                                                <span class="font-semibold">{{ $this->formatProbability($prediction['first_half']['over_0_5']) }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Over 1.5 Goals:</span>
                                                <span class="font-semibold">{{ $this->formatProbability($prediction['first_half']['over_1_5']) }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Both Teams Score:</span>
                                                <span class="font-semibold">{{ $this->formatProbability($prediction['first_half']['both_teams_to_score']['probability']) }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Second Half -->
                                    <div class="bg-white rounded-lg p-4 shadow-sm">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Second Half</h3>
                                        <div class="space-y-2">
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Over 0.5 Goals:</span>
                                                <span class="font-semibold">{{ $this->formatProbability($prediction['second_half']['over_0_5']) }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Over 1.5 Goals:</span>
                                                <span class="font-semibold">{{ $this->formatProbability($prediction['second_half']['over_1_5']) }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Both Teams Score:</span>
                                                <span class="font-semibold">{{ $this->formatProbability($prediction['second_half']['both_teams_to_score']['probability']) }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Total Goals:</span>
                                                <span class="font-semibold">{{ $prediction['second_half']['total_goals_prediction'] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Team Form and Trends -->
                                @if(isset($prediction['statistical_insights']))
                                <div class="bg-white rounded-lg p-4 shadow-sm mb-6">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Team Form & Trends</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <!-- Team Performance -->
                                        <div>
                                            <h4 class="font-medium text-gray-900 mb-3">Recent Performance</h4>
                                            <div class="space-y-2">
                                                <div class="flex justify-between">
                                                    <span class="text-gray-600">{{ $match->homeTeam->name }} Last 5 Wins:</span>
                                                    <span class="font-semibold">{{ $prediction['statistical_insights']['home_team_last_5_wins'] }}/5</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-gray-600">{{ $match->awayTeam->name }} Last 5 Wins:</span>
                                                    <span class="font-semibold">{{ $prediction['statistical_insights']['away_team_last_5_wins'] }}/5</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-gray-600">H2H Home Advantage:</span>
                                                    <span class="font-semibold">{{ $this->formatProbability($prediction['statistical_insights']['h2h_home_advantage']) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Betting Trends -->
                                        <div>
                                            <h4 class="font-medium text-gray-900 mb-3">Betting Insights</h4>
                                            <div class="space-y-2">
                                                <div class="flex justify-between">
                                                    <span class="text-gray-600">Betting Trend:</span>
                                                    <span class="font-semibold capitalize">{{ str_replace('_', ' ', $prediction['statistical_insights']['betting_trend']) }}</span>
                                                </div>
                                                <div class="mt-3">
                                                    @if($prediction['statistical_insights']['high_value_bet'])
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            High Value Bet Opportunity
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="mt-2">
                                                    @if($prediction['statistical_insights']['underdog_opportunity'])
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                            Underdog Opportunity
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif

                                <!-- Risk Combinations -->
                                <div class="bg-white rounded-lg p-4 shadow-sm mb-6">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Betting Combinations</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        @foreach(['safe', 'moderate', 'high_risk'] as $risk)
                                            @if(isset($prediction['risk_combinations'][$risk]))
                                                @php $combination = $prediction['risk_combinations'][$risk]; @endphp
                                                <div class="border rounded-lg p-3 {{ $this->getRiskColor($risk) }}">
                                                    <h4 class="font-semibold capitalize mb-2">{{ str_replace('_', ' ', $risk) }}</h4>
                                                    <div class="space-y-1 text-sm">
                                                        <div><strong>Probability:</strong> {{ $this->formatProbability($combination['combined_probability']) }}</div>
                                                        <div><strong>Odds:</strong> {{ $combination['expected_odds'] }}</div>
                                                        <div class="mt-2">
                                                            <strong>Predictions:</strong>
                                                            <ul class="list-disc list-inside mt-1">
                                                                @foreach($combination['predictions'] as $pred)
                                                                    <li>{{ ucwords(str_replace('_', ' ', $pred)) }}</li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Goal Scorers -->
                                @if(isset($prediction['goal_scorers']))
                                    <div class="bg-white rounded-lg p-4 shadow-sm mb-6">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Likely Goal Scorers</h3>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <!-- Home Team Scorers -->
                                            <div>
                                                <h4 class="font-medium text-gray-900 mb-2">{{ $match->homeTeam->name }}</h4>
                                                <div class="space-y-2">
                                                    @foreach($prediction['goal_scorers']['home_team_likely_scorers'] as $scorer)
                                                        <div class="flex justify-between items-center">
                                                            <span class="text-gray-600">{{ $scorer['player_name'] }}</span>
                                                            <span class="font-semibold">{{ $this->formatProbability($scorer['scoring_probability']) }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            
                                            <!-- Away Team Scorers -->
                                            <div>
                                                <h4 class="font-medium text-gray-900 mb-2">{{ $match->awayTeam->name }}</h4>
                                                <div class="space-y-2">
                                                    @foreach($prediction['goal_scorers']['away_team_likely_scorers'] as $scorer)
                                                        <div class="flex justify-between items-center">
                                                            <span class="text-gray-600">{{ $scorer['player_name'] }}</span>
                                                            <span class="font-semibold">{{ $this->formatProbability($scorer['scoring_probability']) }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Analysis -->
                                @if(isset($prediction['analysis']))
                                    <div class="bg-white rounded-lg p-4 shadow-sm">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Analysis</h3>
                                        <p class="text-gray-700 leading-relaxed">{{ $prediction['analysis'] }}</p>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            @if($matches instanceof \Illuminate\Pagination\LengthAwarePaginator && $matches->hasPages())
                <div class="mt-6">
                    {{ $matches->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="bg-white shadow-sm rounded-lg p-12 text-center">
                <div class="mx-auto h-12 w-12 text-gray-400">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No predictions yet</h3>
                @auth
                    @if(auth()->user() && auth()->user()->is_admin)
                        <p class="mt-1 text-sm text-gray-500">Add some matches and generate predictions to get started.</p>
                        <div class="mt-6">
                            <a href="{{ route('admin') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                Admin Panel
                            </a>
                        </div>
                    @else
                        <p class="mt-1 text-sm text-gray-500">No matches available for predictions yet. Check back later!</p>
                    @endif
                @else
                    <p class="mt-1 text-sm text-gray-500">No matches available for predictions yet. Check back later!</p>
                @endauth
            </div>
        @endif
    </div>
</div> 
</div> 