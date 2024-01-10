<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CreateEventController extends Controller
{
    public function create()
    {
        $categories = Category::pluck('name', 'id');
        $categories = $categories->toArray();
        return view('promotor.createEvent', compact('categories'));
    }

    public function store(Request $request)
    {
        
    }

}
