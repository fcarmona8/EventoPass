<?php

namespace App\Models;

use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{
    use HasFactory;
    
    protected $fillable = ['name', 'description', 'category_id', 'venue_id', 'main_image_id', 'event_date', 'max_capacity', 'video_link', 'hidden', 'nominal', 'user_id'];

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

    public function tickets()
    {
        return $this->hasManyThrough(Ticket::class, Session::class);
    }

    public function images()
    {
        return $this->hasMany(EventImage::class);
    }

    public function comentarios()
    {
        return $this->hasMany(Comentario::class);
    }

    public function obtenerComentarios()
    {
        return $this->comentarios()->orderBy('created_at', 'desc')->get();
    }
    
    public function lowestTicketPrice()
    {
        try {
            Log::info('Iniciando cálculo del precio más bajo para el evento', ['event_id' => $this->id]);

            // Obtén las sesiones para este evento
            $sessionIds = $this->sessions()->pluck('id');

            // Obtén el precio más bajo entre los tickets de esas sesiones
            $lowestPrice = Ticket::whereIn('session_id', $sessionIds)
                                ->join('ticket_types', 'tickets.type_id', '=', 'ticket_types.id')
                                ->min('ticket_types.price');

            Log::info('Precio más bajo encontrado', ['event_id' => $this->id, 'lowestPrice' => $lowestPrice]);

            return $lowestPrice;
        } catch (\Exception $e) {
            Log::error('Error en la consulta de precio más bajo en el modelo Event', [
                'event_id' => $this->id,
                'error_message' => $e->getMessage()
            ]);
            return null;
        }
    }

    public static function eventosDisponibles() {
        $fechaActual = now();

        return Event::with('category', 'venue', 'sessions.tickets')
            ->whereHas('sessions', function ($query) use ($fechaActual) {
                $query->where('date_time', '>', $fechaActual)
                      ->where('closed', false);
            });
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

    public function scopeUserEvent(Builder $query, $user_id){
        try {
            
            return $query->where('user_id', '=', "$user_id");
        } catch (\Exception $e) {
            Log::error('Error en la función scopeUserEvent en el modelo Event', [
                'user_id' => $user_id,
                'error_message' => $e->getMessage()
            ]);
        }
    }

    public function scopeCategoryEvent(Builder $query, int $category){
        try {
            $query->where('category', 'LIKE', "{$category}");

            return $query::orderby("name")->take(5);
        } catch (\Exception $e) {
            Log::error('Error en la función scopeCategoryEvent en el modelo Event', [
                'category' => $category,
                'error_message' => $e->getMessage()
            ]);
        }
    }

    public function optimizedImageSmallUrl()
    {
        if (!$this->main_image_id) {
            return null;
        }
        return "/api/V1/optimized-images/{$this->main_image_id}/small";
    }

    public function optimizedImageMediumUrl()
    {
        if (!$this->main_image_id) {
            return null;
        }
        return "/api/V1/optimized-images/{$this->main_image_id}/medium";
    }

    public function optimizedImageLargeUrl()
    {
        if (!$this->main_image_id) {
            return null;
        }
        return "/api/V1/optimized-images/{$this->main_image_id}/large";
    }

    
}
