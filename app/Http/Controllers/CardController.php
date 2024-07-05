<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use App\Models\Card;
use App\Models\Deck;
use App\Models\User;
use App\Models\Question;
use App\Models\Answer;
use App\Models\CardTier;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\ValidationException;

class CardController extends Controller
{

    //FOR API 
    public function getCardsByDeckID(Request $request,$deck_id)
    {
        // Fetch cards that belong to the specified deck
        $user = Auth::user();
        $cards = Card::where('deck_id', $deck_id)
                     ->whereHas('users', function ($query) use ($user) {
                         $query->where('users.id', $user->id);
                     })
                     ->with('cardTier') // Eager load the cardTier relationship
                     ->get();
        return response()->json($cards);
    }
    public function getCardByID($card_id)
    {
        // Fetch cards that belong to the specified deck
        $card = Card::with('cardTier')->find($card_id);

        // Check if the card exists
        if (!$card) {
            return response()->json(['message' => 'Card not found'], 404);
        }
    
        // Return the card as a JSON response
        return response()->json($card);
    }
    public function countUserTotalCards(Request $request){
        $user = Auth::user();
        $total = DB::table('card_user')
                ->where('user_id', $user->id)
                ->count();
        return response()->json(['total_cards' => $total]);
    }
    public function scanCard(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'card_id' => 'required|integer',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid input.'], 400);
        }
    
        $user = auth()->user();
        $card_id = $request->input('card_id');
    
        try {
            // Fetch card and its energy requirement
            $card = Card::findOrFail($card_id);
            $cardEnergyRequired = $card->cardTier->card_energy_required;
    
            // Check if the user has sufficient energy
            if ($user->energy < $cardEnergyRequired) {
                return response()->json(['message' => 'Not enough energy to scan this card.'], 400);
            }
    
            // Check if the card already exists in the pivot table for this user
            $exists = DB::table('card_user')
                        ->where('user_id', $user->id)
                        ->where('card_id', $card_id)
                        ->exists();
    
            if ($exists) {
                return response()->json(['message' => 'You have already scanned this card.'], 200);
            } else {
                // Use a transaction to attach the card and reduce energy atomically
                DB::transaction(function () use ($user, $card_id, $cardEnergyRequired) {
                    // Attach the card to the user
                    $user->cards()->attach($card_id);
    
                    // Reduce user's energy
                    $user->energy -= $cardEnergyRequired;
                    $user->save();
                });
    
                return response()->json(['message' => 'Card scanned successfully'], 200);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Card not found.'], 404);
        } catch (\Exception $e) {
            // General error handling
            return response()->json(['message' => 'An error occurred. Please try again later.'], 500);
        }
    }
    public function getQuests()
    {
        $user = auth()->user();
        $cards = $user->scannedCards; // Assuming you have a relationship defined
        $quests = [];
    
        foreach ($cards as $card) {
            $quests[] = [
                'question' => $card->quest->question,
                'answers' => $card->quest->answers->map(function ($answer) {
                    return [
                        'id' => $answer->id,
                        'answer' => $answer->answer,
                    ];
                }),
                'selectedAnswer' => null,
            ];
        }
    
        return response()->json($quests);
    }
    //FOR WEB 
    public function index()
    {   
        $cardTiers = CardTier::all();
        $cards = Card::paginate(4); 
        $decks = Deck::all();
        return view('cards.index', compact('cards','cardTiers','decks'));
    }

    public function create()
    {
        $cards = Card::all();
        $decks = Deck::all();
        $cardTiers = CardTier::all();
        $answers = [];
        return view('cards.create', compact('cards', 'decks','cardTiers','answers'));
    }

    public function store(Request $request)
    {
        // Debugging to see the request data
        // dd($request->all()); // Uncomment this line for debugging if needed
    
        $validatedData = $request->validate([
            'card_name' => 'required|string|max:255',
            'card_tier_id' => 'required|string|max:255',
            'deck_id' => 'required|exists:decks,deck_id',
            'card_description' => 'required|string',
            'question' => 'required|string',
            'answers.*' => 'required|min:2',
            'is_correct' => 'required|integer',
        ]);
    
        try {
            DB::transaction(function () use ($request) {
                $card = Card::create([
                    'card_name' => $request->card_name,
                    'card_description' => $request->card_description,
                    'card_tier_id' => $request->card_tier_id,
                    'deck_id' => $request->deck_id,
                ]);
    
                if ($request->question) {
                    $question = Question::create([
                        'card_id' => $card->card_id,
                        'question' => $request->question,
                    ]);
    
                    foreach ($request->answers as $index => $answer) {
                        Answer::create([
                            'question_id' => $question->question_id,
                            'answer' => $answer,
                            'is_correct' => $index == $request->is_correct ? 1 : 0,
                        ]);
                    }
                }
            });
    
            return redirect()->route('cards.create')->with('success', 'Card created successfully!');
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Card creation failed: ' . $e->getMessage());
            dd($e->getMessage());
    
            return redirect()->route('cards.create')->with('error', 'Card creation failed!');
        }
    }

