<?php

namespace App\Jobs;

use App\Models\League;
use App\Models\User;
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

    public $timeout = 600; // 10 minutes timeout
    public $tries = 3; // Retry 3 times on failure

    protected $leagueId;
    protected $userId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $leagueId, int $userId)
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
            'user_id' => $this->userId
        ]);

        try {
            $league = League::find($this->leagueId);
            $user = User::find($this->userId);

            if (!$league) {
                throw new \Exception("League not found with ID: {$this->leagueId}");
            }

            if (!$user || !$user->is_admin) {
                throw new \Exception("Unauthorized user or user not found: {$this->userId}");
            }

            $analysisService = app(SoccerAnalysisService::class);
            $result = $analysisService->analyzeNextGameweek($league);

            Log::info('GeneratePredictionsJob completed successfully', [
                'league_id' => $this->leagueId,
                'league_name' => $league->name,
                'matches_count' => $result['matches_count'],
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
