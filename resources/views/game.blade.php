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
        .show-turn {
            width: 100px;
            height: 50px;
            align-items: center;
            border: 2px solid black;
        }

    </style>
    
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
    <script defer>
        //globals
        let turnDiv = createTurnDiv();
        document.body.appendChild(turnDiv);
        let isYourTurn = false;

        window.addEventListener('DOMContentLoaded', () => {
            
            // on first load initialize the game with it's DATA
            init();

            const locations = document.querySelectorAll('#board_2 .location');
         
            locations.forEach(el => el.addEventListener('click', event => {
              
                let shot_x = event.target.getAttribute('data-x');
               let shot_y = event.target.getAttribute('data-y');
              
               console.log('shot', shot_x, shot_y);

               const shot_location = { y: shot_y, x: shot_x };
               if (isYourTurn) {
                    shot(shot_location);
               }

            }));

        });

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

                ping();

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
                isYourTurn = result.yourturn;
                const board_2 = document.querySelector('#board_2');
                if (isYourTurn) {
                    giveMessedgeToTurnDiv('SHOOT', isYourTurn);
                    if(board_2.classList.contains('locked')){
                        board_2.classList.remove('locked');
                    }
                }
                else{
                    giveMessedgeToTurnDiv('WAIT', isYourTurn);
                    if(!board_2.classList.contains('locked')){
                        board_2.classList.add('locked');
                    }

                    ping();

                }
                
            } catch (error) {
                console.error("Error:", error);
            }
        }

        async function ping() {
            try {
                const response = await fetch("http://localhost:8000/game/{{$game->id}}/ping", {
                method: "POST", 
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: '',
                });

                const result = await response.json();
                console.log("Success:", result);

                isYourTurn = result.yourturn;
                const board_2 = document.querySelector('#board_2');

                if (isYourTurn) {

                    giveMessedgeToTurnDiv('SHOOT', isYourTurn);

                    if(board_2.classList.contains('locked')){
                        board_2.classList.remove('locked');
                    }

                    showEnemyHit(result.lastenemyshot);

                }
                else{

                    giveMessedgeToTurnDiv('WAIT', isYourTurn);

                    if(!board_2.classList.contains('locked')){
                        board_2.classList.add('locked');
                    }

                    setTimeout(() => {
                        ping();
                    }, 1000);

                }


            }catch (error) {
                console.error("Error:", error);
            }
        }


        //functions to reuse

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

        function showEnemyHit(coord) {
            const board = document.querySelector('#board_1');
            const cell = board.querySelector(`.location_${coord.y}_${coord.x}`);
                if(coord.hit === true) {
                    if(!cell.classList.contains('hit')){
                        cell.classList.add(`hit`);
                    }
                }
                else {
                    cell.classList.add('miss');
                }
        }

        function createTurnDiv() {
            let div = document.createElement('div');
            div.classList.add('show-turn');
            let p = document.createElement('p');
            p.classList.add('show-turn-messedge');
            div.appendChild(p);
            return div;
        }
        function giveMessedgeToTurnDiv(messedge, turn) {
            // setAttribute('id','para-1');
            let p = document.querySelector('.show-turn-messedge');
            p.innerText = `${messedge}`
            if(turn) {
                if (p.classList.contains('your-turn')){

                } else {
                    p.classList.add('your-turn');
                }
            } else {
                if (p.classList.contains('enemy-turn')){

                } else {
                    p.classList.add('enemy-turn');
                }
            }

        }

    </script>
</body>
</html>