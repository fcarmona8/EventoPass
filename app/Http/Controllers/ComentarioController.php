<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comentario;
use Illuminate\Support\Facades\Log;

class ComentarioController extends Controller
{
    public function index($token, $compraId, $eventoId) {

        $tokenData = $compraId . '_' . $eventoId;
        $tokenGenerado = hash('sha256', $tokenData);
        $tokenGenerado = substr($tokenGenerado, 0, 32);

        if ($tokenGenerado === $token) {

            // Dades per a les metadades dinàmiques
            $metaData = [
                'title' => 'Enviar un Comentari - EventoPass | Comparteix la Teva Opinió',
                'description' => 'Deixa un comentari sobre la teva experiència en l\'esdeveniment. La teva opinió és important per a nosaltres i ajuda a millorar els nostres serveis.',
                'keywords' => 'EventoPass, enviar comentari, valoració d\'esdeveniments, opinió sobre esdeveniments, feedback d\'usuari',
                'ogType' => 'website',
                'ogUrl' => request()->url(),
                'ogTitle' => 'Comparteix la Teva Opinió a EventoPass',
                'ogDescription' => 'Ens encantaria escoltar el que vas pensar sobre l\'esdeveniment. El teu feedback ajuda a millorar l\'experiència de tots els nostres usuaris.',
                'ogImage' => asset('logo/logo.png'),
                'twitterCard' => 'summary_large_image',
                'twitterUrl' => request()->url(),
                'twitterTitle' => 'Enviar un Comentari a EventoPass',
                'twitterDescription' => 'Has assistit a un esdeveniment recentment? Comparteix la teva opinió i ajuda a la comunitat a conèixer millor l\'esdeveniment.',
                'twitterImage' => asset('logo/logo.png'),
            ];

            return view('tickets.crearComentario', ['eventoId' => $eventoId], 'metaData');
        } else {
            return redirect('');
        }
        
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

        $event_id = $request['eventoId'];

        try {
            $comentario = new Comentario([
                'nombre' => $request->input('nombre'),
                'event_id' => $event_id,
                'smileyRating' => $request->input('smileyRating'),
                'puntuacion' => $request->input('reviewRating'),
                'titulo' => $request->input('titulo'),
                'comentario' => $request->input('comentario'),
            ]);
    
            $comentario->save();

            Log::debug('Nuevo comentario guardado correctamente', ['id_comentario: ' => $comentario->id, 'id_evento' => $event_id]);

            return redirect('')->with('success', 'Comentario enviado con éxito.');

        } catch (\Exception $e) {

            Log::error('Error al guardar el comentario:', ['error_message' => $e->getMessage()]);
            return redirect('')->with('error', 'Error al procesar el comentario.');

        }
    
}

}
