<?php

namespace App\Livewire;

use App\Models\League;
use Livewire\Component;

class LeagueSelector extends Component
{
    public $leagues;
    public $selectedLeagueId;

    public function mount($selectedLeagueId = null)
    {
        $this->leagues = League::where('is_active', true)->get();
        $this->selectedLeagueId = $selectedLeagueId ?? $this->leagues->first()?->id;
    }

    public function selectLeague($leagueId)
    {
        $this->selectedLeagueId = $leagueId;
        $this->dispatch('league-selected', ['leagueId' => $leagueId]);
    }

    public function render()
    {
        return view('livewire.league-selector');
    }
}
