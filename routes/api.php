<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DeckController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\TradeController;
use App\Http\Controllers\TierController;
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
Route::post('/forget-password/request-reset-code', [AuthController::class, 'sendResetCode']);
Route::post('/forget-password/verify-code', [AuthController::class, 'verifyResetCode']);
Route::post('/forget-password/reset', [AuthController::class, 'resetPassword']);

//Protected Route
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [UserController::class, 'getUser']);
    Route::get('/users', [UserController::class, 'getAllUsers']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/decks', [DeckController::class, 'getDecks']);
    Route::get('/tiers', [TierController::class, 'getTiers']);
    Route::get('/decks/{deck_id}/cards', [CardController::class, 'getCardsByDeckID']);
    Route::get('/cards/{card_id}', [CardController::class, 'getCardByID']);
    Route::post('/scan-card', [CardController::class, 'scanCard']);
    Route::get('/user/total-cards', [CardController::class, 'countUserTotalCards']);
    Route::get('/user/quests', [CardController::class, 'getQuests']);
    Route::post('/user/submit-quest', [CardController::class, 'submitQuest']);
    Route::post('/trade/send-trade-request', [TradeController::class, 'sendTradeRequest']); 
    Route::get('/trade/count-trade-request', [TradeController::class, 'countTradeRequest']);
    Route::get('/trade/trade-request', [TradeController::class, 'getTradeRequest']);
    Route::post('/trade/accept-trade/{trade_id}', [TradeController::class, 'acceptTradeRequest']);
    Route::delete('/trade/deny-trade/{trade_id}', [TradeController::class, 'denyTradeRequest']);
    Route::delete('/trade/cancel-trade/{trade_id}', [TradeController::class, 'cancelTradeRequest']);
    Route::post('/trade/complete-trade/{trade_id}', [TradeController::class, 'completeTradeRequest']);
    Route::patch('/trade/revert-trade/{trade_id}', [TradeController::class, 'revertTradeRequest']);
});