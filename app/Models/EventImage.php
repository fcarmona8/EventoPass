<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventImage extends Model
{
    use HasFactory;

    protected $fillable = ['event_id', 'image_id', 'is_main'];

    // Relación con el modelo Event
    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}
