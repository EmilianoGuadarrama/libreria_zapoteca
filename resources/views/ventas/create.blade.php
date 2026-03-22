<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nueva venta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body{
            background:#f4efff;
            font-family:Arial, Helvetica, sans-serif;
        }
        .page-title{
            color:#4b1d95;
            font-weight:700;
        }
        .card-custom{
            border:none;
            border-radius:20px;
            box-shadow:0 10px 25px rgba(75,29,149,0.10);
        }
        .card-header-custom{
            background:#5b21b6;
            color:#fff;
            border-radius:20px 20px 0 0 !important;
            padding:18px 24px;
            font-weight:600;
        }
        .form-label{
            color:#4b1d95;
            font-weight:600;
        }
        .form-control{
            border-radius:14px;
            border:1px solid #d8c7ff;
            padding:12px 14px;
        }
        .form-control:focus{
            border-color:#5b21b6;
            box-shadow:0 0 0 0.2rem rgba(91,33,182,0.15);
        }
        .btn-primary-custom{
            background:#5b21b6;
            border:none;
            color:#fff;
        }
        .btn-primary-custom:hover{
            background:#4b1d95;
            color:#fff;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <h1 class="page-title mb-0">Nueva venta</h1>
            <a href="{{ route('ventas.index') }}" class="btn btn-outline-secondary rounded-pill px-4">Regresar</a>
        </div>

        @if($errors->any())
            <div class="alert alert-danger rounded-4 shadow-sm">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card card-custom">
            <div class="card-header card-header-custom">
                Formulario de venta
            </div>
            <div class="card-body p-4">
                <form action="{{ route('ventas.store') }}" method="POST">
                    @csrf

                    <div class="row g-4">
                        <div class="col-md-6">
                            <label for="folio" class="form-label">Folio</label>
                            <input type="text" name="folio" id="folio" class="form-control" value="{{ old('folio') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label for="usuario_id" class="form-label">ID Usuario</label>
                            <input type="number" name="usuario_id" id="usuario_id" class="form-control" value="{{ old('usuario_id') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label for="fecha" class="form-label">Fecha</label>
                            <input type="datetime-local" name="fecha" id="fecha" class="form-control" value="{{ old('fecha') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label for="total" class="form-label">Total</label>
                            <input type="number" step="0.01" name="total" id="total" class="form-control" value="{{ old('total') }}" required>
                        </div>

                        <div class="col-md-6">
                            <label for="monto_recibido" class="form-label">Monto recibido</label>
                            <input type="number" step="0.01" name="monto_recibido" id="monto_recibido" class="form-control" value="{{ old('monto_recibido') }}">
                        </div>

                        <div class="col-md-6">
                            <label for="cambio" class="form-label">Cambio</label>
                            <input type="number" step="0.01" name="cambio" id="cambio" class="form-control" value="{{ old('cambio') }}">
                        </div>
                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary-custom rounded-pill px-4">Guardar venta</button>
                        <a href="{{ route('ventas.index') }}" class="btn btn-outline-secondary rounded-pill px-4">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>