<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Card;
// use App\Models\Deck;
// use App\Models\User;
use App\Models\CardTier;

class TierController extends Controller
{
    //FOR API 
    public function getTiers(Request $request)
    {   
        $cardtiers = CardTier::get();
        return response()->json($cardtiers);
    }

    
    //FOR WEB
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
            'color' => 'required|string|max:7',
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
    public function editTier(Request $request, $id){
        $request->validate([
            'card_tier_name' => 'required|string|max:255',
            'card_XP' => 'required|integer',
            'card_energy_required' => 'required|integer',
            'card_RP_required' => 'required|integer|min:0|max:100',
            'color' => 'required|string|max:7',
        ]);

        $tier = CardTier::findOrFail($id);
        $tier->update($request->all());

        return redirect()->route('tiers.index')->with('success', 'Card tier updated successfully');
    }
    public function update(){
        return view('tiers.update');
    }   
    public function destroy($id){
        // Get the first 3 tiers based on their creation date or ID
        $firstThreeTiers = CardTier::orderBy('card_tier_id', 'asc')->take(3)->pluck('card_tier_id')->toArray();

        // Check if the tier to be deleted is one of the first 3
        if (in_array($id, $firstThreeTiers)) {
            return redirect()->back()->withErrors(['tierDeleteError' => 'First 3 tiers cannot be deleted.']);
        }

        // Find the tier by ID
        $cardTier = CardTier::findOrFail($id);

        // Define a default tier ID to reassign cards
        $defaultCardTierId = 1; // Replace this with the actual ID of the default tier

        try {
            // Check if there are any cards associated with this tier
            $cardsWithTier = Card::where('card_tier_id', $id)->get();

            if ($cardsWithTier->count() > 0) {
                // Reassign all cards to the default tier
                Card::where('card_tier_id', $id)->update(['card_tier_id' => $defaultCardTierId]);
            }

            // Delete the tier
            $cardTier->delete();

            // Redirect back with success message
            return redirect()->back()->with('tierDeleteSuccess', 'Tier deleted successfully, and cards were reassigned to the default tier.');
        } catch (\Exception $e) {
            // Handle any errors during the deletion process
            return redirect()->back()->withErrors(['tierDeleteError' => 'An error occurred while trying to delete the tier.']);
        }
    }
    public function search(Request $request){
        $query = $request->input('query');
        $cardtiers = CardTier::where('card_tier_name', 'LIKE', "%{$query}%")->paginate(4);
        return view('tiers.index', compact('cardtiers'));
    }
}

