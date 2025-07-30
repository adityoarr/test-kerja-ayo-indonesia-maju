<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $game = Game::all();
        return response()->json($game, 201);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'tanggal' => 'required',
                'waktu' => 'required',
                'team_home_id' => 'required',
                'team_away_id' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $game = new Game();
            $game->tanggal = $request->tanggal;
            $game->waktu = $request->waktu;
            $game->team_home_id = $request->team_home_id;
            $game->team_away_id = $request->team_away_id;
            $game->is_deleted = 0;
            $game->save();

            return response()->json(['message' => 'game stored'], 201);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'game not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $game = Game::where('game_id', $id)->get();
            if (!empty($game)) {
                return response()->json($game);
            } else {
                return response()->json(['message' => 'game not found'], 404);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'game not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred'], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            if (Game::where('game_id', $id)->exists()) {
                $validator = Validator::make($request->all(), [
                    'tanggal' => 'required',
                    'waktu' => 'required',
                    'team_home_id' => 'required',
                    'team_away_id' => 'required',
                ]);

                if ($validator->fails()) {
                    return response()->json(['errors' => $validator->errors()], 422);
                }

                $game = Game::find($id);
                $game->tanggal = $request->tanggal;
                $game->waktu = $request->waktu;
                $game->team_home_id = $request->team_home_id;
                $game->team_away_id = $request->team_away_id;
                $game->is_deleted = 0;
                $game->save();
                return response()->json(['message' => 'game Updated'], 201);
            } else {
                return response()->json(['message' => 'game not found'], 404);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'game not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            if (Game::where('game_id', $id)->exists()) {
                $game = Game::find($id);
                $game->is_deleted = 1;
                $game->save();
                return response()->json([
                    'message' => 'game deleted'
                ], 202);
            } else {
                return response()->json(['message' => 'game not found'], 404);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'game not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred'], 500);
        }
    }

    public function goal_store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'game_id' => 'required',
                'player_id' => 'required',
                'is_team_home' => 'required',
                'is_team_away' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $game_goal = new Game();
            $game_goal->game_id = $request->game_id;
            $game_goal->player_id = $request->player_id;
            $game_goal->is_team_home = $request->is_team_home;
            $game_goal->is_team_away = $request->is_team_away;
            $game_goal->save();

            return response()->json(['message' => 'game goal stored'], 201);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'game goal not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred'], 500);
        }
    }
}
