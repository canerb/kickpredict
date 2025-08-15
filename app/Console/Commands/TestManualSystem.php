<?php

namespace App\Console\Commands;

use App\Models\League;
use App\Models\SoccerMatch;
use App\Models\Team;
use App\Services\SoccerAnalysisService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class TestManualSystem extends Command
{
    protected $signature = 'app:test-manual-system';
    protected $description = 'Test the manual match management system';

    public function handle()
    {
        $this->info('Testing Manual Match Management System...');

        // Get or create league
        $league = League::firstOrCreate(
            ['name' => 'Süper Lig'],
            [
                'name' => 'Süper Lig',
                'country' => 'Turkey',
                'country_code' => 'TR',
                'is_active' => true
            ]
        );

        // Create some test teams if they don't exist
        $teams = [
            ['name' => 'Galatasaray', 'city' => 'Istanbul'],
            ['name' => 'Fenerbahçe', 'city' => 'Istanbul'],
            ['name' => 'Beşiktaş', 'city' => 'Istanbul'],
            ['name' => 'Trabzonspor', 'city' => 'Trabzon'],
        ];

        foreach ($teams as $teamData) {
            Team::firstOrCreate(
                ['name' => $teamData['name'], 'league_id' => $league->id],
                [
                    'name' => $teamData['name'],
                    'city' => $teamData['city'],
                    'league_id' => $league->id,
                    'country_code' => 'TR',
                    'is_active' => true
                ]
            );
        }

        // Create a test match
        $homeTeam = Team::where('name', 'Galatasaray')->first();
        $awayTeam = Team::where('name', 'Fenerbahçe')->first();

        $match = SoccerMatch::create([
            'league_id' => $league->id,
            'home_team_id' => $homeTeam->id,
            'away_team_id' => $awayTeam->id,
            'match_date' => Carbon::now()->addDays(3)->setTime(20, 0),
            'venue' => null,
            'status' => 'upcoming',
            'prediction_generated' => false,
        ]);

        $this->info("Created test match: {$homeTeam->name} vs {$awayTeam->id}");

        // Test the prediction system
        $this->info('Testing prediction generation...');
        
        try {
            $analysisService = app(SoccerAnalysisService::class);
            $result = $analysisService->analyzeNextGameweek($league);
            
            $this->info("Success! Generated predictions for {$result['matches_count']} matches.");
            $this->info("Season: {$result['season']}, Gameday: {$result['current_gameday']}");
            
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
        }

        $this->info('Test completed!');
    }
} 