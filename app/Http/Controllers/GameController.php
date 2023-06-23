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
        $index = $game->id;
        return view('/game', ['game' => $game]);
    }
    
    public function joinGame(Request $request) {
        
        $games = Game::find($request->game_id);
        $games->joinPlayerTwo(auth()->user()->id);
        

    }
}
