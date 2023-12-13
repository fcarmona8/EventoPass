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
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                  ->orWhereHas('venue', function($q) use ($searchTerm) {
                      $q->where('name', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('location', 'LIKE', "%{$searchTerm}%");
                  });
            });
        }

        if ($request->filled('category')) {
            $category = $request->get('category');
            $query->whereHas('category', function ($q) use ($category) {
                $q->where('name', $category);
            });
        }

        // Aplicar la paginación después de los filtros
        $eventsPerPage = config('app.events_per_page', env('PAGINATION_LIMIT', 10));
        $events = $query->orderBy('event_date')->paginate($eventsPerPage);

        return view('home', compact('events'));
    }
}
