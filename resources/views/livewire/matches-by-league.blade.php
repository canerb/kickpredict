<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- League Selector (hidden when only one league) -->
    <livewire:league-selector :selectedLeagueId="$selectedLeagueId" @league-selected="selectLeague($event.detail.leagueId)" />

    <!-- Selected League Header -->
    @if($selectedLeagueId && $leagues->where('id', $selectedLeagueId)->first())
        @php $selectedLeague = $leagues->where('id', $selectedLeagueId)->first(); @endphp
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6 {{ $leagues->count() > 1 ? 'mt-8' : '' }}">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <img src="{{ $selectedLeague->flag_url }}" alt="{{ $selectedLeague->country }}" class="w-10 h-7 rounded shadow-sm">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">{{ $selectedLeague->name }}</h1>
                        <p class="text-gray-600">{{ $selectedLeague->country }} • Next Gameweek Analysis</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <button 
                        wire:click="analyzeNextGameweek" 
                        wire:loading.attr="disabled"
                        class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200"
                    >
                        <svg wire:loading.remove wire:target="analyzeNextGameweek" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        <svg wire:loading wire:target="analyzeNextGameweek" class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="analyzeNextGameweek">Analyze Next Gameweek</span>
                        <span wire:loading wire:target="analyzeNextGameweek">Analyzing...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Loading State -->
    <div wire:loading.delay wire:target="analyzeNextGameweek" class="text-center py-12">
        <div class="inline-flex items-center px-6 py-3 text-blue-600 bg-blue-50 rounded-lg">
            <svg class="animate-spin w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="font-medium">AI is analyzing the complete gameweek with comprehensive predictions...</span>
        </div>
        <p class="text-sm text-gray-500 mt-2">This may take 2-5 minutes as we generate detailed analysis for 8-10 matches with complete betting predictions</p>
        <div class="mt-4 text-xs text-gray-400">
            <p>✨ Generating: Match details • Team analysis • Betting odds • Confidence scores • Key insights</p>
        </div>
    </div>

    <!-- Matches Grid -->
    <div class="grid gap-6 lg:grid-cols-2" wire:loading.remove wire:target="analyzeNextGameweek">
        @forelse($matches as $match)
            <livewire:match-card :match="$match" :key="'match-'.$match->id" />
        @empty
            <div class="col-span-2 text-center py-16">
                <div class="max-w-md mx-auto">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No gameweek analyzed yet</h3>
                    <p class="text-gray-600 mb-6">Click "Analyze Next Gameweek" to get the upcoming Süper Lig matches with complete AI predictions in one go.</p>
                    <button 
                        wire:click="analyzeNextGameweek" 
                        class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                    >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Analyze Next Gameweek
                    </button>
                    <div class="mt-4 text-sm text-gray-500">
                        <p>✨ One AI call gets you:</p>
                        <div class="mt-2 space-y-1">
                            <p>• 6-8 upcoming matches with real teams</p>
                            <p>• Complete betting predictions for each</p>
                            <p>• Detailed analysis and confidence scores</p>
                        </div>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Match Summary -->
    @if($matches->count() > 0)
        <div class="mt-12 text-center">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Gameweek Summary</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
                    <div class="p-4 bg-blue-50 rounded-lg">
                        <div class="text-2xl font-bold text-blue-600">{{ $matches->count() }}</div>
                        <div class="text-sm text-gray-600">Matches Analyzed</div>
                    </div>
                    <div class="p-4 bg-green-50 rounded-lg">
                        <div class="text-2xl font-bold text-green-600">{{ $matches->where('prediction_generated', true)->count() }}</div>
                        <div class="text-sm text-gray-600">With Predictions</div>
                    </div>
                    <div class="p-4 bg-purple-50 rounded-lg">
                        <div class="text-2xl font-bold text-purple-600">1</div>
                        <div class="text-sm text-gray-600">AI Call Used</div>
                    </div>
                </div>
                <div class="mt-4 flex justify-center">
                    <button 
                        wire:click="$refresh" 
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Refresh View
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Notification Toast Area -->
    <div id="notification-area" class="fixed top-4 right-4 z-50"></div>
</div>

@script
<script>
    // Notification system
    $wire.on('notify', (event) => {
        showNotification(event.message, event.type || 'success');
    });

    $wire.on('gameweek-analyzed', (event) => {
        showNotification(`Gameweek ${event.gameweek} analyzed with ${event.count} matches and predictions!`, 'success');
    });

    function showNotification(message, type = 'success') {
        const notificationArea = document.getElementById('notification-area');
        const notification = document.createElement('div');
        
        const bgColor = type === 'error' ? 'bg-red-500' : 'bg-green-500';
        
        notification.className = `${bgColor} text-white px-6 py-4 rounded-md shadow-lg mb-4 transform transition-all duration-300 translate-x-full`;
        notification.innerHTML = `
            <div class="flex items-center justify-between">
                <span>${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        `;
        
        notificationArea.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 300);
        }, 5000);
    }
</script>
@endscript
