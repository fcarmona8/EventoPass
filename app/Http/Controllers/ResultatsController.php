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
        Log::info('Inicio de solicitud a ResultatsController@index', ['request_params' => $request->all()]);

        try {
            $categories = Category::pluck('name', 'id');
            $categories = ['todas' => 'Totes'] + $categories->toArray();
            $selectedCategoria = $request->input('categoria', 'todas');

            $query = Event::with('category', 'venue');

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
                                     ->where('location', 'ILIKE', "%{$searchTerm}%")
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
            $events = $query->orderBy('event_date')->paginate($eventsPerPage);

            Log::info('Consulta completada en ResultatsController@index', [
                'selectedFiltro' => $selectedFiltro,
                'searchTerm' => $searchTerm,
                'eventsPerPage' => $eventsPerPage
            ]);

            $events->appends($request->except('page'));

            return view('resultats', compact('events', 'selectedFiltro', 'searchTerm', 'categories', 'selectedCategoria'));

        } catch (\Exception $e) {

            Log::error('Error en ResultatsController@index', [
                'error_message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
