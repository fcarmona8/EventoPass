<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Event;
use App\Models\Venue;
use App\Models\Session;
use App\Models\TicketType;
use App\Models\Ticket;
use App\Models\EventImage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class CreateEventController extends Controller
{
    public function create()
    {
        $categories = Category::pluck('name', 'id');
        $categories = $categories->toArray();
        $user_id = Auth::id();
        $existingAddresses = Venue::where('user_id', $user_id)->get();

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
                'selector-options' => 'required|integer',
                'entry_type_name.*' => 'required|string',
                'entry_type_price.*' => 'required|numeric',
                'entry_type_quantity.*' => 'required|integer',
                'additional_images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);

            Log::info('Datos del evento validados: ' . json_encode($validatedData));

            // Buscar la categoría y el venue por ID
            $categoryId = $request->input('selector-options-categoria');
            $venueId = $request->input('selector-options');
            $category = Category::find($categoryId);
            $venue = Venue::find($venueId);
            $user_id = Auth::id();

            if (!$category || !$venue) {
                Log::error('Categoría o Venue no encontrados con IDs: ' . $categoryId . ', ' . $venueId);
                return back()->withErrors(['error' => 'Categoría o Venue no encontrados.']);
            }

            Log::info('Categoría y Venue encontrados: ' . $category->id . ', ' . $venue->id);

            // Crear el evento
            $event = new Event([
                'name' => $validatedData['title'],
                'description' => $validatedData['description'],
                'event_date' => $validatedData['event_datetime'],
                'category_id' => $category->id,
                'venue_id' => $venue->id,
                'video_link' => $validatedData['promo_video_link'] ?? null,
                'hidden' => $validatedData['event_hidden'] ?? false,
                'user_id' => $user_id,
            ]);
            
            $event->save();
            Log::info('Evento guardado con éxito: ' . $event->id);

            // Subir y guardar imagen principal
            $mainImageResponse = $this->uploadImageToApi($request->file('event_image'));
            if ($mainImageResponse->successful()) {
                $event->main_image_id = $mainImageResponse->json()['imageId'];
                $event->save();
            } else {
                Log::error('Error al subir la imagen principal: ' . $mainImageResponse->body());
            }

            // Subir y guardar imágenes adicionales (si existen)
            if ($request->hasFile('additional_images')) {
                foreach ($request->file('additional_images') as $additionalImage) {
                    $additionalImageResponse = $this->uploadImageToApi($additionalImage);
                    if ($additionalImageResponse->successful()) {
                        EventImage::create([
                            'event_id' => $event->id,
                            'image_id' => $additionalImageResponse->json()['imageId'],
                            'is_main' => false,
                        ]);
                    } else {
                        Log::error('Error al subir imagen adicional: ' . $additionalImageResponse->body());
                    }
                }
            }

            // Crear el cierre de sesión basándose en la selección del usuario
            $selectorOption = $request->input('selector-options-venue');
            Log::info('Selector Option: ' . $selectorOption);

            $eventDateTime = new \DateTime($validatedData['event_datetime']);
            $onlineSaleEndTime = clone $eventDateTime;

            switch ($selectorOption) {
                case "1":
                    $onlineSaleEndTime;
                    break;
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
                'max_capacity' => $validatedData['max_capacity'],
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
                            'type_id' => $ticketType->id,
                            'session_id' => $session->id,
                        ]);

                        $ticket->save();
                    }
                }
            }

            return redirect()->route('promotorhome')->with('success', 'Evento, sesión y tickets creados con éxito');
        } catch (\Exception $e) {
            Log::error('Error en el proceso de almacenamiento del evento, sesión y tickets: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Error al guardar el evento, sesión y tickets.']);
        }
    }

    public function storeVenue(Request $request)
    {

        try {
            $validatedData = $request->validate([
                'nova_provincia' => 'required|string',
                'nova_ciutat' => 'required|string',
                'codi_postal' => 'required|numeric',
                'nom_local' => 'required|string',
                'capacitat_local' => 'required|numeric',
            ]);

            Log::info('Datos de la direccion validados: ' . json_encode($validatedData));
    
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
            $existingAddresses = Venue::where('user_id', $user_id)->get();
    
            return response()->json(['message' => 'Dirección guardada correctamente', 'addresses' => $existingAddresses]);
        } catch (\Exception $e) {
            Log::error('Error en el proceso de almacenamiento de la dirección: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Error al guardar la dirección.']);
        }
    }

    private function uploadImageToApi($imageFile)
    {
        return Http::withHeaders([
            'Accept' => 'application/json',
            'APP-TOKEN' => env('TIQUETS_APP_TOKEN'),
        ])->attach(
            'image', $imageFile->get(), $imageFile->getClientOriginalName()
        )->post('http://localhost:8080/api/V1/images');
    }
}
