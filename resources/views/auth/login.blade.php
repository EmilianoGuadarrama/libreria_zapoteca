<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ZAPOTECA</title>

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
            max-width: 450px;
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
            width: 120px;
            margin-bottom: 15px;
            filter: drop-shadow(0 5px 10px rgba(75, 28, 113, 0.1));
        }

        .form-label { font-size: 0.85rem; color: var(--purple-900); margin-bottom: 4px; }

        .form-control {
            border-radius: 12px;
            padding: 12px 18px;
            border: 2px solid #eadcf2;
            background-color: #fdf9ff;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--purple-500);
            box-shadow: 0 0 0 4px rgba(181, 126, 220, 0.1);
            background-color: var(--white);
        }

        .input-group-text {
            border-radius: 12px 0 0 12px;
            border: 2px solid #eadcf2;
            border-right: none;
            background-color: #fdf9ff;
            color: var(--purple-700);
        }

        .with-icon { border-left: none; }

        .btn-login {
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

        .btn-login:hover {
            background-color: var(--purple-700);
            transform: translateY(-2px);
            box-shadow: 0 12px 25px rgba(75, 28, 113, 0.3);
            color: white;
        }

        .alert {
            border-radius: 12px;
            font-size: 0.85rem;
            border: none;
        }
    </style>
</head>
<body>

<div class="login-card text-center">
    <a href="/">
        <img src="{{ asset('img/logo.png') }}" alt="Logo Zapoteca" class="login-logo">
    </a>

    <h2 class="bebas mb-1" style="color: var(--purple-900); font-size: 2.5rem;">BIENVENIDO</h2>
    <p class="text-muted mb-4" style="font-size: 0.95rem;">Accede al panel de administración</p>

    @if ($errors->any())
        <div class="alert alert-danger text-start py-2 px-3 mb-4 shadow-sm">
            <i class="fa-solid fa-circle-exclamation me-2"></i> {{ $errors->first() }}
        </div>
    @endif

    <form action="{{ route('login') }}" method="POST" class="text-start">
        @csrf

        <div class="mb-3">
            <label class="form-label fw-bold">Correo Electrónico</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
                <input type="email" name="correo" class="form-control with-icon" placeholder="" value="{{ old('correo') }}" required autofocus>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label fw-bold">Contraseña</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                <input type="password" name="contrasena" class="form-control with-icon" placeholder="" required>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4" style="font-size: 0.85rem;">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                <label class="form-check-label text-muted" for="remember">Recordarme</label>
            </div>
            <a href="#" style="color: var(--purple-700); text-decoration: none;">¿Olvidaste tu contraseña?</a>
        </div>

        <button type="submit" class="btn btn-login w-100">
            <i class="fa-solid fa-right-to-bracket me-2"></i> INICIAR SESIÓN
        </button>
    </form>

    <p class="mt-4 text-center" style="font-size: 0.9rem;">
        ¿Eres nuevo? <a href="{{ route('register') }}" class="fw-bold" style="color: var(--purple-700); text-decoration: none;">Regístrate aquí</a>
    </p>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
