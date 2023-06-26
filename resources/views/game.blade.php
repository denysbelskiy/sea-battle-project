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
            cursor: crosshair;
        }
        #board_2.board.locked .location {
            cursor:not-allowed;
        }
        .hit {
            background-color: rgb(255, 0, 0) !important;
        }
        .miss {
            background-color: gray;
        }
        .one-target{
            background-color: rgb(119, 248, 119);
        }
        .two-target{
            background-color: rgb(30, 236, 30);
        }
        .three-target{
            background-color: rgb(0, 159, 0);
        }
        .four-target{
            background-color: rgb(3, 72, 3);
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

        async function init() {
            try {
                const response = await fetch("http://localhost:8000/game/{{$game->id}}/init", {
                method: "POST", 
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: '',
                });

                const result = await response.json();
                console.log("Success:", result);

                putTargets(result.ownTargets);
                showPlayerHits(result.ownShots);
                showEnemyHits(result.enemyShots);

                // TODO: Create 2 divs: your turn, not your turn (waiting for enemy)
                // show one of this div depending on your-turn-response
                // create global var for yourturn
                // set global var depending on your-turn-response
                // in click eventlistener, don't run shot() if its not your turn
                // add class to board_2 "locked" if not your turn, remove class if it's your turn


            } catch (error) {
                console.error("Error:", error);
            }
        }

        window.addEventListener('DOMContentLoaded', () => {
            
            // on first load initialize the game with it's DATA
            init();

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

        function putTargets(data) {
            data.forEach(e => {
                let coords = e.locations;
                switch(coords.length) {
                    case 1:
                        showTarget(coords, 'one');
                        break;
                    case 2:
                        showTarget(coords, 'two');
                        break;
                    case 3:
                        showTarget(coords, 'three');
                        break;
                    case 4:
                        showTarget(coords, 'four');
                        break;
                }   
            })
        }

        

        function showTarget(array, size) {
            
            array.forEach(e => {
                const board = document.querySelector('#board_1');
                const cell = board.querySelector(`.location_${e.y}_${e.x}`);
                cell.classList.add(`${size}-target`);
            });
        }

        function showPlayerHits(array) {
            const board = document.querySelector('#board_2');
            array.forEach(e => {
                const cell = board.querySelector(`.location_${e.y}_${e.x}`);
                if(e.hit === true) {
                    if(!cell.classList.contains('hit')){
                        cell.classList.add(`hit`);
                    }
                }
                else {
                    cell.classList.add('miss');
                }
            });
        }
      
        function showEnemyHits(array) {
            const board = document.querySelector('#board_1');
            array.forEach(e => {
                const cell = board.querySelector(`.location_${e.y}_${e.x}`);
                if(e.hit === true) {
                    if(!cell.classList.contains('hit')){
                        cell.classList.add(`hit`);
                    }
                }
                else {
                    cell.classList.add('miss');
                }
            });
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
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']); --}}
</body>
</html>