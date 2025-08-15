<?php

namespace App\Livewire;

use App\Models\League;
use App\Models\SoccerMatch;
use App\Services\SoccerAnalysisService;
use Livewire\Component;

class Welcome extends Component
{
    public $leagues;
    public $selectedLeagueId;
    public $matches;
    public $isLoading = false;
    public $expandedMatches = [];

    public function mount()
    {
        $this->leagues = League::all();
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
            $this->matches = SoccerMatch::with(['homeTeam', 'awayTeam', 'league', 'prediction'])
                ->where('league_id', $this->selectedLeagueId)
                ->whereHas('prediction')
                ->orderBy('match_date')
                ->get();
        } else {
            $this->matches = collect();
        }
    }

    public function toggleMatch($matchId)
    {
        if (in_array($matchId, $this->expandedMatches)) {
            $this->expandedMatches = array_diff($this->expandedMatches, [$matchId]);
        } else {
            $this->expandedMatches[] = $matchId;
        }
    }

    public function isExpanded($matchId)
    {
        return in_array($matchId, $this->expandedMatches);
    }

    public function analyzeNextGameweek()
    {
        // Only allow admin users to generate predictions
        if (!auth()->check() || !auth()->user()->is_admin) {
            $this->dispatch('notify', [
                'message' => 'Access denied. Admin privileges required.',
                'type' => 'error'
            ]);
            return;
        }

        if (!$this->selectedLeagueId) return;

        $this->isLoading = true;
        
        try {
            $league = League::find($this->selectedLeagueId);
            $analysisService = app(SoccerAnalysisService::class);
            
            $result = $analysisService->analyzeNextGameweek($league);
            $this->loadMatches();
            
            $this->dispatch('notify', [
                'message' => "Generated predictions for {$result['matches_count']} matches!"
            ]);
            
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => 'Error: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        } finally {
            $this->isLoading = false;
        }
    }

    public function formatProbability($probability)
    {
        return round($probability * 100, 1) . '%';
    }

    public function getConfidenceColor($confidence)
    {
        return match($confidence) {
            'high' => 'text-green-600 bg-green-100',
            'medium' => 'text-yellow-600 bg-yellow-100',
            'low' => 'text-red-600 bg-red-100',
            default => 'text-gray-600 bg-gray-100'
        };
    }

    public function getRiskColor($risk)
    {
        return match($risk) {
            'safe' => 'text-green-600 bg-green-100',
            'moderate' => 'text-yellow-600 bg-yellow-100',
            'high_risk' => 'text-red-600 bg-red-100',
            default => 'text-gray-600 bg-gray-100'
        };
    }

    public function render()
    {
        return view('livewire.welcome')
            ->layout('layouts.app');
    }
} 