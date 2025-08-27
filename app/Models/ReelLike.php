<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReelLike extends Model
{
    use HasFactory;

    protected $table = 'reel_likes';
    protected $fillable = [
        'user_id',
        'reel_id',
    ];

    // Relation with Reel
    public function reel()
    {
        return $this->belongsTo(Reel::class, 'reel_id');
    }

    // Relation with User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
