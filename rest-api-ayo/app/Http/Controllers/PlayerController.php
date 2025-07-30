<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\PlayerPosition;
use App\Models\Team;
use App\Models\TeamNumber;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PlayerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $player = Player::all();
        return response()->json($player, 201);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nama' => 'required',
                'tinggi_badan' => 'required|numeric',
                'berat_badan' => 'required|numeric',
                'no_punggung' => 'required|numeric',
                'team_id' => 'required',
                'player_position_id' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            if (Team::where('team_id', $request->team_id)->doesntExist()) {
                return response()->json(['errors' => 'Team not found'], 404);
            }

            if (PlayerPosition::where('player_position_id', $request->player_position_id)->doesntExist()) {
                return response()->json(['errors' => 'player position not found'], 404);
            }

            $player = new Player();
            $player->nama = $request->nama;
            $player->tinggi_badan = $request->tinggi_badan;
            $player->berat_badan = $request->berat_badan;
            $player->team_id = $request->team_id;
            $player->player_position_id = $request->player_position_id;
            if (TeamNumber::where(['team_id' => $request->team_id, 'number' => $request->no_punggung])->exists()) {
                $team_number = TeamNumber::where(['team_id' => $request->team_id, 'number' => $request->no_punggung])->first();
                if (Player::where('team_number_id', $team_number->team_number_id)->exists()) {
                    return response()->json(['errors' => 'player number used'], 404);
                } else {
                    $player->team_number_id = $team_number->team_number_id;
                }
            } else {
                $team_number = new TeamNumber();
                $team_number->team_id = $request->team_id;
                $team_number->number = $request->no_punggung;
                $team_number->is_deleted = 0;
                $team_number->save();
                $player->team_number_id = $team_number->team_number_id;
            }
            $player->is_deleted = 0;
            $player->save();

            return response()->json(['message' => 'player stored'], 201);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'player not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id) // show player list in a team
    {
        try {
            $player = Player::where('team_id', $id)->get();
            if (!empty($player)) {
                return response()->json($player);
            } else {
                return response()->json(['message' => 'player not found'], 404);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'player not found'], 404);
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
            if (Player::where('player_id', $id)->exists()) {
                $validator = Validator::make($request->all(), [
                    'nama' => 'required',
                    'tinggi_badan' => 'required|numeric',
                    'berat_badan' => 'required|numeric',
                    'no_punggung' => 'required|numeric',
                    'team_id' => 'required',
                    'player_position_id' => 'required',
                ]);

                if ($validator->fails()) {
                    return response()->json(['errors' => $validator->errors()], 422);
                }

                if (Team::where('team_id', $request->team_id)->doesntExist()) {
                    return response()->json(['errors' => 'Team not found'], 404);
                }

                if (PlayerPosition::where('player_position_id', $request->player_position_id)->doesntExist()) {
                    return response()->json(['errors' => 'player position not found'], 404);
                }

                $player = Player::find($id);
                $player->nama = $request->nama;
                $player->tinggi_badan = $request->tinggi_badan;
                $player->berat_badan = $request->berat_badan;
                $player->team_id = $request->team_id;
                $player->player_position_id = $request->player_position_id;
                if (TeamNumber::where(['team_id' => $request->team_id, 'number' => $request->no_punggung])->exists()) {
                    $team_number = TeamNumber::where(['team_id' => $request->team_id, 'number' => $request->no_punggung])->first();
                    if (Player::where('team_number_id', $team_number->team_number_id)->exists()) {
                        return response()->json(['errors' => 'player number used'], 404);
                    } else {
                        $player->team_number_id = $team_number->team_number_id;
                    }
                } else {
                    $team_number = new TeamNumber();
                    $team_number->team_id = $request->team_id;
                    $team_number->number = $request->no_punggung;
                    $team_number->is_deleted = 0;
                    $team_number->save();
                    $player->team_number_id = $team_number->team_number_id;
                }
                $player->save();

                return response()->json(['message' => 'player Updated'], 201);
            } else {
                return response()->json(['message' => 'player not found'], 404);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'player not found'], 404);
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
            if (Player::where('player_id', $id)->exists()) {
                $player = Player::find($id);
                $player->is_deleted = 1;
                $player->save();
                return response()->json([
                    'message' => 'player deleted'
                ], 202);
            } else {
                return response()->json(['message' => 'player not found'], 404);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'player not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred'], 500);
        }
    }
}
