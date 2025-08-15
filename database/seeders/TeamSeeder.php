<?php

namespace Database\Seeders;

use App\Models\League;
use App\Models\Team;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    public function run(): void
    {
        $teamsData = [
            // Süper Lig Teams
            'Süper Lig' => [
                ['name' => 'Galatasaray', 'country_code' => 'TR'],
                ['name' => 'Fenerbahçe', 'country_code' => 'TR'],
                ['name' => 'Beşiktaş', 'country_code' => 'TR'],
                ['name' => 'Trabzonspor', 'country_code' => 'TR'],
                ['name' => 'Başakşehir', 'country_code' => 'TR'],
                ['name' => 'Antalyaspor', 'country_code' => 'TR'],
            ],
            
            // Bundesliga Teams
            'Bundesliga' => [
                ['name' => 'Bayern Munich', 'country_code' => 'DE'],
                ['name' => 'Borussia Dortmund', 'country_code' => 'DE'],
                ['name' => 'RB Leipzig', 'country_code' => 'DE'],
                ['name' => 'Bayer Leverkusen', 'country_code' => 'DE'],
                ['name' => 'Eintracht Frankfurt', 'country_code' => 'DE'],
                ['name' => 'Wolfsburg', 'country_code' => 'DE'],
            ],
            
            // Premier League Teams
            'Premier League' => [
                ['name' => 'Manchester City', 'country_code' => 'GB'],
                ['name' => 'Arsenal', 'country_code' => 'GB'],
                ['name' => 'Liverpool', 'country_code' => 'GB'],
                ['name' => 'Chelsea', 'country_code' => 'GB'],
                ['name' => 'Manchester United', 'country_code' => 'GB'],
                ['name' => 'Tottenham', 'country_code' => 'GB'],
            ],
            
            // La Liga Teams
            'La Liga' => [
                ['name' => 'Real Madrid', 'country_code' => 'ES'],
                ['name' => 'Barcelona', 'country_code' => 'ES'],
                ['name' => 'Atlético Madrid', 'country_code' => 'ES'],
                ['name' => 'Real Sociedad', 'country_code' => 'ES'],
                ['name' => 'Villarreal', 'country_code' => 'ES'],
                ['name' => 'Valencia', 'country_code' => 'ES'],
            ],
        ];

        foreach ($teamsData as $leagueName => $teams) {
            $league = League::where('name', $leagueName)->first();
            
            if ($league) {
                foreach ($teams as $teamData) {
                    Team::create([
                        'name' => $teamData['name'],
                        'league_id' => $league->id,
                        'country_code' => $teamData['country_code'],
                        'is_active' => true,
                    ]);
                }
            }
        }
    }
}
