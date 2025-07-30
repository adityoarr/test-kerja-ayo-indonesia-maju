<?php

namespace App\Http\Controllers;

use App\Models\TeamNumber;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TeamNumberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $team_number = TeamNumber::all();
        return response()->json($team_number);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'team_id' => 'required',
                'number' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $team_number = new TeamNumber();
            $team_number->team_id = $request->team_id;
            $team_number->number = $request->number;
            $team_number->is_deleted = 0;
            $team_number->save();

            return response()->json(['message' => 'Team number added'], 201);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Team number not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id) // show player number list in a team
    {
        try {
            $team_number = TeamNumber::where('team_id', $id)->get();
            if (!empty($team_number)) {
                return response()->json($team_number);
            } else {
                return response()->json(['message' => 'team number not found'], 404);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Team number not found'], 404);
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
            if (TeamNumber::where('team_number_id', $id)->exists()) {
                $team_number = TeamNumber::find($id);
                $team_number->team_id = is_null($request->team_id) ? $team_number->team_id : $request->team_id;
                $team_number->number = is_null($request->number) ? $team_number->number : $request->number;
                $team_number->save();
                return response()->json(['message' => 'team number Updated'], 201);
            } else {
                return response()->json(['message' => 'team number not found'], 404);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Team number not found'], 404);
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
            if (TeamNumber::where('team_number_id', $id)->exists()) {
                $team_number = TeamNumber::find($id);
                $team_number->is_deleted = 1;
                $team_number->save();
                return response()->json([
                    'message' => 'team number deleted'
                ], 202);
            } else {
                return response()->json(['message' => 'team number not found'], 404);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Team number not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred'], 500);
        }
    }
}
