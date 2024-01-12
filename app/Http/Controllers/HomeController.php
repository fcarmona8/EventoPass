<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller{

    public function index(Request $request){
        $selectedFiltro="";
        $searchTerm="";
        $selectedCategoria="";
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
        

        return view('home', compact('selectedFiltro', 'searchTerm', 'categories', 'selectedCategoria', 'categoriesPerPage','events'));
        }
}