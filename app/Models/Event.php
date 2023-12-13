<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{
    use HasFactory;
    
    protected $fillable = ['name', 'description', 'category_id', 'venue_id', 'main_image', 'event_date'];

    protected $dates = ['event_date'];

    public function category() { 
        return $this->belongsTo(Category::class); 
    }

    public function venue() { 
        return $this->belongsTo(Venue::class); 
    }
}
