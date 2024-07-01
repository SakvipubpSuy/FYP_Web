<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    use HasFactory;
    protected $primaryKey = 'card_id';
    protected $fillable = [
        'deck_id',
        'card_tier_id',
        'card_name',
        'card_description',
        'card_version',
    ];
    protected $attributes = [
        'card_version' => 1,
    ];
    public function deck()
    {
        return $this->belongsTo(Deck::class, 'deck_id', 'deck_id'); // Specify both the foreign key and the owner key
    }
    public function users()
    {
        return $this->belongsToMany(User::class, 'card_user', 'card_id', 'user_id');
    }
    public function cardTier()
    {
        return $this->belongsTo(CardTier::class, 'card_tier_id', 'card_tier_id');
    }
}
