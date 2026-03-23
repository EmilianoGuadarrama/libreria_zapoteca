<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AsignaSubgeneroController extends Controller
{
    public function index()
    {
        $asignaciones = DB::table('asigna_subgenero')
            ->leftJoin('libros', 'asigna_subgenero.libro_id', '=', 'libros.id')
            ->leftJoin('subgeneros', 'asigna_subgenero.subgenero_id', '=', 'subgeneros.id')
            ->select(
                'asigna_subgenero.*',
                'libros.titulo as titulo_libro',
                'subgeneros.nombre as nombre_subgenero'
            )
            ->whereNull('asigna_subgenero.deleted_at')
            ->orderBy('asigna_subgenero.id', 'asc')
            ->paginate(10)
            ->withQueryString();

        $librosCatalogo = DB::table('libros')
            ->select('id', 'titulo')
            ->whereNull('deleted_at')
            ->orderBy('titulo', 'asc')
            ->get();

        $subgenerosCatalogo = DB::table('subgeneros')
            ->select('id', 'nombre')
            ->whereNull('deleted_at')
            ->orderBy('nombre', 'asc')
            ->get();

        return view('asigna_subgenero.index', compact('asignaciones', 'librosCatalogo', 'subgenerosCatalogo'));
    }

    public function create()
    {
        return redirect()->route('asigna_subgenero.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'libro_id' => 'required|integer|exists:libros,id',
            'subgenero_id' => 'required|integer|exists:subgeneros,id',
        ]);

        $existente = DB::table('asigna_subgenero')
            ->where('libro_id', $request->libro_id)
            ->where('subgenero_id', $request->subgenero_id)
            ->first();

        if ($existente && is_null($existente->deleted_at)) {
            return redirect()->route('asigna_subgenero.index')
                ->withErrors(['error' => 'Esa asignación ya existe.'])
                ->withInput();
        }

        if ($existente && !is_null($existente->deleted_at)) {
            DB::table('asigna_subgenero')
                ->where('id', $existente->id)
                ->update([
                    'deleted_at' => null,
                    'updated_at' => now(),
                ]);

            return redirect()->route('asigna_subgenero.index')
                ->with('status', 'La asignación ya existía y fue restaurada correctamente.');
        }

        DB::table('asigna_subgenero')->insert([
            'libro_id' => $request->libro_id,
            'subgenero_id' => $request->subgenero_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('asigna_subgenero.index')->with('status', 'Asignación registrada correctamente.');
    }

    public function show(string $id)
    {
        return redirect()->route('asigna_subgenero.index');
    }

    public function edit(string $id)
    {
        return redirect()->route('asigna_subgenero.index');
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'libro_id' => 'required|integer|exists:libros,id',
            'subgenero_id' => 'required|integer|exists:subgeneros,id',
        ]);

        $duplicado = DB::table('asigna_subgenero')
            ->where('libro_id', $request->libro_id)
            ->where('subgenero_id', $request->subgenero_id)
            ->where('id', '<>', $id)
            ->whereNull('deleted_at')
            ->first();

        if ($duplicado) {
            return redirect()->route('asigna_subgenero.index')
                ->withErrors(['error' => 'Ya existe otra asignación activa con esos datos.'])
                ->withInput();
        }

        DB::table('asigna_subgenero')
            ->where('id', $id)
            ->update([
                'libro_id' => $request->libro_id,
                'subgenero_id' => $request->subgenero_id,
                'updated_at' => now(),
            ]);

        return redirect()->route('asigna_subgenero.index')->with('status', 'Asignación actualizada correctamente.');
    }

    public function destroy(string $id)
    {
        DB::table('asigna_subgenero')
            ->where('id', $id)
            ->update([
                'deleted_at' => now(),
                'updated_at' => now(),
            ]);

        return redirect()->route('asigna_subgenero.index')->with('status', 'Asignación eliminada correctamente.');
    }
}
