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
            max-width: 600px; /* Un poco más ancho para el grid de registro */
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

        /* --- INPUTS Y SELECTS --- */
        .form-control, .form-select {
            border-radius: 12px;
            padding: 10px 15px;
            border: 2px solid #eadcf2;
            background-color: #fdf9ff;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        /* --- ESCUDO ANTI-BOOTSTRAP (MORADO) --- */
        .form-control.is-invalid, .form-select.is-invalid {
            border-color: var(--purple-700) !important;
            background-color: var(--purple-100) !important;
            background-image: none !important;
            box-shadow: 0 0 0 4px rgba(127, 76, 165, 0.1) !important;
        }

        .invalid-feedback { display: none !important; }

        .zapoteca-error {
            display: flex !important;
            align-items: center;
            color: var(--purple-700) !important;
            font-weight: 700;
            font-size: 0.75rem;
            margin-top: 6px;
            background: var(--purple-100);
            padding: 6px 12px;
            border-radius: 8px;
            border-left: 3px solid var(--purple-900);
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }

        /* --- ICONOS --- */
        .input-group-text {
            border-radius: 12px 0 0 12px;
            border: 2px solid #eadcf2;
            border-right: none;
            background-color: #fdf9ff;
            color: var(--purple-700);
        }

        .input-group:has(.is-invalid) .input-group-text {
            border-color: var(--purple-700) !important;
            color: var(--purple-900) !important;
            background-color: var(--purple-100) !important;
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
            letter-spacing: 1px;
            transition: all 0.3s ease;
            box-shadow: 0 8px 20px rgba(75, 28, 113, 0.2);
        }

        .btn-register:hover {
            background-color: var(--purple-700);
            transform: translateY(-2px);
            box-shadow: 0 12px 25px rgba(75, 28, 113, 0.3);
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
    <p class="text-muted mb-4" style="font-size: 0.9rem;">Únete a nuestra comunidad bibliotecaria</p>

    <form action="{{ route('register') }}" method="POST" class="text-start" novalidate>
    @csrf

    <div class="row">
        <div class="col-md-12 mb-3">
            <label class="form-label fw-bold">Nombre(s)</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-address-card"></i></span>
                <input type="text" name="nombre" class="form-control with-icon @error('nombre') is-invalid @enderror" value="{{ old('nombre') }}" placeholder="Tu nombre">
            </div>
            @error('nombre') <div class="zapoteca-error"><i class="fa-solid fa-circle-exclamation me-2"></i> {{ $message }}</div> @enderror
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Apellido Paterno</label>
            <input type="text" name="apellido_paterno" class="form-control @error('apellido_paterno') is-invalid @enderror" value="{{ old('apellido_paterno') }}" placeholder="Primer apellido">
            @error('apellido_paterno') <div class="zapoteca-error"><i class="fa-solid fa-circle-exclamation me-2"></i> {{ $message }}</div> @enderror
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Apellido Materno</label>
            <input type="text" name="apellido_materno" class="form-control @error('apellido_materno') is-invalid @enderror" value="{{ old('apellido_materno') }}" placeholder="Segundo apellido">
            @error('apellido_materno') <div class="zapoteca-error"><i class="fa-solid fa-circle-exclamation me-2"></i> {{ $message }}</div> @enderror
        </div>

        <div class="col-md-12 mb-3">
            <label class="form-label fw-bold">Género</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-venus-mars"></i></span>
                <select name="genero" class="form-select with-icon @error('genero') is-invalid @enderror">
                    <option value="" selected disabled>Selecciona tu género</option>
                    <option value="Hombre" {{ old('genero') == 'Hombre' ? 'selected' : '' }}>Hombre</option>
                    <option value="Mujer" {{ old('genero') == 'Mujer' ? 'selected' : '' }}>Mujer</option>
                    <option value="Otro" {{ old('genero') == 'Otro' ? 'selected' : '' }}>Otro</option>
                </select>
            </div>
            @error('genero') <div class="zapoteca-error"><i class="fa-solid fa-circle-exclamation me-2"></i> {{ $message }}</div> @enderror
        </div>

        <div class="col-md-12 mb-3">
            <label class="form-label fw-bold">Correo Electrónico</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
                <input type="email" name="correo" class="form-control with-icon @error('correo') is-invalid @enderror" value="{{ old('correo') }}" placeholder="correo@ejemplo.com">
            </div>
            @error('correo') <div class="zapoteca-error"><i class="fa-solid fa-circle-exclamation me-2"></i> {{ $message }}</div> @enderror
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label fw-bold">Contraseña</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                <input type="password" name="contrasena" class="form-control with-icon @error('contrasena') is-invalid @enderror" placeholder="Mín. 6 caracteres">
            </div>
            @error('contrasena') <div class="zapoteca-error"><i class="fa-solid fa-circle-exclamation me-2"></i> {{ $message }}</div> @enderror
        </div>

        <div class="col-md-6 mb-4">
            <label class="form-label fw-bold">Confirmar</label>
            <input type="password" name="contrasena_confirmation" class="form-control" placeholder="Repite contraseña">
        </div>
    </div>

    <div class="mb-4">
        <label class="form-label fw-bold">Tipo de Cuenta</label>
        <select name="rol_id" class="form-select @error('rol_id') is-invalid @enderror">
            <option value="" selected disabled>Selecciona tu nivel de acceso</option>
            <option value="2" {{ old('rol_id') == '2' ? 'selected' : '' }}>Gerente (Gestión de Stock)</option>
            <option value="1" {{ old('rol_id') == '1' ? 'selected' : '' }}>Administrador (Control Total)</option>
        </select>
        @error('rol_id') <div class="zapoteca-error"><i class="fa-solid fa-circle-exclamation me-2"></i> {{ $message }}</div> @enderror
    </div>

    <button type="submit" class="btn btn-register w-100">
        <i class="fa-solid fa-user-plus me-2"></i> REGISTRAR CUENTA
    </button>
</form>
    <p class="mt-4 text-center" style="font-size: 0.85rem;">
        ¿Ya tienes cuenta? <a href="{{ route('login') }}" class="fw-bold" style="color: var(--purple-700); text-decoration: none;">Inicia sesión aquí</a>
    </p>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>