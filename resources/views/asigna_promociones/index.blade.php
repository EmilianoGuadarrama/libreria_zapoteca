@extends('layouts.dashboard')

@section('dashboard-content')
    <div class="container py-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0 text-dark fw-bold">Libros en Oferta</h3>
            <button type="button" class="btn text-white rounded-pill px-4 fw-bold shadow-sm" style="background-color: #4b1c71;"
                    data-bs-toggle="modal" data-bs-target="#modalAssignPromocion">
                <i class="fa-solid fa-tag me-2"></i> Aplicar Oferta a Libro
            </button>
        </div>

        @if(session('status'))
            <div class="alert alert-dismissible fade show shadow-sm" role="alert" style="background-color: #fff0ff; color: #4b1c71; border: 1px solid #dbb6ee; border-radius: 12px;">
                <i class="fa-solid fa-check-circle me-2" style="color: #7f4ca5;"></i> <span class="fw-semibold">{{ session('status') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert" style="border-radius: 12px;">
                <i class="fa-solid fa-triangle-exclamation me-2"></i> {{ $errors->first('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="table-responsive mb-4">
            <table class="table table-bordered table-striped mi-datatable" style="width:100%">
                <thead style="background-color: #f8f2fb;">
                <tr>
                    <th>#</th>
                    <th>Libro</th>
                    <th>ISBN</th>
                    <th>Promoción Aplicada</th>
                    <th>Descuento</th>
                    <th class="text-end">Acciones</th>
                </tr>
                </thead>
                <tbody>
                @foreach($asignaciones as $asignacion)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="fw-bold" style="color: #4b1c71;">{{ $asignacion->libro_titulo }}</td>
                        <td><small class="text-muted">{{ $asignacion->isbn }}</small></td>
                        <td>{{ $asignacion->promocion_nombre }}</td>
                        <td><span class="badge bg-success">{{ $asignacion->porcentaje_descuento }}%</span></td>
                        <td class="text-end">
                            <button type="button" class="btn btn-link p-0 text-decoration-none fs-5 text-danger"
                                    data-bs-toggle="modal" data-bs-target="#modalQuitarAsignacion{{ $asignacion->id }}"
                                    title="Quitar Oferta del Libro">
                                <i class="fa-solid fa-link-slash"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

    </div>

    <div class="modal fade" id="modalAssignPromocion" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header border-0" style="background-color: #4b1c71; color: white; border-radius: 20px 20px 0 0;">
                    <h5 class="modal-title bebas fs-4"><i class="fa-solid fa-tag me-2"></i> Aplicar Oferta a Libro</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('asigna_promociones.store') }}" method="post">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold" style="color: #4b1c71;">Seleccionar Promoción</label>
                            <select name="promocion_id" class="form-select" required>
                                <option value="" disabled selected>-- Elige una promoción activa --</option>
                                @foreach($promociones as $promo)
                                    <option value="{{ $promo->id }}">{{ $promo->nombre }} ({{ $promo->porcentaje_descuento }}%)</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold" style="color: #4b1c71;">Seleccionar Libro (Edición)</label>
                            <select name="edicion_id" class="form-select" required>
                                <option value="" disabled selected>-- Busca y elige un libro --</option>
                                @foreach($ediciones as $edicion)
                                    <option value="{{ $edicion->id }}">{{ $edicion->titulo }} - ISBN: {{ $edicion->isbn }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn text-white rounded-pill px-4 fw-bold" style="background-color: #4b1c71;">Aplicar Oferta</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @foreach($asignaciones as $asignacion)
        <div class="modal fade" id="modalQuitarAsignacion{{ $asignacion->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                    <div class="modal-header border-0 bg-danger text-white" style="border-radius: 20px 20px 0 0;">
                        <h5 class="modal-title bebas fs-4"><i class="fa-solid fa-link-slash me-2"></i> Quitar Oferta</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4 text-center">
                        <p class="fs-5 mb-1">¿Remover la oferta <strong>"{{ $asignacion->promocion_nombre }}"</strong> <br> del libro <strong>"{{ $asignacion->libro_titulo }}"</strong>?</p>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0 justify-content-center">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                        <form action="{{ route('asigna_promociones.destroy', $asignacion->id) }}" method="post" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">Sí, quitar oferta</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
    @if(session('confirm_replace'))
        @php $data = session('confirm_replace'); @endphp
        <div class="modal fade" id="modalConfirmReplace" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">

                    <div class="modal-header border-0" style="background-color: #7f4ca5; color: white; border-radius: 20px 20px 0 0;">
                        <h5 class="modal-title bebas fs-4">
                            <i class="fa-solid fa-code-compare me-2"></i> Reemplazar Oferta
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body p-4 text-center">
                        <p class="fs-5 mb-3">El libro <br><strong style="color: #4b1c71;">"{{ $data['libro_titulo'] }}"</strong><br> ya tiene una oferta activa:</p>

                        <div class="d-flex flex-column align-items-center gap-2 mb-4">
                            <div class="px-4 py-2 rounded-pill border w-75" style="background-color: #fff0ff; border-color: #dbb6ee !important; color: #7a6a88;">
                                <i class="fa-solid fa-xmark me-2" style="color: #b57edc;"></i> <del>{{ $data['old_promocion_nombre'] }}</del>
                            </div>

                            <i class="fa-solid fa-arrow-down-long" style="color: #b57edc; font-size: 1.2rem;"></i>

                            <div class="px-4 py-2 rounded-pill text-white fw-bold w-75 shadow-sm" style="background-color: #4b1c71;">
                                <i class="fa-solid fa-check me-2"></i> {{ $data['new_promocion_nombre'] }}
                            </div>
                        </div>

                        <p class="text-muted small mb-0">¿Deseas quitar la promoción anterior y aplicar la nueva?</p>
                    </div>

                    <div class="modal-footer border-0 p-4 pt-0 justify-content-center">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal" style="color: #7a6a88;">Mantener Anterior</button>

                        <form action="{{ route('asigna_promociones.store') }}" method="post" class="d-inline">
                            @csrf
                            <input type="hidden" name="promocion_id" value="{{ $data['promocion_id'] }}">
                            <input type="hidden" name="edicion_id" value="{{ $data['edicion_id'] }}">
                            <input type="hidden" name="force_replace" value="1">

                            <button type="submit" class="btn text-white rounded-pill px-4 fw-bold shadow-sm" style="background-color: #7f4ca5; transition: all 0.3s;" onmouseover="this.style.backgroundColor='#4b1c71'" onmouseout="this.style.backgroundColor='#7f4ca5'">
                                Sí, Reemplazar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var myModal = new bootstrap.Modal(document.getElementById('modalConfirmReplace'));
                myModal.show();
            });
        </script>
    @endif
@endsection
