<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Event;
use App\Models\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PromotorSessionsListController extends Controller{
    public function index(Request $request){

        $event_id = $request->input('id');
        $query = Session::query(); 
    
        $query->where(function ($q) use ($event_id) {
            $q->eventSessions($event_id);
        });
    
        $sessions = $query->orderBy('id')->paginate(env('PAGINATION_LIMIT_PROMOTOR', 10));
        return view('promotor/promotorSessionsList', compact('sessions'));
    }

}