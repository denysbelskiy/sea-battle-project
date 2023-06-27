import './bootstrap';

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

        if(result.hit === true) {
            addHitClass(result.y, result.x);
        }
        else {
            addMissClass(result.y, result.x);
        }

        if (result.gameover) {

            showWinner(result.winners_name);

        } else {

            ping();

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
            "X-CSRF-TOKEN": "{{ csrf_token() }}".attr('content')
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
            if (result.status.finished) {
                showWinner(result.winners_name);
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

        //check if player lost

        if (result.finished) {
                showWinner(result.winners_name);
        } else {

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

function showWinner(winner) {
    let container = document.querySelector('.show-turn');
    if(container) {
        container.setAttribute('id','show-winner');
        container.classList.remove('show-turn');
        let p = container.querySelector('p');
        p.innerText = `${winner} win !!!`;
    }
}