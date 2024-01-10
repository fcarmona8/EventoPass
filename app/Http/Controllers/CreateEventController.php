<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venue;

class CreateEventController extends Controller
{
    public function create()
    {
        return view('promotor.createEvent');
    }

    public function store(Request $request)
    {
        
    }

}
