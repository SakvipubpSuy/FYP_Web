<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $primaryKey = 'question_id';

    protected $fillable = ['card_id', 'question'];

    public function answers()
    {
        return $this->hasMany(Answer::class, 'question_id', 'question_id');
    }

    public function card()
    {
        return $this->belongsTo(Card::class, 'card_id', 'card_id');
    }
}