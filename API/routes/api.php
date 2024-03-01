<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\ImageController;
use App\Http\Controllers\V1\OptimizedImageController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('V1')->group(function () {
    Route::get('/images', [ImageController::class, 'index']);
    Route::get('/images/{id}', [ImageController::class, 'show']);
    Route::get('/optimized-images', [OptimizedImageController::class, 'index']);
    Route::get('/optimized-images/{image_id}/{version}', [OptimizedImageController::class, 'show']);

    Route::post('/images', [ImageController::class, 'store'])->middleware('verify.app.token');
    Route::delete('/images/{id}', [ImageController::class, 'destroy'])->middleware('verify.app.token');
    Route::post('/images/{id}', [ImageController::class, 'update'])->middleware('verify.app.token');
});



