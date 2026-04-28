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
use App\Http\Controllers\UserController;

// RUTAS PÚBLICAS
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


// RUTAS PROTEGIDAS (Requieren Login)

Route::middleware(['auth'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ROL: VENDEDOR, GERENTE Y ADMINISTRADOR
    // Acceso a la operación diaria de ventas
    Route::middleware(['rol:Administrador,Vendedor'])->group(function () {
        Route::get('/ventas/buscar-libro', [VentaController::class, 'buscarLibro'])->name('ventas.buscar_libro');
        Route::get('/ventas/{id}/ticket', [TicketVentaController::class, 'show'])->name('ventas.ticket');
        Route::resource('ventas', VentaController::class);
    });

    // ROL: GERENTE Y ADMINISTRADOR
    // Gestión de Inventario, Personal y Catálogos
    Route::middleware(['rol:Administrador,Gerente'])->group(function () {
        
        // Gestión de Personal (UserController)
        Route::resource('personal', UserController::class)->names('usuarios');
        // Catálogo Literario (Libros, Autores, Géneros)
        Route::resource('libros', LibroController::class);
        Route::resource('ediciones', EdicionController::class);
        Route::resource('autores', AutorController::class);
        Route::resource('paises', PaisController::class);
        Route::resource('nacionalidades', NacionalidadController::class);
        Route::resource('asigna_autor', AsignaAutorController::class);
        
        // Categorización
        Route::resource('clasificaciones', ClasificacionController::class);
        Route::resource('generos', GeneroController::class);
        Route::resource('subgeneros', SubgeneroController::class);
        Route::resource('asigna_subgeneros', AsignaSubgeneroController::class);
        
        // Promociones
        Route::post('promociones/{id}/renovar', [PromocionController::class, 'renovar'])->name('promociones.renovar');
        Route::resource('promociones', PromocionController::class);
        Route::resource('asigna_promociones', AsignaPromocionController::class);

        // Operaciones de Almacén y Compras
        Route::resource('compras', CompraController::class);
        Route::resource('mermas', MermaController::class);
        Route::resource('lotes', LoteController::class);
        Route::resource('proveedores', ProveedorController::class);
        Route::resource('ubicaciones', UbicacionController::class);

        // Catálogos Base
        Route::resource('editoriales', EditorialController::class);
        Route::resource('formatos', FormatoController::class);
        Route::resource('idiomas', IdiomaController::class);

        // PDFs y Reportes
        Route::get('/compras/{id}/pdf', [CompraController::class, 'generarPDF'])->name('compras.pdf');
        Route::get('/mermas/{id}/pdf', [MermaController::class, 'generarPDF'])->name('mermas.pdf');
        Route::get('/lotes/{id}/pdf', [LoteController::class, 'generarPDF'])->name('lotes.pdf');
    });

   
    // Acciones de seguridad y validación de nuevos usuarios
   
    Route::middleware(['rol:Administrador,Gerente'])->group(function () {
        Route::get('/admin/usuarios-pendientes', [AdminController::class, 'indexPendientes'])->name('admin.pendientes');
        Route::patch('/admin/usuarios/{id}/activar', [AdminController::class, 'activarUsuario'])->name('admin.activar');
        Route::delete('/admin/rechazar/{id}', [AdminController::class, 'rechazar'])->name('admin.rechazar');
    });

});