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
use App\Http\Controllers\AsignaAutorController;
use App\Http\Controllers\MermaController;


// Rutas públicas
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware(['web'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});


// Rutas protegidas
Route::middleware(['auth'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', function () {
        return view('layouts.dashboard');
    })->name('dashboard');

    //REPORTES

    Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');

    // VENTAS
    Route::get('/ventas/reporte', [VentaController::class, 'reporte'])->name('ventas.reporte');
    Route::get('/ventas/reporte/pdf', [VentaController::class, 'reportePDF'])->name('ventas.reporte.pdf');

    // COMPRAS
    Route::get('/compras/reporte', [CompraController::class, 'reporte'])->name('compras.reporte');
    Route::get('/compras/reporte/pdf', [CompraController::class, 'reportePDF'])->name('compras.reporte.pdf');

    // LOTES
    Route::get('/reportes/lotes', [LoteController::class, 'reporte'])->name('lotes.reporte');
    Route::get('/reportes/lotes/pdf', [LoteController::class, 'reportePDF'])->name('lotes.reporte.pdf');

    // MERMAS
    Route::get('reportes/mermas', [MermaController::class, 'reporte'])->name('reportes.mermas');
    Route::get('/mermas/reporte/pdf', [MermaController::class, 'reportePDF'])->name('mermas.reporte.pdf');


    // =========================
    // VENTAS
    // =========================
    Route::get('/ventas/buscar-libro', [VentaController::class, 'buscarLibro'])->name('ventas.buscar_libro');
    Route::get('ventas/{id}/ticket', [TicketVentaController::class, 'show'])->name('ventas.ticket');
    Route::resource('ventas', VentaController::class);


    // =========================
    // ADMIN
    // =========================
    Route::middleware(['rol:Administrador'])->group(function () {
        Route::get('/admin/usuarios-pendientes', [AuthController::class, 'indexPendientes'])->name('admin.pendientes');
        Route::patch('/admin/usuarios/{id}/activar', [AuthController::class, 'activarUsuario'])->name('admin.activar');
    });


    // =========================
    // ADMIN / GERENTE
    // =========================
    Route::middleware(['rol:Administrador,Gerente'])->group(function () {
        Route::resource('libros', LibroController::class);
        Route::resource('promociones', PromocionController::class);
        Route::resource('clasificaciones', ClasificacionController::class);
        Route::resource('asigna_promociones', AsignaPromocionController::class);
        Route::resource('proveedores', ProveedorController::class);
        Route::resource('paises', PaisController::class);
        Route::resource('nacionalidades', NacionalidadController::class);
        Route::resource('autores', AutorController::class);
        Route::resource('asigna_autor', AsignaAutorController::class);
        Route::resource('mermas', MermaController::class);
    });


    // =========================
    // ADMIN / BIBLIOTECARIO
    // =========================
    Route::middleware(['rol:Administrador,Bibliotecario'])->group(function () {
        Route::get('/libros', [LibroController::class, 'index'])->name('libros.index');
        Route::get('/clasificaciones', [ClasificacionController::class, 'index'])->name('clasificaciones.index');
    });


    // =========================
    // LOTES
    // =========================
    Route::middleware(['rol:Administrador'])->group(function () {
        Route::get('/admin/lotes', [LoteController::class, 'index'])->name('lotes.index');
        Route::post('/admin/lotes', [LoteController::class, 'store'])->name('lotes.store');
        Route::put('/admin/lotes/{id}', [LoteController::class, 'update'])->name('lotes.update');
        Route::delete('/admin/lotes/{id}', [LoteController::class, 'destroy'])->name('lotes.destroy');
    });


    // =========================
    // COMPRAS
    // =========================
    Route::middleware(['rol:Administrador'])->group(function () {
        Route::resource('compras', CompraController::class);
    });
});
