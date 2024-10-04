<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Trade;
use App\Models\Card;


class TradeController extends Controller
{
    public function sendTradeRequest(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|integer',
        ]);
    
        $initiator_id = auth()->id();
    
        if (!$initiator_id) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    
        $trade = Trade::create([
            'initiator_id' => $initiator_id,
            'receiver_id' => $request->input('receiver_id'),
            'initiator_card_id' => $request->input('initiator_card_id'),
        ]);
    
        return response()->json(['message' => 'Trade created successfully', 'trade' => $trade], 201);
    }
    
    public function countTradeRequest(Request $request)
    {
        $userId = auth()->id();
    
        if (!$userId) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    
        $type = $request->input('type');
    
        if (!$type) {
            return response()->json(['message' => 'Type is required'], 400);
        }
    
        $count = 0;
    
        switch ($type) {
            case 'incoming':
                $count = Trade::where('receiver_id', $userId)->where('status', 'pending')->count();
                break;
            case 'outgoing':
                $count = Trade::where('initiator_id', $userId)->where('status', 'pending')->count();
                break;
            case 'pending_approval':
                $count = Trade::where('receiver_id', $userId)->where('status', 'accepted')->count();
                break;
            default:
                return response()->json(['message' => 'Invalid type'], 400);
        }
    
        return response()->json(['count' => $count], 200);
    }
    public function getTradeRequest(Request $request)
    {
        $userId = auth()->id();
    
        if (!$userId) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    
        $incomingTrades = Trade::with('initiator:id,name')
            ->where('receiver_id', $userId)
            ->where('status', 'pending')
            ->get()
            ->map(function ($trade) {
                return [
                    'trade_id' => $trade->trade_id,
                    'initiator_id' => $trade->initiator_id,
                    'initiator_name' => $trade->initiator->name,
                    'initiator_card_name' => $trade->initiatorCard->card_name,
                    'receiver_id' => $trade->receiver_id,
                    'status' => $trade->status,
                    'created_at' => $trade->created_at,
                    'updated_at' => $trade->updated_at,
                ];
            });
    
        $outgoingTrades = Trade::with('receiver:id,name')
            ->where('initiator_id', $userId)
            ->where('status', 'pending')
            ->get()
            ->map(function ($trade) {
                return [
                    'trade_id' => $trade->trade_id,
                    'initiator_id' => $trade->initiator_id,
                    'initiator_card_name' => $trade->initiatorCard->card_name,
                    'receiver_id' => $trade->receiver_id,
                    'receiver_name' => $trade->receiver->name,
                    'status' => $trade->status,
                    'created_at' => $trade->created_at,
                    'updated_at' => $trade->updated_at,
                ];
            });
        
        $pendingApprovalTrades = Trade::with('receiver:id,name')
            ->where('initiator_id', $userId)
            ->where('status', 'accepted')
            ->get()
            ->map(function ($trade) {
                return [
                    'trade_id' => $trade->trade_id,
                    'initiator_id' => $trade->initiator_id,
                    'initiator_card_name' => $trade->initiatorCard->card_name,
                    'receiver_id' => $trade->receiver_id,
                    'receiver_name' => $trade->receiver->name,
                    'receiver_card_name' => $trade->receiverCard->card_name,
                    'status' => $trade->status,
                    'created_at' => $trade->created_at,
                    'updated_at' => $trade->updated_at,
                ];
            });

        $completedTrades = Trade::with('receiver:id,name')
            ->where(function ($query) use ($userId) {
                $query->where('initiator_id', $userId)
                      ->orWhere('receiver_id', $userId);
            })
            ->where('status', 'completed')
            ->get()
            ->map(function ($trade) {
                return [
                    'trade_id' => $trade->trade_id,
                    'initiator_id' => $trade->initiator_id,
                    'initiator_card_name' => $trade->initiatorCard->card_name,
                    'receiver_id' => $trade->receiver_id,
                    'receiver_name' => $trade->receiver->name,
                    'receiver_card_name' => $trade->receiverCard->card_name,
                    'status' => $trade->status,
                    'created_at' => $trade->created_at,
                    'updated_at' => $trade->updated_at,
                ];
            });
    
        return response()->json([
            'incoming_trades' => $incomingTrades,
            'outgoing_trades' => $outgoingTrades,
            'pending_approval_trades' => $pendingApprovalTrades,
            'completed_trades' => $completedTrades,
        ], 200);
    }

    // public function acceptTradeRequest($trade_id){
    //     $userId = auth()->id();
    
    //     if (!$userId) {
    //         return response()->json(['message' => 'Unauthorized'], 401);
    //     }
    //     $trade = Trade::find($trade_id);
    //     if ($trade && $trade->receiver_id == $userId && $trade->status == 'pending') {
    //         $trade->status = 'accepted';
    //         $trade->save();
    //         return response()->json(['message' => 'Trade accepted'], 200);
    //     }
    //     return response()->json(['message' => 'Unauthorized or invalid trade'], 403);
    // }
    public function acceptTradeRequest(Request $request, $trade_id) {
        $userId = auth()->id();
        $cardId = $request->input('receiver_card_id');  // Get the receiver's card ID
        if (!$userId) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    
        $trade = Trade::find($trade_id);
    
        if ($trade && $trade->receiver_id == $userId && $trade->status == 'pending') {
            if ($cardId) {
                $trade->status = 'accepted';
                $trade->receiver_card_id = $cardId; // Save the receiver's card ID
                $trade->save();
                return response()->json(['message' => 'Trade accepted'], 200);
            } else {
                return response()->json(['message' => 'Receiver card ID not provided'], 400);
            }
        }
    
        return response()->json(['message' => 'Unauthorized or invalid trade'], 403);
    }
    
    public function completeTradeRequest($trade_id) {
        $userId = auth()->id();
    
        if (!$userId) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    
        $trade = Trade::find($trade_id);
        if ($trade && $trade->initiator_id == $userId && $trade->status == 'accepted') {
            DB::beginTransaction(); // Start the transaction
    
            try {
                // Fetch the correct card IDs
                $initiatorCard = Card::find($trade->initiator_card_id);
                $receiverCard = Card::find($trade->receiver_card_id);
    
                // Log details for debugging
    
                if ($initiatorCard && $receiverCard) {
                    // Detach the cards from the current owners
                    $initiatorCard->users()->detach($trade->initiator_id);
                    $receiverCard->users()->detach($trade->receiver_id);
    
                    // Attach the cards to the new owners
                    $initiatorCard->users()->attach($trade->receiver_id);
                    $receiverCard->users()->attach($trade->initiator_id);
    
                    // Log the changes for debugging
                    \Log::info('Cards reassigned successfully.');
    
                    $trade->status = 'completed';
                    $trade->save();
    
                    DB::commit(); // Commit the transaction
    
                    return response()->json(['message' => 'Trade completed'], 200);
                } else {
                    DB::rollBack(); // Rollback the transaction if cards are invalid
                    return response()->json(['message' => 'Invalid card(s)'], 400);
                }
            } catch (\Exception $e) {
                DB::rollBack(); // Rollback the transaction on error
                \Log::error('Error completing trade:', ['error' => $e->getMessage()]);
                return response()->json(['message' => 'Failed to complete trade'], 500);
            }
        }
        return response()->json(['message' => 'Unauthorized or invalid trade'], 403);
    }
    
    public function denyTradeRequest($trade_id)
    {
        $userId = auth()->id();
    
        if (!$userId) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $trade = Trade::find($trade_id);
        if ($trade && $trade->receiver_id == $userId && $trade->status == 'pending') {
            $trade->delete();
            return response()->json(['message' => 'Trade denied'], 200);
        }
        return response()->json(['message' => 'Unauthorized or invalid trade'], 403);
    }

    public function cancelTradeRequest($trade_id)
    {
        $userId = auth()->id();
    
        if (!$userId) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $trade = Trade::find($trade_id);
        if ($trade && $trade->initiator_id == $userId && $trade->status == 'pending') {
            $trade->delete();
            return response()->json(['message' => 'Trade cancaled'], 200);
        }
        return response()->json(['message' => 'Unauthorized or invalid trade'], 403);
    }

    public function revertTradeRequest($trade_id)
    {
        $userId = auth()->id();
    
        if (!$userId) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    
        $trade = Trade::find($trade_id);
    
        if ($trade && $trade->initiator_id == $userId && $trade->status == 'accepted') {
            $trade->status = 'pending';
            $trade->receiver_card_id = null; // Clear the receiver's card
            $trade->save();
    
            return response()->json(['message' => 'Trade reverted to pending'], 200);
        }
    
        return response()->json(['message' => 'Unauthorized or invalid trade'], 403);
    }
}
