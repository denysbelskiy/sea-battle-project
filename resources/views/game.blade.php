<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Game</title>
</head>
<body>
    <p>sup{{$game->title}}{{$game->id}}</p>
    <p>X: {{$game->data->configuration->board->x_length}} 
        Y: {{$game->data->configuration->board->y_length}}</p>


    <div id="boards">
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
        </div>
    </div>
    @vite(['resources/css/app.css', 'resources/js/app.js']);
</body>
</html>