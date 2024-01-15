<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PromotorHomeController extends Controller{
    public function index(Request $request){
        $user_id = Auth::id();
        $query = Event::query(); // Cambiado de all() a query()
    
        $query->where(function ($q) use ($user_id) {
            $q->userEvent($user_id);
        });
    
        $events = $query->orderBy('id')->paginate(env('PAGINATION_LIMIT_PROMOTOR', 10));
    
        return view('promotor/promotorhome', compact('events'));
    }
    

}