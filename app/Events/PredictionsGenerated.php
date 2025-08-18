<?php

namespace App\Events;

use App\Models\League;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PredictionsGenerated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $league;
    public $user;
    public $matchesCount;
    public $success;
    public $message;

    /**
     * Create a new event instance.
     */
    public function __construct(League $league, User $user, int $matchesCount, bool $success, string $message)
    {
        $this->league = $league;
        $this->user = $user;
        $this->matchesCount = $matchesCount;
        $this->success = $success;
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('predictions.' . $this->user->id),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'predictions.generated';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'league_id' => $this->league->id,
            'league_name' => $this->league->name,
            'matches_count' => $this->matchesCount,
            'success' => $this->success,
            'message' => $this->message,
            'timestamp' => now()->toISOString(),
        ];
    }
}