    public function show(Card $card)
    {
        return view('cards.index', compact('card'));
    }

    public function editCard(Request $request,Card $card)
    {
        // Validate and edit card details without changing the version
        $validatedData = $request->validate([
            'card_name' => 'string|max:255',
            'card_description' => 'string',
            'card_tier_id' => 'exists:card_tiers,card_tier_id',
            'deck_id' => 'exists:decks,deck_id',
        ]);

        try {
            $card->update($validatedData);
        } catch (\Exception $e) {
            return redirect()->route('cards.index')->with('editError', 'Card edit failed!');
        }

        return redirect()->route('cards.index')->with('editSuccess', 'Card edit successfully.');
    }

    public function updateCard(Request $request, Card $card)
    {
        try {
            // Validate the incoming request
            $request->validate([
                'card_name' => 'string|max:255',
                'card_description' => 'required|string|max:255',
                'deck_id' => 'exists:decks,deck_id',
                'card_tier_id' => 'exists:card_tiers,card_tier_id',
            ]);

            // Find the existing card
            $existingCard = Card::findOrFail($card->card_id);

            // Create a new card entry for the old version
            $oldCard = $existingCard->replicate();
            $oldCard->card_version = $existingCard->card_version;
            $oldCard->parent_card_id = $existingCard->parent_card_id ?: $existingCard->card_id;
            $oldCard->save();

            // Update the existing card with new details and increment the version
            $existingCard->update([
                'card_name' => $request->input('card_name'),
                'card_description' => $request->input('card_description'),
                'deck_id' => $request->input('deck_id'),
                'card_tier_id' => $request->input('card_tier_id'),
                'card_version' => $existingCard->card_version + 1,
                'parent_card_id' => $oldCard->parent_card_id,
            ]);

            return redirect()->route('cards.index')->with('updateSuccess', 'Card updated successfully.');
        } catch (\Exception $e) {
            // Log the error for debugging purposes
            Log::error('Error updating card: ' . $e->getMessage());

            // Redirect back with an error message
            return redirect()->back()->with('updateError', 'There was an error updating the card. Please try again.');
        }
    }

    public function destroy(Request $request,$card_id)
    {
        $card = Card::find($card_id);

        if (!$card) {
            return redirect()->route('cards.index')->with('deleteError', 'Card not found!');
        }
    
        $card->delete();
    
        // Get the current page number
        $page = $request->input('page', 1);
    
        // Retrieve the cards after deletion
        $cards = Card::paginate(4, ['*'], 'page', $page);
    
        // If the current page is empty and it's not the first page, redirect to the previous page
        if ($cards->isEmpty() && $page > 1) {
            return redirect()->route('cards.index', ['page' => $page - 1])->with('deleteSuccess', 'Card has been deleted successfully!');
        }
    
        return redirect()->route('cards.index', ['page' => $page])->with('deleteSuccess', 'Card has been deleted successfully!');
    }

    public function generateQrCode($card_id)
    {

        // Get the card information from the database
        // $card = Card::find($card_id);
        // Create QR code
        try {
            $writer = new PngWriter();
            $qrCode = QrCode::create($card_id)
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(ErrorCorrectionLevel::Low)
            ->setSize(500)
            ->setMargin(5)
            ->setRoundBlockSizeMode(RoundBlockSizeMode::Margin)
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));
            
            $result = $writer->write($qrCode);
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
            // Output the QR code as a PNG image
        return response($result->getString(), 200, [
            'Content-Type' => 'image/png',
            'Content-Disposition' => 'inline; filename="qrcode.png"'
        ]);
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        $decks = Deck::all();
        $cardTiers = CardTier::all();
        $cards = Card::where('card_name', 'LIKE', "%{$query}%")->paginate(4);
        return view('cards.index', compact('cards','decks','cardTiers'));
    }
}
