<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deck extends Model
{
    use HasFactory;
    protected $primaryKey = 'deck_id';
    protected $fillable = [
        'deck_name',
        'deck_description',
        'img_url',
    ];
    public function cards()
    {
        return $this->hasMany(Card::class, 'deck_id', 'deck_id');
    }
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($deck) {
            $deck->cards()->delete();
        });
    }
    public function scannedCards()
    {
        return $this->belongsToMany(Card::class, 'card_user', 'card_id', 'card_id');
    }
}
