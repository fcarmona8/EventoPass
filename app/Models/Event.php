<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;

class Event extends Model
{
    use HasFactory;
    
    protected $fillable = ['name', 'description', 'category_id', 'venue_id', 'main_image', 'event_date'];

    protected $dates = ['event_date'];

    public function category() {
        Log::info('Llamada al método category', ['event_id' => $this->id]);
        return $this->belongsTo(Category::class); 
    }

    public function venue() {
        Log::info('Llamada al método venue', ['event_id' => $this->id]);
        return $this->belongsTo(Venue::class); 
    }

    public function sessions()
    {
        Log::info('Llamada al método sessions', ['event_id' => $this->id]);
        return $this->hasMany(Session::class);
    }

    public function lowestTicketPrice()
    {
        $startTime = microtime(true);

        try {
            $lowestPrice = $this->sessions()
                                ->join('purchases', 'sessions.id', '=', 'purchases.session_id')
                                ->join('tickets', 'purchases.id', '=', 'tickets.purchase_id')
                                ->join('ticket_types', 'tickets.type_id', '=', 'ticket_types.id')
                                ->min('ticket_types.price');

            $endTime = microtime(true);
            Log::info('Tiempo de ejecución de lowestTicketPrice', ['duration' => $endTime - $startTime]);

            return $lowestPrice;
        } catch (\Exception $e) {
            Log::error('Error en la consulta de precio más bajo en el modelo Event', [
                'event_id' => $this->id,
                'error_message' => $e->getMessage()
            ]);
        }
    }

    public function scopeNameEvent(Builder $query, string $name){
        try {
            return $query->where('name', 'ILIKE', "%{$name}%");
        } catch (\Exception $e) {
            Log::error('Error en la función scopeNameEvent en el modelo Event', [
                'name' => $name,
                'error_message' => $e->getMessage()
            ]);
        }
    }
}
