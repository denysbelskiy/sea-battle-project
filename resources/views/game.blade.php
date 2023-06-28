<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf_token" content="{{ csrf_token() }}">
    <title>Game</title>
    <link rel="stylesheet" href="/styles/game.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Martian+Mono:wght@200;300;400;700&display=swap" rel="stylesheet">

</head>
<body>
    <h1 class="main-header greeting"><span class="game-header">{{$game->title}}</span></h1>

    <div id="boards" data-csrf="{{ csrf_token() }}" data-init="/game/{{$game->id}}/init" data-shot="/game/{{$game->id}}/shot" data-ping="/game/{{$game->id}}/ping">
        <div id="board_1" class="board">
            <table>
                @for ($r = 0; $r <= $game->data->configuration->board->x_length; $r++)

                    <tr>
                        @for ($c = 0; $c <= $game->data->configuration->board->y_length; $c++)
                        <td class="location location_{{$r}}_{{$c}}" data-x="{{$r}}" data-y="{{$c}}">{{$r}},{{$c}}</td>
                        @endfor    
                    </tr>
                
                @endfor
            </table>
            <h2 class="reminder greeting"><span class="reminder-text">Your board</h2>    
        </div>
        <div id="board_2" class="board">
            <table>
                @for ($r = 0; $r <= $game->data->configuration->board->x_length; $r++)

                    <tr>
                        @for ($c = 0; $c <= $game->data->configuration->board->y_length; $c++)
                        <td class="location location_{{$r}}_{{$c}}" data-x="{{$c}}" data-y="{{$r}}">{{$r}},{{$c}}</td>
                        @endfor    
                    </tr>
                
                @endfor
            </table>
            <h2 class="reminder greeting"><span class="reminder-text">Enemy board</h2>  
        </div>
    </div>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</body>
</html>