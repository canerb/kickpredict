<?php

namespace App\Livewire;

use App\Models\League;
use App\Models\SoccerMatch;
use App\Models\Team;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class ManageMatches extends Component
{
    use WithPagination;

    public $selectedLeague;
    public $homeTeamId;
    public $awayTeamId;
    public $matchDate;
    public $matchTime;
    public $showAddForm = false;

    protected $rules = [
        'selectedLeague' => 'required|exists:leagues,id',
        'homeTeamId' => 'required|exists:teams,id',
        'awayTeamId' => 'required|exists:teams,id|different:homeTeamId',
        'matchDate' => 'required|date|after:today',
        'matchTime' => 'required|date_format:H:i',
    ];

    protected $messages = [
        'awayTeamId.different' => 'Home and away teams must be different.',
        'matchDate.after' => 'Match date must be in the future.',
    ];

    public function mount()
    {
        $this->selectedLeague = League::first()?->id;
        $this->matchDate = now()->addDay()->format('Y-m-d');
        $this->matchTime = '20:00';
    }

    public function render()
    {
        $leagues = League::all();
        $teams = $this->selectedLeague ? Team::where('league_id', $this->selectedLeague)->get() : collect();
        
        $matches = SoccerMatch::with(['homeTeam', 'awayTeam', 'league'])
            ->when($this->selectedLeague, function ($query) {
                $query->where('league_id', $this->selectedLeague);
            })
            ->orderBy('match_date')
            ->paginate(10);

        return view('livewire.manage-matches', compact('leagues', 'teams', 'matches'))
            ->layout('layouts.app');
    }

    public function updatedSelectedLeague()
    {
        $this->homeTeamId = null;
        $this->awayTeamId = null;
    }

    public function addMatch()
    {
        $this->validate();

        $matchDateTime = Carbon::parse($this->matchDate . ' ' . $this->matchTime);

        SoccerMatch::create([
            'league_id' => $this->selectedLeague,
            'home_team_id' => $this->homeTeamId,
            'away_team_id' => $this->awayTeamId,
            'match_date' => $matchDateTime,
            'venue' => null, // Will be filled by AI prediction
            'status' => 'upcoming',
            'prediction_generated' => false,
        ]);

        $this->resetForm();
        $this->dispatch('notify', [
            'message' => 'Match added successfully!'
        ]);
    }

    public function deleteMatch($matchId)
    {
        $match = SoccerMatch::findOrFail($matchId);
        $match->delete();

        $this->dispatch('notify', [
            'message' => 'Match deleted successfully!'
        ]);
    }

    public function resetForm()
    {
        $this->homeTeamId = null;
        $this->awayTeamId = null;
        $this->matchDate = now()->addDay()->format('Y-m-d');
        $this->matchTime = '20:00';
        $this->showAddForm = false;
    }

    public function toggleAddForm()
    {
        $this->showAddForm = !$this->showAddForm;
    }

    public function refreshMatches()
    {
        $this->render();
    }
} 