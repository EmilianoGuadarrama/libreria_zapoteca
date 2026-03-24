@extends('layouts.dashboard')

@section('dashboard-content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0 text-dark fw-bold bebas" style="font-size: 2rem; color: #4b1c71;">Asignación de Autores</h3>
        <button type="button" class="btn btn-link p-0 text-decoration-none fs-2" 
                data-bs-toggle="modal" data-bs-target="#modalAsignar" title="Asignar Nuevo Autor">
            <i class="fa-solid fa-circle-plus" style="color: #4b1c71;"></i>
        </button>
    </div>

    @if(session('status'))
        <div class="alert alert-dismissible fade show shadow-sm" role="alert" style="background-color: #fff0ff; color: #4b1c71; border: 1px solid #dbb6ee; border-radius: 12px;">
            <i class="fa-solid fa-check-circle me-2"></i> {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="table-responsive shadow-sm" style="border-radius: 12px; overflow: hidden;">
        <table class="table table-bordered table-striped mi-datatable mb-0">
            <thead style="background-color: #fff0ff; color: #4b1c71;">
                <tr class="bebas">
                    <th>Libro</th>
                    <th>Autores Asignados</th>
                </tr>
            </thead>
            <tbody>
                @foreach($libros as $libro)
                <tr>
                    <td class="fw-bold" style="width: 35%;">{{ $libro->titulo }}</td>
                    <td>
                        @forelse($libro->autores as $autor)
                            <div class="d-inline-flex align-items-center badge rounded-pill me-2 mb-2" style="background-color: #4b1c71; color: white; padding: 0.6rem 1rem;">
                                <span class="me-2">{{ $autor->persona->nombre }} {{ $autor->persona->apellido_paterno }}</span>
                                
                                {{-- ICONO EDITAR INDIVIDUAL --}}
                                <a href="#" class="text-white me-2" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $libro->id }}{{ $autor->id }}">
                                    <i class="fa-solid fa-pen-to-square" style="font-size: 0.85rem;"></i>
                                </a>

                                {{-- ICONO ELIMINAR INDIVIDUAL --}}
                                <a href="#" class="text-white" data-bs-toggle="modal" data-bs-target="#modalDelete{{ $libro->id }}{{ $autor->id }}">
                                    <i class="fa-regular fa-trash-can" style="font-size: 0.85rem;"></i>
                                </a>
                            </div>

                            {{-- MODAL EDITAR INDIVIDUAL --}}
                            <div class="modal fade" id="modalEdit{{ $libro->id }}{{ $autor->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                                        <div class="modal-header border-0 text-white" style="background-color: #4b1c71; border-radius: 20px 20px 0 0;">
                                            <h5 class="modal-title bebas fs-4"><i class="fa-solid fa-pen-to-square me-2"></i> Editar Asignación</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="{{ route('asigna_autor.update', $libro->id) }}" method="post">
                                            @csrf @method('PUT')
                                            <div class="modal-body p-4 text-start">
                                                <input type="hidden" name="old_autor_id" value="{{ $autor->id }}">
                                                <label class="form-label fw-bold">Cambiar a {{ $autor->persona->nombre }} por:</label>
                                                <select name="new_autor_id" class="form-select" required>
                                                    @foreach($autores as $a)
                                                        <option value="{{ $a->id }}" {{ $a->id == $autor->id ? 'selected' : '' }}>
                                                            {{ $a->persona->nombre }} {{ $a->persona->apellido_paterno }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="modal-footer border-0 p-4 pt-0">
                                                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn text-white rounded-pill px-4" style="background-color: #4b1c71;">Actualizar</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            {{-- MODAL ELIMINAR INDIVIDUAL (TU ESTILO) --}}
                            <div class="modal fade" id="modalDelete{{ $libro->id }}{{ $autor->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                                        <div class="modal-header border-0 bg-danger text-white" style="border-radius: 20px 20px 0 0;">
                                            <h5 class="modal-title bebas fs-4"><i class="fa-solid fa-triangle-exclamation me-2"></i> Confirmar Eliminación</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body p-4 text-center">
                                            <p class="fs-5 mb-0">¿Quitar a <strong>{{ $autor->persona->nombre }}</strong> del libro <strong>"{{ $libro->titulo }}"</strong>?</p>
                                        </div>
                                        <div class="modal-footer border-0 p-4 pt-0 justify-content-center">
                                            <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                                            <form action="{{ route('asigna_autor.destroy', $libro->id) }}" method="post">
                                                @csrf @method('DELETE')
                                                <input type="hidden" name="autor_id" value="{{ $autor->id }}">
                                                <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold shadow-sm">Sí, eliminar vínculo</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <span class="text-muted small">Sin autores asignados</span>
                        @endforelse
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- MODAL AGREGAR NUEVO --}}
<div class="modal fade" id="modalAsignar" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-0 text-white" style="background-color: #4b1c71; border-radius: 20px 20px 0 0;">
                <h5 class="modal-title bebas fs-4"><i class="fa-solid fa-circle-plus me-2"></i> Nueva Asignación</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('asigna_autor.store') }}" method="post">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3 text-start">
                        <label class="form-label fw-bold">Libro</label>
                        <select name="libro_id" class="form-select" required>
                            <option value="" selected disabled>Selecciona un libro...</option>
                            @foreach($libros as $l)
                                <option value="{{ $l->id }}">{{ $l->titulo }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3 text-start">
                        <label class="form-label fw-bold">Autor(es) a sumar</label>
                        <select name="autor_ids[]" class="form-select" multiple style="height: 120px;" required>
                            @foreach($autores as $a)
                                <option value="{{ $a->id }}">{{ $a->persona->nombre }} {{ $a->persona->apellido_paterno }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn text-white rounded-pill px-4" style="background-color: #4b1c71;">Asignar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection