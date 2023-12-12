<?php
use Illuminate\Support\Facades\Route;

// Página principal
Route::get('/', function () {
    return view('home');
})->name('home');

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
Route::get('/tickets/showevent', function () {
    return view('tickets.showevent');
})->name('tickets.showevent');

// Comprar Entradas
Route::get('/tickets/buytickets', function () {
    return view('tickets.buytickets');
})->name('tickets.buytickets');
