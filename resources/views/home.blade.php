<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>

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
                    <p>it's your game</p>
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

</body>
</html>