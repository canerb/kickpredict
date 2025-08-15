<div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200">
    <!-- Match Header -->
    <div class="p-4 sm:p-6 border-b border-gray-100">
        <!-- Date and Status - Mobile: Stack vertically, Desktop: Side by side -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 space-y-2 sm:space-y-0">
            <div class="text-sm text-gray-500 text-center sm:text-left">
                {{ $match->formatted_date }}
            </div>
            <div class="flex items-center justify-center sm:justify-end space-x-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                    {{ $match->status === 'upcoming' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                    {{ ucfirst($match->status) }}
                </span>
                @if($match->prediction_generated)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        AI Analyzed
                    </span>
                @endif
            </div>
        </div>

        <!-- Teams -->
        <div class="flex items-center">
            <!-- Home Team -->
            <div class="flex items-center space-x-2 sm:space-x-3 flex-1 justify-end">
                <div class="text-right min-w-0">
                    <div class="font-semibold text-gray-900 text-sm sm:text-base truncate">{{ $match->homeTeam->name }}</div>
                    <div class="text-xs sm:text-sm text-gray-500">Home</div>
                </div>
                <img src="{{ $match->homeTeam->flag_url }}" alt="{{ $match->homeTeam->name }}" class="w-6 h-4 sm:w-8 sm:h-6 rounded flex-shrink-0">
            </div>

            <!-- VS -->
            <div class="px-3 sm:px-4 flex-shrink-0">
                <div class="text-base sm:text-lg font-bold text-gray-400">VS</div>
            </div>

            <!-- Away Team -->
            <div class="flex items-center space-x-2 sm:space-x-3 flex-1">
                <img src="{{ $match->awayTeam->flag_url }}" alt="{{ $match->awayTeam->name }}" class="w-6 h-4 sm:w-8 sm:h-6 rounded flex-shrink-0">
                <div class="text-left min-w-0">
                    <div class="font-semibold text-gray-900 text-sm sm:text-base truncate">{{ $match->awayTeam->name }}</div>
                    <div class="text-xs sm:text-sm text-gray-500">Away</div>
                </div>
            </div>
        </div>

        @if($match->venue)
            <div class="mt-3 text-sm text-gray-500 text-center">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                {{ $match->venue }}
            </div>
        @endif
    </div>

    <!-- Prediction Toggle -->
    @if($match->prediction_generated && $match->prediction)
        <div class="px-6 py-4 border-b border-gray-100">
            <button 
                wire:click="togglePredictions" 
                class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                {{ $showPredictions ? 'Hide' : 'Show' }} AI Predictions
            </button>
        </div>
    @else
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
            <div class="text-center text-gray-500">
                <svg class="w-8 h-8 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm">This match will include predictions when analyzed as part of the gameweek.</p>
            </div>
        </div>
    @endif

    <!-- Predictions Display -->
    @if($showPredictions && $match->prediction)
        <div class="p-6 bg-gray-50">
            @php $predictionData = $match->prediction->prediction_data; @endphp
            
            <!-- Predictions Tabs -->
            <div class="mb-6">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                        <button 
                            wire:click="setActiveTab('basic')"
                            class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'basic' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            Basic
                        </button>
                        <button 
                            wire:click="setActiveTab('advanced')"
                            class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'advanced' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            Advanced
                        </button>
                        <button 
                            wire:click="setActiveTab('halftime')"
                            class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'halftime' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            Halftime
                        </button>
                        <button 
                            wire:click="setActiveTab('scorers')"
                            class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'scorers' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            Scorers
                        </button>
                        <button 
                            wire:click="setActiveTab('combinations')"
                            class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'combinations' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            Combinations
                        </button>
                        <button 
                            wire:click="setActiveTab('advanced-goals')"
                            class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'advanced-goals' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            Advanced Goals
                        </button>
                        <button 
                            wire:click="setActiveTab('advanced-betting')"
                            class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'advanced-betting' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            Advanced Betting
                        </button>
                        <button 
                            wire:click="setActiveTab('insights')"
                            class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'insights' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            Insights
                        </button>
                    </nav>
                </div>
            </div>

            <!-- Basic Tab -->
            @if($activeTab === 'basic')
            <!-- Match Result Prediction -->
            <div class="mb-6">
                <h4 class="font-semibold text-gray-900 mb-3">Match Result</h4>
                <div class="grid grid-cols-3 gap-3">
                    <div class="text-center p-3 bg-white rounded border {{ $predictionData['match_result']['winner'] === 'home' ? 'ring-2 ring-green-500 bg-green-50' : '' }}">
                            <div class="text-sm text-gray-600">{{ $match->homeTeam->name }}</div>
                        <div class="text-lg font-semibold {{ $this->getMatchResultColorClass($predictionData['match_result']['winner'] === 'home' ? 'home' : '') }}">
                            {{ number_format($predictionData['match_result']['home_win_probability'] * 100, 1) }}%
                        </div>
                    </div>
                    <div class="text-center p-3 bg-white rounded border {{ $predictionData['match_result']['winner'] === 'draw' ? 'ring-2 ring-yellow-500 bg-yellow-50' : '' }}">
                        <div class="text-sm text-gray-600">Draw</div>
                        <div class="text-lg font-semibold {{ $this->getMatchResultColorClass($predictionData['match_result']['winner'] === 'draw' ? 'draw' : '') }}">
                            {{ number_format($predictionData['match_result']['draw_probability'] * 100, 1) }}%
                        </div>
                    </div>
                    <div class="text-center p-3 bg-white rounded border {{ $predictionData['match_result']['winner'] === 'away' ? 'ring-2 ring-red-500 bg-red-50' : '' }}">
                            <div class="text-sm text-gray-600">{{ $match->awayTeam->name }}</div>
                        <div class="text-lg font-semibold {{ $this->getMatchResultColorClass($predictionData['match_result']['winner'] === 'away' ? 'away' : '') }}">
                            {{ number_format($predictionData['match_result']['away_win_probability'] * 100, 1) }}%
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Over/Under Goals -->
                <div class="mb-6">
                    <h4 class="font-semibold text-gray-900 mb-3">Goals Predictions</h4>
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div class="p-3 bg-white rounded border">
                            <div class="text-sm text-gray-600">Over 2.5 Goals</div>
                            <div class="text-lg font-semibold text-blue-600">{{ number_format(($predictionData['over_under']['over_2_5'] ?? 0) * 100, 1) }}%</div>
                        </div>
                        <div class="p-3 bg-white rounded border">
                            <div class="text-sm text-gray-600">Under 2.5 Goals</div>
                            <div class="text-lg font-semibold text-orange-600">{{ number_format(($predictionData['over_under']['under_2_5'] ?? 0) * 100, 1) }}%</div>
                </div>
            </div>
                    <div class="p-3 bg-white rounded border">
                        <div class="text-sm text-gray-600">Total Goals Prediction</div>
                        <div class="text-lg font-semibold text-purple-600">{{ number_format($predictionData['over_under']['total_goals_prediction'] ?? 0, 1) }}</div>
                    </div>
                </div>

                <!-- Both Teams to Score -->
                <div class="mb-6">
                    <h4 class="font-semibold text-gray-900 mb-3">Both Teams to Score</h4>
                    <div class="p-3 bg-white rounded border">
                        <div class="flex items-center justify-between">
                            <span class="text-lg font-semibold {{ ($predictionData['both_teams_to_score']['prediction'] ?? false) ? 'text-green-600' : 'text-red-600' }}">
                                {{ ($predictionData['both_teams_to_score']['prediction'] ?? false) ? 'Yes' : 'No' }}
                            </span>
                            <span class="text-sm text-gray-600">{{ number_format(($predictionData['both_teams_to_score']['probability'] ?? 0) * 100, 1) }}%</span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Advanced Tab -->
            @if($activeTab === 'advanced')
                <!-- Double Chance -->
                <div class="mb-6">
                    <h4 class="font-semibold text-gray-900 mb-3">Double Chance</h4>
                    <div class="grid grid-cols-3 gap-3">
                        <div class="p-3 bg-white rounded border">
                            <div class="text-sm text-gray-600">{{ $match->homeTeam->name }} or Draw</div>
                            <div class="text-lg font-semibold text-green-600">{{ number_format(($predictionData['double_chance']['home_or_draw'] ?? 0) * 100, 1) }}%</div>
                        </div>
                        <div class="p-3 bg-white rounded border">
                            <div class="text-sm text-gray-600">{{ $match->awayTeam->name }} or Draw</div>
                            <div class="text-lg font-semibold text-red-600">{{ number_format(($predictionData['double_chance']['away_or_draw'] ?? 0) * 100, 1) }}%</div>
                        </div>
                        <div class="p-3 bg-white rounded border">
                            <div class="text-sm text-gray-600">{{ $match->homeTeam->name }} or {{ $match->awayTeam->name }}</div>
                            <div class="text-lg font-semibold text-blue-600">{{ number_format(($predictionData['double_chance']['home_or_away'] ?? 0) * 100, 1) }}%</div>
                        </div>
                    </div>
                </div>

                <!-- Handicaps -->
                @if(isset($predictionData['handicaps']))
                    <div class="mb-6">
                        <h4 class="font-semibold text-gray-900 mb-3">Handicap Betting</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <h5 class="text-sm font-medium text-gray-700 mb-2">{{ $match->homeTeam->name }}</h5>
                                <div class="space-y-2">
                                    <div class="flex justify-between p-2 bg-white rounded border">
                                        <span class="text-sm">-1.0</span>
                                        <span class="text-sm font-medium">{{ number_format(($predictionData['handicaps']['home_minus_1'] ?? 0) * 100, 1) }}%</span>
                                    </div>
                                    <div class="flex justify-between p-2 bg-white rounded border">
                                        <span class="text-sm">+1.0</span>
                                        <span class="text-sm font-medium">{{ number_format(($predictionData['handicaps']['home_plus_1'] ?? 0) * 100, 1) }}%</span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <h5 class="text-sm font-medium text-gray-700 mb-2">{{ $match->awayTeam->name }}</h5>
                                <div class="space-y-2">
                                    <div class="flex justify-between p-2 bg-white rounded border">
                                        <span class="text-sm">-1.0</span>
                                        <span class="text-sm font-medium">{{ number_format(($predictionData['handicaps']['away_minus_1'] ?? 0) * 100, 1) }}%</span>
                                    </div>
                                    <div class="flex justify-between p-2 bg-white rounded border">
                                        <span class="text-sm">+1.0</span>
                                        <span class="text-sm font-medium">{{ number_format(($predictionData['handicaps']['away_plus_1'] ?? 0) * 100, 1) }}%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Multiple Over/Under Options -->
                <div class="mb-6">
                    <h4 class="font-semibold text-gray-900 mb-3">Over/Under Markets</h4>
                    <div class="grid grid-cols-2 gap-3">
                        @if(isset($predictionData['over_under']['over_0_5']))
                            <div class="p-3 bg-white rounded border">
                                <div class="text-sm text-gray-600">Over 0.5</div>
                                <div class="text-lg font-semibold text-blue-600">{{ number_format($predictionData['over_under']['over_0_5'] * 100, 1) }}%</div>
                            </div>
                        @endif
                        @if(isset($predictionData['over_under']['over_1_5']))
                            <div class="p-3 bg-white rounded border">
                                <div class="text-sm text-gray-600">Over 1.5</div>
                                <div class="text-lg font-semibold text-blue-600">{{ number_format($predictionData['over_under']['over_1_5'] * 100, 1) }}%</div>
                            </div>
                        @endif
                        @if(isset($predictionData['over_under']['over_3_5']))
                            <div class="p-3 bg-white rounded border">
                                <div class="text-sm text-gray-600">Over 3.5</div>
                                <div class="text-lg font-semibold text-blue-600">{{ number_format($predictionData['over_under']['over_3_5'] * 100, 1) }}%</div>
                            </div>
                        @endif
                        @if(isset($predictionData['over_under']['under_1_5']))
                            <div class="p-3 bg-white rounded border">
                                <div class="text-sm text-gray-600">Under 1.5</div>
                                <div class="text-lg font-semibold text-orange-600">{{ number_format($predictionData['over_under']['under_1_5'] * 100, 1) }}%</div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Halftime Tab -->
            @if($activeTab === 'halftime')
                @if(isset($predictionData['first_half']))
                    <!-- First Half Result -->
                    <div class="mb-6">
                        <h4 class="font-semibold text-gray-900 mb-3">First Half Result</h4>
                        <div class="grid grid-cols-3 gap-3">
                            <div class="text-center p-3 bg-white rounded border {{ ($predictionData['first_half']['result']['winner'] ?? '') === 'home' ? 'ring-2 ring-green-500 bg-green-50' : '' }}">
                                <div class="text-sm text-gray-600">{{ $match->homeTeam->name }}</div>
                                <div class="text-lg font-semibold">{{ number_format(($predictionData['first_half']['result']['home_win_probability'] ?? 0) * 100, 1) }}%</div>
                            </div>
                            <div class="text-center p-3 bg-white rounded border {{ ($predictionData['first_half']['result']['winner'] ?? '') === 'draw' ? 'ring-2 ring-yellow-500 bg-yellow-50' : '' }}">
                                <div class="text-sm text-gray-600">Draw</div>
                                <div class="text-lg font-semibold">{{ number_format(($predictionData['first_half']['result']['draw_probability'] ?? 0) * 100, 1) }}%</div>
                            </div>
                            <div class="text-center p-3 bg-white rounded border {{ ($predictionData['first_half']['result']['winner'] ?? '') === 'away' ? 'ring-2 ring-red-500 bg-red-50' : '' }}">
                                <div class="text-sm text-gray-600">{{ $match->awayTeam->name }}</div>
                                <div class="text-lg font-semibold">{{ number_format(($predictionData['first_half']['result']['away_win_probability'] ?? 0) * 100, 1) }}%</div>
                    </div>
                </div>
            </div>

                    <!-- First Half Goals & BTTS -->
                    <div class="mb-6">
                        <h4 class="font-semibold text-gray-900 mb-3">First Half Markets</h4>
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div class="p-3 bg-white rounded border">
                                <div class="text-sm text-gray-600">1H Over 0.5</div>
                                <div class="text-lg font-semibold text-blue-600">{{ number_format(($predictionData['first_half']['over_0_5'] ?? 0) * 100, 1) }}%</div>
                            </div>
                            <div class="p-3 bg-white rounded border">
                                <div class="text-sm text-gray-600">1H Over 1.5</div>
                                <div class="text-lg font-semibold text-blue-600">{{ number_format(($predictionData['first_half']['over_1_5'] ?? 0) * 100, 1) }}%</div>
                            </div>
                        </div>
                        <div class="p-3 bg-white rounded border">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">1H Both Teams Score</span>
                                <span class="text-lg font-semibold {{ ($predictionData['first_half']['both_teams_to_score']['prediction'] ?? false) ? 'text-green-600' : 'text-red-600' }}">
                                    {{ ($predictionData['first_half']['both_teams_to_score']['prediction'] ?? false) ? 'Yes' : 'No' }} ({{ number_format(($predictionData['first_half']['both_teams_to_score']['probability'] ?? 0) * 100, 1) }}%)
                                </span>
                            </div>
                        </div>
                    </div>
                @endif

                @if(isset($predictionData['second_half']))
                    <!-- Second Half Markets -->
            <div class="mb-6">
                        <h4 class="font-semibold text-gray-900 mb-3">Second Half Markets</h4>
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div class="p-3 bg-white rounded border">
                                <div class="text-sm text-gray-600">2H Over 0.5</div>
                                <div class="text-lg font-semibold text-blue-600">{{ number_format(($predictionData['second_half']['over_0_5'] ?? 0) * 100, 1) }}%</div>
                            </div>
                            <div class="p-3 bg-white rounded border">
                                <div class="text-sm text-gray-600">2H Over 1.5</div>
                                <div class="text-lg font-semibold text-blue-600">{{ number_format(($predictionData['second_half']['over_1_5'] ?? 0) * 100, 1) }}%</div>
                            </div>
                        </div>
                <div class="p-3 bg-white rounded border">
                    <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">2H Both Teams Score</span>
                                <span class="text-lg font-semibold {{ ($predictionData['second_half']['both_teams_to_score']['prediction'] ?? false) ? 'text-green-600' : 'text-red-600' }}">
                                    {{ ($predictionData['second_half']['both_teams_to_score']['prediction'] ?? false) ? 'Yes' : 'No' }} ({{ number_format(($predictionData['second_half']['both_teams_to_score']['probability'] ?? 0) * 100, 1) }}%)
                        </span>
                            </div>
                        </div>
                    </div>
                @endif
            @endif

            <!-- Scorers Tab -->
            @if($activeTab === 'scorers')
                @if(isset($predictionData['first_goal']))
                    <!-- First Goal -->
                    <div class="mb-6">
                        <h4 class="font-semibold text-gray-900 mb-3">First Goal</h4>
                        <div class="grid grid-cols-3 gap-3">
                            <div class="p-3 bg-white rounded border">
                                <div class="text-sm text-gray-600">{{ $match->homeTeam->name }}</div>
                                <div class="text-lg font-semibold text-green-600">{{ number_format(($predictionData['first_goal']['home_team'] ?? 0) * 100, 1) }}%</div>
                            </div>
                            <div class="p-3 bg-white rounded border">
                                <div class="text-sm text-gray-600">{{ $match->awayTeam->name }}</div>
                                <div class="text-lg font-semibold text-red-600">{{ number_format(($predictionData['first_goal']['away_team'] ?? 0) * 100, 1) }}%</div>
                            </div>
                            <div class="p-3 bg-white rounded border">
                                <div class="text-sm text-gray-600">No Goals</div>
                                <div class="text-lg font-semibold text-gray-600">{{ number_format(($predictionData['first_goal']['no_goals'] ?? 0) * 100, 1) }}%</div>
                            </div>
                        </div>
                    </div>
                @endif

                @if(isset($predictionData['goal_scorers']))
                    <!-- Likely Goal Scorers -->
                    <div class="mb-6">
                        <h4 class="font-semibold text-gray-900 mb-3">Likely Goal Scorers</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <!-- Home Team Scorers -->
                            <div>
                                <h5 class="text-sm font-medium text-gray-700 mb-2">{{ $match->homeTeam->name }}</h5>
                                <div class="space-y-2">
                                    @foreach(($predictionData['goal_scorers']['home_team_likely_scorers'] ?? []) as $scorer)
                                        <div class="flex justify-between p-2 bg-white rounded border">
                                            <span class="text-sm">{{ $scorer['player_name'] ?? 'Unknown' }}</span>
                                            <span class="text-sm font-medium text-green-600">{{ number_format(($scorer['scoring_probability'] ?? 0) * 100, 1) }}%</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <!-- Away Team Scorers -->
                            <div>
                                <h5 class="text-sm font-medium text-gray-700 mb-2">{{ $match->awayTeam->name }}</h5>
                                <div class="space-y-2">
                                    @foreach(($predictionData['goal_scorers']['away_team_likely_scorers'] ?? []) as $scorer)
                                        <div class="flex justify-between p-2 bg-white rounded border">
                                            <span class="text-sm">{{ $scorer['player_name'] ?? 'Unknown' }}</span>
                                            <span class="text-sm font-medium text-red-600">{{ number_format(($scorer['scoring_probability'] ?? 0) * 100, 1) }}%</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endif

            <!-- Combinations Tab -->
            @if($activeTab === 'combinations')
                @if(isset($predictionData['risk_combinations']))
                    <div class="space-y-6">
                        <!-- Safe Combination -->
                        @if(isset($predictionData['risk_combinations']['safe']))
                            <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                                <h4 class="font-semibold text-green-800 mb-2 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Safe Bet
                                </h4>
                                <p class="text-sm text-green-700 mb-3">{{ $predictionData['risk_combinations']['safe']['description'] ?? '' }}</p>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="bg-white p-3 rounded border">
                                        <div class="text-sm text-gray-600">Probability</div>
                                        <div class="text-lg font-semibold text-green-600">{{ number_format(($predictionData['risk_combinations']['safe']['combined_probability'] ?? 0) * 100, 1) }}%</div>
                                    </div>
                                    <div class="bg-white p-3 rounded border">
                                        <div class="text-sm text-gray-600">Expected Odds</div>
                                        <div class="text-lg font-semibold text-green-600">{{ number_format($predictionData['risk_combinations']['safe']['expected_odds'] ?? 0, 2) }}</div>
                                    </div>
                                </div>
                                @if(isset($predictionData['risk_combinations']['safe']['predictions']))
                                    <div class="mt-3">
                                        <div class="text-sm text-green-700 font-medium mb-1">Predictions:</div>
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($predictionData['risk_combinations']['safe']['predictions'] as $prediction)
                                                <span class="inline-block px-2 py-1 bg-green-100 text-green-800 text-xs rounded">{{ $prediction }}</span>
                                            @endforeach
                </div>
            </div>
                                @endif
                            </div>
                        @endif

                        <!-- Moderate Combination -->
                        @if(isset($predictionData['risk_combinations']['moderate']))
                            <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <h4 class="font-semibold text-yellow-800 mb-2 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    Moderate Risk
                                </h4>
                                <p class="text-sm text-yellow-700 mb-3">{{ $predictionData['risk_combinations']['moderate']['description'] ?? '' }}</p>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="bg-white p-3 rounded border">
                                        <div class="text-sm text-gray-600">Probability</div>
                                        <div class="text-lg font-semibold text-yellow-600">{{ number_format(($predictionData['risk_combinations']['moderate']['combined_probability'] ?? 0) * 100, 1) }}%</div>
                                    </div>
                                    <div class="bg-white p-3 rounded border">
                                        <div class="text-sm text-gray-600">Expected Odds</div>
                                        <div class="text-lg font-semibold text-yellow-600">{{ number_format($predictionData['risk_combinations']['moderate']['expected_odds'] ?? 0, 2) }}</div>
                                    </div>
                                </div>
                                @if(isset($predictionData['risk_combinations']['moderate']['predictions']))
                                    <div class="mt-3">
                                        <div class="text-sm text-yellow-700 font-medium mb-1">Predictions:</div>
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($predictionData['risk_combinations']['moderate']['predictions'] as $prediction)
                                                <span class="inline-block px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded">{{ $prediction }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- High Risk Combination -->
                        @if(isset($predictionData['risk_combinations']['high_risk']))
                            <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                                <h4 class="font-semibold text-red-800 mb-2 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    High Risk
                                </h4>
                                <p class="text-sm text-red-700 mb-3">{{ $predictionData['risk_combinations']['high_risk']['description'] ?? '' }}</p>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="bg-white p-3 rounded border">
                                        <div class="text-sm text-gray-600">Probability</div>
                                        <div class="text-lg font-semibold text-red-600">{{ number_format(($predictionData['risk_combinations']['high_risk']['combined_probability'] ?? 0) * 100, 1) }}%</div>
                                    </div>
                                    <div class="bg-white p-3 rounded border">
                                        <div class="text-sm text-gray-600">Expected Odds</div>
                                        <div class="text-lg font-semibold text-red-600">{{ number_format($predictionData['risk_combinations']['high_risk']['expected_odds'] ?? 0, 2) }}</div>
                                    </div>
                                </div>
                                @if(isset($predictionData['risk_combinations']['high_risk']['predictions']))
                                    <div class="mt-3">
                                        <div class="text-sm text-red-700 font-medium mb-1">Predictions:</div>
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($predictionData['risk_combinations']['high_risk']['predictions'] as $prediction)
                                                <span class="inline-block px-2 py-1 bg-red-100 text-red-800 text-xs rounded">{{ $prediction }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                @endif
            @endif

            <!-- Advanced Goals Tab -->
            @if($activeTab === 'advanced-goals')
                @if(isset($predictionData['advanced_goal_statistics']))
                    <!-- Exact Score -->
                    <div class="mb-6">
                        <h4 class="font-semibold text-gray-900 mb-3">Exact Score Prediction</h4>
                        <div class="p-3 bg-white rounded border">
                            <div class="flex items-center justify-between">
                                <span class="text-lg font-semibold text-blue-600">{{ $predictionData['advanced_goal_statistics']['exact_score_prediction'] ?? 'N/A' }}</span>
                                <span class="text-sm text-gray-600">{{ number_format(($predictionData['advanced_goal_statistics']['exact_score_probability'] ?? 0) * 100, 1) }}%</span>
                            </div>
                        </div>
                    </div>

                    <!-- Clean Sheets -->
                    <div class="mb-6">
                        <h4 class="font-semibold text-gray-900 mb-3">Clean Sheet Predictions</h4>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="p-3 bg-white rounded border">
                                <div class="text-sm text-gray-600">{{ $match->homeTeam->name }} Clean Sheet</div>
                                <div class="text-lg font-semibold text-green-600">{{ number_format(($predictionData['advanced_goal_statistics']['clean_sheet_home_probability'] ?? 0) * 100, 1) }}%</div>
                            </div>
                            <div class="p-3 bg-white rounded border">
                                <div class="text-sm text-gray-600">{{ $match->awayTeam->name }} Clean Sheet</div>
                                <div class="text-lg font-semibold text-red-600">{{ number_format(($predictionData['advanced_goal_statistics']['clean_sheet_away_probability'] ?? 0) * 100, 1) }}%</div>
                            </div>
                        </div>
                    </div>

                    <!-- Goal Ranges -->
                    <div class="mb-6">
                        <h4 class="font-semibold text-gray-900 mb-3">Goal Range Predictions</h4>
                        <div class="grid grid-cols-3 gap-3">
                            <div class="p-3 bg-white rounded border">
                                <div class="text-sm text-gray-600">0-1 Goals</div>
                                <div class="text-lg font-semibold text-gray-600">{{ number_format(($predictionData['advanced_goal_statistics']['goal_range_0_1_probability'] ?? 0) * 100, 1) }}%</div>
                            </div>
                            <div class="p-3 bg-white rounded border">
                                <div class="text-sm text-gray-600">2-3 Goals</div>
                                <div class="text-lg font-semibold text-blue-600">{{ number_format(($predictionData['advanced_goal_statistics']['goal_range_2_3_probability'] ?? 0) * 100, 1) }}%</div>
                            </div>
                            <div class="p-3 bg-white rounded border">
                                <div class="text-sm text-gray-600">4+ Goals</div>
                                <div class="text-lg font-semibold text-purple-600">{{ number_format(($predictionData['advanced_goal_statistics']['goal_range_4_plus_probability'] ?? 0) * 100, 1) }}%</div>
                            </div>
                        </div>
                    </div>

                    <!-- Extended Over/Under -->
                    <div class="mb-6">
                        <h4 class="font-semibold text-gray-900 mb-3">Extended Over/Under</h4>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="p-3 bg-white rounded border">
                                <div class="text-sm text-gray-600">Over 3.5 Goals</div>
                                <div class="text-lg font-semibold text-orange-600">{{ number_format(($predictionData['advanced_goal_statistics']['over_35_probability'] ?? 0) * 100, 1) }}%</div>
                            </div>
                            <div class="p-3 bg-white rounded border">
                                <div class="text-sm text-gray-600">Over 4.5 Goals</div>
                                <div class="text-lg font-semibold text-red-600">{{ number_format(($predictionData['advanced_goal_statistics']['over_45_probability'] ?? 0) * 100, 1) }}%</div>
                            </div>
                            <div class="p-3 bg-white rounded border">
                                <div class="text-sm text-gray-600">Under 1.5 Goals</div>
                                <div class="text-lg font-semibold text-green-600">{{ number_format(($predictionData['advanced_goal_statistics']['under_15_probability'] ?? 0) * 100, 1) }}%</div>
                            </div>
                        </div>
                    </div>
                @endif
            @endif

            <!-- Advanced Betting Tab -->
            @if($activeTab === 'advanced-betting')
                @if(isset($predictionData['advanced_betting_markets']))
                    <!-- Draw No Bet -->
                    <div class="mb-6">
                        <h4 class="font-semibold text-gray-900 mb-3">Draw No Bet</h4>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="p-3 bg-white rounded border">
                                <div class="text-sm text-gray-600">{{ $match->homeTeam->name }} (No Draw)</div>
                                <div class="text-lg font-semibold text-green-600">{{ number_format(($predictionData['advanced_betting_markets']['draw_no_bet_home_probability'] ?? 0) * 100, 1) }}%</div>
                            </div>
                            <div class="p-3 bg-white rounded border">
                                <div class="text-sm text-gray-600">{{ $match->awayTeam->name }} (No Draw)</div>
                                <div class="text-lg font-semibold text-red-600">{{ number_format(($predictionData['advanced_betting_markets']['draw_no_bet_away_probability'] ?? 0) * 100, 1) }}%</div>
                            </div>
                        </div>
                    </div>

                    <!-- Asian Handicaps -->
                    <div class="mb-6">
                        <h4 class="font-semibold text-gray-900 mb-3">Asian Handicaps (±0.5)</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <h5 class="text-sm font-medium text-gray-700 mb-2">{{ $match->homeTeam->name }}</h5>
                                <div class="space-y-2">
                                    <div class="flex justify-between p-2 bg-white rounded border">
                                        <span class="text-sm">-0.5</span>
                                        <span class="text-sm font-medium">{{ number_format(($predictionData['advanced_betting_markets']['asian_handicap_home_minus_05_probability'] ?? 0) * 100, 1) }}%</span>
                                    </div>
                                    <div class="flex justify-between p-2 bg-white rounded border">
                                        <span class="text-sm">+0.5</span>
                                        <span class="text-sm font-medium">{{ number_format(($predictionData['advanced_betting_markets']['asian_handicap_home_plus_05_probability'] ?? 0) * 100, 1) }}%</span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <h5 class="text-sm font-medium text-gray-700 mb-2">{{ $match->awayTeam->name }}</h5>
                                <div class="space-y-2">
                                    <div class="flex justify-between p-2 bg-white rounded border">
                                        <span class="text-sm">-0.5</span>
                                        <span class="text-sm font-medium">{{ number_format(($predictionData['advanced_betting_markets']['asian_handicap_away_minus_05_probability'] ?? 0) * 100, 1) }}%</span>
                                    </div>
                                    <div class="flex justify-between p-2 bg-white rounded border">
                                        <span class="text-sm">+0.5</span>
                                        <span class="text-sm font-medium">{{ number_format(($predictionData['advanced_betting_markets']['asian_handicap_away_plus_05_probability'] ?? 0) * 100, 1) }}%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Winning Margins -->
                    <div class="mb-6">
                        <h4 class="font-semibold text-gray-900 mb-3">Winning Margins</h4>
                        <div class="grid grid-cols-3 gap-3">
                            <div class="p-3 bg-white rounded border">
                                <div class="text-sm text-gray-600">1 Goal</div>
                                <div class="text-lg font-semibold text-blue-600">{{ number_format(($predictionData['advanced_betting_markets']['winning_margin_1_goal_probability'] ?? 0) * 100, 1) }}%</div>
                            </div>
                            <div class="p-3 bg-white rounded border">
                                <div class="text-sm text-gray-600">2 Goals</div>
                                <div class="text-lg font-semibold text-orange-600">{{ number_format(($predictionData['advanced_betting_markets']['winning_margin_2_goals_probability'] ?? 0) * 100, 1) }}%</div>
                            </div>
                            <div class="p-3 bg-white rounded border">
                                <div class="text-sm text-gray-600">3+ Goals</div>
                                <div class="text-lg font-semibold text-red-600">{{ number_format(($predictionData['advanced_betting_markets']['winning_margin_3_plus_probability'] ?? 0) * 100, 1) }}%</div>
                            </div>
                        </div>
                    </div>
                @endif
            @endif

            <!-- Insights Tab -->
            @if($activeTab === 'insights')
                @if(isset($predictionData['statistical_insights']))
                    <!-- Team Form -->
                    <div class="mb-6">
                        <h4 class="font-semibold text-gray-900 mb-3">Team Form Analysis</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-4 bg-white rounded border">
                                <h5 class="text-sm font-medium text-gray-700 mb-2">{{ $match->homeTeam->name }}</h5>
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Form Score:</span>
                                        <span class="text-sm font-medium text-green-600">{{ number_format(($predictionData['statistical_insights']['home_team_form_score'] ?? 0) * 100, 1) }}%</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Last 5 Wins:</span>
                                        <span class="text-sm font-medium">{{ $predictionData['statistical_insights']['home_team_last_5_wins'] ?? 0 }}/5</span>
                                    </div>
                                </div>
                            </div>
                            <div class="p-4 bg-white rounded border">
                                <h5 class="text-sm font-medium text-gray-700 mb-2">{{ $match->awayTeam->name }}</h5>
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Form Score:</span>
                                        <span class="text-sm font-medium text-red-600">{{ number_format(($predictionData['statistical_insights']['away_team_form_score'] ?? 0) * 100, 1) }}%</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600">Last 5 Wins:</span>
                                        <span class="text-sm font-medium">{{ $predictionData['statistical_insights']['away_team_last_5_wins'] ?? 0 }}/5</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Head-to-Head -->
                    <div class="mb-6">
                        <h4 class="font-semibold text-gray-900 mb-3">Head-to-Head Advantage</h4>
                        <div class="p-3 bg-white rounded border">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Home Team Advantage</span>
                                <span class="text-lg font-semibold text-blue-600">{{ number_format(($predictionData['statistical_insights']['h2h_home_advantage'] ?? 0) * 100, 1) }}%</span>
                            </div>
                        </div>
                    </div>

                    <!-- Value Betting -->
                    <div class="mb-6">
                        <h4 class="font-semibold text-gray-900 mb-3">Value Betting Opportunities</h4>
                        <div class="space-y-3">
                            @if($predictionData['statistical_insights']['high_value_bet'] ?? false)
                                <div class="p-3 bg-green-50 border border-green-200 rounded-lg">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="text-sm font-medium text-green-800">High Value Bet Identified</span>
                                    </div>
                                </div>
                            @endif
                            @if($predictionData['statistical_insights']['underdog_opportunity'] ?? false)
                                <div class="p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="text-sm font-medium text-blue-800">Underdog Opportunity</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Betting Trend -->
                    <div class="mb-6">
                        <h4 class="font-semibold text-gray-900 mb-3">Betting Trend</h4>
                        <div class="p-3 bg-white rounded border">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Market Trend</span>
                                <span class="text-sm font-medium capitalize {{ $predictionData['statistical_insights']['betting_trend'] === 'trending_up' ? 'text-green-600' : ($predictionData['statistical_insights']['betting_trend'] === 'trending_down' ? 'text-red-600' : 'text-gray-600') }}">
                                    {{ str_replace('_', ' ', $predictionData['statistical_insights']['betting_trend'] ?? 'stable') }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endif
            @endif

            <!-- Analysis -->
            <div class="mt-6 pt-4 border-t border-gray-200">
                <h4 class="font-semibold text-gray-900 mb-2">AI Analysis</h4>
                <p class="text-sm text-gray-600 leading-relaxed">{{ $match->prediction->analysis }}</p>
                <div class="mt-3 flex items-center justify-between text-xs text-gray-500">
                    <span>Confidence Score: {{ number_format($match->prediction->confidence_score * 100, 1) }}%</span>
                    <span>Generated: {{ $match->prediction->predicted_at->diffForHumans() }}</span>
                </div>
            </div>
        </div>
    @endif
</div>
