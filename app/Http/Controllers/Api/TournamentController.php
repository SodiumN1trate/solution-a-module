<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Matches;
use App\Models\Team;
use Illuminate\Http\Request;
use App\Models\Tournament;
class TournamentController extends Controller
{
    public function create(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:100',
            'max_teams' => ['required', 'numeric', function ($attribute, $value, $fail) {
                if ($value < 2 || ($value & ($value - 1)) !== 0) {
                    $fail("Invalid max_teams. Must be power of 2 (4, 8, 16, 32..)");
                }
            }],
        ]);

        $tournament = Tournament::create($validated);

        return response()->json([
            'id' => $tournament->id,
            'name' => $tournament->name,
            'max_teams' => intval($tournament->max_teams),
            'status' => 'created',
            'created_at' => $tournament->created_at,
        ], 201);
    }

    public function addTeam(Request $request, int $tournament)
    {
        $tournament = Tournament::find($tournament);

        if(!$tournament) {
            return response()->json([
                'error' => 'Tournament does not exist.',
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'required|max:40',
        ]);

        if($tournament->teams()->count() + 1 > $tournament->max_teams) {
            return response()->json([
                'error' => 'Cannot add more teams. Tournament is full.'
            ], 409);
        }

        $team = Team::create([
            'name' => $validated['name'],
            'tournament_id' => $tournament->id,
        ]);

        return response()->json([
            'id' => $team->id,
            'name' => $team->name,
            'tournament_id' => $team->tournament_id,
            'created_at' => $team->created_at,
        ]);
    }

    public function generateBracket($tournament)
    {
        $tournament = Tournament::find($tournament);

        if(!$tournament) {
            return response()->json([
                'error' => 'Tournament does not exist.',
            ], 404);
        }

        if($tournament->matches()->count() > 0) {
            return response()->json([
                'error' => 'Brackets already have been generated.'
            ], 409);
        }

        if($tournament->teams()->count() <> $tournament->max_teams) {
            return response()->json([
                'error' => 'Not enough teams to generate bracket.'
            ], 409);
        }

        $shuffledTeams = $tournament->teams()->get()->shuffle();


        for ($i = 0; $i < $tournament->teams()->count(); $i = $i + 2) {
            Matches::create([
                'team_a' => $shuffledTeams[$i]->id,
                'team_b' => $shuffledTeams[$i + 1]->id,
                'round' => 1,
                'tournament_id' => $tournament->id,
            ]);
        }

        $matches = [];

        foreach ($tournament->matches()->get() as $match) {
            $matches[] = [
                'match_id' => $match->id,
                'team_a' => $match->teamA->name,
                'team_b' => $match->teamB->name,
                'round' => 1,
            ];
        }
        return response()->json([
            'matches' => $matches,
        ]);
    }

    public function setMatchScore(Request $request, int $match)
    {
        $match = Matches::find($match);

        if(!$match) {
            return response()->json([
                'error' => 'Match does not exist.',
            ], 404);
        }

        $tournament = Tournament::find($match->tournament_id);

        $validated = $request->validate([
            'score_a' => 'required|integer',
            'score_b' => 'required|integer',
        ]);


        if(!isset($match->team_a) || !isset($match->team_b)) {
            return response()->json([
                'error' => 'One or more teams have not been set.',
            ], 400);
        }

        if($validated['score_a'] == $validated['score_b']) {
            return response()->json([
                'error' => 'Match cannot end in a tie.',
            ], 400);
        }

        if(isset(Matches::find($match->next_match_id)->winner_id)) {
            return response()->json([
                'error' => 'The winner of the next match has already been determined.',
            ], 400);
        }

        $winner = null;
        if($validated['score_a'] > $validated['score_b']) {
            $winner = $match->team_a;
        } else {
            $winner = $match->team_b;
        }

        if(isset($match->next_match_id)) {
            $newMatch = Matches::find($match->next_match_id);
            if ($newMatch->team_a === $match->winner_id) {
                $newMatch->team_a = $winner;
            } else {
                $newMatch->team_b = $winner;
            }

            $newMatch->save();

            $match->score_a = $validated['score_a'];
            $match->score_b = $validated['score_b'];
            $match->winner_id = $winner;
            $match->save();

            return response()->json([
                'match_id' => $match->id,
                'winner' => Team::find($winner)->name,
                'next_match' => $match->next_match_id,
            ]);
        }

        // Iegūt pretējo komandu/matchu
        $matches = $tournament->matches()->where('round', $match->round)->get();

        for($i = 0; $i < count($matches); $i = $i + 2) {
            $firstBracket = $matches[$i];

            if(!isset($matches[$i + 1])) {
                $match->score_a = $validated['score_a'];
                $match->score_b = $validated['score_b'];
                $match->winner_id = $winner;
                $match->save();
                return response()->json([
                    'match_id' => $match->id,
                    'winner' => Team::find($winner)->name,
                    'next_match' => null,
                ]);
            } else {
                $secondBracket = $matches[$i + 1];
            }

            if($firstBracket->id === $match->id) {
                if(isset($secondBracket->next_match_id)) {
                    $match->next_match_id = $secondBracket->next_match_id;
                    $match->score_a = $validated['score_a'];
                    $match->score_b = $validated['score_b'];
                    $match->winner_id = $winner;
                    $match->save();

                    $newMatch = Matches::find($secondBracket->next_match_id);
                    $newMatch->team_a = $winner;
                    $newMatch->save();
                } else {

                    $newMatch = Matches::create([
                        'team_a' => $winner,
                        'round' => $match->round + 1,
                        'tournament_id' => $match->tournament_id,
                    ]);

                    $match->next_match_id = $newMatch->id;
                    $match->score_a = $validated['score_a'];
                    $match->score_b = $validated['score_b'];
                    $match->winner_id = $winner;
                    $match->save();
                }
            } else if($secondBracket->id === $match->id) {
                if(isset($firstBracket->next_match_id)) {
                    $match->next_match_id = $firstBracket->next_match_id;
                    $match->score_a = $validated['score_a'];
                    $match->score_b = $validated['score_b'];
                    $match->winner_id = $winner;
                    $match->save();

                    $newMatch = Matches::find($firstBracket->next_match_id);
                    $newMatch->team_b = $winner;
                    $newMatch->save();
                } else {
                    $newMatch = Matches::create([
                        'team_b' => $winner,
                        'round' => $match->round + 1,
                        'tournament_id' => $match->tournament_id,
                    ]);
                    $match->next_match_id = $newMatch->id;
                    $match->score_a = $validated['score_a'];
                    $match->score_b = $validated['score_b'];
                    $match->winner_id = $winner;
                    $match->save();
                }
            }
        }

        return response()->json([
            'match_id' => $match->id,
            'winner' => Team::find($winner)->name,
            'next_match' => $newMatch->id,
        ]);

    }

    public function getBracket($tournament)
    {
        $tournament = Tournament::find($tournament);

        if(!$tournament) {
            return response()->json([
                'error' => 'Tournament does not exist.',
            ], 404);
        }

        $rounds = [];

        foreach ($tournament->matches->groupBy('round') as $round => $items) {
            $matches = [];

            foreach ($items as $match) {
                $matches[] = [
                    "match_id" => $match->id,
                    "team_a" => $match->teamA->name,
                    "team_b" => $match->teamB->name,
                    "score_a" => $match->score_a,
                    "score_b" => $match->score_b,
                    "winner" => Team::find($match->winner_id)->name,
                ];
            }

            $rounds[] = [
                'round' => $round,
                'matches' => $matches,
            ];
        }

        return response()->json([
            'tournament' => $tournament->name,
            'rounds' => $rounds,
        ]);
    }

    public function getTournaments()
    {
        return Tournament::all();
    }

    public function getTournamentTeams($tournament)
    {
        $tournament = Tournament::find($tournament);

        if(!$tournament) {
            return response()->json([
                'error' => 'Tournament does not exist.',
            ], 404);
        }

        return response()->json([
            'id' => $tournament->id,
            'tournament' => $tournament->name,
            'teams' => $tournament->teams()->select('id', 'name')->get(),
        ]);
    }
}
