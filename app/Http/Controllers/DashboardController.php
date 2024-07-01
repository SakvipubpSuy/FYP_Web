<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Deck;
use App\Models\Card;
use App\Models\CardUser;

class DashboardController extends Controller
{
    public function index()
    {
        $userCount = User::count();
        $deckCount = Deck::count();
        $cardCount = Card::count();
        return view('dashboard.index',compact('userCount','deckCount','cardCount'));
    }
    public function users()
    {
        $users = User::paginate(5); //paginate 10 users per page
        return view('dashboard.users', compact('users'));
    }
}
