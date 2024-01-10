<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EventController;

// Página principal
Route::get('/', [HomeController::class, 'index'])->name('home');

// Home del Promotor
Route::get('/tickets/promoterhome', function () {
    return view('tickets.promoterhome');
})->name('tickets.promoterhome')/*->middleware('auth')*/;

// Sobre Nosotros
Route::get('/tickets/aboutus', function () {
    return view('tickets.aboutus');
})->name('tickets.aboutus');

// Avisos Legales
Route::get('/tickets/legalnotice', function () {
    return view('tickets.legalnotice');
})->name('tickets.legalnotice');

// Mostrar Evento
Route::get('/tickets/showevent/{id}', function () {
    return view('tickets.showevent');
})->name('tickets.showevent');

//Crear Evento
Route::get('/tickets/create-event', [EventController::class, 'create'])->name('event.create');

//Guardar Evento
Route::post('/tickets/create-event', [EventController::class, 'store'])->name('event.create');

// Comprar Entradas
Route::get('/tickets/buytickets', function () {
    return view('tickets.buytickets');
})->name('tickets.buytickets');


