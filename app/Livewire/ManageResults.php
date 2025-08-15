<?php

namespace App\Livewire;

use App\Models\Prediction;
use App\Models\SoccerMatch;
use App\Models\League;
use Livewire\Component;
use Livewire\WithPagination;

class ManageResults extends Component
{
    use WithPagination;

    public $selectedLeagueId = null;
    public $leagues;
    public $searchTerm = '';
    public $statusFilter = 'all'; // all, pending, verified
    public $editingPrediction = null;
    public $actualHomeGoals = '';
    public $actualAwayGoals = '';
    public $showAccuracyDetails = null;

    public function mount()
    {
        $this->leagues = League::all();
        $this->selectedLeagueId = $this->leagues->first()?->id;
    }

    public function selectLeague($leagueId)
    {
        $this->selectedLeagueId = $leagueId;
        $this->resetPage();
    }

    public function setStatusFilter($status)
    {
        $this->statusFilter = $status;
        $this->resetPage();
    }

    public function editResult($predictionId)
    {
        $prediction = Prediction::with(['match.homeTeam', 'match.awayTeam'])->find($predictionId);
        $this->editingPrediction = $prediction;
        $this->actualHomeGoals = $prediction->actual_home_goals ?? '';
        $this->actualAwayGoals = $prediction->actual_away_goals ?? '';
    }

    public function cancelEdit()
    {
        $this->editingPrediction = null;
        $this->actualHomeGoals = '';
        $this->actualAwayGoals = '';
    }

    public function saveResult()
    {
        $this->validate([
            'actualHomeGoals' => 'required|integer|min:0',
            'actualAwayGoals' => 'required|integer|min:0',
        ]);

        $prediction = $this->editingPrediction;
        
        // Determine actual result
        $actualResult = 'draw';
        if ($this->actualHomeGoals > $this->actualAwayGoals) {
            $actualResult = 'home';
        } elseif ($this->actualAwayGoals > $this->actualHomeGoals) {
            $actualResult = 'away';
        }

        // Check if prediction was correct
        $predictionCorrect = $prediction->predicted_winner === $actualResult;

        // Calculate accuracy score based on multiple factors
        $accuracyScore = $this->calculateAccuracyScore($prediction, $actualResult, $this->actualHomeGoals, $this->actualAwayGoals);

        // Update prediction
        $prediction->update([
            'prediction_verified' => true,
            'actual_result' => $actualResult,
            'actual_home_goals' => $this->actualHomeGoals,
            'actual_away_goals' => $this->actualAwayGoals,
            'prediction_correct' => $predictionCorrect,
            'result_verified_at' => now(),
        ]);

        $this->cancelEdit();
        $this->dispatch('notify', [
            'message' => 'Result saved successfully!',
            'type' => 'success'
        ]);
    }

    private function calculateAccuracyScore($prediction, $actualResult, $actualHomeGoals, $actualAwayGoals)
    {
        $score = 0;
        $totalChecks = 0;
        $accuracyDetails = [];

        // 1. Match result accuracy (60% weight)
        $matchResultCorrect = $prediction->predicted_winner === $actualResult;
        if ($matchResultCorrect) {
            $score += 0.6;
        }
        $accuracyDetails['match_result'] = [
            'correct' => $matchResultCorrect,
            'predicted' => $prediction->predicted_winner,
            'actual' => $actualResult,
            'weight' => 0.6
        ];
        $totalChecks++;

        // 2. Over/Under accuracy (20% weight)
        $predictedOver25 = $prediction->over_25_probability > 0.5;
        $actualOver25 = ($actualHomeGoals + $actualAwayGoals) > 2.5;
        $overUnderCorrect = $predictedOver25 === $actualOver25;
        if ($overUnderCorrect) {
            $score += 0.2;
        }
        $accuracyDetails['over_under'] = [
            'correct' => $overUnderCorrect,
            'predicted' => $predictedOver25 ? 'Over 2.5' : 'Under 2.5',
            'actual' => $actualOver25 ? 'Over 2.5' : 'Under 2.5',
            'weight' => 0.2
        ];
        $totalChecks++;

        // 3. Both teams to score accuracy (20% weight)
        $predictedBtts = $prediction->btts_probability > 0.5;
        $actualBtts = $actualHomeGoals > 0 && $actualAwayGoals > 0;
        $bttsCorrect = $predictedBtts === $actualBtts;
        if ($bttsCorrect) {
            $score += 0.2;
        }
        $accuracyDetails['both_teams_to_score'] = [
            'correct' => $bttsCorrect,
            'predicted' => $predictedBtts ? 'Yes' : 'No',
            'actual' => $actualBtts ? 'Yes' : 'No',
            'weight' => 0.2
        ];
        $totalChecks++;



        // Store accuracy details in the prediction
        $prediction->update([
            'accuracy_details' => $accuracyDetails
        ]);

        return round($score, 4);
    }

    public function getPredictions()
    {
        $query = Prediction::with(['match.homeTeam', 'match.awayTeam', 'match.league'])
            ->whereHas('match', function ($q) {
                if ($this->selectedLeagueId) {
                    $q->where('league_id', $this->selectedLeagueId);
                }
                if ($this->searchTerm) {
                    $q->where(function ($subQ) {
                        $subQ->whereHas('homeTeam', function ($teamQ) {
                            $teamQ->where('name', 'like', '%' . $this->searchTerm . '%');
                        })->orWhereHas('awayTeam', function ($teamQ) {
                            $teamQ->where('name', 'like', '%' . $this->searchTerm . '%');
                        });
                    });
                }
            });

        // Apply status filter
        if ($this->statusFilter === 'pending') {
            $query->where('prediction_verified', false);
        } elseif ($this->statusFilter === 'verified') {
            $query->where('prediction_verified', true);
        }

        return $query->orderBy('predicted_at', 'desc')
                    ->paginate(15);
    }



    public function render()
    {
        $predictions = $this->getPredictions();

        return view('livewire.manage-results', compact('predictions'))
            ->layout('layouts.app');
    }
} 