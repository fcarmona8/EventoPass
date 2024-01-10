<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Event;
use App\Models\Category;
use App\Models\Venue;
use Illuminate\Support\Facades\Storage;

class CreateEventController extends Controller
{
    public function create()
    {
        $categories = Category::pluck('name', 'id');
        $categories = $categories->toArray();
        return view('promotor.createEvent', compact('categories'));
        $existingAddresses = Venue::all();

        return view('promotor.createEvent', compact('existingAddresses'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'description' => 'required|string',
            'event_datetime' => 'required|date',
            'event_image' => 'required|image',
            'max_capacity' => 'required|integer|min:1',
            'promo_video_link' => 'nullable|url',
            'event_hidden' => 'sometimes|boolean',
        ]);

        // Subida de la imagen principal
        $imagePath = $request->file('event_image')->store('event_images', 'public');

        // Encuentra o crea la categoría
        $category = Category::firstOrCreate(['name' => $request->input('category')]);

        // Manejo de la dirección
        $venueId = null;
        if ($request->has('existing_address') && $request->input('existing_address')) {
            $venueId = $request->input('existing_address');
        } elseif ($request->has('new_address')) {
            $validatedAddressData = $request->validate([
                'new_address.province' => 'required',
                'new_address.city' => 'required',
            ]);

            $venue = Venue::create([
                'name' => $request->input('new_address.venue_name'),
                'location' => $request->input('new_address.city') . ', ' . $request->input('new_address.province'),
            ]);
            $venueId = $venue->id;
        }

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
        $event->save();

        // Aquí puedes agregar lógica adicional si necesitas procesar imágenes adicionales, entradas, etc.

        return redirect()->route('promotor.createEvent')->with('success', 'Evento creado con éxito');
    }
}
