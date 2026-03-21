<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PromocionController;
use App\Http\Controllers\LibroController;
use App\Http\Controllers\ClasificacionController;
use App\Http\Controllers\AsignaPromocionController;

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', function () {
        return view('layouts.dashboard');
    })->name('dashboard');

    Route::middleware(['rol:Administrador,Gerente'])->group(function () {
        Route::resource('libros', LibroController::class);
        Route::resource('promociones', PromocionController::class);
        Route::resource('clasificaciones', ClasificacionController::class);
        Route::resource('asigna_promociones', AsignaPromocionController::class);
    });

    Route::middleware(['rol:Administrador,Bibliotecario'])->group(function () {
        Route::get('/libros', [LibroController::class, 'index'])->name('libros.index');
        Route::get('/clasificaciones', [ClasificacionController::class, 'index'])->name('clasificaciones.index');
    });
});
