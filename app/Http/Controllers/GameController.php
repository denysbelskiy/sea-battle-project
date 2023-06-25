<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function createGame (Request $request) {
        $incomingFields = $request->validate([
            'gametitle' => ['required', 'min:3', 'max:10'],
        ]);

        $incomingFields['gametitle'] = strip_tags($incomingFields['gametitle']);
        $incomingFields['title'] = $incomingFields['gametitle'];
        unset($incomingFields['gametitle']);
        $incomingFields['user_id'] = auth()->id();
        $game = Game::create($incomingFields);
        $game->initGame();
        return redirect()->route('game', ['id' => $game->id]);
    }
    
    public function joinGame(Request $request) {
        
        $game = Game::find($request->game_id);
        $game->joinPlayerTwo(auth()->user()->id);
        
        return redirect()->route('game' ,['id' => $game->id]);
  

    }
    public function playGame(string $id) {
       
        $game = Game::find($id);
        $game->prepareData();
        return view('game', ['game' => $game]);

    }

    public function shotGame($id, Request $request) {

        $data = $request->json()->all();
        $game = Game::find($id);
        $response = $game->shoot($data['y'], $data['x']);

        
       return response()->json($response);
       
    }

}
