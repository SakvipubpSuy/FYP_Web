<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeckTitle extends Model
{
    use HasFactory;

    protected $primaryKey = 'deck_titles_id';

    protected $fillable = [
    'min_percentage', 
    'max_percentage', 
    'title'
    ];

}
