<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;
    
    protected $fillable = ['name'];
    public function events() { return $this->hasMany(Event::class); }

    public function eventsWithLimit($events)
    {
        return $events->where('hidden', false)->take(env('SHOW_EVENTS_LIMIT', 6));
    }
}
