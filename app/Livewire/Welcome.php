<?php

namespace App\Livewire;

use App\Models\League;
use App\Models\SoccerMatch;
use App\Services\SoccerAnalysisService;
use Livewire\Component;
use Livewire\WithPagination;

class Welcome extends Component
{
    use WithPagination;

    public $leagues;
    public $selectedLeagueId;
    public $selectedGameweek = null;
    public $availableGameweeks = [];
    public $isLoading = false;
    public $expandedMatches = [];

    public function mount()
    {
        $this->leagues = League::all();
        $this->selectedLeagueId = $this->leagues->first()?->id;
        $this->loadAvailableGameweeks();
    }

    public function selectLeague($leagueId)
    {
        $this->selectedLeagueId = $leagueId;
        $this->selectedGameweek = null; // Reset gameweek when changing leagues
        $this->loadAvailableGameweeks();
        $this->resetPage(); // Reset pagination when changing leagues
    }

    public function updatedSelectedLeagueId()
    {
        \Log::info('League changed in Welcome component', [
            'new_league_id' => $this->selectedLeagueId
        ]);
        
        $this->expandedMatches = []; // Reset expanded state when switching leagues
        $this->selectedGameweek = null; // Reset gameweek when changing leagues
        $this->loadAvailableGameweeks();
        $this->resetPage(); // Reset pagination when changing leagues
        
        \Log::info('League changed - gameweeks reloaded', [
            'league_id' => $this->selectedLeagueId,
            'available_gameweeks' => count($this->availableGameweeks)
        ]);
    }

    public function updatedSelectedGameweek()
    {
        $this->resetPage(); // Reset pagination when changing gameweeks
    }

    public function loadAvailableGameweeks()
    {
        if ($this->selectedLeagueId) {
            $this->availableGameweeks = SoccerMatch::where('league_id', $this->selectedLeagueId)
                ->whereNotNull('gameweek')
                ->distinct()
                ->orderBy('gameweek')
                ->pluck('gameweek', 'gameweek')
                ->toArray();
                
            // Auto-select the first available gameweek if none is selected
            if (!$this->selectedGameweek && !empty($this->availableGameweeks)) {
                $this->selectedGameweek = array_key_first($this->availableGameweeks);
            }
        } else {
            $this->availableGameweeks = [];
            $this->selectedGameweek = null;
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
            $this->loadAvailableGameweeks(); // Refresh available gameweeks after generating predictions
            
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
        $matches = collect();
        
        if ($this->selectedLeagueId && $this->selectedGameweek) {
            $matches = SoccerMatch::with(['homeTeam', 'awayTeam', 'league', 'prediction'])
                ->where('league_id', $this->selectedLeagueId)
                ->where('gameweek', $this->selectedGameweek)
                ->whereHas('prediction')
                ->orderBy('match_date')
                ->paginate(10);
        }

        return view('livewire.welcome', compact('matches'))
            ->layout('layouts.app');
    }
} 