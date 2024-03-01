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

}
