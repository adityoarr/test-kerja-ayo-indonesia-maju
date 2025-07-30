<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $team = Team::all();
        return response()->json($team, 201);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nama' => 'required',
                'logo' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                'tahun_berdiri' => 'required',
                'alamat_markas' => 'required',
                'kota_markas' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $team = new Team();
            $team->nama = $request->nama;
            if ($request->hasFile('logo')) {
                $path = $request->file('logo')->store('public/logo');
                $url = Storage::url($path);
                $team->logo = $url;
            }
            $team->tahun_berdiri = $request->tahun_berdiri;
            $team->alamat_markas = $request->alamat_markas;
            $team->kota_markas = $request->kota_markas;
            $team->is_deleted = 0;
            $team->save();

            return response()->json(['message' => 'team stored'], 201);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'team not found'], 404);
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
            $team = Team::find($id);
            if (!empty($team)) {
                return response()->json($team);
            } else {
                return response()->json(['message' => 'team not found'], 404);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'team not found'], 404);
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
            if (Team::where('team_id', $id)->exists()) {
                $team = Team::find($id);
                $team->name = is_null($request->nama) ? $team->nama : $request->nama;
                if ($request->hasFile('logo')) {
                    $validator = Validator::make($request->all(), [
                        'logo' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
                    ]);

                    if ($validator->fails()) {
                        return response()->json(['errors' => $validator->errors()], 422);
                    }
                    $path = $request->file('logo')->store('public/logo');
                    $url = Storage::url($path);
                    $team->logo = $url;
                }
                $team->tahun_berdiri = is_null($request->tahun_berdiri) ? $team->tahun_berdiri : $request->tahun_berdiri;
                $team->alamat_markas = is_null($request->alamat_markas) ? $team->alamat_markas : $request->alamat_markas;
                $team->kota_markas = is_null($request->kota_markas) ? $team->kota_markas : $request->kota_markas;
                $team->save();
                return response()->json(['message' => 'team Updated'], 201);
            } else {
                return response()->json(['message' => 'team not found'], 404);
            }
            $team->save();

            return response()->json(['message' => 'team updated'], 201);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'team not found'], 404);
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
            if (Team::where('team_id', $id)->exists()) {
                $team = Team::find($id);
                $team->is_deleted = 1;
                $team->save();
                return response()->json([
                    'message' => 'team deleted'
                ], 202);
            } else {
                return response()->json(['message' => 'team not found'], 404);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'team not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred'], 500);
        }
    }
}
