<?php

namespace App\Http\Controllers;

use App\Models\Nacionalidad;
use App\Models\Pais;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PDO;

class NacionalidadController extends Controller
{
    // Mensajes de validación de formato (Laravel)
    protected $messages = [
        'nombre.required' => 'El nombre de la nacionalidad es obligatorio.',
        'nombre.max'      => 'El nombre es muy largo (máximo 200 caracteres).',
        'pais_id.required' => 'Debes seleccionar un país.',
        'pais_id.exists'   => 'El país seleccionado no es válido.',
    ];

    public function index()
    {
        $nacionalidades = Nacionalidad::with('pais')->orderBy('nombre', 'asc')->get();
        $paises = Pais::orderBy('nombre', 'asc')->get();
        
        return view('nacionalidades.index', compact('nacionalidades', 'paises'));
    }

   public function store(Request $request)
{
    session()->forget('id_edit');

    // Validamos solo el formato (SIN unique de Laravel para evitar mensajes en inglés)
    $request->validate([
        'nombre' => 'required|max:200',
        'pais_id' => 'required|exists:paises,id',
    ], $this->messages);

    try {
        $resultado = "";
        $bindings = [
            'p_nombre'  => $request->nombre,
            'p_pais_id' => $request->pais_id,
            'p_resultado' => [
                'value' => &$resultado,
                'type'  => \PDO::PARAM_STR,
                'length' => 100
            ]
        ];

        \DB::executeProcedure('sp_guardar_nacionalidad', $bindings);

        // Aquí es donde controlamos que NO se actualice y dé el mensaje en español
        if ($resultado === 'ERROR_DUPLICADO') {
            return back()->withInput()->withErrors([
                'nombre' => 'Esta nacionalidad ya está registrada en el sistema con otro país.'
            ]);
        }

        return redirect()->route('nacionalidades.index')
                         ->with('status', '¡Nacionalidad procesada: ' . $resultado . '!');

    } catch (\Exception $e) {
        return back()->withInput()->withErrors([
            'nombre' => 'Error al comunicar con la base de datos: ' . $e->getMessage()
        ]);
    }
}

    public function update(Request $request, $id)
    {
        // En Update usamos el validador de Laravel pero personalizamos el error de unicidad
        $request->validate([
            'nombre'  => 'required|string|max:200|unique:nacionalidades,nombre,' . $id,
            'pais_id' => 'required|exists:paises,id',
        ], array_merge($this->messages, [
            'nombre.unique' => 'No puedes actualizar a este nombre porque ya existe en otra nacionalidad.'
        ]));

        try {
            $nacionalidad = Nacionalidad::findOrFail($id);
            $nacionalidad->update($request->all());
            
            session()->forget('id_edit');
            return redirect()->route('nacionalidades.index')->with('status', '¡Cambios guardados correctamente!');
        } catch (\Exception $e) {
            return back()->withInput()->with('id_edit', $id)
                         ->withErrors(['nombre' => 'No se pudo actualizar el registro.']);
        }
    }

    public function destroy($id)
    {
        try {
            $nacionalidad = Nacionalidad::findOrFail($id);
            $nacionalidad->delete(); 

            return redirect()->route('nacionalidades.index')->with('status', '¡Registro eliminado y país liberado!');
        } catch (\Exception $e) {
            return back()->with('error', 'El registro no se puede eliminar porque está siendo usado en otra parte del sistema.');
        }
    }
}