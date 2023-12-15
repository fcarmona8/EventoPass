<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use App\Models\Category;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::pluck('name', 'id');
        $categories = ['todas' => 'Totes'] + $categories->toArray();
        $selectedCategoria = $request->input('categoria', 'todas');
        

        $query = Event::with('category', 'venue');

        // Aplicar los filtros
        if ($request->filled('search')) {
            $searchTerm = $request->get('search');
            $filtro = $request->get('filtro');
            $query->where(function ($q) use ($filtro, $searchTerm) {
                if ($filtro === 'evento') {
                    $q->nameEvent("{$searchTerm}");
                }elseif($filtro === 'ciudad'){
                    $q->whereIn('venue_id', function ($subquery) use ($searchTerm) {
                        $subquery->select('id')
                            ->from('venues')
                            ->where('location', 'ILIKE', "%{$searchTerm}%");
                    });
                }elseif($filtro === 'recinto'){
                    $q->whereIn('venue_id', function ($subquery) use ($searchTerm) {
                        $subquery->select('id')
                            ->from('venues')
                            ->where('name', 'ILIKE', "%{$searchTerm}%");
                    });
                }
            });
        }

        if($selectedCategoria !== 'todas'){
            
            $selectedCategoriaName = Category::find($selectedCategoria)->name;
            $query->whereIn('category_id', function ($q) use ($selectedCategoriaName) {
                $q->select('id')
                    ->from('categories')
                    ->where('name', 'LIKE', "{$selectedCategoriaName}");
            });
        }

        $selectedFiltro = $request->input('filtro');
        $searchTerm = $request->input('search');
        // Aplicar la paginación después de los filtros
        $eventsPerPage = config('app.events_per_page', env('PAGINATION_LIMIT', 10));
        $events = $query->orderBy('event_date')->paginate($eventsPerPage);

        $events->appends($request->except('page'));

        return view('home', compact('events', 'selectedFiltro', 'searchTerm', 'categories', 'selectedCategoria'));
    }
}
