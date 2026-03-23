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

// Rutas públicas
Route::get('/', function () {
    return view('welcome');
});

// Rutas de autenticación
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// Rutas protegidas (requieren autenticación)
Route::middleware(['auth'])->group(function () {

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', function () {
        return view('layouts.dashboard');
    })->name('dashboard');

    // Rutas de ventas (tu parte local)
    Route::resource('ventas', VentaController::class);
    Route::get('ventas/{id}/ticket', [TicketVentaController::class, 'show'])->name('ventas.ticket');

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
    });

    Route::middleware(['rol:Administrador,Bibliotecario'])->group(function () {
        Route::get('/libros', [LibroController::class, 'index'])->name('libros.index');
        Route::get('/clasificaciones', [ClasificacionController::class, 'index'])->name('clasificaciones.index');
    });
});


Route::middleware(['auth'])->group(function () {

    Route::middleware(['rol:Administrador,Gerente'])->group(function () {
        Route::resource('libros', LibroController::class);
        Route::resource('generos', GeneroController::class);
        Route::resource('subgeneros', SubgeneroController::class);
        Route::resource('personas', PersonaController::class);
        Route::resource('autores', AutorController::class);
        Route::resource('usuarios', UsuarioController::class);
        Route::resource('ubicaciones', UbicacionController::class);
        Route::resource('mermas', MermaController::class);
        Route::resource('asigna_subgenero', AsignaSubgeneroController::class);
        Route::resource('asigna_autores', AsignaAutoresController::class);
    });

    Route::middleware(['rol:Administrador,Bibliotecario'])->group(function () {
        Route::get('/libros', [LibroController::class, 'index'])->name('libros.index');
        Route::get('/generos', [GeneroController::class, 'index'])->name('generos.index');
        Route::get('/subgeneros', [SubgeneroController::class, 'index'])->name('subgeneros.index');
        Route::get('/personas', [PersonaController::class, 'index'])->name('personas.index');
        Route::get('/autores', [AutorController::class, 'index'])->name('autores.index');
        Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
        Route::get('/ubicaciones', [UbicacionController::class, 'index'])->name('ubicaciones.index');
        Route::get('/mermas', [MermaController::class, 'index'])->name('mermas.index');
        Route::get('/asigna_subgenero', [AsignaSubgeneroController::class, 'index'])->name('asigna_subgenero.index');
        Route::get('/asigna_autores', [AsignaAutoresController::class, 'index'])->name('asigna_autores.index');
    });

});
use App\Http\Controllers\MermaController;
use App\Http\Controllers\GeneroController;
use App\Http\Controllers\SubgeneroController;
use App\Http\Controllers\PersonaController;
use App\Http\Controllers\AutorController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\UbicacionController;
use App\Http\Controllers\AsignaSubgeneroController;
use App\Http\Controllers\AsignaAutoresController;

Route::resource('generos', GeneroController::class);
Route::resource('subgeneros', SubgeneroController::class);
Route::resource('personas', PersonaController::class);
Route::resource('autores', AutorController::class);
Route::resource('usuarios', UsuarioController::class);
Route::resource('ubicaciones', UbicacionController::class);
Route::resource('mermas', MermaController::class);
Route::resource('asigna_subgenero', AsignaSubgeneroController::class);
Route::resource('asigna_autores', AsignaAutoresController::class);

