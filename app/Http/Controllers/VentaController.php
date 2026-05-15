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
use Illuminate\Support\Facades\Http;

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

    public function generarPDF($id)
    {
        $venta = Venta::findOrFail($id);

        $columnas = [
            'ID',
            'Cliente',
            'Total',
            'Fecha'
        ];

        $datos = [
            $venta->id,
            $venta->cliente ?? 'N/A',
            $venta->total,
            $venta->created_at
        ];

        $pdf = Pdf::loadView('pdf.individual', [
            'titulo' => 'Reporte de Venta',
            'columnas' => $columnas,
            'datos' => $datos
        ]);

        return $pdf->download('venta_' . $venta->id . '.pdf');
    }

    public function reporteGeneral(Request $request)
    {
        $query = Venta::query();

        // FILTROS
        if ($request->fecha) {
            $query->whereDate('created_at', $request->fecha);
        }

        if ($request->mes) {
            $query->whereMonth('created_at', $request->mes);
        }

        if ($request->anio) {
            $query->whereYear('created_at', $request->anio);
        }

        // OBTENER VENTAS
        $ventas = $query->orderBy('created_at', 'desc')->get();

        // VALIDACIÓN
        if ($ventas->isEmpty()) {

            return redirect()->back()
                ->with('error', 'No hay ventas registradas con esos filtros.');
        }

        // ESTADÍSTICAS
        $totalVentas = $ventas->sum('total');

        $cantidadVentas = $ventas->count();

        $promedioVentas = $ventas->avg('total');

        // COLUMNAS
        $columnas = [
            'ID',
            'Cliente',
            'Total',
            'Fecha'
        ];

        // DATOS TABLA
        $datos = [];

        foreach ($ventas as $venta) {

            $datos[] = [

                $venta->id,

                $venta->cliente ?? 'N/A',

                '$' . number_format($venta->total, 2),

                $venta->created_at->format('d/m/Y')
            ];
        }

        // AGRUPAR VENTAS POR MES PARA GRÁFICA
        $ventasPorMes = Venta::selectRaw("
        EXTRACT(MONTH FROM created_at) as mes,
        SUM(total) as total
    ")
            ->groupByRaw("EXTRACT(MONTH FROM created_at)")
            ->orderByRaw("EXTRACT(MONTH FROM created_at)")
            ->get();

        $mesesTexto = [
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre'
        ];

        $labels = [];
        $valores = [];

        foreach ($ventasPorMes as $item) {

            $labels[] = $mesesTexto[$item->mes];

            $valores[] = round($item->total, 2);
        }

        // URL DE QUICKCHART
        $chartConfig = [

            'type' => 'bar',

            'data' => [

                'labels' => $labels,

                'datasets' => [[

                    'label' => 'Ventas por Mes',

                    'data' => $valores
                ]]
            ]
        ];

        $chartUrl = 'https://quickchart.io/chart?c=' .
            urlencode(json_encode($chartConfig));

        // DESCARGAR IMAGEN
        $imageContent = file_get_contents($chartUrl);

        // CONVERTIR A BASE64
        $chartBase64 = 'data:image/png;base64,' .
            base64_encode($imageContent);

        // TÍTULO DINÁMICO
        $titulo = 'Reporte General de Ventas';

        if ($request->fecha) {

            $titulo .= ' - Día ' .
                date('d/m/Y', strtotime($request->fecha));
        }

        if ($request->mes) {

            $titulo .= ' - Mes ' . $request->mes;
        }

        if ($request->anio) {

            $titulo .= ' - Año ' . $request->anio;
        }

        // GENERAR PDF
        $pdf = Pdf::loadView('pdf.reporte_general_ventas', [

            'titulo' => $titulo,

            'columnas' => $columnas,

            'datos' => $datos,

            'totalVentas' => $totalVentas,

            'cantidadVentas' => $cantidadVentas,

            'promedioVentas' => $promedioVentas,

            'chartBase64' => $chartBase64
        ]);

        return $pdf->download('reporte_general_ventas.pdf');
    }
}
