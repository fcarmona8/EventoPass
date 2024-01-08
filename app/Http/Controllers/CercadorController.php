<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Log;

class cercadorController extends Controller
{
    public function index(Request $request)
    {
        Log::info('Inicio de solicitud a cercadorController@index', ['request_params' => $request->all()]);

        try {
            $categories = Category::pluck('name', 'id');
            $categories = ['todas' => 'Totes'] + $categories->toArray();
            $selectedCategoria = $request->input('categoria', 'todas');

            $query = Event::with('category', 'venue');

            if ($request->filled('search')) {
                $searchTerm = $request->get('search');
                $filtro = $request->get('filtro');

                Log::info('Filtros aplicados en HomeController@index', ['filtro' => $filtro, 'searchTerm' => $searchTerm]);

                $query->where(function ($q) use ($filtro, $searchTerm) {
                    if ($filtro === 'evento') {
                        $q->nameEvent("{$searchTerm}");
                    } elseif ($filtro === 'ciudad') {
                        $q->whereIn('venue_id', function ($subquery) use ($searchTerm) {
                            $subquery->select('id')
                                     ->from('venues')
                                     ->where('location', 'ILIKE', "%{$searchTerm}%");
                        });
                    } elseif ($filtro === 'recinto') {
                        $q->whereIn('venue_id', function ($subquery) use ($searchTerm) {
                            $subquery->select('id')
                                     ->from('venues')
                                     ->where('name', 'ILIKE', "%{$searchTerm}%");
                        });
                    }
                });
            }

            if ($selectedCategoria !== 'todas') {
                $selectedCategoriaName = Category::find($selectedCategoria)->name;
                $query->whereIn('category_id', function ($q) use ($selectedCategoriaName) {
                    $q->select('id')
                      ->from('categories')
                      ->where('name', 'LIKE', "{$selectedCategoriaName}");
                });
            }

            $selectedFiltro = $request->input('filtro') ?? '';
            $searchTerm = $request->input('search') ?? '';
            $eventsPerPage = config('app.events_per_page', env('PAGINATION_LIMIT', 10));
            $events = $query->orderBy('event_date')->paginate($eventsPerPage);

            Log::info('Consulta completada en cercadorController@index', [
                'selectedFiltro' => $selectedFiltro,
                'searchTerm' => $searchTerm,
                'eventsPerPage' => $eventsPerPage
            ]);

            $events->appends($request->except('page'));

            return view('cercador', compact('events', 'selectedFiltro', 'searchTerm', 'categories', 'selectedCategoria'));

        } catch (\Exception $e) {
            Log::error('Error en cercadorController@index', [
                'error_message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
