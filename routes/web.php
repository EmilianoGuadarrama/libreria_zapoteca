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
use App\Http\Controllers\LoteController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\AutorController;
use App\Http\Controllers\NacionalidadController;
use App\Http\Controllers\PaisController;
use App\Http\controllers\AsignaAutorController;


// Rutas públicas
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware(['web'])->group(function () {
    // Rutas de autenticación
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Rutas protegidas (requieren autenticación)
Route::middleware(['auth'])->group(function () {

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', function () {
        return view('layouts.dashboard');
    })->name('dashboard');

    // Rutas de ventas
    Route::get('/ventas/buscar-libro', [VentaController::class, 'buscarLibro'])->name('ventas.buscar_libro');
    Route::get('ventas/{id}/ticket', [TicketVentaController::class, 'show'])->name('ventas.ticket');
    Route::resource('ventas', VentaController::class);

    // Rutas que requieren rol Administrador
    Route::middleware(['rol:Administrador'])->group(function () {
        Route::get('/admin/usuarios-pendientes', [AuthController::class, 'indexPendientes'])->name('admin.pendientes');
        Route::patch('/admin/usuarios/{id}/activar', [AuthController::class, 'activarUsuario'])->name('admin.activar');
        Route::delete('/admin/rechazar/{id}', [AdminController::class, 'rechazar'])->name('admin.rechazar');
    });

    // Rutas por roles específicos
    Route::middleware(['rol:Administrador,Gerente'])->group(function () {
        Route::resource('libros', LibroController::class);
        Route::resource('promociones', PromocionController::class);
        Route::resource('clasificaciones', ClasificacionController::class);
        Route::resource('asigna_promociones', AsignaPromocionController::class);
        Route::resource('proveedores', ProveedorController::class);
        Route::resource('paises', PaisController::class);
        Route::resource('nacionalidades', NacionalidadController::class);
        Route::resource('autores', AutorController::class);
        Route::resource('asigna_autor',AsignaAutorController::class);
    });

    Route::middleware(['rol:Administrador,Bibliotecario'])->group(function () {
        Route::get('/libros', [LibroController::class, 'index'])->name('libros.index');
        Route::get('/clasificaciones', [ClasificacionController::class, 'index'])->name('clasificaciones.index');
    });

    //Lotes
    Route::middleware(['rol:Administrador'])->group(function () {
        Route::get('/admin/lotes', [LoteController::class, 'index'])->name('lotes.index');
        Route::post('/admin/lotes', [LoteController::class, 'store'])->name('lotes.store');
        Route::put('/admin/lotes/{id}', [LoteController::class, 'update'])->name('lotes.update');
        Route::delete('/admin/lotes/{id}', [LoteController::class, 'destroy'])->name('lotes.destroy');
    });

    //compras
    Route::middleware(['rol:Administrador'])->group(function () {
        Route::resource('compras', CompraController::class);
    });
});

