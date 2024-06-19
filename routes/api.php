<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DeckController;
use App\Http\Controllers\CardController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//Public Route
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

//Protected Route
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [UserController::class, 'getUser']);
    Route::get('/users', [UserController::class, 'getAllUsers']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/decks', [DeckController::class, 'getDecks']);
    Route::get('/decks/{deck_id}/cards', [CardController::class, 'getCardsByDeckID']);
    Route::get('/cards/{card_id}', [CardController::class, 'getCardByID']);
});