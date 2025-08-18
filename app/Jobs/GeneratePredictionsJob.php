<?php

namespace App\Jobs;

use App\Models\League;
use App\Models\User;
use App\Models\SoccerMatch; // Added missing import
use App\Services\SoccerAnalysisService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GeneratePredictionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600; // 1 hour timeout for multiple matches
    public $tries = 3; // Retry 3 times on failure

    protected $leagueId;
    protected $userId;

    /**
     * Create a new job instance.
     */
    public function __construct(?int $leagueId, int $userId)
    {
        $this->leagueId = $leagueId;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Starting GeneratePredictionsJob', [
            'league_id' => $this->leagueId,
            'user_id' => $this->userId,
            'scope' => $this->leagueId ? 'single_league' : 'all_leagues'
        ]);

        try {
            $user = User::find($this->userId);

            if (!$user || !$user->is_admin) {
                throw new \Exception("Unauthorized user or user not found: {$this->userId}");
            }

            // Get matches that don't have predictions yet
            $query = SoccerMatch::whereDoesntHave('prediction');
            
            if ($this->leagueId) {
                // Process specific league
                $league = League::find($this->leagueId);
                if (!$league) {
                    throw new \Exception("League not found with ID: {$this->leagueId}");
                }
                $query->where('league_id', $this->leagueId);
                $scopeMessage = "for {$league->name}";
            } else {
                // Process ALL leagues
                $scopeMessage = "across all leagues";
            }
            
            $matches = $query->with(['homeTeam', 'awayTeam', 'league'])->get();
            
            if ($matches->isEmpty()) {
                throw new \Exception('No matches found without predictions ' . $scopeMessage . '. Please add matches first.');
            }
            
            Log::info('Found matches without predictions', [
                'matches_count' => $matches->count(),
                'scope' => $scopeMessage
            ]);
            
            // Group matches by league for better organization
            $matchesByLeague = $matches->groupBy('league_id');
            $totalProcessed = 0;
            
            foreach ($matchesByLeague as $leagueId => $leagueMatches) {
                $league = $leagueMatches->first()->league;
                
                Log::info('Processing league', [
                    'league_name' => $league->name,
                    'matches_count' => $leagueMatches->count()
                ]);
                
                foreach ($leagueMatches as $index => $match) {
                    Log::info('Generating prediction for match', [
                        'match' => ($index + 1) . '/' . $leagueMatches->count(),
                        'league' => $league->name,
                        'teams' => $match->homeTeam->name . ' vs ' . $match->awayTeam->name
                    ]);
                    
                    $matchData = [
                        'home_team' => ['name' => $match->homeTeam->name, 'city' => $match->homeTeam->city],
                        'away_team' => ['name' => $match->awayTeam->name, 'city' => $match->awayTeam->city],
                        'match_date' => $match->match_date,
                        'venue' => $match->venue ?? 'TBD'
                    ];
                    
                    $analysisService = app(SoccerAnalysisService::class);
                    $prediction = $analysisService->generateMatchPrediction($matchData, $league);
                    
                    // Store prediction
                    $analysisService->storePrediction($match, $prediction);
                    $totalProcessed++;
                }
            }

            Log::info('GeneratePredictionsJob completed successfully', [
                'league_id' => $this->leagueId,
                'total_matches_processed' => $totalProcessed,
                'leagues_processed' => $matchesByLeague->count(),
                'user_id' => $this->userId
            ]);

        } catch (\Exception $e) {
            Log::error('GeneratePredictionsJob failed', [
                'league_id' => $this->leagueId,
                'user_id' => $this->userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e; // Re-throw to mark job as failed
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('GeneratePredictionsJob failed completely', [
            'league_id' => $this->leagueId,
            'user_id' => $this->userId,
            'error' => $exception->getMessage()
        ]);
    }
}
