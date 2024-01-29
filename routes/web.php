<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ResultatsController;
use App\Http\Controllers\ShowEventController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\TicketsPDFController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\CreateEventController;
use App\Http\Controllers\PromotorHomeController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\PromotorSessionsListController;
use App\Http\Controllers\ComentarioController;


// Página principal
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/resultats', [ResultatsController::class, 'index'])->name('resultats');

// Home del Promotor
Route::get('/promotor/promotorhome', [PromotorHomeController::class, 'index'])->name('promotorhome')->middleware('isPromotor');

//Lista sessiones de un evento promotor
Route::get('/promotor/promotorsessionlist', [PromotorSessionsListController::class, 'index'])->name('promotorsessionslist')->middleware('isPromotor');

//Guardar una nueva sesión
Route::post('/promotor/promotorsessionlist', [PromotorSessionsListController::class, 'storeSession'])->name('promotorsessionslist.storeSession');

// Sobre Nosotros
Route::get('/tickets/aboutus', function () {
    return view('tickets.aboutus');
})->name('tickets.aboutus');

// Avisos Legales
Route::get('/tickets/legalnotice', function () {
    return view('tickets.legalnotice');
})->name('tickets.legalnotice');

// Mostrar Evento
Route::get('/tickets/showevent/{id}', [ShowEventController::class, 'show'])->name('tickets.showevent');

//Crear Evento
Route::get('/promotor/create-event', [CreateEventController::class, 'create'])->name('promotor.createEvent');

//Guardar Evento
Route::post('/promotor/create-event', [CreateEventController::class, 'store'])->name('promotor.storeEvent');

//Editar Evento
Route::post('/promotor/promotorhomeedit', [PromotorHomeController::class, 'edit'])->name('promotor.editEvent');

//Guardar store
Route::post('/promotor/create-venue', [CreateEventController::class, 'storeVenue'])->name('promotor.createVenue');

// Comprar Entradas
Route::get('/tickets/buytickets', function () {
    return view('tickets.buytickets');
})->name('tickets.buytickets');

Route::get('/tickets/comentario', [ComentarioController::class, 'index'])->name('tickets.comentarios');

// Home Administradores
Route::get('/admin/home', function () {
    return view('admin.home');
})->name('ruta.admin')->middleware('isAdmin');;

//Perfil
Route::get('/user/profile', function () {
    return view('user.profile');
})->name('user.profile');


// Rutas de autenticación
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');


// Mostrar formulario para solicitar restablecimiento de contraseña
Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');

// Enviar enlace de restablecimiento de contraseña
Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

// Mostrar formulario para restablecer la contraseña
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');

// Procesar el restablecimiento de contraseña
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

// Generar PDF
Route::get('/a', [TicketsPDFController::class, 'generatePdf']);


