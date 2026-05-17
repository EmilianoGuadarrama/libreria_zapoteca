<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Ubicacion;
use App\Models\Edicion;

class UbicacionController extends Controller
{
    public function index()
    {
        $ubicaciones = Ubicacion::with([
                'genero',
                'lotes.edicion.libro.autores',
                'lotes.edicion.libro.subgeneros',
                'lotes.edicion.libro.genero',
                'lotes.edicion.editorial',
            ])
            ->orderBy('codigo', 'asc')
            ->get();

        $generosCatalogo = DB::table('generos')
            ->whereNull('deleted_at')
            ->orderBy('nombre', 'asc')
            ->get();

        // Ediciones con al menos un lote (para selector de asignación)
        $ediciones = Edicion::with(['libro', 'editorial'])
            ->whereHas('libro')
            ->whereHas('lotes')
            ->orderBy('isbn', 'asc')
            ->get();

        return view('ubicaciones.index', compact('ubicaciones', 'generosCatalogo', 'ediciones'));
    }

    public function create()
    {
        return redirect()->route('ubicaciones.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'pasillo' => 'required|string|max:50',
            'estante' => 'required|string|max:10',
            'nivel' => 'required|string|max:10',
            'genero_id' => 'required|integer|exists:generos,id',
            'edicion_ids' => 'nullable|array',
            'edicion_ids.*' => 'integer|exists:ediciones,id',
        ]);

        $pasillo = trim($request->pasillo);
        $estante = trim($request->estante);
        $nivel = trim($request->nivel);

        $existente = DB::table('ubicaciones')
            ->whereRaw('upper(trim(pasillo)) = ?', [mb_strtoupper($pasillo)])
            ->whereRaw('upper(trim(estante)) = ?', [mb_strtoupper($estante)])
            ->whereRaw('upper(trim(nivel)) = ?', [mb_strtoupper($nivel)])
            ->first();

        if ($existente && is_null($existente->deleted_at)) {
            return redirect()->route('ubicaciones.index')
                ->withErrors(['error' => 'Ya existe una ubicación activa con ese pasillo, estante y nivel.'])
                ->withInput();
        }

        $ubicacionId = null;

        if ($existente && !is_null($existente->deleted_at)) {
            DB::table('ubicaciones')
                ->where('id', $existente->id)
                ->update([
                    'deleted_at' => null,
                    'genero_id' => $request->genero_id,
                    'updated_at' => now(),
                ]);
            $ubicacionId = $existente->id;
        } else {
            $ubicacionId = DB::table('ubicaciones')->insertGetId([
                'pasillo' => $pasillo,
                'estante' => $estante,
                'nivel' => $nivel,
                'genero_id' => $request->genero_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Asignar ediciones (mover lotes a esta ubicación)
        $edicionIds = $request->input('edicion_ids', []);
        if (!empty($edicionIds) && $ubicacionId) {
            DB::table('lotes')
                ->whereIn('edicion_id', $edicionIds)
                ->whereNull('deleted_at')
                ->update(['ubicacion_id' => $ubicacionId]);
        }

        $msg = ($existente && !is_null($existente->deleted_at))
            ? 'La ubicación ya existía y fue restaurada correctamente.'
            : 'Ubicación registrada correctamente.';

        return redirect()->route('ubicaciones.index')->with('status', $msg);
    }

    public function show(string $id)
    {
        return redirect()->route('ubicaciones.index');
    }

    public function edit(string $id)
    {
        return redirect()->route('ubicaciones.index');
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'pasillo' => 'required|string|max:50',
            'estante' => 'required|string|max:10',
            'nivel' => 'required|string|max:10',
            'genero_id' => 'required|integer|exists:generos,id',
            'edicion_ids' => 'nullable|array',
            'edicion_ids.*' => 'integer|exists:ediciones,id',
        ]);

        $pasillo = trim($request->pasillo);
        $estante = trim($request->estante);
        $nivel = trim($request->nivel);

        $duplicado = DB::table('ubicaciones')
            ->whereRaw('upper(trim(pasillo)) = ?', [mb_strtoupper($pasillo)])
            ->whereRaw('upper(trim(estante)) = ?', [mb_strtoupper($estante)])
            ->whereRaw('upper(trim(nivel)) = ?', [mb_strtoupper($nivel)])
            ->where('id', '<>', $id)
            ->whereNull('deleted_at')
            ->first();

        if ($duplicado) {
            return redirect()->route('ubicaciones.index')
                ->withErrors(['error' => 'Ya existe otra ubicación activa con ese pasillo, estante y nivel.'])
                ->withInput();
        }

        DB::table('ubicaciones')
            ->where('id', $id)
            ->update([
                'pasillo' => $pasillo,
                'estante' => $estante,
                'nivel' => $nivel,
                'genero_id' => $request->genero_id,
                'updated_at' => now(),
            ]);

        // Asignar lotes de ediciones seleccionadas a esta ubicación
        // Nota: NO se hace update a null porque LOTES.UBICACION_ID es NOT NULL en Oracle
        $edicionIds = $request->input('edicion_ids', []);
        if (!empty($edicionIds)) {
            DB::table('lotes')
                ->whereIn('edicion_id', $edicionIds)
                ->whereNull('deleted_at')
                ->update(['ubicacion_id' => $id]);
        }

        return redirect()->route('ubicaciones.index')->with('status', 'Ubicación actualizada correctamente.');
    }

    public function destroy(string $id)
    {
        DB::table('ubicaciones')
            ->where('id', $id)
            ->update([
                'deleted_at' => now(),
                'updated_at' => now(),
            ]);

        return redirect()->route('ubicaciones.index')->with('status', 'Ubicación eliminada correctamente.');
    }
}
