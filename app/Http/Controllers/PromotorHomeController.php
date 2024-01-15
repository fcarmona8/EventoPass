<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Log;

class PromotorHomeController extends Controller{
    public function index(Request $request){
    
    
        return view('home', compact('selectedFiltro'));
    }

}