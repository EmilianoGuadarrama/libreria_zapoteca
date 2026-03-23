<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class GeneroController extends Controller
{
    public function index()
    {
        $generos = DB::table('generos')
            ->whereNull('deleted_at')
            ->orderBy('nombre', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('generos.index', compact('generos'));
    }

    public function create()
    {
        return redirect()->route('generos.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:255',
            ],
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
        ]);

        $nombre = trim($request->nombre);

        $existente = DB::table('generos')
            ->whereRaw('upper(trim(nombre)) = ?', [mb_strtoupper($nombre)])
            ->first();

        if ($existente && is_null($existente->deleted_at)) {
            return redirect()->route('generos.index')
                ->withErrors(['error' => 'Ya existe un género activo con ese nombre.'])
                ->withInput();
        }

        if ($existente && !is_null($existente->deleted_at)) {
            DB::table('generos')
                ->where('id', $existente->id)
                ->update([
                    'deleted_at' => null,
                    'updated_at' => now(),
                ]);

            return redirect()->route('generos.index')
                ->with('status', 'El género ya existía y fue restaurado correctamente.');
        }

        DB::table('generos')->insert([
            'nombre' => $nombre,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('generos.index')
            ->with('status', 'Género registrado correctamente.');
    }

    public function show(string $id)
    {
        return redirect()->route('generos.index');
    }

    public function edit(string $id)
    {
        return redirect()->route('generos.index');
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:255',
            ],
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
        ]);

        $nombre = trim($request->nombre);

        $duplicado = DB::table('generos')
            ->whereRaw('upper(trim(nombre)) = ?', [mb_strtoupper($nombre)])
            ->where('id', '<>', $id)
            ->whereNull('deleted_at')
            ->first();

        if ($duplicado) {
            return redirect()->route('generos.index')
                ->withErrors(['error' => 'Ya existe otro género activo con ese nombre.'])
                ->withInput();
        }

        DB::table('generos')
            ->where('id', $id)
            ->update([
                'nombre' => $nombre,
                'updated_at' => now(),
            ]);

        return redirect()->route('generos.index')
            ->with('status', 'Género actualizado correctamente.');
    }

    public function destroy(string $id)
    {
        DB::table('generos')
            ->where('id', $id)
            ->update([
                'deleted_at' => now(),
                'updated_at' => now(),
            ]);

        return redirect()->route('generos.index')
            ->with('status', 'Género eliminado correctamente.');
    }
}
