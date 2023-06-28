<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>SEA-BATTLE</title>
    <link rel="stylesheet" href="/styles/home.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Martian+Mono:wght@200;300;400;700&display=swap" rel="stylesheet">
</head>

<body>

    @auth
        <h1 class="greeting">Hello there! Welcome to the game.</h1>
        <div class="logout-div">
            <form action="/logout" method="post">
                @csrf
                <button class="buttonClass">Log out</button>
            </form>
        </div>
        <div class="container">
                <div class="div1">
                    @foreach ($games as $game)
                        <div class="gameByMary">
                            <div class="h4-name">
                                <h4>{{$game->title}} by {{$game->user->name}}</h4>
                            </div>
                        @if ($game->user_id != auth()->user()->id)
                        <div class="divButton">
                            <form action="/join-game" method="post">
                                @csrf
                                <button class="buttonJoinGame" name="game_id" value="{{$game->id}}">Join this game</button>
                            </form>
                        </div>
                        @else
                            <p><a href="@php echo route('game', ['id' => $game->id]); @endphp">go play</a></p>
                        @endif
                        </div>
                    @endforeach
                </div>

                <div class="div2">
                    <form action="/create-game" method="post" class="start-game-form">
                        @csrf
                        <input type="text" name="gametitle" placeholder="Game Name"><br>
                        <button class="startBtn">Start Game</button>
                    </form>
                    <div class="ship-icon">
                        <img src="https://i.imgur.com/57IGWyn.png" alt="ship">
                    </div>
                </div>
        </div>

    @else   
    <h3>Battle Ship</h3>
    <div class="shipLogo"><img src="https://i.imgur.com/072JIRV.png" alt="ship logo">
    </div>
    <div class="container1">
        <div class="login">
            <h2>Register</h2>
            <form action="/register" method="post">
                @csrf
                <input name="name" placeholder="name" type="text">
                <input name="email" placeholder="email" type="text">
                <input name="password" placeholder="password" type="password">
                <button class="buttonClass">Register</button>
            </form>
        </div>

        <div class="register">
            <h2>Login</h2>
            <form action="/login" method="post">
                @csrf
                <input name="loginname" placeholder="name" type="text">
                <input name="loginpassword" placeholder="password" type="password">
                <button class="buttonClass">Log in</button>
            </form>
        </div>
    </div>
    @endauth

</body>

{{-- <body>

    @auth
        <p>you are here</p>
        <form action="/logout" method="post">
            @csrf
            <button>Log out</button>
        </form>

        <div style="border: 3px solid black; padding: 10px;">
            @foreach ($games as $game)
                <div style="background: gray; padding: 10px; margin: 10px;">
                    <h3>{{$game->title}} by {{$game->user->name}}</h3>
                 
                 @if ($game->user_id != auth()->user()->id)
                    <form action="/join-game" method="post">
                        @csrf
                        <button name="game_id" value="{{$game->id}}">Join this game</button>
                    </form>
                @else
                    <p><a href="@php echo route('game', ['id' => $game->id]); @endphp">go play</a></p>
                @endif
                </div>
            @endforeach
        </div>

        <div style="border: 3px solid black; padding: 10px;">
            <form action="/create-game" method="post">
                @csrf
                <input type="text" name="gametitle" placeholder="Game Name">
                <button>Start Game</button>
            </form>
        </div>

    @else
        <div style="border: 3px solid black; padding: 10px;">
            <h2>Register</h2>
            <form action="/register" method="post">
                @csrf
                <input name="name" placeholder="name" type="text">
                <input name="email" placeholder="email" type="text">
                <input name="password" placeholder="password" type="password">
                <button>Register</button>
            </form>
        </div>

        <div style="border: 3px solid black; padding: 10px;">
            <h2>Login</h2>
            <form action="/login" method="post">
                @csrf
                <input name="loginname" placeholder="name" type="text">
                <input name="loginpassword" placeholder="password" type="password">
                <button>Log in</button>
            </form>
        </div>
    @endauth

</body> --}}
</html>