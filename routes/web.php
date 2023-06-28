<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;
use App\Http\Controllers\UserController;
use Illuminate\Routing\Redirector;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [UserController::class, 'showGames']);

Route::post('/register', [UserController::class, 'register']);
Route::post('/logout', [UserController::class, 'logout']);
Route::post('/login', [UserController::class, 'login']);

//Game related stuff

Route::post('/create-game',[GameController::class, 'createGame']);
Route::post('/join-game',[GameController::class, 'joinGame']);
Route::get('/game/{id}',[GameController::class, 'playGame'])->name('game');
Route::post('/game/{id}/shot',[GameController::class, 'shotGame']);
Route::post('/game/{id}/init',[GameController::class, 'initGame']);
Route::post('/game/{id}/ping',[GameController::class, 'pingGame']);

Route::get('/game/{id}/init',[GameController::class, 'initGame']);
Route::get('/game/{id}/shot',[GameController::class, 'shotGame']);
Route::get('/game/{id}/ping',[GameController::class, 'pingGame']);


