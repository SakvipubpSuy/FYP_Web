<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
     protected $table = 'password_resets';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $primaryKey = 'email'; // Set email as the primary key
    public $incrementing = false; // Disable auto-increment since 'email' is not an integer
    protected $keyType = 'string'; // Set the key type to string
    protected $fillable = [
        'email',
        'code',
        'created_at',
    ];

    /**
     * Get the user associated with the reset code.
     */
    public function users()
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }
}
