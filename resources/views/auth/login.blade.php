<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - ZAPOTECA</title>

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
            --text-muted: #7a6a88;
            --white: #ffffff;

            --font-display: 'Bebas Neue', sans-serif;
            --font-body: 'Archivo', system-ui, -apple-system, sans-serif;

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
        }

        .bebas {
            font-family: var(--font-display);
            letter-spacing: 1px;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            box-shadow: var(--shadow);
            border: 1px solid var(--purple-300);
            width: 100%;
            max-width: 450px;
            padding: 40px;
            overflow: hidden;
            position: relative;
        }

        .login-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(90deg, var(--purple-700), var(--purple-900));
        }

        .login-logo {
            width: 120px;
            height: auto;
            margin-bottom: 15px;
            filter: drop-shadow(0 8px 12px rgba(75, 28, 113, 0.2));
        }

        .form-control {
            border-radius: 12px;
            padding: 12px 18px;
            border: 2px solid #eadcf2;
            background-color: #fdf9ff;
            color: var(--text-dark);
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--purple-500);
            box-shadow: 0 0 0 4px rgba(181, 126, 220, 0.15);
            background-color: var(--white);
        }

        .input-group-text {
            border-radius: 12px 0 0 12px;
            border: 2px solid #eadcf2;
            border-right: none;
            background-color: #fdf9ff;
            color: var(--purple-700);
        }

        .form-control.with-icon {
            border-left: none;
            padding-left: 0;
        }

        /* Ajuste de foco para cuando hay ícono */
        .input-group:focus-within .input-group-text,
        .input-group:focus-within .form-control {
            border-color: var(--purple-500);
            background-color: var(--white);
        }

        .btn-login {
            background-color: var(--purple-900);
            color: var(--white);
            border: none;
            padding: 14px;
            border-radius: 50px; /* Pill shape */
            font-weight: 700;
            font-size: 1.1rem;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            box-shadow: 0 8px 20px rgba(75, 28, 113, 0.25);
        }

        .btn-login:hover {
            background-color: var(--purple-700);
            transform: translateY(-2px);
            box-shadow: 0 12px 25px rgba(75, 28, 113, 0.35);
            color: white;
        }
    </style>
</head>
<body>

<div class="login-card text-center">
    <img src="{{ asset('img/logo.png') }}" alt="Logo Catálogo" class="login-logo">

    <h2 class="bebas mb-1" style="color: var(--purple-900); font-size: 2.5rem;">BIENVENIDO</h2>
    <p class="text-muted mb-4" style="font-size: 0.95rem;">Ingresa tus credenciales para continuar</p>

    @if ($errors->any())
        <div class="alert alert-dismissible fade show text-start shadow-sm mb-4" role="alert" style="background-color: var(--purple-100); color: var(--purple-900); border: 1px solid var(--purple-300); border-radius: 12px; font-size: 0.9rem;">
            <i class="fa-solid fa-triangle-exclamation me-2" style="color: var(--purple-700);"></i>
            {{ $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="font-size: 0.75rem;"></button>
        </div>
    @endif

    <form action="{{ url('/login') }}" method="POST" class="text-start">
        @csrf

        <div class="mb-3">
            <label class="form-label fw-bold" style="color: var(--purple-900); font-size: 0.9rem;">Usuario</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
                <input type="text" name="nombre_usuario" class="form-control with-icon" placeholder="Ej. admin_123" value="{{ old('nombre_usuario') }}" required autofocus autocomplete="username">
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label fw-bold" style="color: var(--purple-900); font-size: 0.9rem;">Contraseña</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                <input type="password" name="contrasenia" class="form-control with-icon" placeholder="••••••••" required autocomplete="current-password">
            </div>
        </div>

        <button type="submit" class="btn btn-login w-100 mt-2">
            <i class="fa-solid fa-right-to-bracket me-2"></i> INICIAR SESIÓN
        </button>
    </form>
</div>

</body>
</html>
