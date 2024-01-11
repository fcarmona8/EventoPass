<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Event;
use App\Models\Venue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class CreateEventController extends Controller
{
    public function create()
    {
        $categories = Category::pluck('name', 'id');
        $categories = $categories->toArray();
        $existingAddresses = Venue::all();

        return view('promotor.createEvent', compact('existingAddresses', 'categories'));
    }

    public function store(Request $request)
    {
        try {
            Log::info('Inicio del proceso de almacenamiento del evento.');

            // Validación de los datos del formulario
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'selector-options-categoria' => 'required|string|max:255',
                'description' => 'required|string',
                'event_datetime' => 'required|date',
                'event_image' => 'required|image',
                'max_capacity' => 'required|integer|min:1',
                'promo_video_link' => 'nullable|url',
                'event_hidden' => 'sometimes|boolean',
            ]);

            Log::info('Datos del evento validados: ' . json_encode($validatedData));

            // Guardar imagen principal del evento
            $imagePath = $request->file('event_image')->store('event_images', 'public');
            Log::info('Imagen del evento almacenada: ' . $imagePath);

             // Buscar la categoría por ID
            $categoryId = $request->input('selector-options-categoria');
            $category = Category::find($categoryId);

            if (!$category) {
                Log::error('Categoría no encontrada con ID: ' . $categoryId);
                return back()->withErrors(['error' => 'Categoría no encontrada.']);
            }

            Log::info('Categoría encontrada: ' . $category->id);

            // Obtener el ID del venue
            $venueId = $request->input('selector-options');
            Log::info('Venue ID obtenido: ' . $venueId);

            // Crear el evento
            $event = new Event([
                'name' => $validatedData['title'],
                'description' => $validatedData['description'],
                'main_image' => $imagePath,
                'event_date' => $validatedData['event_datetime'],
                'category_id' => $category->id,
                'venue_id' => $venueId,
                'max_capacity' => $validatedData['max_capacity'],
                'video_link' => $validatedData['promo_video_link'] ?? null,
                'hidden' => $validatedData['event_hidden'] ?? false,
            ]);

            // Guardar el evento
            $event->save();
            Log::info('Evento guardado con éxito: ' . $event->id);

            // Redirigir con mensaje de éxito
            return redirect()->route('promotor.createEvent')->with('success', 'Evento creado con éxito');
        } catch (\Exception $e) {
            Log::error('Error en el proceso de almacenamiento del evento: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Error al guardar el evento.']);
        }
    }

    public function storeVenue(Request $request){
        $request->validate([
            'nova_provincia' => 'required|string',
            'nova_ciutat' => 'required|string',
            'codi_postal' => 'required|numeric',
            'nom_local' => 'required|string',
            'capacitat_local' => 'required|numeric',
        ]);

        $user_id = Auth::id();

        $venue = new Venue([
            'province' => $request['nova_provincia'],
            'city' => $request['nova_ciutat'],
            'postal_code' => $request['codi_postal'],
            'venue_name' => $request['nom_local'],
            'capacity' => $request['capacitat_local'],
            'user_id' => $user_id

        ]);

        $venue->save();

        $existingAddresses = Venue::all();

        return response()->json(['message' => 'Dirección guardada correctamente', 'addresses' => $existingAddresses]);
            
        }
}
