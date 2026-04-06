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
use App\Http\Controllers\GeneroController;
use App\Http\Controllers\SubgeneroController;
use App\Http\Controllers\UbicacionController;
use App\Http\Controllers\AsignaSubgeneroController;
use App\Http\Controllers\AdminController;

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

    // VENTAS
    Route::get('/ventas/reporte', [VentaController::class, 'reporte'])->name('ventas.reporte');
    Route::get('/ventas/reporte/pdf', [VentaController::class, 'reportePDF'])->name('ventas.reporte.pdf');

    // Compras
    Route::get('/compras/reporte', [CompraController::class, 'reporte'])->name('compras.reporte');
    Route::get('/compras/reporte/pdf', [CompraController::class, 'reportePDF'])->name('compras.reporte.pdf');

    // Lotes
    Route::get('/reportes/lotes', [LoteController::class, 'reporte'])->name('lotes.reporte');
    Route::get('/reportes/lotes/pdf', [LoteController::class, 'reportePDF'])->name('lotes.reporte.pdf');

    // MERMAS
    Route::get('reportes/mermas', [MermaController::class, 'reporte'])->name('reportes.mermas');
    Route::get('/mermas/reporte/pdf', [MermaController::class, 'reportePDF'])->name('mermas.reporte.pdf');

    // =========================
    // VENTAS
    // =========================
    Route::get('/ventas/buscar-libro', [VentaController::class, 'buscarLibro'])->name('ventas.buscar_libro');
    Route::get('/ventas/{id}/ticket', [TicketVentaController::class, 'show'])->name('ventas.ticket');
    Route::resource('ventas', VentaController::class);

    // =========================
    // ADMIN
    // =========================
    Route::middleware(['rol:Administrador'])->group(function () {
    Route::get('/admin/usuarios-pendientes', [AdminController::class, 'indexPendientes'])->name('admin.pendientes');
    Route::patch('/admin/usuarios/{id}/activar', [AdminController::class, 'activarUsuario'])->name('admin.activar'); 
    Route::delete('/admin/rechazar/{id}', [AdminController::class, 'rechazar'])->name('admin.rechazar');
});

    // =========================
    // LECTURA DE CATÁLOGO
    // =========================
    Route::middleware(['rol:Administrador,Gerente,Bibliotecario'])->group(function () {
        Route::get('/libros', [LibroController::class, 'index'])->name('libros.index');
        Route::get('/clasificaciones', [ClasificacionController::class, 'index'])->name('clasificaciones.index');
        Route::get('/generos', [GeneroController::class, 'index'])->name('generos.index');
        Route::get('/subgeneros', [SubgeneroController::class, 'index'])->name('subgeneros.index');
        Route::get('/ubicaciones', [UbicacionController::class, 'index'])->name('ubicaciones.index');
        Route::get('/asigna_subgeneros', [AsignaSubgeneroController::class, 'index'])->name('asigna_subgeneros.index');
    });

    // =========================
    // ADMIN / GERENTE
    // =========================
    Route::middleware(['rol:Administrador,Gerente'])->group(function () {
        Route::resource('libros', LibroController::class)->except(['index']);
        Route::resource('promociones', PromocionController::class);
        Route::resource('clasificaciones', ClasificacionController::class)->except(['index']);
        Route::resource('asigna_promociones', AsignaPromocionController::class);
        Route::resource('proveedores', ProveedorController::class);
        Route::resource('paises', PaisController::class);
        Route::resource('nacionalidades', NacionalidadController::class);
        Route::resource('autores', AutorController::class);
        Route::resource('asigna_autor', AsignaAutorController::class);
        Route::resource('mermas', MermaController::class);
        Route::resource('generos', GeneroController::class)->except(['index']);
        Route::resource('subgeneros', SubgeneroController::class)->except(['index']);
        Route::resource('ubicaciones', UbicacionController::class)->except(['index']);
        Route::resource('asigna_subgeneros', AsignaSubgeneroController::class)->except(['index']);
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
