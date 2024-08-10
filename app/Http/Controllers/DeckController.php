<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Models\Deck;
use App\Models\Card;

class DeckController extends Controller
{
    public function getDecks(Request $request)
    {
        //Get total cards of a deck and cards scanned by the authenticated user in that deck
        $userId = auth()->id();
        $decks = Deck::withCount('cards as total_cards_count')
        ->withCount(['cards as scanned_cards_count' => function ($query) use ($userId) {
            $query->join('card_user', 'cards.card_id', '=', 'card_user.card_id')
                  ->where('card_user.user_id', $userId);
        }])
        ->get();
        return response()->json($decks);
    }
    public function index()
    {
        $decks = Deck::withCount('cards')->paginate(4); // Assuming you want 4 decks per page
        return view('decks.index', compact('decks'));
    }
    public function create()
    {
        $decks = Deck::all();
        return view('decks.create', compact('decks'));
    }
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'deck_name' => 'required|string|max:255',
            'deck_description' => 'nullable|string',
            'deck_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            // Add any other validation rules for your deck fields
        ]);

        $img_url = null;
        // Check if an image is uploaded
        if ($request->hasFile('deck_image')) {
            $image = $request->file('deck_image');
            $timestamp = time();
            $deck_name = $request->deck_name;
            // $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $image->getClientOriginalExtension();
            $imageName = $deck_name . '_' . $timestamp . '.' . $extension;
            $imagePath = 'deck-img/' . $imageName; //'card-img/' . $imageName to specify the directory or folder later on
    
            // Store the image using the storage disk
            Storage::disk('remote')->put($imagePath, file_get_contents($image->getRealPath()));
    
            // Generate the image URL
            $img_url = Storage::disk('remote')->url($imagePath);
        }

        try {
            $deck = Deck::create([
                'deck_name' => $validatedData['deck_name'],
                'deck_description' => $validatedData['deck_description'],
                'img_url' => $img_url,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('decks.create')->with('error', 'Deck creation failed!');
        }

        return redirect()->route('decks.create')->with('success', 'Deck created successfully!');
    }
    public function show($deck_id)
    {
        $decks = Deck::findOrFail($deck_id);
        $cards = Deck::find($deck_id)->cards()->paginate(4); // Assuming you have defined the cards relationship in your Deck model
        if ($cards->isEmpty()) {
            $cards = collect(); // If there are no cards, create an empty collection
        }
        return view('decks.show', compact('decks', 'cards'));
    }
    public function editDeck(Request $request, Deck $deck)
    {
        // Validate and edit deck details
        $validatedData = $request->validate([
            'deck_name' => 'string|max:255',
            'deck_description' => 'string',
            'deck_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        $old_img_url = $deck->img_url;
        $img_url = $old_img_url;
        if ($request->hasFile('deck_image')) {
            $image = $request->file('deck_image');
            $deckname = $request->deck_name;
            $timestamp = time();
            // $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $image->getClientOriginalExtension();
            $imageName = $deckname . '_' . $timestamp . '.' . $extension;
            $imagePath = 'deck-img/' . $imageName;
            // Store the new image on the SFTP server
            $path = $image->storeAs('', $imagePath, 'remote');
            $img_url = Storage::disk('remote')->url($imagePath);

            // Delete the old image if it exists
            if ($old_img_url) {
                $oldImagePath = parse_url($old_img_url, PHP_URL_PATH);
                $relativeImagePath = 'deck-img/' . basename($oldImagePath);
                if (Storage::disk('remote')->exists($relativeImagePath)) {
                    Storage::disk('remote')->delete($relativeImagePath);
                } else {
                    \Log::info('Old image not found: ' . $relativeImagePath);
                }
            }
        }
        try {
            $deck->update(array_merge($validatedData, ['img_url' => $img_url]));
        } catch (\Exception $e) {
            return redirect()->route('decks.index')->with('editError', 'Deck edit failed!');
        }

        return redirect()->route('decks.index')->with('editSuccess', 'Deck edit successfully.');
    }
    public function update(Deck $deck)
    {
        return redirect()->route('decks.index');
    }
    public function destroy(Request $request, $deck_id)
    {
        // Validate the admin password in order to delete the deck
        $request->validate([
            'admin_password' => 'required|string',
        ]);
    
        $adminUser = auth()->user(); // Assuming the authenticated user is an admin
        if (!Hash::check($request->admin_password, $adminUser->password)) {
            return redirect()->back()->with('error', 'Invalid admin password!');
        }
        else{
        //Delete deck along with its cards
            $deck = Deck::findOrFail($deck_id);
            
            if (!$deck) {
                return redirect()->route('decks.index')->with('error', 'Deck not found!');
            }
            $old_img_url = $deck->img_url;
            if ($old_img_url) {
                $oldImagePath = parse_url($old_img_url, PHP_URL_PATH);
                $relativeImagePath = 'deck-img/' . basename($oldImagePath);
                if (Storage::disk('remote')->exists($relativeImagePath)) {
                    Storage::disk('remote')->delete($relativeImagePath);
                } else {
                    \Log::info('Old image not found: ' . $relativeImagePath);
                }
            }
            $deck->delete();
        
            // Check if there are decks on the current page
            $page = $request->input('page', 1);
            $decks = Deck::paginate(4, ['*'], 'page', $page);
        
            if ($decks->isEmpty() && $page > 1) {
                return redirect()->route('decks.index', ['page' => $page - 1])->with('success', 'Deck and all its cards deleted successfully!');
            }
        
            return redirect()->route('decks.index', ['page' => $page])->with('success', 'Deck and all its cards deleted successfully!');
        }
    }
    public function search(Request $request)
    {
        $query = $request->input('query');
        $decks = Deck::where('deck_name', 'LIKE', "%{$query}%")->paginate(4);
        return view('decks.index', compact('decks'));
    }
}
