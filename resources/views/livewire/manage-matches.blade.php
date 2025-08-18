<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Manage Matches</h1>
            <div class="flex space-x-3">
                <button 
                    wire:click="refreshMatches"
                    class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded"
                >
                    Refresh
                </button>
                <button 
                    wire:click="toggleAddForm"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                >
                    {{ $showAddForm ? 'Cancel' : 'Add Match' }}
                </button>
            </div>
        </div>

        <!-- Add Match Form -->
        @if($showAddForm)
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Add New Match</h2>
            
            <form wire:submit.prevent="addMatch" class="space-y-4">
                <!-- League Selection -->
                <div>
                    <label for="league" class="block text-sm font-medium text-gray-700">League</label>
                    <select 
                        wire:model.live="selectedLeague" 
                        id="league"
                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                    >
                        <option value="">Select a league</option>
                        @foreach($leagues as $league)
                            <option value="{{ $league->id }}">{{ $league->name }}</option>
                        @endforeach
                    </select>
                    @error('selectedLeague') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Gameweek Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="gameweek" class="block text-sm font-medium text-gray-700">Gameweek Number</label>
                        <input 
                            type="number" 
                            wire:model="gameweek"
                            id="gameweek"
                            min="1"
                            max="50"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            placeholder="1"
                        >
                        @error('gameweek') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="gameweekLabel" class="block text-sm font-medium text-gray-700">Gameweek Label (Optional)</label>
                        <input 
                            type="text" 
                            wire:model="gameweekLabel"
                            id="gameweekLabel"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                            placeholder="e.g., Matchday 1, Week 5"
                        >
                        @error('gameweekLabel') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Team Selection -->
                @if($selectedLeague)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="homeTeam" class="block text-sm font-medium text-gray-700">Home Team</label>
                        <select 
                            wire:model="homeTeamId" 
                            id="homeTeam"
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                        >
                            <option value="">Select home team</option>
                            @foreach($teams as $team)
                                <option value="{{ $team->id }}">{{ $team->name }}</option>
                            @endforeach
                        </select>
                        @error('homeTeamId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="awayTeam" class="block text-sm font-medium text-gray-700">Away Team</label>
                        <select 
                            wire:model="awayTeamId" 
                            id="awayTeam"
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                        >
                            <option value="">Select away team</option>
                            @foreach($teams as $team)
                                <option value="{{ $team->id }}">{{ $team->name }}</option>
                            @endforeach
                        </select>
                        @error('awayTeamId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
                @endif

                <!-- Date and Time -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="matchDate" class="block text-sm font-medium text-gray-700">Match Date</label>
                        <input 
                            type="date" 
                            wire:model="matchDate"
                            id="matchDate"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        >
                        @error('matchDate') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="matchTime" class="block text-sm font-medium text-gray-700">Match Time</label>
                        <input 
                            type="time" 
                            wire:model="matchTime"
                            id="matchTime"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        >
                        @error('matchTime') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end space-x-3">
                    <button 
                        type="button"
                        wire:click="resetForm"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded"
                    >
                        Reset
                    </button>
                    <button 
                        type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded"
                    >
                        Add Match
                    </button>
                </div>
            </form>
        </div>
        @endif

        <!-- Matches List -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Matches</h3>
                
                @if($matches->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">League</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gameweek</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Match</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($matches as $match)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $match->league->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div class="font-medium">{{ $match->gameweek ?? 'N/A' }}</div>
                                        @if($match->gameweek_label)
                                            <div class="text-gray-500 text-xs">{{ $match->gameweek_label }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div class="font-medium">{{ $match->homeTeam->name }}</div>
                                        <div class="text-gray-500">vs</div>
                                        <div class="font-medium">{{ $match->awayTeam->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $match->match_date->format('M j, Y') }}<br>
                                        <span class="text-gray-500">{{ $match->match_date->format('H:i') }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($match->prediction_generated)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Predicted
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button 
                                            wire:click="deleteMatch({{ $match->id }})"
                                            wire:confirm="Are you sure you want to delete this match?"
                                            class="text-red-600 hover:text-red-900"
                                        >
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $matches->links() }}
                    </div>
                @else
                    <div class="text-center py-8">
                        <p class="text-gray-500">No matches found. Add some matches to get started!</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div> 