<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trade;

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
        ]);
    
        return response()->json(['message' => 'Trade created successfully', 'trade' => $trade], 201);
    }
    
    public function countTradeRequest(Request $request)
    {
        $reciever_id = auth()->id();

        $trades = Trade::where('receiver_id', $reciever_id)->where('status', 'pending')->count();

        return response()->json(['total_trade_request' => $trades], 200);
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
                    'receiver_id' => $trade->receiver_id,
                    'receiver_name' => $trade->receiver->name,
                    'status' => $trade->status,
                    'created_at' => $trade->created_at,
                    'updated_at' => $trade->updated_at,
                ];
            });
    
        return response()->json([
            'incoming_trades' => $incomingTrades,
            'outgoing_trades' => $outgoingTrades,
        ], 200);
    }

    public function acceptTradeRequest($trade_id){
        $userId = auth()->id();
    
        if (!$userId) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $trade = Trade::find($trade_id);
        if ($trade && $trade->receiver_id == $userId && $trade->status == 'pending') {
            $trade->status = 'accepted';
            $trade->save();
            return response()->json(['message' => 'Trade accepted'], 200);
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
}
