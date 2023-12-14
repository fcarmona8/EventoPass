<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::with('category', 'venue');

        // Aplicar los filtros
        if ($request->filled('search')) {
            $searchTerm = $request->get('search');
            $filtro = $request->get('filtro');
            $query->where(function ($q) use ($filtro, $searchTerm) {
                if ($filtro === 'evento') {
                    $q->where('name', 'ILIKE', "%{$searchTerm}%"); 
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
        

        if ($request->filled('category')) {
            $category = $request->get('category');
            $query->whereHas('category', function ($q) use ($category) {
                $q->where('name', $category);
            });
        }

        $selectedFiltro = $request->input('filtro');
        $searchTerm = $request->input('search');
        // Aplicar la paginación después de los filtros
        $eventsPerPage = config('app.events_per_page', env('PAGINATION_LIMIT', 10));
        $events = $query->orderBy('event_date')->paginate($eventsPerPage);

        return view('home', compact('events', 'selectedFiltro', 'searchTerm'));
    }
}
