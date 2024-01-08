<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CercadorController;

// Página principal
Route::get('/cercador', [CercadorController::class, 'index'])->name('cercador');

// Home del Promotor
Route::get('/tickets/promoterhome', function () {
    return view('tickets.promoterhome');
})->name('tickets.promoterhome');

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

// Comprar Entradas
Route::get('/tickets/buytickets', function () {
    return view('tickets.buytickets');
})->name('tickets.buytickets');
