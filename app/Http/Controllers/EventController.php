<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venue;

class EventController extends Controller
{
    public function create()
    {
        return view('tickets.createEvent');
    }

    public function store(Request $request)
    {
        // Validación de datos (puedes personalizar esto según tus necesidades)
        $request->validate([
            'nova_provincia' => 'required|string',
            'nova_ciutat' => 'required|string',
            'codi_postal' => 'required|numeric',
            'nom_local' => 'required|string',
            'capacitat_local' => 'required|string',
        ]);

        // Obtener el ID del usuario actualmente autenticado
        $user_id = auth()->user()->id;

        // Crear una nueva instancia del modelo y asignar los datos
        $adreca = new Venue;
        $adreca->provincia = $request->nova_provincia;
        $adreca->ciutat = $request->nova_ciutat;
        $adreca->codi_postal = $request->codi_postal;
        $adreca->nom_local = $request->nom_local;
        $adreca->capacitat_local = $request->capacitat_local;
        $adreca->user_id = $user_id;

        // Guardar en la base de datos
        $adreca->save();

    }

}
