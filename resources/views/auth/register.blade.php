<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - ZAPOTECA</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;500;600;700&family=Bebas+Neue&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root{
            --purple-900: #4b1c71;
            --purple-700: #7f4ca5;
            --purple-500: #b57edc;
            --purple-300: #dbb6ee;
            --purple-100: #fff0ff;
            --text-dark: #2d1f3a;
            --white: #ffffff;
            --font-display: 'Bebas Neue', sans-serif;
            --font-body: 'Archivo', sans-serif;
            --shadow: 0 15px 35px rgba(75, 28, 113, 0.15);
        }

        body {
            background:
                radial-gradient(circle at top right, rgba(181,126,220,.15), transparent 40%),
                radial-gradient(circle at bottom left, rgba(127,76,165,.10), transparent 40%),
                linear-gradient(180deg, #f8f2fb 0%, #fdf9ff 100%);
            font-family: var(--font-body);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }

        .bebas { font-family: var(--font-display); letter-spacing: 1px; }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            box-shadow: var(--shadow);
            border: 1px solid var(--purple-300);
            width: 100%;
            max-width: 500px;
            padding: 40px;
            position: relative;
        }

        .login-card::before {
            content: "";
            position: absolute;
            top: 0; left: 0; right: 0; height: 6px;
            background: linear-gradient(90deg, var(--purple-700), var(--purple-900));
            border-radius: 24px 24px 0 0;
        }

        .login-logo {
            width: 100px;
            margin-bottom: 10px;
            filter: drop-shadow(0 5px 10px rgba(75, 28, 113, 0.1));
        }

        .form-label { font-size: 0.85rem; color: var(--purple-900); margin-bottom: 4px; }

        .form-control, .form-select {
            border-radius: 12px;
            padding: 10px 15px;
            border: 2px solid #eadcf2;
            background-color: #fdf9ff;
            font-size: 0.95rem;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--purple-500);
            box-shadow: 0 0 0 4px rgba(181, 126, 220, 0.1);
        }

        .input-group-text {
            border-radius: 12px 0 0 12px;
            border: 2px solid #eadcf2;
            border-right: none;
            background-color: #fdf9ff;
            color: var(--purple-700);
        }

        .with-icon { border-left: none; }

        .btn-register {
            background-color: var(--purple-900);
            color: var(--white);
            border: none;
            padding: 14px;
            border-radius: 50px;
            font-weight: 700;
            text-transform: uppercase;
            transition: all 0.3s ease;
            box-shadow: 0 8px 20px rgba(75, 28, 113, 0.2);
        }

        .btn-register:hover {
            background-color: var(--purple-700);
            transform: translateY(-2px);
            color: white;
        }
    </style>
</head>
<body>

<div class="login-card text-center">
    <a href="/">
        <img src="{{ asset('img/logo.png') }}" alt="Logo Zapoteca" class="login-logo">
    </a>

    <h2 class="bebas mb-1" style="color: var(--purple-900); font-size: 2.2rem;">CREAR CUENTA</h2>

    @if ($errors->any())
        <div class="alert alert-danger text-start py-2 px-3 shadow-sm mb-4" style="border-radius: 12px; font-size: 0.85rem;">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('register') }}" method="POST" class="text-start">
        @csrf

        <div class="row">
            <div class="col-md-12 mb-3">
                <label class="form-label fw-bold">Nombre(s)</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-solid fa-address-card"></i></span>
                    <input type="text" name="nombre" class="form-control with-icon" placeholder="Ingresa tu nombre" value="{{ old('nombre') }}" required autofocus>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Apellido Paterno</label>
                <input type="text" name="apellido_paterno" class="form-control" placeholder="Ingresa tu primer apellido" value="{{ old('apellido_paterno') }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Apellido Materno</label>
                <input type="text" name="apellido_materno" class="form-control" placeholder="Ingresa tu segundo apellido" value="{{ old('apellido_materno') }}">
            </div>

            <div class="col-md-12 mb-3">
                <label class="form-label fw-bold">Género</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-solid fa-venus-mars"></i></span>
                    <select name="genero" class="form-select with-icon" required>
                        <option value="" selected disabled>Selecciona tu género</option>
                        <option value="Hombre" {{ old('genero') == 'Hombre' ? 'selected' : '' }}>Hombre</option>
                        <option value="Mujer" {{ old('genero') == 'Mujer' ? 'selected' : '' }}>Mujer</option>
                        <option value="Otro" {{ old('genero') == 'Otro' ? 'selected' : '' }}>Otro</option>
                    </select>
                </div>
            </div>

            <div class="col-md-12 mb-3">
                <label class="form-label fw-bold">Correo Electrónico</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
                    <input type="email" name="correo" class="form-control with-icon" placeholder="ingresa tu correo" value="{{ old('correo') }}" required>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Contraseña</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                    <input type="password" name="contrasena" class="form-control with-icon" placeholder="Ingresa tu contraseña" required>
                </div>
            </div>
            <div class="col-md-6 mb-4">
                <label class="form-label fw-bold">Confirmar</label>
                <input type="password" name="contrasena_confirmation" class="form-control" placeholder="Confirma tu contraseña" required>
            </div>
        </div>
        <div class="mb-3 text-start">
            <label class="form-label fw-bold">Tipo de Cuenta</label>
            <select name="rol_id" class="form-control" id="rol_select">
                <option value="3" selected>Cliente (Acceso inmediato)</option>
                <option value="2">Usuario Staff (Requiere aprobación)</option>
                <option value="1">Administrador (Requiere aprobación)</option>
            </select>
        </div>

        <button type="submit" class="btn btn-register w-100">
            <i class="fa-solid fa-user-plus me-2"></i> REGISTRAR
        </button>
    </form>

    <p class="mt-4 text-center" style="font-size: 0.85rem;">
        ¿Ya tienes cuenta? <a href="{{ route('login') }}" class="fw-bold" style="color: var(--purple-700); text-decoration: none;">Inicia sesión aquí</a>
    </p>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
