<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Deck;
use App\Models\Card;
use Dompdf\Dompdf;
use Dompdf\Options;

class DeckController extends Controller
{
    public function getDecks(Request $request)
    {
        $userId = auth()->id();
    
        // Get decks with total and scanned card counts for the authenticated user
        $decks = Deck::withCount('cards as total_cards_count')
            ->withCount(['cards as scanned_cards_count' => function ($query) use ($userId) {
                $query->join('card_user', 'cards.card_id', '=', 'card_user.card_id')
                      ->where('card_user.user_id', $userId);
            }])
            ->get();
    
        // Fetch deck titles from DeckTitle table
        $deckTitles = DB::table('deck_titles')->get();
    
        // Loop through each deck to calculate the percentage and determine the title
        foreach ($decks as $deck) {
            $totalCards = $deck->total_cards_count;
            $scannedCards = $deck->scanned_cards_count;
    
            // Calculate percentage of scanned cards
            $percentage = $totalCards > 0 ? ($scannedCards / $totalCards) * 100 : 0;
    
            // Find the appropriate title based on the percentage
            $deck->title = $deckTitles->first(function ($title) use ($percentage) {
                return $percentage >= $title->min_percentage && $percentage <= $title->max_percentage;
            })->title ?? 'No Title'; // Default to 'No Title' if no match is found
        }
    
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
            Storage::disk('public')->put($imagePath, file_get_contents($image->getRealPath()));
    
            // Generate the image URL
            $img_url = Storage::disk('public')->url($imagePath);
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
        $allCards = $decks->cards;
        if ($cards->isEmpty()) {
            $cards = collect(); // If there are no cards, create an empty collection
        }
        return view('decks.show', compact('decks', 'cards','allCards'));
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
            $path = $image->storeAs('', $imagePath, 'public');
            $img_url = Storage::disk('public')->url($imagePath);

            // Delete the old image if it exists
            if ($old_img_url) {
                $oldImagePath = parse_url($old_img_url, PHP_URL_PATH);
                $relativeImagePath = 'deck-img/' . basename($oldImagePath);
                if (Storage::disk('public')->exists($relativeImagePath)) {
                    Storage::disk('public')->delete($relativeImagePath);
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

        return redirect()->route('decks.index')->with('editSuccess', 'Deck edited');
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
                if (Storage::disk('public')->exists($relativeImagePath)) {
                    Storage::disk('public')->delete($relativeImagePath);
                } else {
                    \Log::info('Old image not found: ' . $relativeImagePath);
                }
            }
            $deck->delete();
        
            // Check if there are decks on the current page
            $page = $request->input('page', 1);
            $decks = Deck::paginate(4, ['*'], 'page', $page);
        
            if ($decks->isEmpty() && $page > 1) {
                return redirect()->route('decks.index', ['page' => $page - 1])->with('deleteSuccess', 'Deck and all its cards deleted successfully!');
            }
        
            return redirect()->route('decks.index', ['page' => $page])->with('deleteSuccess', 'Deck and all its cards deleted successfully!');
        }
    }
    public function search(Request $request)
    {
        $query = $request->input('query');
        $decks = Deck::where('deck_name', 'LIKE', "%{$query}%")->paginate(4);
        return view('decks.index', compact('decks'));
    }

    public function downloadPDF($deckId)
    {
        try {
            $decks = Deck::with('cards')->findOrFail($deckId);
            $cards = $decks->cards;
        
            foreach ($cards as $card) {
                $relativePath = parse_url($card->qr_code_path, PHP_URL_PATH); 
                $path = public_path($relativePath); // Adjust as per file location
            
                if (file_exists($path)) {
                    $type = pathinfo($path, PATHINFO_EXTENSION);
                    $data = file_get_contents($path);
                    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                    $card->qr_code_base64 = $base64;
                } else {
                    $card->qr_code_base64 = null;
                }
            }
            
            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $dompdf = new Dompdf($options);
        
            $html = view('decks.pdf-template', compact('cards', 'decks'))->render();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
        
            $filename = preg_replace('/[^A-Za-z0-9_\-]/', '_', $decks->deck_name) . '_qr_codes.pdf';
        
            return $dompdf->stream($filename);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to generate PDF: ' . $e->getMessage());
        }
    }
}
