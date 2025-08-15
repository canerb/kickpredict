<?php

namespace App\Livewire;

use App\Models\League;
use App\Models\SoccerMatch;
use App\Services\SoccerAnalysisService;
use Livewire\Component;

class MatchesByLeague extends Component
{
    public $selectedLeagueId = null;
    public $leagues;
    public $matches;
    public $isLoadingMatches = false;

    public function mount()
    {
        $this->leagues = $this->getOrCreateLeagues();
        $this->selectedLeagueId = $this->leagues->first()?->id;
        $this->loadMatches();
    }

    public function selectLeague($leagueId)
    {
        $this->selectedLeagueId = $leagueId;
        $this->loadMatches();
    }

    public function loadMatches()
    {
        if ($this->selectedLeagueId) {
            $this->matches = SoccerMatch::with(['homeTeam', 'awayTeam', 'prediction'])
                ->where('league_id', $this->selectedLeagueId)
                ->whereHas('prediction') // Only show matches that have predictions
                ->orderBy('match_date')
                ->take(10)
                ->get();
        } else {
            $this->matches = collect();
        }
    }

    public function analyzeNextGameweek()
    {
        if (!$this->selectedLeagueId) return;

        $this->isLoadingMatches = true;
        
        try {
            // Add debugging
            \Log::info('analyzeNextGameweek method called', ['league_id' => $this->selectedLeagueId]);
            
            $league = League::find($this->selectedLeagueId);
            $analysisService = app(SoccerAnalysisService::class);
            
            $result = $analysisService->analyzeNextGameweek($league);
            $this->loadMatches();
            
            $this->dispatch('gameweek-analyzed', [
                'gameweek' => $result['current_gameday'],
                'count' => $result['matches_count']
            ]);
            $this->dispatch('notify', [
                'message' => "Analyzed gameweek {$result['current_gameday']} with {$result['matches_count']} matches and predictions for {$league->name}!"
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error in analyzeNextGameweek', ['error' => $e->getMessage()]);
            
            // Check if it's a timeout error
            if (str_contains($e->getMessage(), 'timeout') || str_contains($e->getMessage(), 'timed out')) {
                $this->dispatch('notify', [
                    'message' => 'AI analysis is taking longer than expected. This is normal for complex predictions. Please try again in a moment.', 
                    'type' => 'error'
                ]);
            } else {
                $this->dispatch('notify', [
                    'message' => 'Error analyzing gameweek: ' . $e->getMessage(), 
                    'type' => 'error'
                ]);
            }
        } finally {
            $this->isLoadingMatches = false;
        }
    }

    private function getOrCreateLeagues()
    {
        // Only Süper Lig for now
        $leagueData = [
            'name' => 'Süper Lig',
            'country' => 'Turkey',
            'country_code' => 'TR',
            'is_active' => true
        ];

        $league = League::firstOrCreate(
            ['name' => $leagueData['name']],
            $leagueData
        );

        return collect([$league]);
    }

    public function render()
    {
        return view('livewire.matches-by-league');
    }
}
