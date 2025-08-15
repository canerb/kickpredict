<?php

namespace Database\Seeders;

use App\Models\League;
use Illuminate\Database\Seeder;

class LeagueSeeder extends Seeder
{
    public function run(): void
    {
        $leagues = [
            [
                'name' => 'Süper Lig',
                'country' => 'Turkey',
                'country_code' => 'TR',
                'is_active' => true,
            ],
            [
                'name' => 'Bundesliga',
                'country' => 'Germany', 
                'country_code' => 'DE',
                'is_active' => true,
            ],
            [
                'name' => 'Premier League',
                'country' => 'England',
                'country_code' => 'GB',
                'is_active' => true,
            ],
            [
                'name' => 'La Liga',
                'country' => 'Spain',
                'country_code' => 'ES',
                'is_active' => true,
            ],
        ];

        foreach ($leagues as $league) {
            League::create($league);
        }
    }
}
