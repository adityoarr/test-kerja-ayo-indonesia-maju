<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\GameGoal;
use App\Models\GameResult;
use App\Models\Player;
use App\Models\Team;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    /** ----------------------------Goal Store-------------------------------------------- */
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

            return response()->json(['message' => 'game goal stored'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'game goal not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred'], 500);
        }
    }

    /** ----------------------------Goal Report-------------------------------------------- */
    public function goals_report($id)
    {
        try {
            $game = $id === 0 ? Game::all() : Game::find($id);
            if (empty($game)) {
                return response()->json(["error" => "no data"], 200);
            } else {
                $game_goal = GameGoal::where('game_id', $id)->get();
                $skor_home = 0;
                $skor_away = 0;
                $goals = array();

                foreach ($game_goal as $gg) {
                    $player = Player::find($gg->player_id);
                    if ($gg->is_team_home === 1) {
                        $skor_home++;
                        array_push($goals, array(
                            'pemain' => $player->nama,
                            'waktu' => $gg->created_at,
                        ));
                    }
                    if ($gg->is_team_away === 1) {
                        $skor_away++;
                        array_push($goals, array(
                            'pemain' => $player->nama,
                            'waktu' => $gg->created_at,
                        ));
                    }
                }

                $res = array();
                $res['skor_akhir'] = $skor_home . " - " . $skor_away;
                $res['goals'] = $goals;
                return response()->json($res, 200);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'report not created. check again'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred'], 500);
        }
    }

    /** ----------------------------Game Report-------------------------------------------- */
    public function games_report($id)
    {
        try {
            $game = $id === 0 ? Game::all() : Game::find($id);
            if (empty($game)) {
                return response()->json(["error" => "no data"], 200);
            } else {
                $game_goal = GameGoal::where('game_id', $id)->get();
                $team_home = Team::find($game->team_home_id);
                $team_away = Team::find($game->team_away_id);
                $skor_home = 0;
                $skor_away = 0;

                foreach ($game_goal as $gg) {
                    if ($gg->is_team_home === 1) {
                        $skor_home++;
                    }
                    if ($gg->is_team_away === 1) {
                        $skor_away++;
                    }
                }

                $res = array();
                $res['jadwal_pertandingan'] = $game->tanggal . ' ' . $game->waktu;
                $res['tim_home'] = $team_home->nama;
                $res['tim_away'] = $team_away->nama;
                $res['skor_akhir'] = $skor_home . " - " . $skor_away;
                $game_result = new GameResult();
                if ($skor_home > $skor_away) {
                    $res['status_akhir_pertandingan'] = 'Tim Home Menang';
                    $game_result->team_winner_id = $game->team_home_id;
                    $game_result->is_draw = 0;
                    $game_result->save();
                } elseif ($skor_home < $skor_away) {
                    $res['status_akhir_pertandingan'] = 'Tim Away Menang';
                    $game_result->team_winner_id = $game->team_away_id;
                    $game_result->is_draw = 0;
                    $game_result->save();
                } else {
                    $res['status_akhir_pertandingan'] = 'Draw';
                    $game_result->is_draw = 0;
                    $game_result->save();
                }

                $maxCountResult = GameGoal::selectRaw('COUNT(player_id) as total_count, player_id')
                    ->where('game_id', $id)
                    ->groupBy('player_id')
                    ->orderByDesc('total_count')
                    ->first();
                $maxCount = 0;
                if ($maxCountResult) {
                    $maxCount = $maxCountResult->total_count;
                }
                $valuesWithMaxCount = [];
                if ($maxCount > 0) {
                    $valuesWithMaxCount = GameGoal::select('player_id')
                        ->selectRaw('COUNT(player_id) as total_count')
                        ->groupBy('player_id')
                        ->having('total_count', '=', $maxCount)
                        ->get();
                }
                if ($valuesWithMaxCount->isNotEmpty()) {
                    $res['pemain_gol_terbanyak'] = '-';
                } else {
                    $player = Player::find($maxCountResult->player_id);
                    $res['pemain_gol_terbanyak'] = $player->nama;
                }

                $res['total_menang_tim_home'] = DB::table('game_result')->where('team_winner_id', $game->team_home_id)->count();
                $res['total_menang_tim_away'] = DB::table('game_result')->where('team_winner_id', $game->team_away_id)->count();
                return response()->json($res, 200);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'report not created. check again'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred'], 500);
        }
    }
}
