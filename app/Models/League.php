<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class League extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'country',
        'country_code',
        'external_api_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    public function matches(): HasMany
    {
        return $this->hasMany(SoccerMatch::class);
    }

    public function upcomingMatches(): HasMany
    {
        return $this->hasMany(SoccerMatch::class)
            ->where('status', 'upcoming')
            ->orderBy('match_date');
    }

    public function getFlagUrlAttribute(): string
    {
        return "https://flagcdn.com/w20/" . strtolower($this->country_code) . ".png";
    }
}
