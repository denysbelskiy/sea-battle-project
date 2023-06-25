<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Game</title>
    <style>
        .board {
            border: 3px solid black;
            padding: 5px;
        }

        .board table td {
            border: 1px solid black;
        }
        #board_2.board .location {
            cursor: pointer;
        }
        .hit {
            background-color: rgb(255, 0, 0);
        }
        .miss {
            background-color: gray;
        }
    </style>
    <script defer>

        async function shot(data) {
            try {
                const response = await fetch("http://localhost:8000/game/{{$game->id}}/shot", {
                method: "POST", 
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify(data),
                });

                const result = await response.json();
                console.log("Success:", result);

                // TODO: check îf gameover
                // Show winner

                if(result.hit === true) {
                    addHitClass(result.y, result.x);
                }
                else {
                    addMissClass(result.y, result.x);
                }


            } catch (error) {
                console.error("Error:", error);
            }
        }

        window.addEventListener('DOMContentLoaded', () => {
            
            const locations = document.querySelectorAll('#board_2 .location');
         
            locations.forEach(el => el.addEventListener('click', event => {
              
                let shot_x = event.target.getAttribute('data-x');
               let shot_y = event.target.getAttribute('data-y');
              
               console.log('shot', shot_x, shot_y);

               const shot_location = { y: shot_y, x: shot_x };
               shot(shot_location);

            }));

        });

        function addHitClass (y, x) {
            const board = document.querySelector('#board_2');
            const cell = board.querySelector(`.location_${y}_${x}`);
            cell.classList.add('hit');
        }

        function addMissClass (y, x) {
            const board = document.querySelector('#board_2');
            const cell = board.querySelector(`.location_${y}_${x}`);
            cell.classList.add('miss');
        }


    </script>
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
</body>
</html>