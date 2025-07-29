<?php

namespace App\Http\Controllers;

use App\Models\PlayerPosition;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PlayerPositionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $player_position = PlayerPosition::all();
        return response()->json($player_position);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $player_position = new PlayerPosition();
            $player_position->name = $request->name;
            $player_position->is_deleted = 0;
            $player_position->save();

            return response()->json(['message' => 'Player Position added'], 201);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'PlayerPosition not found'], 404);
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
            $player_position = PlayerPosition::find($id);
            if (!empty($player_position)) {
                return response()->json($player_position);
            } else {
                return response()->json(['message' => 'player position not found'], 404);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'PlayerPosition not found'], 404);
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
            if (PlayerPosition::where('player_position_id', $id)->exists()) {
                $player_position = PlayerPosition::find($id);
                $player_position->name = is_null($request->name) ? $player_position->name : $request->name;
                $player_position->save();
                return response()->json(['message' => 'player position Updated'], 201);
            } else {
                return response()->json(['message' => 'player position not found'], 404);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'PlayerPosition not found'], 404);
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
            if (PlayerPosition::where('player_position_id', $id)->exists()) {
                $player_position = PlayerPosition::find($id);
                $player_position->is_deleted = 1;
                $player_position->save();
                return response()->json([
                    'message' => 'player position deleted'
                ], 202);
            } else {
                return response()->json(['message' => 'player position not found'], 404);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'PlayerPosition not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred'], 500);
        }
    }
}
