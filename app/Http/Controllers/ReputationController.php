<?php

namespace App\Http\Controllers;
use App\Models\DeckTitle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReputationController extends Controller
{
    // FOR WEB ROUTES
    public function index()
    {
        $deckTitles = DeckTitle::paginate(5);
        return view('decks.reputation-titles', compact('deckTitles'));
    }
    public function edit(Request $request)
    {
        // Validate the incoming data
        $validatedData = $request->validate([
            'min_percentage' => 'required|numeric|min:0|max:100',
            'max_percentage' => 'required|numeric|min:0|max:100',
            'title' => 'required|string|max:255',
            'deck_titles_id' => 'required|integer|exists:deck_titles,deck_titles_id', // Ensure the ID exists
        ]);
    
        // Retrieve the current deck title using the hidden field
        $deckTitle = DeckTitle::findOrFail($validatedData['deck_titles_id']);
    
        // Get the previous and next deck titles for min/max restriction
        $previousTitle = DeckTitle::where('deck_titles_id', '<', $deckTitle->deck_titles_id)->orderBy('deck_titles_id', 'desc')->first();
        $nextTitle = DeckTitle::where('deck_titles_id', '>', $deckTitle->deck_titles_id)->orderBy('deck_titles_id', 'asc')->first();
    
        // Ensure the new min and max percentages are within the allowed range
        if ($previousTitle && $validatedData['min_percentage'] <= $previousTitle->max_percentage) {
            return back()->withErrors(['error_min_percentage' => 'Minimum percentage must be greater than the previous title\'s maximum percentage.']);
        }
        
        if ($nextTitle && $validatedData['max_percentage'] >= $nextTitle->min_percentage) {
            return back()->withErrors(['error_max_percentage' => 'Maximum percentage must be less than the next title\'s minimum percentage.']);
        }
    
        // Update the deck title with validated data
        $deckTitle->min_percentage = $validatedData['min_percentage'];
        $deckTitle->max_percentage = $validatedData['max_percentage'];
        $deckTitle->title = $validatedData['title'];
        $deckTitle->save();
    
        // Redirect back with success message
        return redirect()->route('reputation-titles.index')->with('editRepuationSuccess', 'Reputation title updated successfully!');
    }

}
