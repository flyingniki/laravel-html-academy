<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Film extends Model
{
    use HasFactory;

    public function comments()
    {
        $this->hasMany(Comment::class);
    }

    public function genres()
    {
        $this->belongsToMany(Genre::class);
    }
}
