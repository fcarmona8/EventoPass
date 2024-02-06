<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Venue extends Model
{

    use HasFactory;
    
    protected $fillable = ['province', 'city', 'postal_code', 'venue_name', 'capacity', 'user_id'];

    // Relación con el modelo User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function events() { return $this->hasMany(Event::class); }

    public function scopeSearchByVenueId($query, $venueId)
    {
        return $query->where('id', $venueId);
    }



}

