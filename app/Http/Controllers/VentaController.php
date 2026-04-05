<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Lote;
use App\Models\Edicion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;
use Barryvdh\DomPDF\Facade\Pdf;

class VentaController extends Controller
{
    public function index()
    {
        $ventas = Venta::with('usuario.persona')->orderBy('id', 'desc')->paginate(10);
        return view('ventas.index', compact('ventas'));
    }

    public function create()
    {
        return view('ventas.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'datos_venta' => 'required|json'
        ]);

        $datos = json_decode($request->datos_venta, true);

        if (!$datos || empty($datos['carrito'])) {
            return back()->with('error', 'El carrito de ventas está vacío.');
        }

        $folio = 'VNT-' . strtoupper(uniqid());
        $fecha = Carbon::now()->format('Y-m-d H:i:s');
        $usuarioId = auth()->id() ?? 1;

        try {
            DB::beginTransaction();

            $venta = Venta::create([
                'folio' => $folio,
                'usuario_id' => $usuarioId,
                'fecha' => $fecha,
                'total' => $datos['total'],
                'monto_recibido' => $datos['monto_recibido'],
                'cambio' => $datos['cambio']
            ]);

            foreach ($datos['carrito'] as $item) {
                $edicionId = $item['edicion_id'];
                $cantidadRequerida = $item['cantidad'];
                $precioUnitario = $item['precio_unitario'];

                // --- SOLUCIÓN PARA ORACLE ORA-00933 ---
                // Obtenemos los lotes con el bloqueo, pero SIN el orderBy en SQL
                // Luego usamos ->sortBy() para ordenarlos en PHP (PEPS)
                $lotesDisponibles = Lote::where('edicion_id', $edicionId)
                    ->where('cantidad', '>', 0)
                    ->lockForUpdate()
                    ->get()
                    ->sortBy('fecha_entrada'); // Orden PEPS en la colección de Laravel

                $cantidadFaltante = $cantidadRequerida;

                foreach ($lotesDisponibles as $lote) {
                    if ($cantidadFaltante <= 0) {
                        break;
                    }

                    $cantidadATomar = min($lote->cantidad, $cantidadFaltante);
                    $subtotalParcial = $cantidadATomar * $precioUnitario;

                    DetalleVenta::create([
                        'venta_id' => $venta->id,
                        'lote_id' => $lote->id,
                        'cantidad' => $cantidadATomar,
                        'subtotal' => $subtotalParcial
                    ]);

                    // Actualizar el lote
                    $lote->cantidad -= $cantidadATomar;
                    $lote->save();

                    $cantidadFaltante -= $cantidadATomar;
                }

                if ($cantidadFaltante > 0) {
                    throw new Exception("Stock insuficiente en lotes para la edición ID: {$edicionId}.");
                }

                // Actualizar inventario global
                $edicion = Edicion::findOrFail($edicionId);
                $edicion->existencias -= $cantidadRequerida;
                $edicion->save();
            }

            DB::commit();

            // Redirigimos al index con éxito
            return redirect()->route('ventas.index')->with('success', 'Venta registrada correctamente');
        } catch (Exception $e) {
            DB::rollBack();
            // Si quieres ver el error real si vuelve a fallar algo, usa: dd($e->getMessage());
            return back()->with('error', 'Error al procesar la venta: ' . $e->getMessage());
        }
    }

    public function show(string $id)
    {
        $venta = Venta::with('usuario', 'detallesVentas.lote.edicion.libro')->findOrFail($id);
        return view('ventas.show', compact('venta'));
    }

    public function edit(string $id)
    {
        $venta = Venta::findOrFail($id);
        return view('ventas.edit', compact('venta'));
    }

    public function update(Request $request, string $id)
    {
        $venta = Venta::findOrFail($id);

        $request->validate([
            'folio' => 'required|max:50|unique:ventas,folio,' . $venta->id,
            'usuario_id' => 'required',
            'fecha' => 'required',
            'total' => 'required|numeric',
            'monto_recibido' => 'nullable|numeric',
            'cambio' => 'nullable|numeric'
        ]);

        $data = $request->all();
        $data['fecha'] = Carbon::parse($request->fecha)->format('Y-m-d H:i:s');

        $venta->update($data);

        return redirect()->route('ventas.index')->with('success', 'Venta actualizada correctamente');
    }

    public function destroy(string $id)
    {
        $venta = Venta::findOrFail($id);
        $venta->delete();

        return redirect()->route('ventas.index')->with('success', 'Venta eliminada correctamente');
    }

    public function buscarLibro(Request $request)
    {
        $termino = strtolower($request->get('q'));

        if (!$termino) {
            return response()->json([]);
        }

        $ediciones = Edicion::with('libro')
            ->where('existencias', '>', 0)
            ->where(function ($query) use ($termino) {
                $query->whereRaw('LOWER(isbn) LIKE ?', ["%{$termino}%"])
                    ->orWhereHas('libro', function ($q) use ($termino) {
                        $q->whereRaw('LOWER(titulo) LIKE ?', ["%{$termino}%"]);
                    });
            })
            ->take(10)
            ->get();

        $resultados = $ediciones->map(function ($edicion) {
            return [
                'edicion_id' => $edicion->id,
                'isbn' => $edicion->isbn,
                'titulo' => $edicion->libro->titulo,
                'precio_venta' => $edicion->precio_venta,
                'existencias' => $edicion->existencias
            ];
        });

        return response()->json($resultados);
    }

    //Sección de reportes
    public function reporte()
    {
        $datos = DB::select("
        SELECT 
            v.id as venta,
            b.titulo as libro,
            SUM(dv.cantidad) as total_vendidos,
            SUM(dv.subtotal) as total_venta,
            v.created_at as fecha
        FROM VENTAS v
        JOIN DETALLES_VENTAS dv ON dv.venta_id = v.id
        JOIN LOTES l ON dv.lote_id = l.id
        JOIN EDICIONES e ON l.edicion_id = e.id
        JOIN LIBROS b ON e.libro_id = b.id
        GROUP BY v.id, b.titulo, v.created_at
        ORDER BY v.created_at DESC
    ");

        return view('reportes.ventas', compact('datos'));
    }

    public function reportePDF()
    {
        $datos = DB::select("
        SELECT 
            v.id as venta,
            b.titulo as libro,
            SUM(dv.cantidad) as total_vendidos,
            SUM(dv.subtotal) as total_venta,
            v.created_at as fecha
        FROM VENTAS v
        JOIN DETALLES_VENTAS dv ON dv.venta_id = v.id
        JOIN LOTES l ON dv.lote_id = l.id
        JOIN EDICIONES e ON l.edicion_id = e.id
        JOIN LIBROS b ON e.libro_id = b.id
        GROUP BY v.id, b.titulo, v.created_at
        ORDER BY v.created_at DESC
    ");

        $pdf = Pdf::loadView('reportes.ventas_pdf', compact('datos'));
        return $pdf->download('reporte_ventas.pdf');
    }
}
