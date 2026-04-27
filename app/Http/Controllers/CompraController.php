<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Proveedor;
use App\Models\Edicion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;
use Barryvdh\DomPDF\Facade\Pdf;


class CompraController extends Controller
{
    public function index()
    {
        $compras = Compra::with(['proveedor', 'usuario'])
            ->orderBy('id', 'desc')
            ->paginate(10);

        $proveedores = Proveedor::where('estado', 'Activo')->get();

        $ediciones = Edicion::with('libro')->get();

        return view('compras.index', compact('compras', 'proveedores', 'ediciones'));
    }
    public function create()
    {
        $proveedores = Proveedor::where('estado', 'Activo')->get();

        $ediciones = Edicion::with('libro')->get();

        return view('compras.create', compact('proveedores', 'ediciones'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'proveedor_id' => 'required|exists:proveedores,id',
            'folio_factura' => 'required|string|max:50',
            'fecha_compra' => 'required|date',
            'items_compra' => 'required|json'
        ]);

        $datosCarrito = json_decode($request->items_compra, true);

        if (empty($datosCarrito)) {
            return back()->with('error', 'Debe añadir al menos un producto a la compra.')->withInput();
        }

        try {
            DB::beginTransaction();

            $totalCalculado = 0;
            foreach ($datosCarrito as $item) {
                $totalCalculado += $item['cantidad'] * $item['precio_costo'];
            }

            $compra = Compra::create([
                'proveedor_id'  => $request->proveedor_id,
                'folio_factura' => strtoupper($request->folio_factura),
                'fecha_compra'  => $request->fecha_compra,
                'total_compra'  => $totalCalculado,
                'usuario_id'    => auth()->id() ?? 1,
                'estado'        => 'Recibida'
            ]);

            foreach ($datosCarrito as $item) {
                DetalleCompra::create([
                    'compra_id'  => $compra->id,
                    'edicion_id' => $item['edicion_id'],
                    'cantidad'   => $item['cantidad'],
                    'subtotal'   => $item['cantidad'] * $item['precio_costo']
                ]);
            }

            DB::commit();

            return redirect()->route('compras.index')
                ->with('success', 'Compra #' . $compra->folio_factura . ' registrada exitosamente.');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al registrar la compra: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $compra = Compra::with(['proveedor', 'usuario', 'detalles.edicion.libro'])
            ->findOrFail($id);

        return view('compras.show', compact('compra'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'folio_factura' => 'required|string|max:50',
            'estado' => 'required|in:Recibida,Pendiente,Cancelada'
        ]);

        $compra = Compra::findOrFail($id);
        $compra->update($request->only('folio_factura', 'estado'));

        return redirect()->route('compras.index')->with('success', 'Datos de la compra actualizados.');
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $compra = Compra::findOrFail($id);

            if ($compra->lotes()->count() > 0) {
                return back()->with('error', 'No se puede eliminar la compra porque ya tiene lotes de inventario asociados.');
            }

            $compra->detalles()->delete();
            $compra->delete();

            DB::commit();
            return redirect()->route('compras.index')->with('success', 'Compra eliminada correctamente.');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al eliminar la compra: ' . $e->getMessage());
        }
    }

    public function generarPDF($id)
    {
        $compra = Compra::findOrFail($id);

        $columnas = ['ID', 'Proveedor', 'Total', 'Fecha'];

        $datos = [
            $compra->id,
            $compra->proveedor->nombre ?? 'N/A',
            $compra->total,
            $compra->created_at
        ];

        $pdf = Pdf::loadView('pdf.individual', [
            'titulo' => 'Reporte de Compra',
            'columnas' => $columnas,
            'datos' => $datos
        ]);

        return $pdf->download('compra_' . $compra->id . '.pdf');
    }

    public function reporteGeneral()
    {
        $compras = Compra::all();

        if ($compras->isEmpty()) {
            return redirect()->back()->with('error', 'No hay compras');
        }

        $columnas = ['ID', 'Proveedor', 'Total', 'Fecha'];

        $datos = [];

        foreach ($compras as $compra) {
            $datos[] = [
                $compra->id,
                $compra->proveedor->nombre ?? 'N/A',
                $compra->total_compra,
                $compra->created_at
            ];
        }

        $pdf = Pdf::loadView('pdf.reporte_general', [
            'titulo' => 'Reporte General de Compras',
            'columnas' => $columnas,
            'datos' => $datos
        ]);

        return $pdf->download('reporte_compras.pdf');
    }
}
