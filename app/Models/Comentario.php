<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Comentario extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'event_id',
        'smileyRating',
        'puntuacion',
        'titulo',
        'comentario',
    ];

    // Relación con el modelo Evento
    public function evento()
    {
        return $this->belongsTo(Evento::class);
    }

    // Función para obtener todos los comentarios de un evento específico
    public static function comentariosPorEvento($event_id)
    {
        try {
            return Comentario::where('evento_id', $event_id)->get();
        } catch (\Exception $e) {
            Log::error('Error en la función comentariosPorEvento', [
                'event_id' => $event_id,
                'error_message' => $e->getMessage()
            ]);
        }
    }

}
