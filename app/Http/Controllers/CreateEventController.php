<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Event;
use App\Models\Venue;
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

        $imagePath = $request->file('event_image')->store('event_images', 'public');
        
        $category = Category::firstOrCreate(['name' => $request->input('category')]);

        $venueId = $request->input('selector-options');

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

        return redirect()->route('promotor.createEvent')->with('success', 'Evento creado con éxito');
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
