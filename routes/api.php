<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TournamentController;

Route::prefix('v1')->group(function () {
    Route::post('/tournaments',[TournamentController::class, 'create']);
    Route::post('/tournaments/{tournament}/teams',[TournamentController::class, 'addTeam']);
    Route::post('/tournaments/{tournament}/generate-bracket',[TournamentController::class, 'generateBracket']);
    Route::patch('/matches/{match}',[TournamentController::class, 'setMatchScore']);
    Route::get('/tournaments/{tournament}/bracket',[TournamentController::class, 'getBracket']);
    Route::get('/tournaments',[TournamentController::class, 'getTournaments']);
    Route::get('/tournaments/{tournament}/teams',[TournamentController::class, 'getTournamentTeams']);
});
