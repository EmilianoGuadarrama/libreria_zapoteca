<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\Libro;
use App\Models\User;
use App\Models\Persona;
use App\Models\Autor;
use App\Models\Clasificacion;
use App\Models\Edicion;
use App\Models\DetalleVenta;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // KPIs Básicos
        $total_ventas = Venta::count();
        $monto_vendido = Venta::sum('total');
        $total_libros = Libro::count();
        $total_clientes = Persona::count(); // Asumiremos que las personas base sirven de métrica general o clientes
        $total_autores = Autor::count();
        $total_categorias = Clasificacion::count();
        $stock_total = Edicion::sum('existencias');
        
        $conteo_bajo_stock = Edicion::whereColumn('existencias', '<=', 'stock_minimo')->count();
        $libros_bajo_stock = Edicion::with('libro')
                                    ->whereColumn('existencias', '<=', 'stock_minimo')
                                    ->take(5)
                                    ->get();

        // Gráfica de Ventas de los últimos 7 días
        $ultimos_dias = [];
        $ventas_por_dia = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $fecha = Carbon::today()->subDays($i);
            $ultimos_dias[] = $fecha->format('d/m');
            
            $ventas = Venta::whereDate('fecha', $fecha)->sum('total');
            $ventas_por_dia[] = $ventas;
        }

        // Gráfica de Libros Más Vendidos (Top 5)
        $top_libros = DetalleVenta::select('lotes.edicion_id', DB::raw('SUM(detalles_ventas.cantidad) as total_vendido'))
            ->join('lotes', 'detalles_ventas.lote_id', '=', 'lotes.id')
            ->groupBy('lotes.edicion_id')
            ->orderBy('total_vendido', 'desc')
            ->take(5)
            ->get();
            
        $nombres_top_libros = [];
        $cantidades_top_libros = [];
        
        foreach ($top_libros as $item) {
            $edicion = Edicion::with('libro')->find($item->edicion_id);
            if ($edicion && $edicion->libro) {
                $nombres_top_libros[] = substr($edicion->libro->titulo, 0, 15) . '...';
                $cantidades_top_libros[] = $item->total_vendido;
            }
        }

        // Últimas Ventas
        $ultimas_ventas = Venta::with('usuario')->orderBy('fecha', 'desc')->take(5)->get();

        return view('admin.dashboard', compact(
            'total_ventas',
            'monto_vendido',
            'total_libros',
            'total_clientes',
            'total_autores',
            'total_categorias',
            'stock_total',
            'conteo_bajo_stock',
            'libros_bajo_stock',
            'ultimas_ventas',
            'ultimos_dias',
            'ventas_por_dia',
            'nombres_top_libros',
            'cantidades_top_libros'
        ));
    }
}
