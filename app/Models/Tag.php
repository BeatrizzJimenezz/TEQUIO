<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    // Relation with events
    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_tag');
    }

    // Relation with users
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_interests')
            ->withPivot('score')
            ->withTimestamps();
    }
}