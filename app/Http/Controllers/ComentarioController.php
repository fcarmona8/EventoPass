<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comentario;
use Illuminate\Support\Facades\Log;

class ComentarioController extends Controller
{
    public function index() {
        return view('/tickets/crearComentario');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:25',
            'smileyRating' => 'required|integer|min:1|max:3',
            'reviewRating' => 'required|integer|min:1|max:5',
            'titulo' => 'required|string|max:50',
            'comentario' => 'required|string|max:300',
        ]);

        $event_id = 1;

        try {
            $comentario = new Comentario([
                'nombre' => $request->input('nombre'),
                'event_id' => 1,
                'smileyRating' => $request->input('smileyRating'),
                'puntuacion' => $request->input('reviewRating'),
                'titulo' => $request->input('titulo'),
                'comentario' => $request->input('comentario'),
            ]);
    
            $comentario->save();

            Log::debug('Nuevo comentario guardado correctamente', ['id: ' => $comentario->id]);

            return redirect('')->with('success', 'Comentario enviado con éxito.');

        } catch (\Exception $e) {

            Log::error('Error al guardar el comentario:', ['error_message' => $e->getMessage()]);
            return redirect('')->with('error', 'Error al procesar el comentario.');

        }
    
}

}
