<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
// use App\Models\Card;
// use App\Models\Deck;
// use App\Models\User;
use App\Models\CardTier;

class TierController extends Controller
{
    public function index()
    {   
        $cardtiers = CardTier::paginate(5);
        return view('tiers.index',compact('cardtiers'));
    }
    public function create(){
        return view('tiers.create');
    }
    public function store(Request $request){
        $validatedData = $request->validate([
            'card_tier_name' => 'required|string|max:255',
            'card_XP' => 'required|integer|min:1|',
            'card_energy_required' => 'required|integer|min:1|',
        ]);
        try {
            $cardtiers = CardTier::create($validatedData);
        } catch (\Exception $e) {
            return redirect()->route('tiers.create')->with('error', 'Card Tier creation failed!');
        }

        return redirect()->route('tiers.create')->with('success', 'Card Tier created successfully!');
    }
    public function show(){
        return view('tiers.show');
    }   
    public function edit(){
        return view('tiers.edit');
    }
    public function update(){
        return view('tiers.update');
    }   
    public function destroy(){
        return view('tiers.destroy');
    }
    public function search(Request $request){
        $query = $request->input('query');
        $cardtiers = CardTier::where('card_tier_name', 'LIKE', "%{$query}%")->paginate(4);
        return view('tiers.index', compact('cardtiers'));
    }
}

