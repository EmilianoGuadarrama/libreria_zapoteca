<?php

namespace App\Http\Controllers;

use App\Models\Edicion;
use App\Models\Libro;
use App\Models\Editorial;
use App\Models\Idioma;
use App\Models\Formato;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class EdicionController extends Controller
{
    /**
     * Mostrar listado de ediciones.
     */
    public function index()
    {
        $ediciones = Edicion::with([
                'libro',
                'editorial',
                'idioma',
                'formato'
            ])
            ->orderBy('id', 'desc')
            ->get();

        $librosCatalogo = Libro::orderBy('titulo', 'asc')->get();
        $editorialesCatalogo = Editorial::orderBy('nombre', 'asc')->get();
        $idiomasCatalogo = Idioma::orderBy('nombre', 'asc')->get();
        $formatosCatalogo = Formato::orderBy('nombre', 'asc')->get();

        return view('ediciones.index', compact(
            'ediciones',
            'librosCatalogo',
            'editorialesCatalogo',
            'idiomasCatalogo',
            'formatosCatalogo'
        ));
    }

    /**
     * No se usa porque el formulario está en modal.
     */
    public function create()
    {
        return redirect()->route('ediciones.index');
    }

    /**
     * Guardar nueva edición.
     */
    public function store(Request $request)
    {
        $data = $request->validate($this->rules(), $this->messages());

        try {
            DB::beginTransaction();

            if ($request->hasFile('portada')) {
                $data['portada'] = $request->file('portada')->store('ediciones_portadas', 'public');
            }

            Edicion::create($data);

            DB::commit();

            return redirect()
                ->route('ediciones.index')
                ->with('status', 'Edición registrada correctamente.');
        } catch (\Throwable $e) {
            DB::rollBack();

            if (isset($data['portada']) && Storage::disk('public')->exists($data['portada'])) {
                Storage::disk('public')->delete($data['portada']);
            }

            return redirect()
                ->route('ediciones.index')
                ->withInput()
                ->with('error', 'No se pudo registrar la edición. Verifica que no exista una edición repetida para el mismo libro, editorial, idioma, formato, año y número de edición.');
        }
    }

    /**
     * Mostrar detalle individual.
     * En este proyecto normalmente se usa modal, por eso redirige al index.
     */
    public function show(string $id)
    {
        return redirect()->route('ediciones.index');
    }

    /**
     * No se usa porque la edición está en modal.
     */
    public function edit(string $id)
    {
        return redirect()->route('ediciones.index');
    }

    /**
     * Actualizar edición.
     */
    public function update(Request $request, string $id)
    {
        $edicion = Edicion::findOrFail($id);

        $data = $request->validate($this->rules($edicion->id), $this->messages());

        try {
            DB::beginTransaction();

            if ($request->hasFile('portada')) {
                if ($edicion->portada && Storage::disk('public')->exists($edicion->portada)) {
                    Storage::disk('public')->delete($edicion->portada);
                }

                $data['portada'] = $request->file('portada')->store('ediciones_portadas', 'public');
            }

            $edicion->update($data);

            DB::commit();

            return redirect()
                ->route('ediciones.index')
                ->with('status', 'Edición actualizada correctamente.');
        } catch (\Throwable $e) {
            DB::rollBack();

            if (isset($data['portada']) && Storage::disk('public')->exists($data['portada'])) {
                Storage::disk('public')->delete($data['portada']);
            }

            return redirect()
                ->route('ediciones.index')
                ->withInput()
                ->with('error', 'No se pudo actualizar la edición. Verifica que los datos no se repitan con otra edición existente.');
        }
    }

    /**
     * Eliminar edición.
     */
    public function destroy(string $id)
    {
        $edicion = Edicion::findOrFail($id);

        try {
            $edicion->delete();

            return redirect()
                ->route('ediciones.index')
                ->with('status', 'Edición eliminada correctamente.');
        } catch (\Throwable $e) {
            return redirect()
                ->route('ediciones.index')
                ->with('error', 'No se pudo eliminar la edición porque puede estar relacionada con compras, lotes o ventas.');
        }
    }

    /**
     * Reglas de validación.
     */
    private function rules($id = null): array
    {
        return [
            'libro_id' => [
                'required',
                'integer',
                'exists:libros,id',
            ],
            'editorial_id' => [
                'required',
                'integer',
                'exists:editoriales,id',
            ],
            'idioma_id' => [
                'required',
                'integer',
                'exists:idiomas,id',
            ],
            'formato_id' => [
                'required',
                'integer',
                'exists:formatos,id',
            ],
            'isbn' => [
                'required',
                'string',
                'max:17',
                Rule::unique('ediciones', 'isbn')->ignore($id),
            ],
            'anio_publicacion' => [
                'required',
                'integer',
                'min:1000',
                'max:' . date('Y'),
            ],
            'numero_edicion' => [
                'required',
                'integer',
                'min:1',
            ],
            'numero_paginas' => [
                'required',
                'integer',
                'min:1',
            ],
            'precio_venta' => [
                'required',
                'numeric',
                'min:0',
            ],
            'portada' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:4096',
            ],
            'alt_imagen' => [
                'nullable',
                'string',
                'max:255',
            ],
            'existencias' => [
                'required',
                'integer',
                'min:0',
            ],
            'stock_minimo' => [
                'required',
                'integer',
                'min:0',
            ],
        ];
    }

    /**
     * Mensajes personalizados.
     */
    private function messages(): array
    {
        return [
            'libro_id.required' => 'Debe seleccionar un libro.',
            'libro_id.exists' => 'El libro seleccionado no existe.',

            'editorial_id.required' => 'Debe seleccionar una editorial.',
            'editorial_id.exists' => 'La editorial seleccionada no existe.',

            'idioma_id.required' => 'Debe seleccionar un idioma.',
            'idioma_id.exists' => 'El idioma seleccionado no existe.',

            'formato_id.required' => 'Debe seleccionar un formato.',
            'formato_id.exists' => 'El formato seleccionado no existe.',

            'isbn.required' => 'El ISBN es obligatorio.',
            'isbn.max' => 'El ISBN no debe superar los 17 caracteres.',
            'isbn.unique' => 'Ya existe una edición registrada con ese ISBN.',

            'anio_publicacion.required' => 'El año de publicación es obligatorio.',
            'anio_publicacion.integer' => 'El año de publicación debe ser un número entero.',
            'anio_publicacion.min' => 'El año de publicación no es válido.',
            'anio_publicacion.max' => 'El año de publicación no puede ser mayor al año actual.',

            'numero_edicion.required' => 'El número de edición es obligatorio.',
            'numero_edicion.integer' => 'El número de edición debe ser un número entero.',
            'numero_edicion.min' => 'El número de edición debe ser mínimo 1.',

            'numero_paginas.required' => 'El número de páginas es obligatorio.',
            'numero_paginas.integer' => 'El número de páginas debe ser un número entero.',
            'numero_paginas.min' => 'El número de páginas debe ser mínimo 1.',

            'precio_venta.required' => 'El precio de venta es obligatorio.',
            'precio_venta.numeric' => 'El precio de venta debe ser numérico.',
            'precio_venta.min' => 'El precio de venta no puede ser negativo.',

            'portada.image' => 'El archivo debe ser una imagen.',
            'portada.mimes' => 'La portada debe estar en formato JPG, JPEG, PNG o WEBP.',
            'portada.max' => 'La portada no debe pesar más de 4 MB.',

            'alt_imagen.max' => 'El texto alternativo no debe superar los 255 caracteres.',

            'existencias.required' => 'Las existencias son obligatorias.',
            'existencias.integer' => 'Las existencias deben ser un número entero.',
            'existencias.min' => 'Las existencias no pueden ser negativas.',

            'stock_minimo.required' => 'El stock mínimo es obligatorio.',
            'stock_minimo.integer' => 'El stock mínimo debe ser un número entero.',
            'stock_minimo.min' => 'El stock mínimo no puede ser negativo.',
        ];
    }
}