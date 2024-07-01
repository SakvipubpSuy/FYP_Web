<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DeckController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\TierController;

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


    Route::get('/', function () {
        return view('test');
    });


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/users',[DashboardController::class,'users'])->name('dashboard.users');
    Route::get('/admins',[AdminController::class,'index'])->name('admins.index');
    Route::get('/decks/search', [DeckController::class, 'search'])->name('decks.search');
    Route::get('cards/search',[CardController::class, 'search'])->name('cards.search');
    Route::get('/decks/{card_id}/qrcode', [CardController::class, 'generateQrCode'])->name('cards.qrcode');
    Route::get('/tiers/search',[TierController::class,'search'])->name('tiers.search');
    
    Route::resource('decks', DeckController::class);
    Route::resource('cards', CardController::class);
    Route::resource('tiers', TierController::class);
    
});

