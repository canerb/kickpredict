<?php

namespace Database\Seeders;

use App\Models\League;
use App\Models\Team;
use App\Models\SoccerMatch;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SoccerMatchSeeder extends Seeder
{
    public function run(): void
    {
        $leagues = League::with('teams')->get();

        foreach ($leagues as $league) {
            $teams = $league->teams->toArray();
            $teamCount = count($teams);

            // Generate 5 upcoming matches per league
            for ($i = 0; $i < 5; $i++) {
                // Randomly select two different teams
                $homeTeamIndex = rand(0, $teamCount - 1);
                do {
                    $awayTeamIndex = rand(0, $teamCount - 1);
                } while ($homeTeamIndex === $awayTeamIndex);

                // Generate match date between tomorrow and next 30 days
                $matchDate = Carbon::now()->addDays(rand(1, 30))->addHours(rand(12, 20));

                SoccerMatch::create([
                    'league_id' => $league->id,
                    'home_team_id' => $teams[$homeTeamIndex]['id'],
                    'away_team_id' => $teams[$awayTeamIndex]['id'],
                    'match_date' => $matchDate,
                    'venue' => $this->generateVenueName($teams[$homeTeamIndex]['name']),
                    'status' => 'upcoming',
                    'prediction_generated' => false,
                ]);
            }
        }
    }

    private function generateVenueName(string $teamName): string
    {
        $venues = [
            'Stadium',
            'Arena',
            'Park',
            'Ground',
            'Stadion',
        ];

        return $teamName . ' ' . $venues[array_rand($venues)];
    }
}
