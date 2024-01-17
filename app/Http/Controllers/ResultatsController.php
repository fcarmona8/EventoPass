<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Log;

class ResultatsController extends Controller
{
    public function index(Request $request)
    {
        $start = microtime(true);
        Log::channel('resultats')->info('Inicio de solicitud a ResultatsController@index');

        try {
            $categories = Category::pluck('name', 'id');
            $categories = ['todas' => 'Totes'] + $categories->toArray();
            $selectedCategoria = $request->input('categoria', 'todas');

            $query = Event::with('category', 'venue', 'sessions.tickets'); 

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

            // Inicializamos $lowestPrice
            $lowestPrice = null;

            // Verificamos que $event no sea null antes de llamar al método
            if ($event !== null) {
                $lowestPrice = $event->lowestTicketPrice();
            }

            $endDuration = microtime(true) - $start;
            Log::channel('resultats')->info('Fin de solicitud a ResultatsController@index', ['duration' => $endDuration]);

            return view('resultats', compact('events', 'selectedFiltro', 'searchTerm', 'categories', 'selectedCategoria', 'lowestPrice'));
 
        } catch (\Exception $e) {

            Log::error('Error en ResultatsController@index', [
                'error_message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
