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
    /**
     * Muestra el formulario para crear un nuevo evento.
     * Recupera las categorías disponibles y las direcciones existentes del usuario autenticado para mostrarlas en el formulario.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $categories = Category::pluck('name', 'id');
        $categories = $categories->toArray();
        $user_id = Auth::id();
        $existingAddresses = Venue::where('user_id', $user_id)->get();

        $metaData = [
            'title' => 'Crear Nou Esdeveniment - EventoPass | Publica el Teu Esdeveniment',
            'description' => 'Crea i publica un nou esdeveniment a EventoPass. Afegeix detalls com el títol, la descripció, la data, l\'ubicació, i més per a atraure assistents.',
            'keywords' => 'EventoPass, crear esdeveniment, publicar esdeveniment, organització d\'esdeveniments, gestió d\'esdeveniments',
            'ogType' => 'website',
            'ogUrl' => request()->url(),
            'ogTitle' => 'Publica un Nou Esdeveniment a EventoPass',
            'ogDescription' => 'Utilitza EventoPass per a crear i gestionar els teus esdeveniments fàcilment. Atrau més assistents amb una pàgina d\'esdeveniment atractiva.',
            'ogImage' => asset('logo/logo.png'),
            'twitterCard' => 'summary_large_image',
            'twitterUrl' => request()->url(),
            'twitterTitle' => 'Crea un Nou Esdeveniment a EventoPass',
            'twitterDescription' => 'Descobreix com EventoPass pot ajudar-te a promocionar i gestionar els teus esdeveniments de manera eficaç.',
            'twitterImage' => asset('logo/logo.png'),
        ];

        return view('promotor.createEvent', compact('existingAddresses', 'categories', 'metaData'));
    }

    /**
     * Procesa los datos enviados desde el formulario de creación de evento, valida y almacena el evento en la base de datos.
     * También gestiona la carga de la imagen principal del evento y las imágenes adicionales, si las hay.
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse 
     */
    public function store(Request $request)
    {
        try {
            Log::info('Inicio del proceso de almacenamiento del evento.');

            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'selector-options-categoria' => 'required|string|max:255',
                'description' => 'required|string',
                'event_datetime' => 'required|date',
                'event_image' => 'required|image',
                'max_capacity' => 'required|integer|min:1',
                'promo_video_link' => 'nullable|url',
                'event_hidden' => 'sometimes|boolean',
                'nominal_entries' => 'sometimes|boolean',
                'selector-options' => 'required|integer',
                'entry_type_name.*' => 'required|string',
                'entry_type_price.*' => 'required|numeric',
                'entry_type_quantity.*' => 'required|integer',
                'additional_images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            ]);

            Log::info('Datos del evento validados: ' . json_encode($validatedData));

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

            $event = new Event([
                'name' => $validatedData['title'],
                'description' => $validatedData['description'],
                'event_date' => $validatedData['event_datetime'],
                'category_id' => $category->id,
                'venue_id' => $venue->id,
                'video_link' => $validatedData['promo_video_link'] ?? null,
                'hidden' => $validatedData['event_hidden'] ?? false,
                'nominal' => $validatedData['nominal_entries'] ?? false,
                'user_id' => $user_id,
            ]);
            
            $event->save();
            Log::info('Evento guardado con éxito: ' . $event->id);

            $mainImageResponse = $this->uploadImageToApi($request->file('event_image'));
            if ($mainImageResponse->successful()) {
                $event->main_image_id = $mainImageResponse->json()['imageId'];
                $event->save();
            } else {
                Log::error('Error al subir la imagen principal: ' . $mainImageResponse->body());
            }

            if ($request->hasFile('additional_images')) {
                foreach ($request->file('additional_images') as $additionalImage) {
                    $additionalImageResponse = $this->uploadImageToApi($additionalImage);
                    if ($additionalImageResponse->successful()) {
                        $imageId = $additionalImageResponse->json()['imageId'];
                        EventImage::create([
                            'event_id' => $event->id,
                            'image_id' => $imageId,
                            'is_main' => false,
                        ]);
                    } else {
                        Log::error('Error al subir imagen adicional: ' . $additionalImageResponse->body());
                    }
                }
            }

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

            $typeNames = $request->input('entry_type_name');
            $typePrices = $request->input('entry_type_price');
            $typeQuantities = $request->input('entry_type_quantity');

            if (is_array($typeNames) && is_array($typePrices) && is_array($typeQuantities)) {
                foreach ($typeNames as $index => $name) {
                    $ticketType = new TicketType([
                        'name' => $name,
                        'price' => $typePrices[$index],
                        'available_tickets' => $typeQuantities[$index],
                    ]);

                    $ticketType->save();

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

    /**
     * Maneja la creación de una nueva ubicación a partir de los datos proporcionados en el formulario.
     * Valida los datos y almacena la nueva ubicación en la base de datos.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * Funcion privado para subir imágenes a través de una API.
     * Prepara y envía una solicitud HTTP con el archivo de imagen.
     *
     * @param  mixed $imageFile
     * @return \Illuminate\Http\Client\Response
     */
    private function uploadImageToApi($imageFile)
    {
        return Http::withHeaders([
            'Accept' => 'application/json',
            'APP-TOKEN' => config('services.api.token'),
        ])->attach(
            'image', $imageFile->get(), $imageFile->getClientOriginalName()
        )->post(config('services.api.url').'/api/V1/images');
    }
}
