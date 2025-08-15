<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Manage Prediction Results</h1>
            <p class="mt-2 text-gray-600">Enter actual match results to verify predictions and track accuracy</p>
        </div>



        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- League Selector -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">League</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach($leagues as $league)
                            <button 
                                wire:click="selectLeague({{ $league->id }})"
                                class="px-3 py-1 rounded text-sm font-medium transition-colors duration-200 {{ $selectedLeagueId == $league->id ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                            >
                                {{ $league->name }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <div class="flex gap-2">
                        <button 
                            wire:click="setStatusFilter('all')"
                            class="px-3 py-1 rounded text-sm font-medium transition-colors duration-200 {{ $statusFilter === 'all' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                        >
                            All
                        </button>
                        <button 
                            wire:click="setStatusFilter('pending')"
                            class="px-3 py-1 rounded text-sm font-medium transition-colors duration-200 {{ $statusFilter === 'pending' ? 'bg-yellow-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                        >
                            Pending
                        </button>
                        <button 
                            wire:click="setStatusFilter('verified')"
                            class="px-3 py-1 rounded text-sm font-medium transition-colors duration-200 {{ $statusFilter === 'verified' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                        >
                            Verified
                        </button>
                    </div>
                </div>

                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search Teams</label>
                    <input 
                        type="text" 
                        wire:model.live="searchTerm"
                        placeholder="Search by team name..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                </div>
            </div>
        </div>

        <!-- Results Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Match</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prediction</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Confidence</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($predictions as $prediction)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $prediction->match->homeTeam->name }} vs {{ $prediction->match->awayTeam->name }}
                                    </div>
                                    <div class="text-sm text-gray-500">{{ $prediction->match->league->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $prediction->match->match_date->format('M d, Y') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 capitalize">{{ $prediction->predicted_winner }}</div>
                                    <div class="text-sm text-gray-500">{{ number_format($prediction->confidence_score * 100, 1) }}%</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $prediction->confidence_score > 0.7 ? 'bg-green-100 text-green-800' : ($prediction->confidence_score > 0.5 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ number_format($prediction->confidence_score * 100, 0) }}%
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($prediction->prediction_verified)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $prediction->prediction_correct ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $prediction->prediction_correct ? 'Correct' : 'Incorrect' }}
                                        </span>
                                        @if($prediction->actual_home_goals !== null && $prediction->actual_away_goals !== null)
                                            <div class="text-sm text-gray-500 mt-1">
                                                {{ $prediction->actual_home_goals }}-{{ $prediction->actual_away_goals }}
                                            </div>
                                        @endif
                                        @if($prediction->accuracy_details)
                                            <button 
                                                wire:click="$set('showAccuracyDetails', {{ $prediction->id }})"
                                                class="text-xs text-blue-600 hover:text-blue-800 mt-1 underline"
                                            >
                                                View Details
                                            </button>
                                        @endif
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Pending
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    @if(!$prediction->prediction_verified)
                                        <button 
                                            wire:click="editResult({{ $prediction->id }})"
                                            class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-3 py-1 rounded-md text-sm transition-colors duration-200"
                                        >
                                            Enter Result
                                        </button>
                                    @else
                                        <button 
                                            wire:click="editResult({{ $prediction->id }})"
                                            class="text-gray-600 hover:text-gray-900 bg-gray-50 hover:bg-gray-100 px-3 py-1 rounded-md text-sm transition-colors duration-200"
                                        >
                                            Edit Result
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                    No predictions found matching your criteria.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($predictions->hasPages())
                <div class="px-6 py-3 border-t border-gray-200">
                    {{ $predictions->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Edit Result Modal -->
    @if($editingPrediction)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" id="modal">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Enter Match Result</h3>
                    <div class="mb-4">
                        <div class="text-sm text-gray-600 mb-2">
                            <strong>{{ $editingPrediction->match->homeTeam->name }}</strong> vs 
                            <strong>{{ $editingPrediction->match->awayTeam->name }}</strong>
                        </div>
                        <div class="text-sm text-gray-500">
                            {{ $editingPrediction->match->match_date->format('M d, Y H:i') }}
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                {{ $editingPrediction->match->homeTeam->name }} Goals
                            </label>
                            <input 
                                type="number" 
                                wire:model="actualHomeGoals"
                                min="0"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                {{ $editingPrediction->match->awayTeam->name }} Goals
                            </label>
                            <input 
                                type="number" 
                                wire:model="actualAwayGoals"
                                min="0"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            >
                        </div>
                    </div>

                    <div class="mb-4 p-3 bg-gray-50 rounded-md">
                        <div class="text-sm text-gray-600">
                            <strong>Prediction:</strong> {{ ucfirst($editingPrediction->predicted_winner) }} win
                            ({{ number_format($editingPrediction->confidence_score * 100, 1) }}% confidence)
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button 
                            wire:click="cancelEdit"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-400 transition-colors duration-200"
                        >
                            Cancel
                        </button>
                        <button 
                            wire:click="saveResult"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700 transition-colors duration-200"
                        >
                            Save Result
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Accuracy Details Modal -->
    @if($showAccuracyDetails)
        @php
            $prediction = $predictions->firstWhere('id', $showAccuracyDetails);
        @endphp
        @if($prediction && $prediction->accuracy_details)
            <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" id="accuracyModal">
                <div class="relative top-10 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
                    <div class="mt-3">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Prediction Accuracy Details</h3>
                            <button 
                                wire:click="$set('showAccuracyDetails', null)"
                                class="text-gray-400 hover:text-gray-600"
                            >
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        
                        <div class="mb-4">
                            <div class="text-sm text-gray-600 mb-2">
                                <strong>{{ $prediction->match->homeTeam->name }}</strong> vs 
                                <strong>{{ $prediction->match->awayTeam->name }}</strong>
                            </div>
                            <div class="text-sm text-gray-500 mb-2">
                                {{ $prediction->match->match_date->format('M d, Y H:i') }}
                            </div>
                            <div class="text-sm text-gray-500">
                                Final Score: {{ $prediction->actual_home_goals }}-{{ $prediction->actual_away_goals }}
                            </div>
                        </div>

                        <div class="space-y-4">
                            @foreach($prediction->accuracy_details as $type => $details)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex justify-between items-center mb-2">
                                        <h4 class="font-medium text-gray-900 capitalize">
                                            {{ str_replace('_', ' ', $type) }}
                                        </h4>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $details['correct'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $details['correct'] ? '✓ Correct' : '✗ Incorrect' }}
                                        </span>
                                    </div>
                                    

                                        <div class="text-sm text-gray-600">
                                            <div><strong>Predicted:</strong> {{ $details['predicted'] }}</div>
                                            <div><strong>Actual:</strong> {{ $details['actual'] }}</div>
                                        </div>
                                    
                                    <div class="text-xs text-gray-500 mt-2">
                                        Weight: {{ number_format($details['weight'] * 100, 0) }}%
                                    </div>
                                </div>
                            @endforeach
                        </div>



                        <div class="flex justify-end mt-6">
                            <button 
                                wire:click="$set('showAccuracyDetails', null)"
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-400 transition-colors duration-200"
                            >
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif
</div> 