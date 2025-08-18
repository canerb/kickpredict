<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Admin Management</h1>
            <p class="mt-2 text-gray-600">Manage leagues, teams, and matches</p>
        </div>

        <!-- Success Message -->
        @if (session()->has('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md">
                {{ session('success') }}
            </div>
        @endif

        <!-- Tab Navigation -->
        <div class="bg-white shadow-sm rounded-lg mb-6">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                    <button 
                        wire:click="setActiveTab('leagues')"
                        class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'leagues' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                    >
                        Leagues
                    </button>
                    <button 
                        wire:click="setActiveTab('teams')"
                        class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'teams' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                    >
                        Teams
                    </button>
                    <button 
                        wire:click="setActiveTab('matches')"
                        class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'matches' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                    >
                        Matches
                    </button>
                </nav>
            </div>
        </div>

        <!-- Tab Content -->
        <div class="bg-white shadow-sm rounded-lg">
            @if($activeTab === 'leagues')
                <!-- Leagues Tab -->
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold text-gray-900">Leagues</h2>
                    </div>

                    <!-- Add/Edit League Form -->
                    <div class="bg-gray-50 rounded-lg p-4 mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            {{ $editingLeague ? 'Edit League' : 'Add New League' }}
                        </h3>
                        <form wire:submit.prevent="saveLeague">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">League Name</label>
                                    <input type="text" wire:model="leagueName" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                    @error('leagueName') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                                    <input type="text" wire:model="leagueCountry" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                    @error('leagueCountry') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Country Code</label>
                                    <input type="text" wire:model="leagueCountryCode" maxlength="2" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                    @error('leagueCountryCode') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="mt-4 flex space-x-3">
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    {{ $editingLeague ? 'Update League' : 'Add League' }}
                                </button>
                                @if($editingLeague)
                                    <button type="button" wire:click="resetLeagueForm" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                                        Cancel
                                    </button>
                                @endif
                            </div>
                        </form>
                    </div>

                    <!-- Leagues List -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Country</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teams</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($leagues as $league)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $league->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $league->country }} ({{ $league->country_code }})</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $league->teams->count() }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-3">
                                            <button wire:click="editLeague({{ $league->id }})" class="text-blue-600 hover:text-blue-900">Edit</button>
                                            <button wire:click="deleteLeague({{ $league->id }})" wire:confirm="Are you sure?" class="text-red-600 hover:text-red-900">Delete</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $leagues->links() }}
                </div>

            @elseif($activeTab === 'teams')
                <!-- Teams Tab -->
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold text-gray-900">Teams</h2>
                    </div>

                    <!-- Add/Edit Team Form -->
                    <div class="bg-gray-50 rounded-lg p-4 mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            {{ $editingTeam ? 'Edit Team' : 'Add New Team' }}
                        </h3>
                        <form wire:submit.prevent="saveTeam">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Team Name</label>
                                    <input type="text" wire:model="teamName" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                    @error('teamName') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">League</label>
                                    <select wire:model="teamLeagueId" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                        <option value="">Select League</option>
                                        @foreach($allLeagues as $league)
                                            <option value="{{ $league->id }}">{{ $league->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('teamLeagueId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Country Code</label>
                                    <input type="text" wire:model="teamCountryCode" maxlength="2" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                    @error('teamCountryCode') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="mt-4 flex space-x-3">
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    {{ $editingTeam ? 'Update Team' : 'Add Team' }}
                                </button>
                                @if($editingTeam)
                                    <button type="button" wire:click="resetTeamForm" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                                        Cancel
                                    </button>
                                @endif
                            </div>
                        </form>
                    </div>

                    <!-- Teams List -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">League</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Country</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($teams as $team)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $team->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $team->league->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $team->country_code }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-3">
                                            <button wire:click="editTeam({{ $team->id }})" class="text-blue-600 hover:text-blue-900">Edit</button>
                                            <button wire:click="deleteTeam({{ $team->id }})" wire:confirm="Are you sure?" class="text-red-600 hover:text-red-900">Delete</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $teams->links() }}
                </div>

            @elseif($activeTab === 'matches')
                <!-- Matches Tab -->
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold text-gray-900">Matches</h2>
                    </div>

                    <!-- Add/Edit Match Form -->
                    <div class="bg-gray-50 rounded-lg p-4 mb-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">
                            {{ $editingMatch ? 'Edit Match' : 'Add New Match' }}
                        </h3>
                        <form wire:submit.prevent="saveMatch">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">League</label>
                                    <select wire:model="matchLeagueId" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                        <option value="">Select League</option>
                                        @foreach($allLeagues as $league)
                                            <option value="{{ $league->id }}">{{ $league->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('matchLeagueId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Gameweek Number</label>
                                    <input type="number" wire:model="matchGameweek" min="1" max="50" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                    @error('matchGameweek') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Gameweek Label (Optional)</label>
                                    <input type="text" wire:model="matchGameweekLabel" placeholder="e.g., Matchday 1" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    @error('matchGameweekLabel') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Home Team</label>
                                    <select wire:model="matchHomeTeamId" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                        <option value="">Select Home Team</option>
                                        @foreach($allTeams as $team)
                                            <option value="{{ $team->id }}">{{ $team->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('matchHomeTeamId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Away Team</label>
                                    <select wire:model="matchAwayTeamId" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                        <option value="">Select Away Team</option>
                                        @foreach($allTeams as $team)
                                            <option value="{{ $team->id }}">{{ $team->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('matchAwayTeamId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            
                            <!-- Second row for date and venue -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Match Date</label>
                                    <input type="datetime-local" wire:model="matchDate" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                    @error('matchDate') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Venue (Optional)</label>
                                    <input type="text" wire:model="matchVenue" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                            <div class="mt-4 flex space-x-3">
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    {{ $editingMatch ? 'Update Match' : 'Add Match' }}
                                </button>
                                @if($editingMatch)
                                    <button type="button" wire:click="resetMatchForm" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                                        Cancel
                                    </button>
                                @endif
                            </div>
                        </form>
                    </div>

                    <!-- Matches List -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">League</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gameweek</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Match</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($matches as $match)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $match->league->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $match->gameweek }}
                                            @if($match->gameweek_label)
                                                ({{ $match->gameweek_label }})
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $match->homeTeam->name }} vs {{ $match->awayTeam->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $match->match_date->format('M j, Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                {{ $match->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                                   ($match->status === 'live' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                {{ ucfirst($match->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-3">
                                            <button wire:click="editMatch({{ $match->id }})" class="text-blue-600 hover:text-blue-900">Edit</button>
                                            <button wire:click="deleteMatch({{ $match->id }})" wire:confirm="Are you sure?" class="text-red-600 hover:text-red-900">Delete</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $matches->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
