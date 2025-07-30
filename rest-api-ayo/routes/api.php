<?php

use App\Http\Controllers\GameController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\PlayerPositionController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TeamNumberController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResources([
    'teams' => TeamController::class,
    'team_number' => TeamNumberController::class,
    'players' => PlayerController::class,
    'player_position' => PlayerPositionController::class,
    'games' => GameController::class,
]);
