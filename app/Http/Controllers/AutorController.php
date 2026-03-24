<?php

namespace App\Http\Controllers;

use App\Models\Autor;
use App\Models\Persona;
use App\Models\Nacionalidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AutorController extends Controller
{
    public function index()
    {
        
        $autores = Autor::with(['persona', 'nacionalidad'])->get();
        $nacionalidades = Nacionalidad::orderBy('nombre', 'asc')->get();
        
        return view('autores.index', compact('autores', 'nacionalidades'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'           => 'required|string|max:200',
            'apellido_paterno' => 'required|string|max:200',
            'genero'           => 'required|in:Hombre,Mujer,Otro',
            'nacionalidad_id'  => 'required|exists:nacionalidades,id',
            'biografia'        => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($request) {
                
                $persona = Persona::create([
                    'nombre'           => $request->nombre,
                    'apellido_paterno' => $request->apellido_paterno,
                    'apellido_materno' => $request->apellido_materno,
                    'genero'           => $request->genero,
                ]);

                
                Autor::create([
                    'persona_id'      => $persona->id,
                    'nacionalidad_id' => $request->nacionalidad_id,
                    'biografia'       => $request->biografia,
                ]);
            });

            return redirect()->route('autores.index')->with('status', '¡Autor y datos personales registrados!');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['error' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        $autor = Autor::findOrFail($id);
        
        try {
            DB::transaction(function () use ($request, $autor) {
                
                $autor->persona->update([
                    'nombre'           => $request->nombre,
                    'apellido_paterno' => $request->apellido_paterno,
                    'apellido_materno' => $request->apellido_materno,
                    'genero'           => $request->genero,
                ]);

                $autor->update([
                    'nacionalidad_id' => $request->nacionalidad_id,
                    'biografia'       => $request->biografia,
                ]);
            });

            return redirect()->route('autores.index')->with('status', '¡Datos del autor actualizados!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'No se pudo actualizar.']);
        }
    }

    public function destroy($id)
    {
        $autor = Autor::findOrFail($id);
        $autor->delete(); 
        return redirect()->route('autores.index')->with('status', '¡Autor eliminado!');
    }
}