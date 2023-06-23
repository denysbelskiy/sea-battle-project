<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'user_id'];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function joinPlayerTwo($user2_id) {
        // store user_in in the table games field user2_id
        $this->user2_id = $user2_id;
        $this->save();
    }
}
