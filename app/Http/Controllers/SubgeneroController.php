<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubgeneroController extends Controller
{
    public function index()
    {
        $subgeneros = DB::table('subgeneros')
            ->leftJoin('generos', 'subgeneros.genero_id', '=', 'generos.id')
            ->select(
                'subgeneros.*',
                'generos.nombre as nombre_genero'
            )
            ->whereNull('subgeneros.deleted_at')
            ->orderBy('subgeneros.nombre', 'asc')
            ->paginate(10)
            ->withQueryString();

        $generosCatalogo = DB::table('generos')
            ->whereNull('deleted_at')
            ->orderBy('nombre', 'asc')
            ->get();

        return view('subgeneros.index', compact('subgeneros', 'generosCatalogo'));
    }

    public function create()
    {
        return redirect()->route('subgeneros.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'genero_id' => 'required|integer|exists:generos,id',
        ]);

        $nombre = trim($request->nombre);

        $existente = DB::table('subgeneros')
            ->whereRaw('upper(trim(nombre)) = ?', [mb_strtoupper($nombre)])
            ->where('genero_id', $request->genero_id)
            ->first();

        if ($existente && is_null($existente->deleted_at)) {
            return redirect()->route('subgeneros.index')
                ->withErrors(['error' => 'Ya existe un subgénero activo con ese nombre para ese género.'])
                ->withInput();
        }

        if ($existente && !is_null($existente->deleted_at)) {
            DB::table('subgeneros')
                ->where('id', $existente->id)
                ->update([
                    'deleted_at' => null,
                    'updated_at' => now(),
                ]);

            return redirect()->route('subgeneros.index')
                ->with('status', 'El subgénero ya existía y fue restaurado correctamente.');
        }

        DB::table('subgeneros')->insert([
            'nombre' => $nombre,
            'genero_id' => $request->genero_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('subgeneros.index')->with('status', 'Subgénero registrado correctamente.');
    }

    public function show(string $id)
    {
        return redirect()->route('subgeneros.index');
    }

    public function edit(string $id)
    {
        return redirect()->route('subgeneros.index');
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'genero_id' => 'required|integer|exists:generos,id',
        ]);

        $nombre = trim($request->nombre);

        $duplicado = DB::table('subgeneros')
            ->whereRaw('upper(trim(nombre)) = ?', [mb_strtoupper($nombre)])
            ->where('genero_id', $request->genero_id)
            ->where('id', '<>', $id)
            ->whereNull('deleted_at')
            ->first();

        if ($duplicado) {
            return redirect()->route('subgeneros.index')
                ->withErrors(['error' => 'Ya existe otro subgénero activo con ese nombre para ese género.'])
                ->withInput();
        }

        DB::table('subgeneros')
            ->where('id', $id)
            ->update([
                'nombre' => $nombre,
                'genero_id' => $request->genero_id,
                'updated_at' => now(),
            ]);

        return redirect()->route('subgeneros.index')->with('status', 'Subgénero actualizado correctamente.');
    }

    public function destroy(string $id)
    {
        DB::table('subgeneros')
            ->where('id', $id)
            ->update([
                'deleted_at' => now(),
                'updated_at' => now(),
            ]);

        return redirect()->route('subgeneros.index')->with('status', 'Subgénero eliminado correctamente.');
    }
}
