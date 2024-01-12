<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Event;
use App\Models\Venue;
use App\Models\Session;
use App\Models\TicketType;
use App\Models\Ticket;
use Illuminate\Support\Facades\Log;
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
                'selector-options-venue' => 'required|integer',
                'entry_type_name.*' => 'required|string',
                'entry_type_price.*' => 'required|numeric',
                'entry_type_quantity.*' => 'required|integer'
            ]);

            Log::info('Datos del evento validados: ' . json_encode($validatedData));

            // Guardar imagen principal del evento
            $image = $request->file('event_image');
            $imagePath = $image->storeAs('main_images', time().'_'.$image->getClientOriginalName(), 'public');
            Log::info('Imagen del evento almacenada: ' . $imagePath);

            // Buscar la categoría y el venue por ID
            $categoryId = $request->input('selector-options-categoria');
            $venueId = $request->input('selector-options-venue');
            $category = Category::find($categoryId);
            $venue = Venue::find($venueId);

            if (!$category || !$venue) {
                Log::error('Categoría o Venue no encontrados con IDs: ' . $categoryId . ', ' . $venueId);
                return back()->withErrors(['error' => 'Categoría o Venue no encontrados.']);
            }

            Log::info('Categoría y Venue encontrados: ' . $category->id . ', ' . $venue->id);

            // Crear el evento
            $event = new Event([
                'name' => $validatedData['title'],
                'description' => $validatedData['description'],
                'main_image' => $imagePath,
                'event_date' => $validatedData['event_datetime'],
                'category_id' => $category->id,
                'venue_id' => $venue->id,
                'max_capacity' => $validatedData['max_capacity'],
                'video_link' => $validatedData['promo_video_link'] ?? null,
                'hidden' => $validatedData['event_hidden'] ?? false,
            ]);

            $event->save();
            Log::info('Evento guardado con éxito: ' . $event->id);

            // Crear el cierre de sesión basándose en la selección del usuario
            $selectorOption = $request->input('selector-options-venue');
            Log::info('Selector Option: ' . $selectorOption);

            $eventDateTime = new \DateTime($validatedData['event_datetime']);
            $onlineSaleEndTime = clone $eventDateTime;

            switch ($selectorOption) {
                case "2":
                    $onlineSaleEndTime->modify('-1 hour');
                    break;
                case "3":
                    $onlineSaleEndTime->modify('-2 hours');
                    break;
            }

            $session = new Session([
                'event_id' => $event->id,
                'date_time' => $eventDateTime,
                'online_sale_end_time' => $onlineSaleEndTime,
                'ticket_quantity' => $validatedData['max_capacity'],
            ]);

            $session->save();
            Log::info('Sesión guardada con éxito: ' . $session->id);

            // Procesar los tipos de tickets
            $typeNames = $request->input('entry_type_name');
            $typePrices = $request->input('entry_type_price');
            $typeQuantities = $request->input('entry_type_quantity');

            if (is_array($typeNames) && is_array($typePrices) && is_array($typeQuantities)) {
                foreach ($typeNames as $index => $name) {
                    // Crear el tipo de ticket
                    $ticketType = new TicketType([
                        'name' => $name,
                        'price' => $typePrices[$index],
                        'available_tickets' => $typeQuantities[$index],
                    ]);

                    $ticketType->save();

                    // Crear los tickets
                    for ($i = 0; $i < $ticketType->available_tickets; $i++) {
                        $ticket = new Ticket([
                            // Asignar 'purchase_id' según tu lógica de negocio
                            'type_id' => $ticketType->id,
                            'session_id' => $session->id,
                        ]);

                        $ticket->save();
                    }
                }
            }

            return redirect()->route('promotor.createEvent')->with('success', 'Evento, sesión y tickets creados con éxito');
        } catch (\Exception $e) {
            Log::error('Error en el proceso de almacenamiento del evento, sesión y tickets: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Error al guardar el evento, sesión y tickets.']);
        }
    }

    public function storeVenue(Request $request)
    {
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
