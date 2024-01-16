<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Session extends Model
{
    use HasFactory;

    protected $fillable = ['event_id', 'date_time', 'online_sale_end_time', 'ticket_quantity', 'named_tickets', 'max_capacity'];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }


    public function scopeEventSessions(Builder $query, $event_id){
        try {
            
            return $query->where('event_id', '=', "$event_id");
        } catch (\Exception $e) {
            Log::error('Error en la función scopeUserEvent en el modelo Event', [
                'event_id' => $event_id,
                'error_message' => $e->getMessage()
            ]);
        }
    }

}