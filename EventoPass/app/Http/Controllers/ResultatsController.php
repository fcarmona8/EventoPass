<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Log;

class ResultatsController extends Controller
{
    /**
     * Muestra los resultados de búsqueda de eventos basados en filtros aplicados por el usuario.
     * Incluye la búsqueda por nombre de evento, ciudad, recinto y categoría. Los eventos se presentan
     * ordenados por fecha y se paginan según la configuración.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $start = microtime(true);
        Log::channel('resultats')->info('Inicio de solicitud a ResultatsController@index');

        try {
            $categories = Category::pluck('name', 'id');
            $categories = ['todas' => 'Totes'] + $categories->toArray();
            $selectedCategoria = $request->input('categoria', 'todas');

            $query = Event::eventosDisponibles();

            if ($request->filled('search')) {
                $searchTerm = $request->get('search');
                $filtro = $request->get('filtro');

                Log::info('Filtros aplicados en RerController@index', ['filtro' => $filtro, 'searchTerm' => $searchTerm]);

                $query->where(function ($q) use ($filtro, $searchTerm) {
                    if ($filtro === 'evento') {
                        $q->nameEvent("{$searchTerm}")->where('hidden', '=', 'false');
                    } elseif ($filtro === 'ciudad') {
                        $q->whereIn('venue_id', function ($subquery) use ($searchTerm) {
                            $subquery->select('id')
                                     ->from('venues')
                                     ->where('city', 'ILIKE', "%{$searchTerm}%")
                                     ->where('hidden', '=', 'false');
                        });
                    } elseif ($filtro === 'recinto') {
                        $q->whereIn('venue_id', function ($subquery) use ($searchTerm) {
                            $subquery->select('id')
                                     ->from('venues')
                                     ->where('name', 'ILIKE', "%{$searchTerm}%")
                                     ->where('hidden', '=', 'false');
                        });
                    }
                });
            }

            $midDuration = microtime(true) - $start;
            Log::channel('resultats')->info('Procesamiento parcial en ResultatsController@index', ['duration' => $midDuration]);

            if ($selectedCategoria !== 'todas') {
                $selectedCategoriaName = Category::find($selectedCategoria)->name;
                $query->whereIn('category_id', function ($q) use ($selectedCategoriaName) {
                    $q->select('id')
                      ->from('categories')
                      ->where('name', 'LIKE', "{$selectedCategoriaName}")
                      ->where('hidden', '=', 'false');
                });
            }

            $selectedFiltro = $request->input('filtro') ?? '';
            $searchTerm = $request->input('search') ?? '';
            $eventsPerPage = config('app.events_per_page', env('PAGINATION_LIMIT', 10));
            $events = $query->with('sessions.tickets')->orderBy('event_date')->paginate($eventsPerPage);

            

            Log::info('Consulta completada en ResultatsController@index', [
                'selectedFiltro' => $selectedFiltro,
                'searchTerm' => $searchTerm,
                'eventsPerPage' => $eventsPerPage
            ]);

            $events->appends($request->except('page'));
            $eventId = $request->input('eventId');
            $event = Event::find($eventId);

            $lowestPrice = null;

            if ($event !== null) {
                $lowestPrice = $event->lowestTicketPrice();
            }

            $endDuration = microtime(true) - $start;
            Log::channel('resultats')->info('Fin de solicitud a ResultatsController@index', ['duration' => $endDuration]);

            $metaData = [
                'title' => 'Resultats de Cerca - EventoPass | Troba els Millors Esdeveniments',
                'description' => 'Descobreix esdeveniments basats en la teva cerca. Explora una àmplia varietat d\'esdeveniments i troba el que més s\'ajusta als teus interessos.',
                'keywords' => 'EventoPass, resultats de cerca, esdeveniments, trobar esdeveniments, cerca d\'esdeveniments',
                'ogType' => 'website',
                'ogUrl' => request()->url(),
                'ogTitle' => 'Explora Esdeveniments a EventoPass',
                'ogDescription' => 'Veure els esdeveniments que coincideixen amb la teva cerca. Navega pels resultats i descobreix esdeveniments únics.',
                'ogImage' => asset('logo/logo.png'),
                'twitterCard' => 'summary_large_image',
                'twitterUrl' => request()->url(),
                'twitterTitle' => 'Resultats de Cerca a EventoPass',
                'twitterDescription' => 'Troba esdeveniments perfectes per a tu amb la nostra funció de cerca. Descobreix noves experiències i plans emocionants.',
                'twitterImage' => asset('logo/logo.png'),
            ];

            return view('resultats', compact('events', 'selectedFiltro', 'searchTerm', 'categories', 'selectedCategoria', 'lowestPrice', 'metaData'));
 
        } catch (\Exception $e) {

            Log::error('Error en ResultatsController@index', [
                'error_message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
