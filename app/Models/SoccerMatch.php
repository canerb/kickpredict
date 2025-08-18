<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SoccerMatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'league_id',
        'gameweek',
        'gameweek_label',
        'home_team_id',
        'away_team_id',
        'match_date',
        'venue',
        'home_goals',
        'away_goals',
        'status',
        'external_api_id',
        'prediction_generated',
    ];

    protected $casts = [
        'match_date' => 'datetime',
        'prediction_generated' => 'boolean',
    ];

    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    public function homeTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    public function awayTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'away_team_id');
    }

    public function prediction(): HasOne
    {
        return $this->hasOne(Prediction::class, 'match_id');
    }

    public function getIsUpcomingAttribute(): bool
    {
        return $this->status === 'upcoming' && $this->match_date->isFuture();
    }

    public function getFormattedDateAttribute(): string
    {
        return $this->match_date->format('M d, Y H:i');
    }
}
