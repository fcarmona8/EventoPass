<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $start = microtime(true);
        Log::channel('home')->info('Inicio de solicitud a HomeController@index');

        try {
            $selectedFiltro = "";
            $searchTerm = "";
            $selectedCategoria = "";
            $categories = Category::pluck('name', 'id');
            $categories = ['todas' => 'Totes'] + $categories->toArray();
            
            $events = collect();
            $categories2 = Category::all();
            foreach ($categories2 as $category) {
                $eventsCollection = $category->eventsWithLimit()->get();
                $events = $events->merge($eventsCollection);
            }

            $query = Category::query();
            $categoriesPage = config('app.events_per_page', env('CATEGORIES_PAGE', 7));
            $categoriesPerPage = $query->orderBy("name")->paginate($categoriesPage);

            $duration = microtime(true) - $start;
            Log::channel('home')->info('Fin de solicitud a HomeController@index', ['duration' => $duration]);

            return view('home', compact('selectedFiltro', 'searchTerm', 'categories', 'selectedCategoria', 'categoriesPerPage', 'events'));
        } catch (\Exception $e) {
            Log::channel('home')->error('Error en HomeController@index', [
                'error_message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

        }
    }
}
