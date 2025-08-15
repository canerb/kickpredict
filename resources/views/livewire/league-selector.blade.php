<div>
    @if($leagues->count() > 1)
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 overflow-x-auto" aria-label="Leagues">
                @foreach($leagues as $league)
                    <button 
                        wire:click="selectLeague({{ $league->id }})"
                        class="flex items-center space-x-2 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200
                            {{ $selectedLeagueId == $league->id 
                                ? 'border-blue-500 text-blue-600' 
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                    >
                        <img 
                            src="{{ $league->flag_url }}" 
                            alt="{{ $league->country }}" 
                            class="w-6 h-4 rounded shadow-sm"
                        >
                        <span class="hidden sm:inline">{{ $league->name }}</span>
                        <span class="sm:hidden">{{ $league->country_code }}</span>
                    </button>
                @endforeach
            </nav>
        </div>

        <style>
            /* Custom scrollbar for horizontal scroll on mobile */
            nav::-webkit-scrollbar {
                height: 4px;
            }
            
            nav::-webkit-scrollbar-track {
                background: #f1f5f9;
            }
            
            nav::-webkit-scrollbar-thumb {
                background: #cbd5e1;
                border-radius: 2px;
            }
            
            nav::-webkit-scrollbar-thumb:hover {
                background: #94a3b8;
            }
        </style>
    @endif
</div>
