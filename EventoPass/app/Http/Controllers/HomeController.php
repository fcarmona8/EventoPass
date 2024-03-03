<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    /**
    * Maneja la solicitud GET para la página de inicio de la aplicación.
    * Recupera y muestra una lista de eventos disponibles y categorías para filtrar esos eventos.
    *
    * @param  \Illuminate\Http\Request $request
    * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
    */
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
            
            $events = Event::eventosDisponibles()->get();

            $categories2 = Category::all();
            foreach ($categories2 as $category) {
                $eventsCollection = $category->eventsWithLimit($events);
                $events = $events->merge($eventsCollection);
            }

            $query = Category::query();
            $categoriesPage = config('app.events_per_page', env('CATEGORIES_PAGE', 7));
            $categoriesPerPage = $query->orderBy("name")->paginate($categoriesPage);

            $duration = microtime(true) - $start;
            Log::channel('home')->info('Fin de solicitud a HomeController@index', ['duration' => $duration]);

            $metaData = [
                'title' => 'Pàgina d\'Inici - EventoPass | Descobreix els Millors Esdeveniments',
                'description' => 'Explora i descobreix els esdeveniments més emocionants a prop teu. Cerca per nom, recinte o ciutat. Troba esdeveniments per a totes les categories.',
                'keywords' => 'esdeveniments, concerts, festivals, exposicions, cerca d\'esdeveniments, categories d\'esdeveniments',
                'ogType' => 'website',
                'ogUrl' => request()->url(),
                'ogTitle' => 'EventoPass | La teva guia d\'esdeveniments',
                'ogDescription' => 'La teva destinació per a descobrir esdeveniments únics. Navega per la nostra llista completa d\'esdeveniments i troba el teu proper pla!',
                'ogImage' => asset('logo/logo.png'),
                'twitterCard' => 'summary_large_image',
                'twitterUrl' => request()->url(),
                'twitterTitle' => 'EventoPass | Explora Esdeveniments Increïbles',
                'twitterDescription' => 'Troba i explora els millors esdeveniments. Utilitza el nostre cercador per descobrir esdeveniments que coincideixen amb els teus interessos.',
                'twitterImage' => asset('logo/logo.png'),
            ];

            return view('home', compact('selectedFiltro', 'searchTerm', 'categories', 'selectedCategoria', 'categoriesPerPage', 'events', 'metaData'));
        } catch (\Exception $e) {
            Log::channel('home')->error('Error en HomeController@index', [
                'error_message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

        }
    }
}