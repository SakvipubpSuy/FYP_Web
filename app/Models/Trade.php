<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trade extends Model
{
    use HasFactory;

    protected $primaryKey = 'trade_id';
    protected $fillable = [
        'initiator_id',
        'receiver_id',
        'initiator_card_id',
        'receiver_card_id',
        'status',
    ];

    public function initiator()
    {
        return $this->belongsTo(User::class, 'initiator_id','id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id', 'id');
    }
}

