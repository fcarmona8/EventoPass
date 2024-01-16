<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Venue;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PromotorHomeController extends Controller{
    public function index(Request $request){
        $user_id = Auth::id();
        $query = Event::query(); 

        $existingAddresses = Venue::where('user_id', $user_id)->get();
    
        $query->where(function ($q) use ($user_id) {
            $q->userEvent($user_id);
        });
    
        $events = $query->orderBy('id')->paginate(env('PAGINATION_LIMIT_PROMOTOR', 10));
    
        return view('promotor/promotorhome', compact('events', 'existingAddresses'));
    }

    public function edit(Request $request){
        dd();
        // Validación de los datos del formulario
        $validatedData = $request->validate([
            'eventId' => 'required',
            'eventName' => 'required|string|max:255',
            'eventDesc' => 'required|string',
            //'event_image' => 'image',
            'eventVid' => 'nullable|url',
            //'event_hidden' => 'sometimes|boolean',
            'eventAddress' => 'required|integer',
        ]);

        $venueId = $request->input('eventAddress');
        $eventId = $request->input('eventId');
        $eventName = $request->input('eventName');

        $venue = Venue::find($venueId);
        $event = Event::find($eventId);

        if (!$venue) {
            Log::error('Venue no encontrado con ID: ' . $venueId);
            return back()->withErrors(['error' => 'Venue no encontrado.']);
        }

        $event->name = $eventName;

        dd($event);

        $event->save();
    }

}