<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PromocionController;
use App\Http\Controllers\LibroController;
use App\Http\Controllers\ClasificacionController;
use App\Http\Controllers\AsignaPromocionController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\TicketVentaController;

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::resource('ventas', VentaController::class);
Route::get('ventas/{id}/ticket', [TicketVentaController::class, 'show'])->name('ventas.ticket');

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
        Route::resource('proveedores', ProveedorController::class);
    });

    Route::middleware(['rol:Administrador,Bibliotecario'])->group(function () {
        Route::get('/libros', [LibroController::class, 'index'])->name('libros.index');
        Route::get('/clasificaciones', [ClasificacionController::class, 'index'])->name('clasificaciones.index');
    });
});