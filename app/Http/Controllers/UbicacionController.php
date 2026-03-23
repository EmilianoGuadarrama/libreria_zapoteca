<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UbicacionController extends Controller
{
    public function index()
    {
        $ubicaciones = DB::table('ubicaciones')
            ->leftJoin('generos', 'ubicaciones.genero_id', '=', 'generos.id')
            ->select(
                'ubicaciones.*',
                'generos.nombre as nombre_genero'
            )
            ->whereNull('ubicaciones.deleted_at')
            ->orderBy('ubicaciones.codigo', 'asc')
            ->paginate(10)
            ->withQueryString();

        $generosCatalogo = DB::table('generos')
            ->whereNull('deleted_at')
            ->orderBy('nombre', 'asc')
            ->get();

        return view('ubicaciones.index', compact('ubicaciones', 'generosCatalogo'));
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

        if ($existente && !is_null($existente->deleted_at)) {
            DB::table('ubicaciones')
                ->where('id', $existente->id)
                ->update([
                    'deleted_at' => null,
                    'genero_id' => $request->genero_id,
                    'updated_at' => now(),
                ]);

            return redirect()->route('ubicaciones.index')
                ->with('status', 'La ubicación ya existía y fue restaurada correctamente.');
        }

        DB::table('ubicaciones')->insert([
            'pasillo' => $pasillo,
            'estante' => $estante,
            'nivel' => $nivel,
            'genero_id' => $request->genero_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('ubicaciones.index')->with('status', 'Ubicación registrada correctamente.');
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
