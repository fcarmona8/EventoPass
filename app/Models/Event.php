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

    public function sessions()
    {
        return $this->hasMany(Session::class);
    }


    public function lowestTicketPrice()
    {
        return $this->sessions()
                    ->join('purchases', 'sessions.id', '=', 'purchases.session_id')
                    ->join('tickets', 'purchases.id', '=', 'tickets.purchase_id')
                    ->join('ticket_types', 'tickets.type_id', '=', 'ticket_types.id')
                    ->min('ticket_types.price');
    }

}