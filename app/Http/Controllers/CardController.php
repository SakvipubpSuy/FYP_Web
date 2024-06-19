<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Card;
use App\Models\Deck;

class CardController extends Controller
{
    public function getCardsByDeckID(Request $request,$deck_id)
    {
        // Fetch cards that belong to the specified deck
        $user = Auth::user();
        $cards = Card::where('deck_id', $deck_id)
             ->whereHas('users', function ($query) use ($user) {
                 $query->where('users.id', $user->id);
             })
             ->get();
        return response()->json($cards);
    }
    public function getCardByID($card_id)
    {
        // Fetch cards that belong to the specified deck
        $card = Card::where('card_id', $card_id)->get();

        // Return the cards as a JSON response
        return response()->json($card);
    }
    public function index()
    {   

        $cards = Card::paginate(4); // Assuming you want x cards per page
        return view('cards.index', compact('cards'));
    }

    public function create()
    {
        $cards = Card::all();
        $decks = Deck::all();
        return view('cards.create', compact('cards', 'decks'));
    }

    public function store(Request $request)
    {
        // dd("hi");
        $validatedData = $request->validate([
            'card_name' => 'required|string|max:255',
            'card_tier' => 'required|string|max:255',
            'deck_id' => 'required|exists:decks,deck_id',
            'card_description' => 'required|string',
        ]);
        $validatedData['version'] = 1;
        try {
            $card = Card::create($validatedData);
        } catch (\Exception $e) {
            return redirect()->route('cards.create')->with('error', 'Card creation failed!');
        }

        return redirect()->route('cards.create')->with('success', 'Card created successfully!');
    }

    public function show(Card $card)
    {
        return view('cards.index', compact('card'));
    }
    public function edit(Card $card)
    {
        return view('cards.index', compact('card'));
    }

    public function update(Request $request, Card $card)
    {
        $validatedData = $request->validate([
            'card_name' => 'required|string|max:255',
            'card_tier' => 'required|string|max:255',
            'deck_id' => 'required|exists:decks,id',
            'card_description' => 'required|string',
        ]);

        $validatedData['version'] = $card->version + 1; // Increment version

        try {
            $card->update($validatedData);
        } catch (\Exception $e) {
            return redirect()->route('cards.edit', $deck)->with('error', 'Card update failed!');
        }

        return redirect()->route('cards.index')->with('success', 'Card updated successfully!');
    }

    public function destroy(Request $request,$card_id)
    {
        $card = Card::find($card_id);

        if (!$card) {
            return redirect()->route('cards.index')->with('error', 'Card not found!');
        }
    
        $card->delete();
    
        // Get the current page number
        $page = $request->input('page', 1);
    
        // Retrieve the cards after deletion
        $cards = Card::paginate(4, ['*'], 'page', $page);
    
        // If the current page is empty and it's not the first page, redirect to the previous page
        if ($cards->isEmpty() && $page > 1) {
            return redirect()->route('cards.index', ['page' => $page - 1])->with('success', 'Card has been deleted successfully!');
        }
    
        return redirect()->route('cards.index', ['page' => $page])->with('success', 'Card has been deleted successfully!');
    }
}
