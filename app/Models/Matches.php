<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Matches extends Model
{
    protected $fillable = [
        'team_a',
        'team_b',
        'round',
        'tournament_id',
        'next_match_id',
        'score_a',
        'score_b',
        'winner_id',
    ];

    public function teamA()
    {
        return $this->belongsTo(Team::class, 'team_a');
    }

    public function teamB()
    {
        return $this->belongsTo(Team::class, 'team_b');
    }
}
