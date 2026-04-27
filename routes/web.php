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
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EdicionController;
use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\EditorialController;
use App\Http\Controllers\FormatoController;
use App\Http\Controllers\IdiomaController;

// =========================
// RUTAS PÚBLICAS
// =========================
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/catalogo', [CatalogoController::class, 'index'])->name('catalogo');

Route::middleware(['web'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// =========================
// RUTAS PROTEGIDAS
// =========================
Route::middleware(['auth'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // =========================
    // REPORTES
    // =========================

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
    Route::get('/reportes/mermas', [MermaController::class, 'reporte'])->name('reportes.mermas');
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
    // Administrador, Gerente y Bibliotecario pueden ver
    // =========================
    Route::middleware(['rol:Administrador,Gerente,Bibliotecario'])->group(function () {
        Route::get('/libros', [LibroController::class, 'index'])->name('libros.index');
        Route::get('/clasificaciones', [ClasificacionController::class, 'index'])->name('clasificaciones.index');
        Route::get('/generos', [GeneroController::class, 'index'])->name('generos.index');
        Route::get('/subgeneros', [SubgeneroController::class, 'index'])->name('subgeneros.index');
        Route::get('/ubicaciones', [UbicacionController::class, 'index'])->name('ubicaciones.index');
        Route::get('/asigna_subgeneros', [AsignaSubgeneroController::class, 'index'])->name('asigna_subgeneros.index');

        // EDICIONES - SOLO LISTADO
        Route::get('/ediciones', [EdicionController::class, 'index'])->name('ediciones.index');

        // CATÁLOGOS BASE - SOLO LISTADO
        Route::get('/editoriales', [EditorialController::class, 'index'])->name('editoriales.index');
        Route::get('/formatos', [FormatoController::class, 'index'])->name('formatos.index');
        Route::get('/idiomas', [IdiomaController::class, 'index'])->name('idiomas.index');
    });

    // =========================
    // ADMIN / GERENTE
    // Administrador y Gerente pueden crear, editar y eliminar
    // =========================
    Route::middleware(['rol:Administrador,Gerente'])->group(function () {

        // =========================
        // EDICIONES
        // =========================
        Route::post('/ediciones', [EdicionController::class, 'store'])->name('ediciones.store');
        Route::put('/ediciones/{edicion}', [EdicionController::class, 'update'])->name('ediciones.update');
        Route::delete('/ediciones/{edicion}', [EdicionController::class, 'destroy'])->name('ediciones.destroy');

        // =========================
        // LIBROS
        // =========================
        Route::resource('libros', LibroController::class)->except(['index']);

        // =========================
        // PROMOCIONES
        // =========================
        Route::post('promociones/{id}/renovar', [PromocionController::class, 'renovar'])->name('promociones.renovar');
        Route::resource('promociones', PromocionController::class);

        Route::post('asigna_promociones/ediciones/{edicion}/portada', [AsignaPromocionController::class, 'updatePortada'])
            ->name('asigna_promociones.portada.update');

        Route::resource('asigna_promociones', AsignaPromocionController::class);

        // =========================
        // CATÁLOGOS BASE
        // =========================
        Route::resource('clasificaciones', ClasificacionController::class)->except(['index']);
        Route::resource('generos', GeneroController::class)->except(['index']);
        Route::resource('subgeneros', SubgeneroController::class)->except(['index']);
        Route::resource('ubicaciones', UbicacionController::class)->except(['index']);
        Route::resource('asigna_subgeneros', AsignaSubgeneroController::class)->except(['index']);

        // EDITORIALES / FORMATOS / IDIOMAS
        Route::resource('editoriales', EditorialController::class)->except(['index']);
        Route::resource('formatos', FormatoController::class)->except(['index']);
        Route::resource('idiomas', IdiomaController::class)->except(['index']);

        // =========================
        // PROVEEDORES / AUTORES
        // =========================
        Route::resource('proveedores', ProveedorController::class);
        Route::resource('paises', PaisController::class);
        Route::resource('nacionalidades', NacionalidadController::class);
        Route::resource('autores', AutorController::class);
        Route::resource('asigna_autor', AsignaAutorController::class);

        // =========================
        // MERMAS
        // =========================
        Route::resource('mermas', MermaController::class);
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