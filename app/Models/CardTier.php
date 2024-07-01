<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CardTier extends Model
{
    use HasFactory;
    protected $primaryKey = 'card_tier_id';
    protected $fillable = [
        'card_tier_name',
        'card_XP',
        'card_energy_required',
    ];
    public function cards(){
        return $this->hasMany(Card::class, 'card_tier_id', 'card_tier_id');
    }
}
