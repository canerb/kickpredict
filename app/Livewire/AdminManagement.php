<?php

namespace App\Livewire;

use App\Models\League;
use App\Models\Team;
use App\Models\SoccerMatch;
use Livewire\Component;
use Livewire\WithPagination;

class AdminManagement extends Component
{
    use WithPagination;

    public $activeTab = 'leagues';
    
    // League properties
    public $leagueName = '';
    public $leagueCountry = '';
    public $leagueCountryCode = '';
    public $leagueApiId = '';
    public $editingLeague = null;
    
    // Team properties
    public $teamName = '';
    public $teamLeagueId = '';
    public $teamCountryCode = '';
    public $teamApiId = '';
    public $editingTeam = null;
    
    // Match properties
    public $matchLeagueId = '';
    public $matchHomeTeamId = '';
    public $matchAwayTeamId = '';
    public $matchDate = '';
    public $matchVenue = '';
    public $editingMatch = null;

    public function mount()
    {
        // Set default match date to today
        $this->matchDate = now()->format('Y-m-d\TH:i');
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    // League methods
    public function saveLeague()
    {
        $this->validate([
            'leagueName' => 'required|string|max:255',
            'leagueCountry' => 'required|string|max:255',
            'leagueCountryCode' => 'required|string|max:2',
        ]);

        if ($this->editingLeague) {
            $this->editingLeague->update([
                'name' => $this->leagueName,
                'country' => $this->leagueCountry,
                'country_code' => $this->leagueCountryCode,
                'external_api_id' => $this->leagueApiId,
            ]);
            session()->flash('success', 'League updated successfully!');
        } else {
            League::create([
                'name' => $this->leagueName,
                'country' => $this->leagueCountry,
                'country_code' => $this->leagueCountryCode,
                'external_api_id' => $this->leagueApiId,
            ]);
            session()->flash('success', 'League created successfully!');
        }

        $this->resetLeagueForm();
    }

    public function editLeague(League $league)
    {
        $this->editingLeague = $league;
        $this->leagueName = $league->name;
        $this->leagueCountry = $league->country;
        $this->leagueCountryCode = $league->country_code;
        $this->leagueApiId = $league->external_api_id;
    }

    public function deleteLeague(League $league)
    {
        $league->delete();
        session()->flash('success', 'League deleted successfully!');
    }

    public function resetLeagueForm()
    {
        $this->leagueName = '';
        $this->leagueCountry = '';
        $this->leagueCountryCode = '';
        $this->leagueApiId = '';
        $this->editingLeague = null;
    }

    // Team methods
    public function saveTeam()
    {
        $this->validate([
            'teamName' => 'required|string|max:255',
            'teamLeagueId' => 'required|exists:leagues,id',
            'teamCountryCode' => 'required|string|max:2',
        ]);

        if ($this->editingTeam) {
            $this->editingTeam->update([
                'name' => $this->teamName,
                'league_id' => $this->teamLeagueId,
                'country_code' => $this->teamCountryCode,
                'external_api_id' => $this->teamApiId,
            ]);
            session()->flash('success', 'Team updated successfully!');
        } else {
            Team::create([
                'name' => $this->teamName,
                'league_id' => $this->teamLeagueId,
                'country_code' => $this->teamCountryCode,
                'external_api_id' => $this->teamApiId,
            ]);
            session()->flash('success', 'Team created successfully!');
        }

        $this->resetTeamForm();
    }

    public function editTeam(Team $team)
    {
        $this->editingTeam = $team;
        $this->teamName = $team->name;
        $this->teamLeagueId = $team->league_id;
        $this->teamCountryCode = $team->country_code;
        $this->teamApiId = $team->external_api_id;
    }

    public function deleteTeam(Team $team)
    {
        $team->delete();
        session()->flash('success', 'Team deleted successfully!');
    }

    public function resetTeamForm()
    {
        $this->teamName = '';
        $this->teamLeagueId = '';
        $this->teamCountryCode = '';
        $this->teamApiId = '';
        $this->editingTeam = null;
    }

    // Match methods
    public function saveMatch()
    {
        $this->validate([
            'matchLeagueId' => 'required|exists:leagues,id',
            'matchHomeTeamId' => 'required|exists:teams,id',
            'matchAwayTeamId' => 'required|exists:teams,id|different:matchHomeTeamId',
            'matchDate' => 'required|date',
        ]);

        if ($this->editingMatch) {
            $this->editingMatch->update([
                'league_id' => $this->matchLeagueId,
                'home_team_id' => $this->matchHomeTeamId,
                'away_team_id' => $this->matchAwayTeamId,
                'match_date' => $this->matchDate,
                'venue' => $this->matchVenue,
            ]);
            session()->flash('success', 'Match updated successfully!');
        } else {
            SoccerMatch::create([
                'league_id' => $this->matchLeagueId,
                'home_team_id' => $this->matchHomeTeamId,
                'away_team_id' => $this->matchAwayTeamId,
                'match_date' => $this->matchDate,
                'venue' => $this->matchVenue,
            ]);
            session()->flash('success', 'Match created successfully!');
        }

        $this->resetMatchForm();
    }

    public function editMatch(SoccerMatch $match)
    {
        $this->editingMatch = $match;
        $this->matchLeagueId = $match->league_id;
        $this->matchHomeTeamId = $match->home_team_id;
        $this->matchAwayTeamId = $match->away_team_id;
        $this->matchDate = $match->match_date->format('Y-m-d\TH:i');
        $this->matchVenue = $match->venue;
    }

    public function deleteMatch(SoccerMatch $match)
    {
        $match->delete();
        session()->flash('success', 'Match deleted successfully!');
    }

    public function resetMatchForm()
    {
        $this->matchLeagueId = '';
        $this->matchHomeTeamId = '';
        $this->matchAwayTeamId = '';
        $this->matchDate = now()->format('Y-m-d\TH:i');
        $this->matchVenue = '';
        $this->editingMatch = null;
    }

    public function render()
    {
        return view('livewire.admin-management', [
            'leagues' => League::paginate(10, ['*'], 'leagues-page'),
            'teams' => Team::with('league')->paginate(10, ['*'], 'teams-page'),
            'matches' => SoccerMatch::with(['league', 'homeTeam', 'awayTeam'])->orderBy('match_date', 'desc')->paginate(10, ['*'], 'matches-page'),
            'allLeagues' => League::all(),
            'allTeams' => Team::all(),
        ])->layout('layouts.app');
    }
}
