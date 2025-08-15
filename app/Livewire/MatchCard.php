<?php

namespace App\Livewire;

use App\Models\SoccerMatch;
use Livewire\Component;

class MatchCard extends Component
{
    public SoccerMatch $match;
    public $showPredictions = false;
    public $activeTab = 'basic';

    public function mount(SoccerMatch $match)
    {
        $this->match = $match->load(['homeTeam', 'awayTeam', 'prediction']);
    }

    public function togglePredictions()
    {
        $this->showPredictions = !$this->showPredictions;
        // Reset to basic tab when opening predictions
        if ($this->showPredictions) {
            $this->activeTab = 'basic';
        }
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function generatePrediction()
    {
        if (!$this->match->prediction_generated) {
            $predictionService = app(\App\Services\PredictionService::class);
            $predictionService->generatePrediction($this->match);
            
            $this->match->update(['prediction_generated' => true]);
            $this->match->refresh();
            
            $this->dispatch('prediction-generated');
        }
    }

    public function getMatchResultColorClass($winner)
    {
        return match($winner) {
            'home' => 'text-green-600 font-semibold',
            'away' => 'text-red-600 font-semibold',
            'draw' => 'text-yellow-600 font-semibold',
            default => 'text-gray-600'
        };
    }

    public function getConfidenceColorClass($confidence)
    {
        return match($confidence) {
            'high' => 'bg-green-100 text-green-800',
            'medium' => 'bg-yellow-100 text-yellow-800',
            'low' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800'
        };
    }

    public function render()
    {
        return view('livewire.match-card');
    }
}
