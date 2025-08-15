<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'league_id',
        'country_code',
        'external_api_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    public function homeMatches(): HasMany
    {
        return $this->hasMany(SoccerMatch::class, 'home_team_id');
    }

    public function awayMatches(): HasMany
    {
        return $this->hasMany(SoccerMatch::class, 'away_team_id');
    }

    public function getFlagUrlAttribute(): string
    {
        return "https://flagcdn.com/w20/" . strtolower($this->country_code) . ".png";
    }
}
