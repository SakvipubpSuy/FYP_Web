<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
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
    public function decrypt($encrypted)
    {
        $decrypted = Crypt::decryptString($encrypted);
        return response()->json($decrypted);
    }
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
        // Fetch cards 
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
            'card_id' => 'required|string',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid input.'], 400);
        }
    
        $user = auth()->user();
        $card_id = $request->input('card_id');
        $decrypted_card_id = Crypt::decryptString($card_id);
    
        try {
            // Fetch card and its energy requirement
            $card = Card::findOrFail($decrypted_card_id);
            $cardEnergyRequired = $card->cardTier->card_energy_required;
            $cardRPRequired = $card->cardTier->card_RP_required;
    
            // Check if the user has sufficient energy
            if ($user->energy < $cardEnergyRequired) {
                return response()->json(['message' => 'Not enough energy to scan this card.'], 400);
            }
            
             // Fetch the deck the card belongs to
            $deck = $card->deck;
            
            // Calculate total and scanned cards in the deck for the user
            $totalCardsInDeck = $deck->cards()->count();
            $scannedCardsInDeck = $deck->cards()->whereHas('users', function ($query) use ($user) {
                $query->where('users.id', $user->id);
            })->count();

             // Calculate percentage of cards scanned by the user
            $scannedPercentage = ($scannedCardsInDeck / $totalCardsInDeck) * 100;
                    // Check if the user meets the percentage requirement for scanning
            if ($scannedPercentage < $cardRPRequired) {
                return response()->json([
                    'message' => "You need to scan at least $cardRPRequired% of this deck to scan this card."
                ], 400);
            }

            // Check if the card already exists in the pivot table for this user
            $exists = DB::table('card_user')
                        ->where('user_id', $user->id)
                        ->where('card_id', $decrypted_card_id)
                        ->exists();
    
            if ($exists) {
                return response()->json(['message' => 'You have already scanned this card.'], 200);
            } else {
                // Use a transaction to attach the card and reduce energy atomically
                DB::transaction(function () use ($user, $decrypted_card_id, $cardEnergyRequired) {
                    // Attach the card to the user
                    $user->cards()->attach($decrypted_card_id);
    
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
        $cards = $user->cards()->with('question.answers')->get();
        $quests = [];
    
        foreach ($cards as $card) {
            if ($card->question) {
                $quests[] = [
                    'question_id' => $card->question->question_id,
                    'question' => $card->question->question,
                    'answers' => $card->question->answers->map(function ($answer) {
                        return [
                            'answer_id' => $answer->answer_id,
                            'answer' => $answer->answer,
                            'is_correct' => $answer->is_correct,
                        ];
                    }),
                ];
            }
        }
    
        return response()->json($quests);
    }
    public function submitQuest(Request $request)
    {
        $user = auth()->user();
        $questionId = $request->input('question_id');
        $answerId = $request->input('answer_id');
    
        $question = Question::find($questionId);
        $answer = Answer::find($answerId);
    
        if (!$question || !$answer) {
            return response()->json(['error' => 'Invalid question or answer'], 400);
        }
    
        // Fetch the card associated with the question
        $card = $question->card;
    
        if (!$card) {
            return response()->json(['error' => 'Question is not associated with any card'], 400);
        }
    
        // Fetch the card tier associated with the card
        $cardTier = $card->cardTier;
    
        if (!$cardTier) {
            return response()->json(['error' => 'Card is not associated with any card tier'], 400);
        }
    
        $energyReward = $cardTier->card_energy_required; // Adjust this to match the field name in your database
        $energyCap = 160;
    
        if ($answer->is_correct) {
            // Reward user with energy associated with the card tier, ensuring it doesn't exceed the cap
            $newEnergy = $user->energy + $energyReward;
            $user->energy = min($newEnergy, $energyCap);
            $user->save();
    
            return response()->json(['message' => 'Correct answer!', 'energy' => $user->energy], 200);
        } else {
            // Deduct energy for wrong answer, ensuring it doesn't go below zero
            $user->energy = max(0, $user->energy - $energyReward);
            $user->save();
            return response()->json(['message' => 'Wrong answer!', 'energy' => $user->energy]);
        }
    }
    

    //////////////////////////////////////////////

    //////////////////////////////////////////////
    //FOR WEB 
    public function index()
    {   
        $cards = Card::with('question.answers')->paginate(4);
        $cardTiers = CardTier::all();
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
        // dd($request->all()); // Uncomment this line for debugging if needed
    
        $validatedData = $request->validate([
            'card_name' => 'required|string|max:255|unique:cards,card_name',
            'card_tier_id' => 'required|string|max:255',
            'deck_id' => 'required|exists:decks,deck_id',
            'card_description' => 'required|string',
            'question' => 'required|string',
            'answers.*' => 'required|min:2',
            'is_correct' => 'required|integer',
            'card_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', 
        ]);
        $img_url = null;
        // Check if an image is uploaded
        if ($request->hasFile('card_image')) {
            $image = $request->file('card_image');
            $cardname = $request->card_name;
            $timestamp = time();
            // $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $image->getClientOriginalExtension();
            $imageName = $cardname . '_' . $timestamp . '.' . $extension;
            $imagePath = 'card-img/' . $imageName; //'card-img/' . $imageName to specify the directory or folder later on
    
            // Store the image using the storage disk
            Storage::disk('public')->put($imagePath, file_get_contents($image->getRealPath()));
    
            // Generate the image URL
            $img_url = Storage::disk('public')->url($imagePath);
        }

        try {
            DB::transaction(function () use ($request, $img_url) {
                $card = Card::create([
                    'card_name' => $request->card_name,
                    'card_description' => $request->card_description,
                    'card_tier_id' => $request->card_tier_id,
                    'deck_id' => $request->deck_id,
                    'img_url' => $img_url,
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

                // Generate and save QR code
                $qrCodePath = $this->generateAndStoreQrCode($card->card_id);
                $card->update(['qr_code_path' => $qrCodePath]);
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

    public function editCard(Request $request, Card $card)
    {
        // Validate the input
        $validatedData = $request->validate([
            'card_name' => 'string|max:255',
            'card_description' => 'string',
            'card_tier_id' => 'exists:card_tiers,card_tier_id',
            'deck_id' => 'exists:decks,deck_id',
            'card_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'edit_question' => 'nullable|string',  // Make sure question is coming from 'question'
            'edit_answers.*' => 'string|min:2',  // Validate individual answers
            'is_correct' => 'nullable|integer',  // Correct answer index
        ]);

        // Handle image upload if present
        $old_img_url = $card->img_url;
        $img_url = $old_img_url;
        if ($request->hasFile('card_image')) {
            $image = $request->file('card_image');
            $cardname = $request->card_name;
            $timestamp = time();
            $extension = $image->getClientOriginalExtension();
            $imageName = $cardname . '_' . $timestamp . '.' . $extension;
            $imagePath = 'card-img/' . $imageName;
            // Store the new image
            $path = $image->storeAs('', $imagePath, 'public');
            $img_url = Storage::disk('public')->url($imagePath);

            // Delete old image if exists
            if ($old_img_url) {
                $oldImagePath = parse_url($old_img_url, PHP_URL_PATH);
                $relativeImagePath = 'card-img/' . basename($oldImagePath);
                if (Storage::disk('public')->exists($relativeImagePath)) {
                    Storage::disk('public')->delete($relativeImagePath);
                } else {
                    \Log::info('Old image not found: ' . $relativeImagePath);
                }
            }
        }

        try {
            // Update the card's basic details
            $card->update(array_merge($validatedData, ['img_url' => $img_url]));

            // Check if the question is being updated
        if ($request->filled('edit_question')) {
            $question = Question::where('card_id', $card->card_id)->first();

            // If question exists, update it; otherwise, create a new one
            if ($question) {
                $question->update([
                    'question' => $request->edit_question,
                ]);
            } else {
                $question = Question::create([
                    'card_id' => $card->card_id, // Link question to the new card version
                    'question' => $request->edit_question,
                ]);
            }

            // Delete previous answers associated with the question (if updating)
            $question->answers()->delete();

            // Add new answers
            foreach ($validatedData['edit_answers'] as $index => $answerText) {
                $isCorrect = $index == $validatedData['is_correct']; // Compare index with is_correct value

                // Create a new answer for the question
                $question->answers()->create([
                    'answer' => $answerText,
                    'is_correct' => $isCorrect ? 1 : 0, // Mark the correct answer
                ]);
            }
        }

        return redirect()->route('cards.index')->with('editSuccess', 'Card edited successfully.');
    } catch (\Exception $e) {
        \Log::error('Card edit failed: ' . $e->getMessage());
        return redirect()->route('cards.index')->with('editError', 'Card edit failed!');
    }
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
                'card_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'update_question' => 'required|string', // Allow question update
                'update_answers.*' => 'required|string|min:2', // Validate answers
                'is_correct' => 'required|integer', // Correct answer index
            ]);
    
            // Find the existing card
            $existingCard = Card::findOrFail($card->card_id);

            // Check if the card is the latest version (parent_card_id must be null)
            if (!is_null($existingCard->parent_card_id)) {
                return redirect()->back()->with('updateError', 'You can only update the latest version of the card.');
            }
    
            // If a new image is uploaded, handle the upload and update the img_url
            if ($request->hasFile('card_image')) {
                $image = $request->file('card_image');
                $cardname = $request->card_name;
                $timestamp = time();
                $extension = $image->getClientOriginalExtension();
                $imageName = $cardname . '_' . $timestamp . '.' . $extension;
                $imagePath = 'card-img/' . $imageName;
    
                // Store the new image
                $path = $image->storeAs('', $imagePath, 'public');
                $img_url = Storage::disk('public')->url($imagePath);
    
                // Update the existing card's img_url
                $existingCard->img_url = $img_url;
            }

            // Create a new card entry for the old version before updating
            $oldCard = $existingCard->replicate();
            $oldCard->card_version = $existingCard->card_version;
            $oldCard->parent_card_id = $existingCard->parent_card_id ?: $existingCard->card_id;
            $oldCard->save();
    
            // Replicate the old question and its answers to associate them with the old card version
            $oldQuestion = Question::where('card_id', $existingCard->card_id)->first();
            if ($oldQuestion) {
                $replicatedQuestion = $oldQuestion->replicate();
                $replicatedQuestion->card_id = $oldCard->card_id; // Link to the old version
                $replicatedQuestion->save();
    
                // Replicate the answers associated with the old question
                $oldAnswers = Answer::where('question_id', $oldQuestion->question_id)->get();
                foreach ($oldAnswers as $oldAnswer) {
                    $replicatedAnswer = $oldAnswer->replicate();
                    $replicatedAnswer->question_id = $replicatedQuestion->question_id; // Link to the replicated question
                    $replicatedAnswer->save();
                }
            }
    
            // Update the existing card with new details and increment the version
            $existingCard->update([
                'card_name' => $request->input('card_name'),
                'card_description' => $request->input('card_description'),
                'deck_id' => $request->input('deck_id'),
                'card_tier_id' => $request->input('card_tier_id'),
                'card_version' => $existingCard->card_version + 1, //latest version +1
                'parent_card_id' => $oldCard->parent_card_id,
            ]);
    
            // Handle question update if present
            if ($request->has('update_question')) {
                // Check if the card already has a question linked to it
                $question = Question::where('card_id', $existingCard->card_id)->first();
                
                // If question exists, update it; otherwise, create a new one
                if ($question) {
                    $question->update([
                        'question' => $request->update_question,
                    ]);
                } else {
                    $question = Question::create([
                        'card_id' => $existingCard->card_id, // Link question to the new card version
                        'question' => $request->update_question,
                    ]);
                }
    
                if ($request->has('update_answers')) {
                    // First delete existing answers to ensure a clean slate
                    Answer::where('question_id', $question->question_id)->delete();
                
                    // Create new answers
                    foreach ($request->update_answers as $index => $answer) {
                        Answer::create([
                            'question_id' => $question->question_id,
                            'answer' => $answer,
                            // Use the exact value from $request->is_correct to check if the current answer is correct
                            'is_correct' => ($index == $request->is_correct) ? 1 : 0,
                        ]);
                    }
                }
            }
    
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
        $old_img_url = $card->img_url;
        if ($old_img_url) {
            $oldImagePath = parse_url($old_img_url, PHP_URL_PATH);
            $relativeImagePath = 'card-img/' . basename($oldImagePath);
            if (Storage::disk('public')->exists($relativeImagePath)) {
                Storage::disk('public')->delete($relativeImagePath);
            } else {
                \Log::info('Old image not found: ' . $relativeImagePath);
            }
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

    public function generateAndStoreQrCode($card_id)
    {
        try {
            // Fetch the card from the database
            $card = Card::findOrFail($card_id);
    
            // Encrypt the card ID
            $encryptedCardId = Crypt::encryptString($card_id);
    
            // Create the QR code
            $writer = new PngWriter();
            $qrCode = QrCode::create($encryptedCardId)
                ->setEncoding(new Encoding('UTF-8'))
                ->setErrorCorrectionLevel(ErrorCorrectionLevel::Low)
                ->setSize(500)
                ->setMargin(5)
                ->setRoundBlockSizeMode(RoundBlockSizeMode::Margin)
                ->setForegroundColor(new Color(0, 0, 0))
                ->setBackgroundColor(new Color(255, 255, 255));
    
            $result = $writer->write($qrCode);
    
            // Sanitize the card name to remove or replace any special characters
            $sanitizedCardName = preg_replace('/[^A-Za-z0-9\-]/', '_', $card->card_name);
    
            // Generate a unique filename for the QR code using both the card name and ID
            $timestamp = time();
            $qrCodeName = 'qrcode_' . $sanitizedCardName . '_' . $card_id . '_' . $timestamp . '.png';
            $qrCodePath = 'qr-codes/' . $qrCodeName;
    
            // Store the QR code in the specified storage disk
            Storage::disk('public')->put($qrCodePath, $result->getString());
    
            // Optionally return the full URL instead of just the path
            return Storage::disk('public')->url($qrCodePath);
        } catch (\Exception $e) {
            // Handle the exception (log it, return a response, etc.)
            \Log::error('QR code generation failed: ' . $e->getMessage());
            return response()->json(['message' => 'QR code generation failed'], 500);
        }
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
